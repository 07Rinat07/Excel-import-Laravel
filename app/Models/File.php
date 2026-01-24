<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $fillable = [
        'path',
        'mime_type',
        'title',
    ];

    protected $table = 'files';

    public static function putAndCreate($dataFile)
    {
        $path = Storage::disk('public')->put('files/', $dataFile);

        return File::create([
            'path' => $path,
            'mime_type' => $dataFile->getClientMimeType(),
            'title' => $dataFile->getClientOriginalName(),
        ]);
    }
}
