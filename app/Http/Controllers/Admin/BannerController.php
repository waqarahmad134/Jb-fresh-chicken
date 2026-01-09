<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(): View
    {
        $placeholderImage = 'https://picsum.photos/seed/jbfreshbanner/900/600';
        $banners = Banner::orderBy('sort_order')->get();

        return view('admin.banners.index', compact('banners', 'placeholderImage'));
    }

    public function create(): View
    {
        return view('admin.banners.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'button_label' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'string', 'max:1000'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'image_url' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = (bool) $request->boolean('is_active', true);

        if (!isset($validated['sort_order'])) {
            $maxSortOrder = Banner::max('sort_order') ?? 0;
            $validated['sort_order'] = $maxSortOrder + 1;
        }

        if ($request->hasFile('featured_image')) {
            $imagesPath = public_path('images/banners');
            if (!File::exists($imagesPath)) {
                File::makeDirectory($imagesPath, 0755, true);
            }

            $image = $request->file('featured_image');
            $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($imagesPath, $filename);
            $validated['image_url'] = '/images/banners/' . $filename;
        } elseif (!filled($validated['image_url'])) {
            unset($validated['image_url']);
        }

        Banner::create($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully!');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'button_label' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'string', 'max:1000'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'image_url' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = (bool) $request->boolean('is_active');

        if ($request->has('remove_image') && $request->boolean('remove_image')) {
            if ($banner->image_url && !str_starts_with($banner->image_url, 'http')) {
                $oldImagePath = public_path($banner->image_url);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            $validated['image_url'] = null;
        } elseif ($request->hasFile('featured_image')) {
            if ($banner->image_url && !str_starts_with($banner->image_url, 'http')) {
                $oldImagePath = public_path($banner->image_url);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            $imagesPath = public_path('images/banners');
            if (!File::exists($imagesPath)) {
                File::makeDirectory($imagesPath, 0755, true);
            }

            $image = $request->file('featured_image');
            $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($imagesPath, $filename);
            $validated['image_url'] = '/images/banners/' . $filename;
        } elseif (!filled($validated['image_url'])) {
            unset($validated['image_url']);
        }

        $banner->update($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully!');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        if ($banner->image_url && !str_starts_with($banner->image_url, 'http')) {
            $imagePath = public_path($banner->image_url);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $banner->delete();

        return back()->with('success', 'Banner deleted successfully!');
    }
}
