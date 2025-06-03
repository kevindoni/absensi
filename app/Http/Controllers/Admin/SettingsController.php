<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class SettingsController extends Controller
{
    /**
     * Show the general settings page
     */
    public function index()
    {
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();        
        $defaultSettings = [
            'school_name' => '',
            'school_address' => '',
            'school_phone' => '',
            'school_email' => '',
            'school_website' => '',
            'logo_path' => '',
            'timezone' => 'Asia/Jakarta',
            'academic_year' => date('Y'),
            'semester' => '1',
            'enable_fingerprint' => false,
            'enable_face_recognition' => false,              
            'allow_late_attendance' => true,            
            'enable_late_tolerance_system' => true,            
            'late_tolerance_minutes' => 5,            
            'max_late_minutes' => 30,
            'notification_email' => '',
            'kepala_sekolah' => '',            
            'nip_kepala_sekolah' => '',
            'ttd_kepala_sekolah' => '',
        ];
        
        $settings = array_merge($defaultSettings, $settings);
        $timezones = timezone_identifiers_list();
        
        return view('admin.settings.index', compact('settings', 'timezones'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'required|string',
            'school_phone' => 'required|string|max:20',
            'school_email' => 'required|email',
            'school_website' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'timezone' => 'required|string|timezone',
            'academic_year' => 'required|digits:4',                
            'semester' => 'required|in:1,2',
            'late_tolerance_minutes' => 'required|integer|min:0|max:120',
            'max_late_minutes' => 'required|integer|min:0|max:180|gte:late_tolerance_minutes',            
            'notification_email' => 'nullable|email',            
            'kepala_sekolah' => 'required|string|max:255',
            'nip_kepala_sekolah' => 'nullable|string|max:50|regex:/^[0-9\s-]*$/',
        ]);

        try {
            DB::beginTransaction();

            // Process settings before saving
            $settings = $request->except(['_token', '_method', 'logo']);            
            // Handle nullable fields            
            $nullableFields = ['school_website', 'notification_email', 'ttd_kepala_sekolah', 'nip_kepala_sekolah'];
            foreach ($nullableFields as $field) {
                if (!isset($settings[$field])) {
                    $settings[$field] = '';
                }
            }            // Handle boolean fields
            $booleanFields = ['enable_fingerprint', 'enable_face_recognition', 'allow_late_attendance', 'enable_late_tolerance_system'];
            foreach ($booleanFields as $field) {
                $settings[$field] = isset($settings[$field]) ? '1' : '0';
            }// Handle logo upload
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                
                // Ensure uploads/logos directory exists
                $logoDir = public_path('uploads/logos');
                if (!file_exists($logoDir)) {
                    mkdir($logoDir, 0755, true);
                }
                  // Store in public directory instead of storage
                $fileName = time() . '_logo.' . $logo->getClientOriginalExtension();
                $logoPath = 'uploads/logos/' . $fileName;
                $logo->move(public_path('uploads/logos'), $fileName);
                $settings['logo_path'] = $logoPath;
                  // Delete old logo if exists
                if ($oldLogo = DB::table('settings')->where('key', 'logo_path')->first()) {
                    $oldValue = trim($oldLogo->value);
                    if (!empty($oldValue) && $oldValue !== 'public' && !is_dir(public_path($oldValue))) {
                        $oldPath = public_path($oldValue);
                        if (file_exists($oldPath) && is_file($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                }
            }

            // Save all settings
            foreach ($settings as $key => $value) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $key],
                    [
                        'value' => (string) $value, // Convert all values to string
                        'updated_at' => now()
                    ]
                );
            }

            DB::commit();
            return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil disimpan');
              } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
