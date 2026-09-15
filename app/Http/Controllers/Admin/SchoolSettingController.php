<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolSettingController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'website_name' => ['nullable', 'string', 'max:100'],
        ]);

        $school = auth()->user()->school ?? School::first();
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

    /**
     * Update the certificate signatories (principal & kesiswaan name + handwritten signature image)
     * used when rendering the auto-issued Sertifikat Penghargaan PDF.
     */
    public function updateSignatories(Request $request)
    {
        $validated = $request->validate([
            'principal_name' => ['nullable', 'string', 'max:255'],
            'principal_signature' => ['nullable', 'image', 'max:2048'],
            'remove_principal_signature' => ['nullable', 'boolean'],
            'kesiswaan_name' => ['nullable', 'string', 'max:255'],
            'kesiswaan_signature' => ['nullable', 'image', 'max:2048'],
            'remove_kesiswaan_signature' => ['nullable', 'boolean'],
        ]);

        $school = auth()->user()->school ?? School::first();

        if (! $school) {
            return back()->with('error', 'Data sekolah tidak ditemukan.');
        }

        $settings = $school->settings ?? [];

        $settings['principal_name'] = trim($validated['principal_name'] ?? '') ?: null;
        if (! $settings['principal_name']) {
            unset($settings['principal_name']);
        }

        $settings['kesiswaan_name'] = trim($validated['kesiswaan_name'] ?? '') ?: null;
        if (! $settings['kesiswaan_name']) {
            unset($settings['kesiswaan_name']);
        }

        $this->handleSignatureUpload($request, $settings, 'principal_signature', 'remove_principal_signature');
        $this->handleSignatureUpload($request, $settings, 'kesiswaan_signature', 'remove_kesiswaan_signature');

        $school->update(['settings' => $settings]);

        return back()->with('success', 'Penandatangan sertifikat berhasil diperbarui.');
    }

    private function handleSignatureUpload(Request $request, array &$settings, string $field, string $removeField): void
    {
        if ($request->boolean($removeField) && ! empty($settings[$field])) {
            Storage::disk('public')->delete($settings[$field]);
            unset($settings[$field]);
        }

        if ($request->hasFile($field)) {
            if (! empty($settings[$field])) {
                Storage::disk('public')->delete($settings[$field]);
            }
            $settings[$field] = $request->file($field)->store('signatures', 'public');
        }
    }
}
