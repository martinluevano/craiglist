-- this is my first attempt at SQL database
DROP TABLE IF EXISTS showMap;
DROP TABLE IF EXISTS postingPlace;
DROP TABLE IF EXISTS contactInfo;

-- create contactInfo table
-- AUTO_INCREMENT can only be used once per table.  It also forces it
-- to be the PRIMARY KEY.

CREATE TABLE contactInfo (
	contactId INT NOT NULL AUTO_INCREMENT,
	email VARCHAR(64) NOT NULL,
	phone VARCHAR(24),
	contactName VARCHAR(64),
	PRIMARY KEY (contactId),
	UNIQUE (email)
	);


-- create PostingPlace table
CREATE TABLE postingPlace (
	postingId INT NOT NULL AUTO_INCREMENT,
	contactId INT NOT NULL,
	postingTitle VARCHAR(250) NOT NULL,
	location VARCHAR(64),
	zipCode VARCHAR(15),
	postingBody VARCHAR(4000) NOT NULL,
	compensation VARCHAR(250) NOT NULL,
	PRIMARY KEY(postingId),
	UNIQUE(contactId),
	FOREIGN KEY(contactId) REFERENCES contactInfo(contactId),
	INDEX(location),
	INDEX(zipCode),
	INDEX(postingTitle),
	INDEX(compensation),
	INDEX(compensation, postingTitle)
	);

-- create showMap table
CREATE TABLE showMap (
	mapId INT NOT NULL AUTO_INCREMENT,
	streetAddress VARCHAR(64) NOT NULL,
	crossStreet VARCHAR(64),
	city VARCHAR(64) NOT NULL,
	state CHAR(2) NOT NULL,
	postingId INT NOT NULL,
	PRIMARY KEY(mapId),
	UNIQUE(postingId),
	FOREIGN KEY(postingId)  REFERENCES postingPlace(postingId)
	);