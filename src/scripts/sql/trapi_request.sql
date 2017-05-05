CREATE TABLE IF NOT EXISTS `trapi_request` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, 
	`service` varchar(64) NOT NULL,
	`payload` text NOT NULL,
	`created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
