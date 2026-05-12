<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ImageUploadService
{
    public function upload(
        UploadedFile $file,
        string $directory = 'uploads',
        int $maxWidth = 1920
    ): string {
        $filename  = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path      = $file->storeAs("public/{$directory}", $filename);

        return Storage::url($path);
    }

    public function delete(?string $url): void
    {
        if (! $url) return;

        // Convert URL back to storage path
        $path = str_replace('/storage/', 'public/', $url);
        Storage::delete($path);
    }

    public function uploadMultiple(array $files, string $directory): array
    {
        return array_map(
            fn($file) => $this->upload($file, $directory),
            $files
        );
    }
}
