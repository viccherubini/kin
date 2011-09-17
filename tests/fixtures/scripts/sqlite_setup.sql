DROP TABLE IF EXISTS fixture1;
CREATE TABLE fixture1 (
	id integer PRIMARY KEY,
	created TEXT NOT NULL,
	updated TEXT DEFAULT NULL,
	name TEXT NOT NULL,
	identifier TEXT NOT NULL,
	status INTEGER DEFAULT 0
);