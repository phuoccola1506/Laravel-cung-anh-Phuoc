<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        // Get all settings grouped by group
        $settingsByGroup = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        
        return view('admin.settings', compact('settingsByGroup'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string|max:1000',
        ]);

        foreach ($request->settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            
            if ($setting) {
                // Handle file upload for image type
                if ($setting->type === 'image' && $request->hasFile("settings.{$key}")) {
                    $image = $request->file("settings.{$key}");
                    $imageName = time() . '_' . $key . '.' . $image->getClientOriginalExtension();
                    
                    // Delete old image if exists
                    if ($setting->value && file_exists(public_path('images/' . $setting->value))) {
                        unlink(public_path('images/' . $setting->value));
                    }
                    
                    $image->move(public_path('images'), $imageName);
                    $value = $imageName;
                }
                
                // Handle boolean values
                if ($setting->type === 'boolean') {
                    $value = $request->has("settings.{$key}") ? '1' : '0';
                }
                
                $setting->update(['value' => $value]);
                
                // Clear cache
                Cache::forget("setting.{$key}");
                Cache::forget("settings.group.{$setting->group}");
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật cài đặt thành công!'
            ]);
        }

        return redirect()->route('admin.settings')
            ->with('success', 'Cập nhật cài đặt thành công!');
    }
}
