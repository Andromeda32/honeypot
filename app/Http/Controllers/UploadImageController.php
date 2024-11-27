<?php

namespace App\Http\Controllers;
use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Requests\UploadImageRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadImageController extends Controller
{
    public function store(UploadImageRequest $request)
    {
        try {
            $imageName = Str::random(32) . '.' . $request->image->getClientOriginalExtension();

            // Create Image
            Image::create([
                'name' => $request->name,
                'image' => $imageName,
                'user_id' => auth()->user()->id,
            ]);

            // Save Image in Storage folder
            Storage::disk('public')->put($imageName, file_get_contents($request->image));

            // Return Json Response
            return response()->json([
                'message' => 'Image uploaded successfully',
                'image' => $imageName,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $image = Image::where('user_id', $id)->get();
        return response()->json($image, 200);
    }
}
