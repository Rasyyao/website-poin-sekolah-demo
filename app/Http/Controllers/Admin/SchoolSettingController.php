<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SchoolSettingController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'website_name' => ['nullable', 'string', 'max:100'],
        ]);

        $school = auth()->user()->school ?? \App\Models\School::first();
        if ($school) {
            $settings = $school->settings ?? [];
            if ($request->filled('website_name')) {
                $settings['website_name'] = trim($validated['website_name']);
            } else {
                unset($settings['website_name']);
            }

            $school->update([
                'name' => $validated['name'],
                'settings' => $settings,
            ]);
        }

        return back()->with('success', 'Konfigurasi sistem berhasil diperbarui.');
    }
}
