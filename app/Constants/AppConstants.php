<?php

namespace App\Constants;

class AppConstants
{
    // Cache TTL values (in seconds)
    public const CACHE_TTL_NEIGHBORHOODS = 3600; // 1 hour
    public const CACHE_TTL_GEMINI_RESPONSE = 86400; // 24 hours
    
    // Neighborhood filtering
    public const MAX_CANDIDATE_NEIGHBORHOODS = 10;
    public const MIN_NEIGHBORHOODS_FOR_FILTERING = 10;
    public const MIN_NEIGHBORHOODS_TO_CACHE = 20;
    
    // API Configuration
    public const GEMINI_DEFAULT_TEMPERATURE = 0.2;
    public const GEMINI_MAX_TOKENS = 4096;
    public const OVERPASS_TIMEOUT_SECONDS = 20;
    public const OVERPASS_RETRY_ATTEMPTS = 3;
    public const OVERPASS_RETRY_DELAY_MS = 300;
    
    // Validation
    public const MIN_PROMPT_LENGTH = 10;
    public const MAX_RESULTS_TO_DISPLAY = 10;
    
    // Fallback KPI range
    public const FALLBACK_KPI_MIN = 1;
    public const FALLBACK_KPI_MAX = 10;
}
