
<?php

use App\Models\Feature;

if (!function_exists('getFeatureId')) {

    /**
     * Get feature ID by feature name (with cache + DB + fallback)
     */
    function getFeatureId($featureName)
    {
        if (!$featureName) return null;

        // 🔥 Static fallback (backup)
        $staticFeatures = [
            'connection_request_send'   => 1,
            'connection_request_accept' => 2,
            'leads_create'              => 3,            
            'profile_view'              => 5,
            'ai_generated_feedback'     => 6,
            'create-flyer'              => 7,
            'mini-website'              => 8,
        ];

        // 🔥 Cache key
        $cacheKey = "feature_id_" . $featureName;

        return cache()->rememberForever($cacheKey, function () use ($featureName, $staticFeatures) {

            // ✅ Try DB first
            $feature = Feature::where('feature', $featureName)->first();

            if ($feature) {
                return $feature->id;
            }

            // ⚠️ Fallback to static
            return $staticFeatures[$featureName] ?? null;
        });
    }
}

