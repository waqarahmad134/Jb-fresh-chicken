<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * Get all settings.
     */
    public function index()
    {
        $settings = Setting::all()->mapWithKeys(function ($setting) {
            return [$setting->key => Setting::castValue($setting->value, $setting->type)];
        });

        return response()->json($settings);
    }

    /**
     * Get settings by group.
     */
    public function byGroup($group)
    {
        $settings = Setting::where('group', $group)->get()->mapWithKeys(function ($setting) {
            return [$setting->key => Setting::castValue($setting->value, $setting->type)];
        });

        return response()->json($settings);
    }
}

