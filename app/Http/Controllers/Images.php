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

        if ($image->restaurant) {
            $image->restaurant->image_id = null;
            $image->restaurant->save();
        }

        Storage::delete(str_replace("storage/", "", $image->path));

        $image->delete();

        return response()->noContent();
    }
}
