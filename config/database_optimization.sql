-- Database optimization queries for better performance (MySQL 8.0+ compatible)
-- Run these queries on your MySQL database for optimal performance

-- Optimize best_activities table
ALTER TABLE best_activities 
ADD INDEX IF NOT EXISTS idx_year_name_combo (year, name(100)),
ADD INDEX IF NOT EXISTS idx_grade_year (grade_levels(50), year),
ADD FULLTEXT INDEX IF NOT EXISTS ft_search (name, description);

-- Optimize best_members table  
ALTER TABLE best_members
ADD INDEX IF NOT EXISTS idx_activity_year_student (activity_id, year, student_id),
ADD INDEX IF NOT EXISTS idx_student_year_activity (student_id, year, activity_id),
ADD INDEX IF NOT EXISTS idx_created_at (created_at);

-- Optimize students table (if you have access to modify it)
-- ALTER TABLE students 
-- ADD INDEX IF NOT EXISTS idx_stu_id_name (Stu_id, Stu_name(50)),
-- ADD INDEX IF NOT EXISTS idx_major_room (Stu_major, Stu_room);

-- MySQL 8.0+ configuration optimizations (add to my.cnf)
-- [mysqld]
-- # Query cache removed in MySQL 8.0+, use these alternatives:
-- innodb_buffer_pool_size = 1G                 # Increase based on available RAM
-- innodb_log_buffer_size = 32M
-- innodb_log_file_size = 256M
-- innodb_flush_log_at_trx_commit = 2           # Better performance, slight durability trade-off
-- innodb_io_capacity = 2000                    # Adjust based on storage type (SSD)
-- key_buffer_size = 32M
-- table_open_cache = 4000
-- thread_cache_size = 50
-- tmp_table_size = 64M
-- max_heap_table_size = 64M

-- Performance monitoring
-- slow_query_log = 1
-- long_query_time = 1
-- log_queries_not_using_indexes = 1

-- Modern MySQL 8.0 optimizations
-- # Enable Performance Schema for monitoring
-- performance_schema = ON
-- # Optimize for SSD
-- innodb_flush_method = O_DIRECT
-- # Connection optimizations  
-- max_connections = 200
-- connect_timeout = 10
