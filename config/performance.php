<?php
/**
 * Performance Configuration and Monitoring
 * Central configuration for caching, optimization settings and performance monitoring
 */

class PerformanceMonitor 
{
    private static $startTime;
    private static $memoryStart;
    private static $queryCount = 0;
    private static $queryTimes = [];

    public static function start() {
        self::$startTime = microtime(true);
        self::$memoryStart = memory_get_usage();
        self::$queryCount = 0;
        self::$queryTimes = [];
    }

    public static function addQuery($query, $time) {
        self::$queryCount++;
        self::$queryTimes[] = ['query' => $query, 'time' => $time];
    }

    public static function end() {
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        return [
            'execution_time' => round(($endTime - self::$startTime) * 1000, 2), // milliseconds
            'memory_usage' => round(($endMemory - self::$memoryStart) / 1024 / 1024, 2), // MB
            'query_count' => self::$queryCount,
            'slow_queries' => array_filter(self::$queryTimes, function($q) { 
                return $q['time'] > 0.1; // Queries slower than 100ms
            })
        ];
    }
}

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
        'enable_query_cache' => false, // Query cache removed in MySQL 8.0+
        'use_prepared_statements' => true,
        'enable_persistent_connections' => false,
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
