<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Support\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->orderByDesc('id')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image'      => ['required', 'image', 'max:4096'],
            'title'      => ['nullable', 'string', 'max:255'],
            'subtitle'   => ['nullable', 'string', 'max:255'],
            'kicker'     => ['nullable', 'string', 'max:255'],
            'link'       => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status'     => ['nullable'],
        ]);

        $imagePath = ImageStorage::storeWebp($request->file('image'), 'banners', 'public', 82, 'image');

        Banner::create([
            'image'      => $imagePath,
            'title'      => $request->title,
            'subtitle'   => $request->subtitle,
            'kicker'     => $request->kicker,
            'link'       => $request->link,
            'sort_order' => $request->sort_order ?? 0,
            'status'     => $request->has('status') ? 1 : 0,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner adăugat!');
    }

    public function destroy(Banner $banner)
    {
        if (!empty($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return back()->with('success', 'Banner șters!');
    }
}
