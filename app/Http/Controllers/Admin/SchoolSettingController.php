<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolSettingController extends Controller
{
    public function index()
    {
        $setting = SchoolSetting::query()->first() ?: new SchoolSetting(SchoolSetting::fallback());

        return view('admin.school-settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'school_name' => 'required|string|max:255',
            'school_short_name' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'village' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'npsn' => 'nullable|string|max:50',
            'nsm' => 'nullable|string|max:50',
            'accreditation' => 'nullable|string|max:50',
            'school_status' => 'nullable|string|max:100',
            'principal_name' => 'required|string|max:255',
            'principal_nip' => 'nullable|string|max:50',
            'principal_nuptk' => 'nullable|string|max:50',
            'foundation_name' => 'nullable|string|max:255',
            'school_year' => 'required|string|max:20',
            'semester' => 'required|string|max:20',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'motto' => 'nullable|string|max:255',
            'primary_color' => ['required', 'string', 'max:20', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['required', 'string', 'max:20', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sidebar_color' => ['nullable', 'string', 'max:20', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'navbar_color' => ['nullable', 'string', 'max:20', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo' => 'nullable|file|mimes:jpg,jpeg,png,webp|extensions:jpg,jpeg,png,webp|max:2048',
            'favicon' => 'nullable|file|mimes:ico,png,jpg,jpeg,webp|extensions:ico,png,jpg,jpeg,webp|max:1024',
        ]);

        unset($data['logo'], $data['favicon']);

        $setting = SchoolSetting::query()->firstOrNew(['singleton_key' => SchoolSetting::SINGLETON_ID]);

        if ($request->hasFile('logo')) {
            $this->deletePublicFile($setting->logo_path);
            $data['logo_path'] = $request->file('logo')->store('school', 'public');
        }

        if ($request->hasFile('favicon')) {
            $this->deletePublicFile($setting->favicon_path);
            $data['favicon_path'] = $request->file('favicon')->store('school', 'public');
        }

        $setting->fill($data);
        $setting->singleton_key = SchoolSetting::SINGLETON_ID;
        $setting->save();

        clear_school_setting_cache();

        return back()->with('success', 'Pengaturan sekolah berhasil disimpan.');
    }

    private function deletePublicFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
