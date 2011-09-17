DROP TABLE IF EXISTS fixture1;
CREATE TABLE fixture1 (
	id serial NOT NULL, 
	created timestamp without time zone NOT NULL, 
	updated timestamp without time zone, 
	"name" character varying(64), 
	identifier character varying(64), 
	status smallint DEFAULT 0, 
	CONSTRAINT id PRIMARY KEY (id)
) WITH (OIDS = FALSE);
ALTER TABLE fixture1 OWNER TO postgres;