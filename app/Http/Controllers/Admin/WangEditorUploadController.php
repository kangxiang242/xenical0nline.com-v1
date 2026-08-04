<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WangEditorUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $path = $request->file('file')->store('editor-images', 'public');

        $url = Storage::url($path);

        return response()->json([
            'errno' => 0,
            'data' => [
                'url' => $url,
                'alt' => '',
                'href' => '',
            ],
        ]);
    }
}