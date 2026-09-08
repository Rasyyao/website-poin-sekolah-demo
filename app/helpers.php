<?php

use App\Models\School;

if (! function_exists('website_name')) {
    /**
     * Get the configured website/application name.
     */
    function website_name(): string
    {
        try {
            if (auth()->check() && auth()->user()->school) {
                $name = auth()->user()->school->settings['website_name'] ?? null;
                if (! empty($name) && $name !== 'Laravel') {
                    return $name;
                }
            }

            $firstSchool = School::first();
            if ($firstSchool) {
                $name = $firstSchool->settings['website_name'] ?? null;
                if (! empty($name) && $name !== 'Laravel') {
                    return $name;
                }
            }
        } catch (\Throwable $e) {
            // fallback
        }

        $appName = config('app.name');
        if (! empty($appName) && $appName !== 'Laravel') {
            return $appName;
        }

        return 'Sistem Poin Sekolah';
    }
}
