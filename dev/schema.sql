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
);
