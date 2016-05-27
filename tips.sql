CREATE TABLE IF NOT EXISTS `tips` (
	`id` int(10) NOT NULL auto_increment,
	`title` varchar(255),
	`description` text,
	`author` varchar(255),
	`approved` tinyint(1),
	`submit_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY( `id` )
);

CREATE TABLE IF NOT EXISTS `tip_votes` (
	`id` int(10) NOT NULL auto_increment,
	`tip_id` int(10) NOT NULL,
	`ip_addr` varchar(255) NOT NULL,
	PRIMARY KEY( `id` )
);
