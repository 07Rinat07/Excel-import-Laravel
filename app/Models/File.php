<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $guarded = false;
    protected $table = 'files';

    public function putAndCreate($dataFile)
    {
        $file = Storage::disk('public')->put('files/', $dataFile);
        File::create([
            'path' => $file,
            'mime_type' => $dataFile->getClientoriginalExtension(),
            'title' => $dataFile->getClientoriginalName(),
        ]);
        return $file;
    }
}
