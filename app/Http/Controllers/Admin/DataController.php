<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProjectValuesExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\Process\Process;
use ZipArchive;

class DataController extends Controller
{
    public function index(Request $request): Response
    {
        $tables = $this->listTables();
        $selected = $request->string('table')->toString();
        if ($selected === '' && $tables) {
            $selected = $tables[0];
        }

        $columns = $selected !== '' ? $this->describeTable($selected) : [];
        $primaryKey = $selected !== '' ? $this->primaryKey($selected) : null;
        $filters = $request->input('filters', []);

        $rows = $selected !== '' ? $this->fetchRows($selected, $columns, $filters, $primaryKey) : null;

        return Inertia::render('Admin/Data/Index', [
            'tables' => $tables,
            'table' => $selected,
            'columns' => $columns,
            'primaryKey' => $primaryKey,
            'rows' => $rows,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $table = $this->validateTable($request->input('table'));
        $columns = $this->describeTable($table);
        $data = $this->sanitizeRow($request->input('data', []), $columns, null);

        DB::table($table)->insert($data);

        return redirect()->back()->with(['message' => 'Row created.']);
    }

    public function update(Request $request): RedirectResponse
    {
        $table = $this->validateTable($request->input('table'));
        $primaryKey = $this->primaryKey($table);
        if (! $primaryKey) {
            return redirect()->back()->withErrors(['table' => 'Table does not have a primary key.']);
        }

        $id = $request->input('id');
        $columns = $this->describeTable($table);
        $data = $this->sanitizeRow($request->input('data', []), $columns, $primaryKey);

        DB::table($table)->where($primaryKey, $id)->update($data);

        return redirect()->back()->with(['message' => 'Row updated.']);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $table = $this->validateTable($request->input('table'));
        $primaryKey = $this->primaryKey($table);
        if (! $primaryKey) {
            return redirect()->back()->withErrors(['table' => 'Table does not have a primary key.']);
        }

        $id = $request->input('id');
        DB::table($table)->where($primaryKey, $id)->delete();

        return redirect()->back()->with(['message' => 'Row deleted.']);
    }

    public function export(Request $request)
    {
        $table = $this->validateTable($request->input('table'));
        $format = strtolower((string) $request->input('format', 'xlsx'));
        if (! in_array($format, ['xlsx', 'csv', 'tsv'], true)) {
            return redirect()->back()->withErrors(['format' => 'Unsupported export format.']);
        }

        $columns = $this->describeTable($table);
        $allowed = collect($columns)->pluck('name')->all();
        $selectedColumns = $request->input('columns', $allowed);
        if (is_string($selectedColumns)) {
            $selectedColumns = array_filter(explode(',', $selectedColumns), 'strlen');
        }
        if (! is_array($selectedColumns)) {
            $selectedColumns = $allowed;
        }
        $selectedColumns = array_values(array_intersect($selectedColumns, $allowed));
        if (! $selectedColumns) {
            return redirect()->back()->withErrors(['columns' => 'No columns selected for export.']);
        }

        $primaryKey = $this->primaryKey($table);
        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids), 'strlen');
        }
        $filters = $request->input('filters', []);

        $query = DB::table($table)->select($selectedColumns);
        if ($primaryKey && is_array($ids) && $ids) {
            $query->whereIn($primaryKey, $ids);
        } elseif (is_array($filters)) {
            foreach ($filters as $key => $value) {
                if ($value === '' || ! in_array($key, $allowed, true)) {
                    continue;
                }
                $query->where($key, 'like', '%'.$value.'%');
            }
        }

        $rows = $query->get();
        $headings = $selectedColumns;
        $collection = new Collection($rows->map(fn ($row) => array_values((array) $row)));
        $filename = $table.'-export.'.$format;
        $writerType = $this->writerType($format);

        return Excel::download(new ProjectValuesExport($headings, $collection), $filename, $writerType);
    }

    public function backup(Request $request)
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            abort(422, 'Backup is supported only for MySQL.');
        }

        $includeFiles = (bool) $request->input('include_files', false);
        $timestamp = now()->format('Ymd_His');
        $backupDir = storage_path('app/backups');
        File::ensureDirectoryExists($backupDir);

        $dumpPath = $backupDir.'/db_'.$timestamp.'.sql';
        $this->runDump($dumpPath);

        if (! $includeFiles) {
            return response()->download($dumpPath)->deleteFileAfterSend();
        }

        $zipPath = $backupDir.'/backup_'.$timestamp.'.zip';
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFile($dumpPath, basename($dumpPath));

        $storagePath = storage_path('app/public');
        if (File::exists($storagePath)) {
            $this->addFolderToZip($zip, $storagePath, 'storage');
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend();
    }

    public function cleanup(Request $request): RedirectResponse
    {
        $tables = $request->input('tables', []);
        $mode = $request->input('mode', 'selected');
        $all = $this->listTables();

        $target = $mode === 'all' ? $all : array_values(array_intersect($tables, $all));
        if (! $target) {
            return redirect()->back()->withErrors(['tables' => 'No tables selected for cleanup.']);
        }

        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
        foreach ($target as $table) {
            if ($driver === 'sqlite') {
                DB::table($table)->delete();
                DB::statement("DELETE FROM sqlite_sequence WHERE name = '{$table}'");
            } else {
                DB::table($table)->truncate();
            }
        }
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        return redirect()->back()->with(['message' => 'Tables cleared.']);
    }

    private function listTables(): array
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $rows = DB::select("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
        } else {
            $database = DB::getDatabaseName();
            $rows = DB::select('SELECT table_name AS name FROM information_schema.tables WHERE table_schema = ? ORDER BY table_name', [$database]);
        }

        return array_map(function ($row) {
            if (isset($row->name)) {
                return $row->name;
            }
            if (isset($row->table_name)) {
                return $row->table_name;
            }
            if (isset($row->TABLE_NAME)) {
                return $row->TABLE_NAME;
            }
            $values = array_values((array) $row);
            return $values[0] ?? '';
        }, $rows);
    }

    private function describeTable(string $table): array
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $rows = DB::select("PRAGMA table_info('{$table}')");
            return array_map(function ($row) {
                return [
                    'name' => $row->name ?? $row->column_name ?? '',
                    'data_type' => $row->type ?? null,
                    'nullable' => isset($row->notnull) ? ((int) $row->notnull === 0) : true,
                    'default' => $row->dflt_value ?? null,
                ];
            }, $rows);
        }

        $database = DB::getDatabaseName();
        $rows = DB::select(
            'SELECT column_name AS name, data_type AS data_type, is_nullable AS is_nullable, column_default AS column_default FROM information_schema.columns WHERE table_schema = ? AND table_name = ? ORDER BY ordinal_position',
            [$database, $table]
        );

        return array_map(function ($row) {
            $name = $row->name ?? $row->column_name ?? $row->COLUMN_NAME ?? (array_values((array) $row)[0] ?? '');
            $dataType = $row->data_type ?? $row->DATA_TYPE ?? (array_values((array) $row)[1] ?? null);
            $nullable = $row->is_nullable ?? $row->IS_NULLABLE ?? (array_values((array) $row)[2] ?? null);
            $default = $row->column_default ?? $row->COLUMN_DEFAULT ?? (array_values((array) $row)[3] ?? null);
            return [
                'name' => $name,
                'data_type' => $dataType,
                'nullable' => $nullable === 'YES',
                'default' => $default,
            ];
        }, $rows);
    }

    private function primaryKey(string $table): ?string
    {
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $rows = DB::select("PRAGMA table_info('{$table}')");
            foreach ($rows as $row) {
                if (isset($row->pk) && (int) $row->pk === 1) {
                    return $row->name ?? null;
                }
            }
            return null;
        }

        $database = DB::getDatabaseName();
        $rows = DB::select(
            'SELECT kcu.column_name AS name FROM information_schema.table_constraints tc JOIN information_schema.key_column_usage kcu ON tc.constraint_name = kcu.constraint_name AND tc.table_schema = kcu.table_schema WHERE tc.table_schema = ? AND tc.table_name = ? AND tc.constraint_type = "PRIMARY KEY" ORDER BY kcu.ordinal_position',
            [$database, $table]
        );

        if (! $rows) {
            return null;
        }

        $first = $rows[0];
        return $first->name ?? $first->column_name ?? $first->COLUMN_NAME ?? (array_values((array) $first)[0] ?? null);
    }

    private function fetchRows(string $table, array $columns, array $filters, ?string $primaryKey)
    {
        $allowed = array_column($columns, 'name');
        $query = DB::table($table);

        if (is_array($filters)) {
            foreach ($filters as $key => $value) {
                $value = trim((string) $value);
                if ($value === '' || ! in_array($key, $allowed, true)) {
                    continue;
                }
                $query->where($key, 'like', '%'.$value.'%');
            }
        }

        if ($primaryKey && in_array($primaryKey, $allowed, true)) {
            $query->orderBy($primaryKey, 'desc');
        } elseif ($allowed) {
            $query->orderBy($allowed[0], 'desc');
        }

        return $query->paginate(20)->through(function ($row) {
            return (array) $row;
        })->withQueryString();
    }

    private function validateTable(?string $table): string
    {
        $table = (string) $table;
        $tables = $this->listTables();
        if (! in_array($table, $tables, true)) {
            abort(404);
        }

        return $table;
    }

    private function sanitizeRow(array $data, array $columns, ?string $primaryKey): array
    {
        $allowed = collect($columns)->keyBy('name');
        $payload = [];

        foreach ($data as $key => $value) {
            if (! $allowed->has($key)) {
                continue;
            }
            if ($primaryKey && $key === $primaryKey) {
                continue;
            }
            $column = $allowed->get($key);
            if ($value === '' && $column['nullable']) {
                $payload[$key] = null;
            } else {
                $payload[$key] = $value;
            }
        }

        return $payload;
    }

    private function writerType(string $format): string
    {
        if ($format === 'csv') return \Maatwebsite\Excel\Excel::CSV;
        if ($format === 'tsv') return \Maatwebsite\Excel\Excel::TSV;
        return \Maatwebsite\Excel\Excel::XLSX;
    }

    private function runDump(string $dumpPath): void
    {
        $config = config('database.connections.mysql');
        $user = $config['username'] ?? 'root';
        $password = $config['password'] ?? '';
        $host = $config['host'] ?? '127.0.0.1';
        $port = (string) ($config['port'] ?? 3306);
        $database = $config['database'] ?? '';

        $command = [
            'mysqldump',
            '--no-tablespaces',
            '-h', $host,
            '-P', $port,
            '-u', $user,
            $database,
        ];

        $env = null;
        if ($password !== '') {
            $env = ['MYSQL_PWD' => $password];
        }

        $process = new Process($command, null, $env);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new \RuntimeException('Database dump failed: '.$process->getErrorOutput());
        }

        File::put($dumpPath, $process->getOutput());
    }

    private function addFolderToZip(ZipArchive $zip, string $folder, string $base): void
    {
        $files = File::allFiles($folder);
        foreach ($files as $file) {
            $relative = $base.'/'.ltrim(str_replace($folder, '', $file->getPathname()), DIRECTORY_SEPARATOR);
            $zip->addFile($file->getPathname(), $relative);
        }
    }
}
