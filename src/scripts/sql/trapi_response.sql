CREATE TABLE IF NOT EXISTS `trapi_response` (
	`id` bigint(20) unsigned NOT NULL, 
	`success` varchar(5) NOT NULL, 
	`code` varchar(32) NOT NULL,
	`payload` text NOT NULL,
	`created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	KEY (`id`)
)
