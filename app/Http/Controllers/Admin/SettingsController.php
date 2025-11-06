<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard');
    }

    public function general(): View
    {
        // Fetch flat key=>value settings
        $settings = Setting::query()
            ->get()
            ->mapWithKeys(function ($setting) {
                $value = match ($setting->type) {
                    'boolean' => (bool) $setting->value,
                    'integer' => (int) $setting->value,
                    'json' => json_decode($setting->value, true),
                    default => $setting->value,
                };
                return [$setting->key => $value];
            })
            ->toArray();

        // Defaults
        $defaults = [
            'site_name' => config('app.name'),
            'logo_url' => null,
            'meta_description' => null,
            'stripe_key' => null,
            'blog_enabled' => false,
            'newsletter_enabled' => false,
            'maintenance_mode' => false,
        ];

        $settings = array_merge($defaults, $settings);

        return view('admin.settings.general', compact('settings'));
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'logo_url' => ['nullable', 'string', 'max:1000'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'stripe_key' => ['nullable', 'string', 'max:255'],
            'blog_enabled' => ['nullable', 'boolean'],
            'newsletter_enabled' => ['nullable', 'boolean'],
            'maintenance_mode' => ['nullable', 'boolean'],
        ]);

        // Normalise booleans from checkboxes
        $validated['blog_enabled'] = (bool) $request->boolean('blog_enabled');
        $validated['newsletter_enabled'] = (bool) $request->boolean('newsletter_enabled');
        $validated['maintenance_mode'] = (bool) $request->boolean('maintenance_mode');

        DB::transaction(function () use ($validated) {
            foreach ($validated as $key => $value) {
                $type = match (true) {
                    is_bool($value) => 'boolean',
                    is_int($value) => 'integer',
                    is_array($value) => 'json',
                    default => 'string',
                };

                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $type === 'json' ? json_encode($value) : $value, 'type' => $type]
                );
            }
        });

        return back()->with('success', 'Settings saved successfully');
    }

    public function pages(Request $request): View
    {
        $pages = Page::orderBy('title')->get();
        
        // Select first page or the requested one
        $selectedSlug = $request->get('page', $pages->first()?->slug);
        $selectedPage = Page::where('slug', $selectedSlug)->first();

        return view('admin.settings.pages', compact('pages', 'selectedPage'));
    }

    public function updatePages(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'exists:pages,slug'],
            'content' => ['required', 'string'],
        ]);

        $page = Page::where('slug', $validated['slug'])->firstOrFail();
        $page->update(['content' => $validated['content']]);

        return redirect()->route('admin.settings.pages', ['page' => $validated['slug']])
            ->with('success', "Page \"{$page->title}\" updated successfully!");
    }

    public function product(): View
    {
        $settings = Setting::query()
            ->get()
            ->mapWithKeys(function ($setting) {
                $value = match ($setting->type) {
                    'boolean' => (bool) $setting->value,
                    'integer' => (int) $setting->value,
                    'json' => json_decode($setting->value, true),
                    default => $setting->value,
                };
                return [$setting->key => $value];
            })
            ->toArray();

        $defaults = [
            'product_card_style' => 'style1',
            'quick_view_enabled' => false,
            'add_to_cart_behavior' => 'page',
        ];

        $settings = array_merge($defaults, $settings);

        return view('admin.settings.product', compact('settings'));
    }

    public function updateProduct(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_card_style' => ['required', 'in:style1,style2,style3'],
            'quick_view_enabled' => ['nullable', 'boolean'],
            'add_to_cart_behavior' => ['required', 'in:drawer,page'],
        ]);

        $validated['quick_view_enabled'] = (bool) $request->boolean('quick_view_enabled');

        DB::transaction(function () use ($validated) {
            foreach ($validated as $key => $value) {
                $type = match (true) {
                    is_bool($value) => 'boolean',
                    is_int($value) => 'integer',
                    is_array($value) => 'json',
                    default => 'string',
                };

                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $type === 'json' ? json_encode($value) : $value, 'type' => $type]
                );
            }
        });

        return back()->with('success', 'Product settings saved successfully');
    }

    public function robots(): View
    {
        $robotsPath = public_path('robots.txt');
        $content = File::exists($robotsPath) ? File::get($robotsPath) : '';

        return view('admin.settings.robots', compact('content'));
    }

    public function updateRobots(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $robotsPath = public_path('robots.txt');
        
        try {
            File::put($robotsPath, $validated['content']);
            
            return back()->with('success', 'robots.txt updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['content' => 'Failed to update robots.txt: ' . $e->getMessage()]);
        }
    }
}


