<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        $allCategories = Category::orderBy('name')->get();

        return view('admin.categories.index', compact('categories', 'allCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required','string','max:255'],
            'parent_id' => ['nullable','exists:categories,id'],
        ]);

        // slug unic
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $i = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$i;
            $i++;
        }

        Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'parent_id' => $request->parent_id ?: null,
        ]);

        return back()->with('success', 'Categorie salvată!');
    }

    public function destroy(Category $category)
    {
        // dezleagă produse legate de categoria asta
        Product::where('category_id', $category->id)->update(['category_id' => null]);
        Product::where('subcategory_id', $category->id)->update(['subcategory_id' => null]);

        // șterge subcategoriile întâi (copiii)
        $category->children()->delete();

        $category->delete();

        return back()->with('success', 'Categorie ștearsă!');
    }
}
