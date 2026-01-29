<?php

namespace App\Providers;

use App\Services\ProjectImportService;
use App\Services\ProjectImportServiceInterface;
use App\Domain\Import\Repositories\ImportTaskRepository;
use App\Domain\Import\Repositories\ImportedRowRepository;
use App\Domain\Import\Services\ExcelParserService;
use App\Domain\Import\Services\ValidationService;
use App\Domain\Import\Services\RowProcessorService;
use App\Domain\Export\Repositories\ExportTaskRepository;
use App\Domain\Export\Services\ExcelExportService;
use App\Domain\Export\Services\ExportDataTransformerService;
use App\Infrastructure\Persistence\Eloquent\EloquentImportTaskRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentImportedRowRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentExportTaskRepository;
use App\Infrastructure\Excel\PhpSpreadsheetParserService;
use App\Infrastructure\Excel\PhpSpreadsheetExportService;
use App\Infrastructure\Validation\DomainValidationService;
use App\Infrastructure\Import\PhpSpreadsheetRowProcessorService;
use App\Infrastructure\Export\DefaultExportDataTransformerService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProjectImportServiceInterface::class, ProjectImportService::class);

        // DDD Repositories - Import Domain
        $this->app->bind(
            ImportTaskRepository::class,
            EloquentImportTaskRepository::class
        );
        $this->app->bind(
            ImportedRowRepository::class,
            EloquentImportedRowRepository::class
        );

        // DDD Repositories - Export Domain
        $this->app->bind(
            ExportTaskRepository::class,
            EloquentExportTaskRepository::class
        );

        // DDD Services - Import Domain
        $this->app->bind(
            ExcelParserService::class,
            PhpSpreadsheetParserService::class
        );
        $this->app->bind(
            ValidationService::class,
            DomainValidationService::class
        );
        $this->app->bind(
            RowProcessorService::class,
            PhpSpreadsheetRowProcessorService::class
        );

        // DDD Services - Export Domain
        $this->app->bind(
            ExcelExportService::class,
            PhpSpreadsheetExportService::class
        );
        $this->app->bind(
            ExportDataTransformerService::class,
            DefaultExportDataTransformerService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
