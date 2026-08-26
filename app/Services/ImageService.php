<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ImageService
{
    private ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new GdDriver);
    }

    /**
     * Optimize and store an uploaded image.
     *
     * Resizes to max 1200x1200, converts to WebP (82% quality) when supported,
     * otherwise JPEG. Returns the storage path on the public disk.
     */
    public function storeOptimized(UploadedFile $file, string $directory = 'products'): string
    {
        $image = $this->manager->decode($file->getPathname());

        // Scale down so longest side is 1200px, never upscale.
        $image->scaleDown(width: 1200, height: 1200);

        $useWebp = function_exists('imagewebp');
        $extension = $useWebp ? 'webp' : 'jpg';
        $filename = Str::uuid()->toString().'.'.$extension;
        $path = trim($directory, '/').'/'.$filename;

        if ($useWebp) {
            $encoded = $image->encode(new WebpEncoder(quality: 82));
        } else {
            $encoded = $image->encode(new JpegEncoder(quality: 82));
        }

        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }
}
