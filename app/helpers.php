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
                return auth()->user()->school->settings['website_name'] 
                    ?? config('app.name', 'Poin Sekolah');
            }

            $firstSchool = School::first();
            if ($firstSchool && ! empty($firstSchool->settings['website_name'])) {
                return $firstSchool->settings['website_name'];
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return config('app.name', 'Poin Sekolah');
    }
}
