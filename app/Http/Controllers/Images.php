<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class Images extends Controller
{
    /**
     * Delete
     */
    public function destroy(Image $image)
    {
        
        $image->category->image_id = null;
        $image->category->save();

        Storage::delete($image->name);

        $image->delete();

        return response()->noContent();
    }
}
