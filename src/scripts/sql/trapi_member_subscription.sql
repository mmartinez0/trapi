CREATE TABLE IF NOT EXISTS `trapi_member_subscription` (
	`member_id` bigint(20) unsigned NOT NULL, 
	`item_code` varchar(64) NOT NULL,
	`subscription_type` varchar(16) NOT NULL,
	`access_level` int unsigned NOT NULL,
	`created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	KEY (`member_id`)
)
