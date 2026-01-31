<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;
    protected $fillable = [
        'path',
        'mime_type',
        'title',
    ];

    protected $table = 'files';

    public static function putAndCreate($dataFile)
    {
        $disk = config('imports.disk', 'public');
        $path = Storage::disk($disk)->put('files/', $dataFile);

        return File::create([
            'path' => $path,
            'mime_type' => $dataFile->getClientMimeType(),
            'title' => $dataFile->getClientOriginalName(),
        ]);
    }
}
