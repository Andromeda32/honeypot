<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function image($fileName)
    {
        $path = storage_path('app/public/' . $fileName);

        if (!file_exists($path)) {
            return response()->json(['message' => 'File not found.'], 404);
        }

        return Response::download($path);
    }
}
