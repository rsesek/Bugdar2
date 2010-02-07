CREATE TABLE users
(
	user_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
	email varchar(250) NOT NULL DEFAULT '',
	alias varchar(50) NOT NULL DEFAULT '',
	usergroup_id int NOT NULL DEFAULT 0,
	other_usergroup_ids varchar(250) NOT NULL DEFAULT '',
	password varchar(32) NOT NULL DEFAULT '',
	salt varchar(10) NOT NULL DEFAULT '',
	authkey varchar(250) NOT NULL DEFAULT '',
	show_email boolean NOT NULL DEFAULT 0,
	language_id int NOT NULL DEFAULT 0,
	timezone float NOT NULL DEFAULT 0,
	user_auth_id int NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE bugs
(
	bug_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
	title varchar(250) NOT NULL DEFAULT '',
	reporting_user_id int NOT NULL DEFAULT 0,
	reporting_date int NOT NULL DEFAULT 0,
	hidden boolean NOT NULL DEFAULT 0,
	first_comment_id int NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE comments
(
	comment_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
	bug_id int NOT NULL DEFAULT 0,
	post_user_id int NOT NULL DEFAULT 0,
	post_date int NOT NULL DEFAULT 0,
	hidden boolean NOT NULL DEFAULT 0,
	body text NOT NULL DEFAULT ''
) ENGINE=InnoDB;
