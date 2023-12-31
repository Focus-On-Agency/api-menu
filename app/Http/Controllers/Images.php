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
        if ($image->category) {
            $image->category->image_id = null;
            $image->category->save();
        }

        Storage::delete(str_replace("storage/", "", $image->path));

        $image->delete();

        return response()->noContent();
    }
}
