<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Log;  



class ImageUploadController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function store(Request $request)
    {
        try {
            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'error' => 'No file uploaded'
                ]);
            }

            // Log the folder parameter
            Log::info('Upload request', [
                'folder' => $request->input('folder'),
                'file' => $request->file('file')->getClientOriginalName()
            ]);

            $folder = $request->input('folder', 'sop_images');
            $result = $this->cloudinaryService->upload(
                $request->file('file'),
                ['folder' => $folder] // Pass as options array
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'error' => $result['error']
                ]);
            }

            return response()->json([
                'success' => true,  
                'url' => $result['url'],
                'public_id' => $result['public_id']
            ]);
        } catch (\Exception $e) {
            Log::error('Upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
