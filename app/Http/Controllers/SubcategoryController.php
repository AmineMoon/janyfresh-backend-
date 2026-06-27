<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SubcategoryController extends Controller
{
    // GET /api/subcategories
    public function index()
    {
        return response()->json(
            Subcategory::with('category')->latest()->get()
        );
    }

    // POST /api/subcategories
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
           
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('subcategories', 'public');
        }

        $subcategory = Subcategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Subcategory created successfully',
            'data' => $subcategory
        ], 201);
    }

    // GET /api/subcategories/{id}
    public function show($id)
    {
        $subcategory = Subcategory::with('category')->find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($subcategory);
    }

    // PUT /api/subcategories/{id}
    public function update(Request $request, $id)
    {
        $subcategory = Subcategory::find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ]);

        $data = [
            'category_id' => $request->category_id,
            'name' => $request->name,
        ];

        // handle image update
        if ($request->hasFile('image')) {

            // delete old image
            if ($subcategory->image) {
                Storage::disk('public')->delete($subcategory->image);
            }

            $data['image'] = $request->file('image')->store('subcategories', 'public');
        }

        $subcategory->update($data);

        return response()->json([
            'message' => 'Subcategory updated successfully',
            'data' => $subcategory
        ]);
    }

    // DELETE /api/subcategories/{id}
    public function destroy($id)
    {
        $subcategory = Subcategory::find($id);

        if (!$subcategory) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // delete image from storage
        if ($subcategory->image) {
            Storage::disk('public')->delete($subcategory->image);
        }

        $subcategory->delete();

        return response()->json([
            'message' => 'Subcategory deleted successfully'
        ]);
    }
}