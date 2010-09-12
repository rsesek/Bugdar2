CREATE TABLE users
(
	user_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
	email varchar(250) NOT NULL DEFAULT '',
	alias varchar(50) NOT NULL DEFAULT '',
	usergroup_id int unsigned NOT NULL DEFAULT 0,
	other_usergroup_ids varchar(250) NOT NULL DEFAULT '',
	password varchar(32) NOT NULL DEFAULT '',
	salt varchar(10) NOT NULL DEFAULT '',
	authkey varchar(250) NOT NULL DEFAULT '',
	show_email boolean NOT NULL DEFAULT 0,
	language_id int unsigned NOT NULL DEFAULT 0,
	timezone float NOT NULL DEFAULT 0,
	user_auth_id int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE bugs
(
	bug_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
	title varchar(250) NOT NULL DEFAULT '',
	reporting_user_id int unsigned NOT NULL DEFAULT 0,
	reporting_date int NOT NULL DEFAULT 0,
	hidden boolean NOT NULL DEFAULT 0,
	first_comment_id int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE comments
(
	comment_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
	bug_id int unsigned NOT NULL DEFAULT 0,
	post_user_id int unsigned NOT NULL DEFAULT 0,
	post_date int NOT NULL DEFAULT 0,
	hidden boolean NOT NULL DEFAULT 0,
	body text NOT NULL DEFAULT ''
) ENGINE=InnoDB;

CREATE TABLE attributes
(
	title varchar(50) NOT NULL DEFAULT '' PRIMARY KEY,
	description text NULL,
	type enum('text', 'boolean', 'list', 'date', 'user') NOT NULL DEFAULT 'text',
	validator_pattern varchar(250) NULL,
	required boolean NOT NULL DEFAULT 0,
	default_value varchar(250) NULL,
	can_search boolean NOT NULL DEFAULT 1,
	color_background varchar(6) NULL,
	color_foreground varchar(6) NULL
) ENGINE=InnoDB;

CREATE TABLE bug_attributes
(
	bug_id int unsigned NOT NULL DEFAULT 0,
	attribute_title varchar(50) NOT NULL DEFAULT '',
	value varchar(250) NOT NULL DEFAULT '',
	PRIMARY KEY (bug_id, attribute_title, value)
) ENGINE=InnoDB;

CREATE TABLE usergroups
(
	usergroup_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
	title varchar(50) NOT NULL DEFAULT '',
	display_title varchar(100) NOT NULL DEFAULT '',
	mask int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB;
INSERT INTO usergroups VALUES (1, 'Anonymous', 'Not Logged In', 1);
INSERT INTO usergroups VALUES (2, 'Registered', 'Reporter', 15);
INSERT INTO usergroups VALUES (3, 'Developers', 'Developer', 383);
INSERT INTO usergroups VALUES (4, 'Administrators', 'Admin', 2047);
