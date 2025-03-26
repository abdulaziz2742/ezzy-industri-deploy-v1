<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud_name' => config('services.cloudinary.cloud_name'),
            'api_key' => config('services.cloudinary.api_key'),
            'api_secret' => config('services.cloudinary.api_secret'),
            'secure' => true
        ]);
    }

    public function upload($file, $options = [])
    {
        try {
            if (!$file->isValid()) {
                throw new \Exception('Invalid file upload');
            }

            // Set default options and merge with provided options
            $uploadOptions = array_merge([
                'folder' => 'sop_images',
                'resource_type' => 'auto'
            ], $options);

            Log::info('Uploading to Cloudinary', [
                'folder' => $uploadOptions['folder'],
                'filename' => $file->getClientOriginalName()
            ]);

            $result = $this->cloudinary->uploadApi()->upload($file->getPathname(), $uploadOptions);

            if (!isset($result['secure_url'])) {
                throw new \Exception('Upload failed - No URL returned');
            }

            return [
                'success' => true,
                'url' => $result['secure_url'],
                'public_id' => $result['public_id']
            ];

        } catch (\Exception $e) {
            Log::error('Cloudinary upload error: ' . $e->getMessage());
            Log::error('File details: ' . json_encode([
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType()
            ]));
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function delete($publicId)
    {
        try {
            $this->cloudinary->uploadApi()->destroy($publicId);
            return true;
        } catch (\Exception $e) {
            Log::error('Cloudinary delete error: ' . $e->getMessage());
            return false;
        }
    }
}