<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageUploadService
{
    protected ImageManager $imageManager;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver);
    }

    /**
     * Upload a product image and create a thumbnail.
     */
    public function uploadProductImage(UploadedFile $image, string $sku, int $index): array
    {
        // Generate unique filename
        $extension = $image->getClientOriginalExtension();
        $filename = $sku.'_'.$index.'_'.time().'.'.$extension;

        // Store original image
        $originalPath = $image->storeAs('products/original', $filename, 'public');

        // Create and store thumbnail
        $imageResource = $this->imageManager->read($image->getRealPath());
        $imageResource->scale(width: 300);

        $thumbnailFilename = $sku.'_'.$index.'_'.time().'_thumb.'.$extension;
        $thumbnailPath = 'products/thumbnails/'.$thumbnailFilename;

        Storage::disk('public')->put($thumbnailPath, (string) $imageResource->encode());

        return [
            'original' => $originalPath,
            'thumbnail' => $thumbnailPath,
        ];
    }

    /**
     * Delete multiple images from storage.
     */
    public function deleteImages(array $paths): void
    {
        foreach ($paths as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
