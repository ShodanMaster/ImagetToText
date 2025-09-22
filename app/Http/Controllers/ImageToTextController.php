<?php

namespace App\Http\Controllers;

use App\Models\ImageText;
use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ImageToTextController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $path = $request->file('image')->store('uploads', 'public');
        $fullPath = storage_path('app/public/' . $path);

        try {
            $text = (new TesseractOCR($fullPath))
                ->lang('eng') // optional, defaults to eng
                ->run();

            $imageText = new ImageText();
            $imageText->image_text = $text;
            $imageText->image_path = $path;
            $imageText->save();

            return response()->json([
                'success' => true,
                'text' => $text,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to extract text from image.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
