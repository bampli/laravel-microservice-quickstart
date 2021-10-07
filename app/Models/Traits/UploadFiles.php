<?php

namespace App\Models\Traits;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\UploadedFile;

trait UploadFiles
{
    protected abstract function uploadDir();

    /**
     * 
     * @param UploadedFile[] $files 
     * @return void 
     */
    public function uploadFiles(array $files)
    {
        foreach ($files as $file) {
            $this->uploadFile($file);
        }
    }

    public function uploadFile(UploadedFile $file)
    {
        $file->store($this->uploadDir());
    }

    public function deleteFiles(array $files)
    {
        foreach ($files as $file) {
            $this->deleteFile($file);
        }
    }

    /**
     * 
     * @param string|UploadedFile $file 
     * @return void 
     */
    public function deleteFile($file)
    {
        $filename = $file instanceof UploadedFile ? $file->hashName() : $file;
        \Storage::delete("{$this->uploadDir()}/{$filename}");
    }
}