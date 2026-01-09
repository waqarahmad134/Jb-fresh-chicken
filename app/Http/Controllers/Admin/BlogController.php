<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');

        $posts = BlogPost::query()
            ->with(['blogCategory', 'author'])
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.blog.index', compact('posts', 'search'));
    }

    public function create(): View
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.blog.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blog_posts,slug'],
            'blog_category_id' => ['required', 'exists:blog_categories,id'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'], // 5MB max
            'image_url' => ['nullable', 'string', 'max:1000'],
            'is_published' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $imagesPath = public_path('images/blog');
            
            // Create directory if it doesn't exist
            if (!File::exists($imagesPath)) {
                File::makeDirectory($imagesPath, 0755, true);
            }

            $image = $request->file('featured_image');
            $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Move file to public/images/blog
            $image->move($imagesPath, $filename);
            
            // Set image URL
            $validated['image_url'] = '/images/blog/' . $filename;
        } elseif (empty($validated['image_url'])) {
            // Remove image_url from validated if not provided
            unset($validated['image_url']);
        }

        $validated['author_id'] = auth()->id();
        $validated['is_published'] = (bool) $request->boolean('is_published', true);
        $validated['is_featured'] = (bool) $request->boolean('is_featured');

        BlogPost::create($validated);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post created successfully!');
    }

    public function edit(BlogPost $blog): View
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.blog.edit', compact('blog', 'categories'));
    }

    public function update(Request $request, BlogPost $blog): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blog_posts,slug,' . $blog->id],
            'blog_category_id' => ['required', 'exists:blog_categories,id'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'], // 5MB max
            'image_url' => ['nullable', 'string', 'max:1000'],
            'is_published' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle image removal
        if ($request->has('remove_image') && $request->boolean('remove_image')) {
            // Delete old image if it exists and is a local file
            if ($blog->image_url && !str_starts_with($blog->image_url, 'http')) {
                $oldImagePath = public_path($blog->image_url);
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
            if ($blog->image_url && !str_starts_with($blog->image_url, 'http')) {
                $oldImagePath = public_path($blog->image_url);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            $imagesPath = public_path('images/blog');
            
            // Create directory if it doesn't exist
            if (!File::exists($imagesPath)) {
                File::makeDirectory($imagesPath, 0755, true);
            }

            $image = $request->file('featured_image');
            $filename = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Move file to public/images/blog
            $image->move($imagesPath, $filename);
            
            // Set image URL
            $validated['image_url'] = '/images/blog/' . $filename;
        } elseif (empty($validated['image_url'])) {
            // If image_url is empty and no file uploaded, keep existing or set to null
            if (!$request->has('image_url')) {
                // Keep existing image_url if not explicitly cleared
                unset($validated['image_url']);
            }
        }

        $validated['is_published'] = (bool) $request->boolean('is_published');
        $validated['is_featured'] = (bool) $request->boolean('is_featured');

        $blog->update($validated);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post updated successfully!');
    }

    public function destroy(BlogPost $blog): RedirectResponse
    {
        $blog->delete();

        return back()->with('success', 'Blog post deleted successfully!');
    }
}

