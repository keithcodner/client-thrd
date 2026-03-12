<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Models\SiteSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SiteSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        try {
            $settings = SiteSettings::orderBy('type1')
                ->orderBy('name')
                ->get();

            return Inertia::render('Admin/Support/SiteSettings/SiteSettings', [
                'settings' => $settings,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching GigBizness settings: ' . $e->getMessage());
            return Inertia::render('Admin/Support/SiteSettings/SiteSettings', [
                'settings' => [],
                'error' => 'Failed to load settings.'
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'settings' => 'required|array',
            ]);

            DB::transaction(function () use ($request) {
                foreach ($request->input('settings') as $settingName => $settingValue) {
                    $setting = SiteSettings::where('name', $settingName)->first();
                    
                    if ($setting) {
                        // Validate value based on type
                        $validatedValue = $this->validateSettingValue($setting, $settingValue);
                        
                        $setting->update([
                            'value' => $validatedValue,
                            'updated_at' => now()
                        ]);
                        
                        Log::info('Setting updated', [
                            'setting_name' => $settingName,
                            'old_value' => $setting->value,
                            'new_value' => $validatedValue,
                            'admin_user' => Auth::user()->id
                        ]);
                    }
                }
            });

            // return response()->json([
            //     'success' => true,
            //     'message' => 'Settings updated successfully!'
            // ]);

        } catch (\Exception $e) {
            Log::error('Error updating GigBizness settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings. Please try again.'
            ], 500);
        }
    }

    public function getSetting(Request $request)
    {
        try {
            $request->validate([
                'setting_name' => 'required|string',
            ]);

            $setting = SiteSettings::where('name', $request->setting_name)->first();

            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'setting' => $setting
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching setting: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch setting.'
            ], 500);
        }
    }

    public function resetSetting(Request $request)
    {
        try {
            $request->validate([
                'setting_name' => 'required|string',
            ]);

            $setting = SiteSettings::where('name', $request->setting_name)->first();

            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting not found.'
                ], 404);
            }

            // You could implement default values logic here
            // For now, we'll just log the reset request
            Log::info('Setting reset requested', [
                'setting_name' => $request->setting_name,
                'current_value' => $setting->value,
                'admin_user' => Auth::user()->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Setting reset request logged. Implement default values as needed.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error resetting setting: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset setting.'
            ], 500);
        }
    }

    public function createSetting(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:settings_GigBizness,name',
                'value' => 'required|string',
                'type1' => 'required|string',
                'type2' => 'required|string',
                'description' => 'nullable|string',
                'op4' => 'nullable|string',
                'op5' => 'nullable|string',
            ]);

            $setting = SiteSettings::create([
                'name' => $request->name,
                'value' => $request->value,
                'type1' => $request->type1,
                'type2' => $request->type2,
                'description' => $request->description,
                'op4' => $request->op4,
                'op5' => $request->op5,
            ]);

            Log::info('New setting created', [
                'setting_name' => $request->name,
                'setting_value' => $request->value,
                'admin_user' => Auth::user()->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Setting created successfully!',
                'setting' => $setting
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating setting: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create setting.'
            ], 500);
        }
    }

    private function validateSettingValue($setting, $value)
    {
        switch ($setting->type2) {
            case 'switch_true_false':
                return in_array($value, ['true', 'false']) ? $value : $setting->value;
                
            case 'switch_yes_no':
                return in_array($value, ['yes', 'no']) ? $value : $setting->value;
                
            case 'switch_on_off':
                return in_array($value, ['on', 'off']) ? $value : $setting->value;
                
            case 'threshold':
                return is_numeric($value) && $value >= 0 ? (string)$value : $setting->value;
                
            case 'value':
                // For 'value' type settings, validate based on specific setting rules
                return $this->validateValueTypeSetting($setting, $value);
                
            default:
                return (string)$value;
        }
    }

    private function validateValueTypeSetting($setting, $value)
    {
        switch ($setting->name) {
            case 'circle_who_initiates_offer':
                return in_array($value, ['initiator', 'prospect', 'both']) ? $value : $setting->value;
                
            default:
                // For other value type settings, just ensure it's a string
                return trim((string)$value) !== '' ? (string)$value : $setting->value;
        }
    }

    public function exportSettings()
    {
        try {
            $settings = SiteSettings::all();
            
            $exportData = [
                'exported_at' => now()->toISOString(),
                'exported_by' => Auth::user()->id,
                'settings_count' => $settings->count(),
                'settings' => $settings->toArray()
            ];

            return response()->json($exportData)
                ->header('Content-Disposition', 'attachment; filename="GigBizness-settings-' . now()->format('Y-m-d-H-i-s') . '.json"');

        } catch (\Exception $e) {
            Log::error('Error exporting settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to export settings.'
            ], 500);
        }
    }
}
