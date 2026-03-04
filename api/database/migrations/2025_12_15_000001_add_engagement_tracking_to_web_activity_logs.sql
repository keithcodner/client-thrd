-- Migration: Add Engagement Tracking to web_activity_logs
-- Date: 2025-12-15
-- Description: Adds event_type, related_id, and related_type columns for tracking views, clicks, and other engagement metrics

-- Add event_type column
ALTER TABLE `web_activity_logs` 
ADD COLUMN `event_type` ENUM('view', 'click', 'apply', 'share', 'save', 'other') NULL DEFAULT NULL AFTER `route_name`;

-- Add related_id column (for linking to job posts, profiles, etc.)
ALTER TABLE `web_activity_logs` 
ADD COLUMN `related_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `event_type`;

-- Add related_type column (polymorphic relationship: 'job', 'profile', 'post', etc.)
ALTER TABLE `web_activity_logs` 
ADD COLUMN `related_type` VARCHAR(50) NULL DEFAULT NULL AFTER `related_id`;

-- Add indexes for better query performance
ALTER TABLE `web_activity_logs` 
ADD INDEX `web_activity_logs_event_type_index` (`event_type`);

ALTER TABLE `web_activity_logs` 
ADD INDEX `web_activity_logs_related_id_related_type_index` (`related_id`, `related_type`);

ALTER TABLE `web_activity_logs` 
ADD INDEX `web_activity_logs_event_type_related_id_related_type_index` (`event_type`, `related_id`, `related_type`);

-- Verify the changes
SELECT 
    COLUMN_NAME, 
    COLUMN_TYPE, 
    IS_NULLABLE, 
    COLUMN_DEFAULT
FROM 
    INFORMATION_SCHEMA.COLUMNS
WHERE 
    TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'web_activity_logs'
    AND COLUMN_NAME IN ('event_type', 'related_id', 'related_type')
ORDER BY 
    ORDINAL_POSITION;
