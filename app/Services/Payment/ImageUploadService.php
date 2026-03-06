<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use RuntimeException;

class ImageUploadService
{
    /**
     * @var ImageManager
     */
    private ImageManager $imageManager;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Initialize Intervention Image with GD driver
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Upload product image
     *
     * @param UploadedFile $file
     * @param string $productSku
     * @param int $sortOrder
     * @return array<string, string> Paths to original and thumbnail
     * @throws RuntimeException
     */
    public function uploadProductImage(UploadedFile $file, string $productSku, int $sortOrder): array
    {
        try {
            // Generate unique filename
            $timestamp = now()->format('Y-m-d-His');
            $random = substr(md5(uniqid()), 0, 8);
            $extension = $file->getClientOriginalExtension();
            $filename = "{$productSku}-{$timestamp}-{$random}.{$extension}";
            
            // Define paths
            $originalPath = "products/original/{$filename}";
            $thumbnailPath = "products/thumbnail/{$filename}";
            
            // Read uploaded file
            $image = $this->imageManager->read($file->getRealPath());
            
            // Save original image (max width 1200px)
            $image->scaleDown(width: 1200);
            Storage::disk('public')->put($originalPath, (string) $image->encode());
            
            // Create and save thumbnail (300x300 crop)
            $thumbnail = $this->imageManager->read($file->getRealPath());
            $thumbnail->cover(300, 300);
            Storage::disk('public')->put($thumbnailPath, (string) $thumbnail->encode());
            
            return [
                'original' => $originalPath,
                'thumbnail' => $thumbnailPath,
                'filename' => $filename,
            ];
            
        } catch (\Exception $e) {
            throw new RuntimeException("Failed to upload image: " . $e->getMessage());
        }
    }

    /**
     * Delete product images
     *
     * @param array<string> $paths
     * @return bool
     */
    public function deleteImages(array $paths): bool
    {
        try {
            foreach ($paths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Get image URL
     *
     * @param string $path
     * @return string
     */
    public function getImageUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }
}