<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ImageUploadService
{
    /**
     * Upload a product image and generate thumbnail.
     *
     * @return array{original: string, thumbnail: string}
     */
    public function uploadProductImage(UploadedFile $file, string $sku, int $index): array
    {
        $extension = $file->getClientOriginalExtension();
        $filename = "{$sku}_{$index}_" . uniqid() . ".{$extension}";
        $thumbFilename = "{$sku}_{$index}_" . uniqid() . "_thumb.{$extension}";

        // Store original
        $originalPath = $file->storeAs('products', $filename, 'public');

        // Generate and store thumbnail
        $image = Image::read($file->getRealPath());
        $image->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $thumbPath = 'products/thumbnails/' . $thumbFilename;
        Storage::disk('public')->put($thumbPath, (string) $image->encode());

        return [
            'original' => 'products/' . $filename,
            'thumbnail' => 'products/thumbnails/' . $thumbFilename,
        ];
    }

    /**
     * Delete multiple images from storage.
     */
    public function deleteImages(array $paths): void
    {
        foreach ($paths as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}