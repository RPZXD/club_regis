<?php
/**
 * Performance Configuration
 * Central configuration for caching, optimization settings
 */

return [
    'cache' => [
        'enabled' => true,
        'default_timeout' => 300, // 5 minutes
        'activity_timeout' => 600, // 10 minutes for activities
        'member_timeout' => 180,   // 3 minutes for member counts
    ],
    
    'database' => [
        'connection_pool_size' => 10,
        'query_timeout' => 30,
        'slow_query_threshold' => 1.0, // Log queries slower than 1 second
    ],
    
    'optimization' => [
        'batch_size' => 100,
        'enable_query_cache' => true,
        'compress_responses' => true,
        'minify_json' => false,
    ],
    
    'debug' => [
        'enable_profiling' => false,
        'log_slow_queries' => true,
        'show_query_count' => false,
    ]
];
?>
