<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $categories = Category::query()
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.categories.index', compact('categories', 'search'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'], // 5MB max
            'image_url' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        $validated['is_active'] = (bool) $request->boolean('is_active', true);
        
        // Set default sort_order if not provided
        if (!isset($validated['sort_order'])) {
            $maxSortOrder = Category::max('sort_order') ?? 0;
            $validated['sort_order'] = $maxSortOrder + 1;
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $imagesPath = public_path('images/categories');
            
            // Create directory if it doesn't exist
            if (!File::exists($imagesPath)) {
                File::makeDirectory($imagesPath, 0755, true);
            }

            $image = $request->file('featured_image');
            $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Move file to public/images/categories
            $image->move($imagesPath, $filename);
            
            // Set image URL
            $validated['image_url'] = '/images/categories/' . $filename;
        } elseif (empty($validated['image_url'])) {
            // Remove image_url from validated if not provided
            unset($validated['image_url']);
        }

        Category::create($validated);

        return redirect()->route('admin.product-categories.index')->with('success', 'Category created');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug,' . $category->id],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'], // 5MB max
            'image_url' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        $validated['is_active'] = (bool) $request->boolean('is_active');

        // Handle image removal
        if ($request->has('remove_image') && $request->boolean('remove_image')) {
            // Delete old image if it exists and is a local file
            if ($category->image_url && !str_starts_with($category->image_url, 'http')) {
                $oldImagePath = public_path($category->image_url);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }
            // Clear image_url
            $validated['image_url'] = null;
        }
        // Handle featured image upload
        elseif ($request->hasFile('featured_image')) {
            // Delete old image if it exists and is a local file
            if ($category->image_url && !str_starts_with($category->image_url, 'http')) {
                $oldImagePath = public_path($category->image_url);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            $imagesPath = public_path('images/categories');
            
            // Create directory if it doesn't exist
            if (!File::exists($imagesPath)) {
                File::makeDirectory($imagesPath, 0755, true);
            }

            $image = $request->file('featured_image');
            $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Move file to public/images/categories
            $image->move($imagesPath, $filename);
            
            // Set image URL
            $validated['image_url'] = '/images/categories/' . $filename;
        } elseif (empty($validated['image_url'])) {
            // If image_url is empty and no file uploaded, keep existing or set to null
            if (!$request->has('image_url')) {
                // Keep existing image_url if not explicitly cleared
                unset($validated['image_url']);
            }
        }

        $category->update($validated);

        return redirect()->route('admin.product-categories.index')->with('success', 'Category updated');
    }
    
    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'categories' => ['required', 'array'],
            'categories.*.id' => ['required', 'exists:categories,id'],
            'categories.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($request->categories as $item) {
            Category::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Sort order updated']);
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return back()->with('success', 'Category deleted');
    }
}


