<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\Dompdf\Facade as PDF;

class ImageController extends Controller
{
    public function index() // Отображение главной страницы 
    {
        return view('images.index');
    }

    public function upload(Request $request) // Обработка загрузки изображения
    {
        // Валидация загружаемых файлов
        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'image|mimes:jpeg,png|max:5000', 
        ]);

        $uploadedImages = [];
        foreach ($request->file('images') as $image) {
            $path = $image->store('public/uploads');
            $uploadedImages[] = asset('storage/' . $path); 
            Image::create([
                'path' => $path,
                'original_name' => $image->getClientOriginalName()
            ]);
        }

        return response()->json([
            'success' => true,
            'images' => $uploadedImages,
        ]);
    }

    public function generatePdf(Request $request) // Генерация пдф
    {
        $images = Image::all();

        if ($images->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Не загружено ни одного изображения.'
            ]);
        }

        $imagePaths = $images->pluck('path')->toArray();
        shuffle($imagePaths);
        $imageGroups = array_chunk($imagePaths, rand(1, 4));

        $html = '<html>';
        foreach ($imageGroups as $group) {
            $html .= '<div style="text-align: center;">';
            foreach ($group as $imagePath) {
                $imageUrl = asset('storage/' . $imagePath);
                $html .= '<img src="' . $imageUrl . '" style="max-width: 100%;">'; 
            }
            $html .= '</div>';
        }
        $html .= '</html>';

        $pdf = PDF::loadHTML($html);
        return $pdf->stream('images.pdf', ['Attachment' => 1]);
    }

    // Функция для преобразования URL в base64-строку
    private function convertUrlToBase64($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $imageContent = curl_exec($ch);
        curl_close($ch);

        return base64_encode($imageContent);
    }
}
