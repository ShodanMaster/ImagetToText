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
            $ocr = new TesseractOCR($fullPath);
            $ocr->lang('eng');
            $ocr->executable('C:\Program Files\Tesseract-OCR\tesseract.exe');
            $text = $ocr->run();

            if (trim($text) === '') {
                throw new \Exception('No text was extracted from the image.');
            }

            ImageText::create([
                'image_text' => $text,
                'image_path' => $path,
            ]);

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
