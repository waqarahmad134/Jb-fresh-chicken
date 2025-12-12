<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');

        $products = Product::query()
            ->with('category')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.products.index', compact('products', 'search'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'description' => ['nullable', 'string'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'], // 5MB max per image
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = (bool) $request->boolean('is_active', true);
        $validated['is_featured'] = (bool) $request->boolean('is_featured');
        
        // Handle compare_at_price (sale_price)
        if (isset($validated['sale_price'])) {
            $validated['compare_at_price'] = $validated['sale_price'];
            unset($validated['sale_price']);
        }

        $product = Product::create($validated);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagesPath = public_path('images/products');
            
            // Create directory if it doesn't exist
            if (!File::exists($imagesPath)) {
                File::makeDirectory($imagesPath, 0755, true);
            }

            foreach ($request->file('images') as $index => $image) {
                // Generate unique filename
                $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // Move file to public/images/products
                $image->move($imagesPath, $filename);
                
                // Create relative path for database
                $imageUrl = '/images/products/' . $filename;
                
                // Create ProductImage record
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $imageUrl,
                    'alt_text' => $product->name . ' - Image ' . ($index + 1),
                    'sort_order' => $index,
                    'is_primary' => $index === 0, // First image is primary
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        $product->load('images');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,' . $product->id],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku,' . $product->id],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'description' => ['nullable', 'string'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'], // 5MB max per image
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer', 'exists:product_images,id'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = (bool) $request->boolean('is_active');
        $validated['is_featured'] = (bool) $request->boolean('is_featured');
        
        // Handle compare_at_price (sale_price)
        if (isset($validated['sale_price'])) {
            $validated['compare_at_price'] = $validated['sale_price'];
            unset($validated['sale_price']);
        }

        $product->update($validated);

        // Handle image deletion
        if ($request->has('delete_images') && is_array($request->delete_images)) {
            foreach ($request->delete_images as $imageId) {
                $image = ProductImage::find($imageId);
                if ($image && $image->product_id === $product->id) {
                    // Delete physical file
                    $imagePath = public_path($image->image_url);
                    if (File::exists($imagePath)) {
                        File::delete($imagePath);
                    }
                    // Delete database record
                    $image->delete();
                }
            }
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $imagesPath = public_path('images/products');
            
            // Create directory if it doesn't exist
            if (!File::exists($imagesPath)) {
                File::makeDirectory($imagesPath, 0755, true);
            }

            // Get current max sort_order to append new images
            $maxSortOrder = $product->images()->max('sort_order') ?? -1;

            foreach ($request->file('images') as $index => $image) {
                // Generate unique filename
                $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // Move file to public/images/products
                $image->move($imagesPath, $filename);
                
                // Create relative path for database
                $imageUrl = '/images/products/' . $filename;
                
                // Determine if this should be primary (only if no primary exists)
                $hasPrimary = $product->images()->where('is_primary', true)->exists();
                
                // Create ProductImage record
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $imageUrl,
                    'alt_text' => $product->name . ' - Image ' . ($maxSortOrder + $index + 2),
                    'sort_order' => $maxSortOrder + $index + 1,
                    'is_primary' => !$hasPrimary && $index === 0, // First new image is primary only if no primary exists
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return back()->with('success', 'Product deleted successfully!');
    }
}

