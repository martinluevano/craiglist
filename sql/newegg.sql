-- this is a comment
-- SQL is crazy with requiring a space after the two dashes

-- SQL uses keywords in ALL CAPS by convention  - it is technically case insensitive
-- but, by the power of the style guide, we're using ALL CAPS because I said so :D
-- (besides, this is how most real world code is)

-- we added these "DROP TABLE" commands to reset the development database as we debug
-- NEVER, NEVER, NEVER, ****NEVER**** try this on live data!!!!
-- these go in reverse order of the create table commands
DROP TABLE IF EXISTS orderLine;
DROP TABLE IF EXISTS orderHeader;
DROP TABLE IF EXISTS product;
DROP TABLE IF EXISTS address;
DROP TABLE IF EXISTS profile;
DROP TABLE IF EXISTS user;
-- create a user table:  note CREATE TABLE is one huge function call so is separated by
-- parentheses instead of the braces we see in other blocks
CREATE TABLE user (
-- AUTO_INCREMENT is for system assigned numbers and counts 1, 2, 3, ...
-- NOT NULL means the field can *NEVER* be empty
	userId INT UNSIGNED NOT NULL AUTO_INCREMENT,
	email VARCHAR(64) NOT NULL,
	passwordHash CHAR(128) NOT NULL,
	salt CHAR (64) NOT NULL,
	authToken CHAR (32),
-- a primary key is the unique identifier for the table
	PRIMARY KEY(userId),
-- a unique field enforces duplicates may not exist
	UNIQUE(email)
);

-- create profile table
CREATE TABLE profile (
	profileId INT UNSIGNED AUTO_INCREMENT NOT NULL,
	userId INT UNSIGNED NOT NULL,
	firstName VARCHAR(32)NOT NULL,
	middleName VARCHAR(32),
	lastName VARCHAR(32)NOT NULL,
	PRIMARY KEY(profileId),
	UNIQUE(userId),
-- this establishes a 1-1 relationship with user
	FOREIGN KEY(userId) REFERENCES user(userId)
);

-- create address table
CREATE TABLE address (
	addressId INT UNSIGNED AUTO_INCREMENT NOT NULL,
	profileId INT UNSIGNED NOT NULL,
	name VARCHAR(64)NOT NULL,
	attention VARCHAR(64),
	street1 VARCHAR(64)NOT NULL,
	street2 VARCHAR(64),
	city VARCHAR(64)NOT NULL,
	region VARCHAR(64)NOT NULL,
	postalCode VARCHAR(32)NOT NULL,
	country CHAR(2),
	phone VARCHAR(24),
	PRIMARY KEY(addressId),
--  INDEX creates a search index on a field - this is required for non unique foreign keys
	INDEX(profileId),
	FOREIGN KEY(profileId) REFERENCES profile(profileId)
);

-- create product table (
CREATE TABLE product (
	productId INT UNSIGNED AUTO_INCREMENT NOT NULL,
	manufacturer VARCHAR(32) NOT NULL,
	model VARCHAR(32) NOT NULL,
	title VARCHAR(32) NOT NULL,
	description TEXT,
	price DECIMAL(9, 2) NOT NULL,
	PRIMARY KEY(productId),
-- index the next fields because users search for them a lot
	INDEX(manufacturer),
	INDEX(model),
	INDEX(title),
-- index two fields because users search "manufacturer title" a lot
	INDEX(manufacturer, title)
);

-- create OrderHeader
CREATE TABLE orderHeader (
	orderHeaderId INT UNSIGNED AUTO_INCREMENT NOT NULL,
	profileid INT UNSIGNED NOT NULL,
	date DATETIME,
	PRIMARY KEY(orderHeaderId),
	INDEX(profileid),
	FOREIGN KEY(profileid) REFERENCES profile(profileId)
);

-- create orderLine
-- this is a "slave" table by the m-to-n relationship from product to order
-- the technical name for this table is an 'intersection table"
CREATE TABLE orderLine (
	orderHeaderId INT UNSIGNED NOT NULL,
	productId INT UNSIGNED NOT NULL,
	quantity SMALLINT UNSIGNED NOT NULL,
	discount DECIMAL(9, 2),
-- in an intersection table, first INDEX the two foreign keys
	INDEX(orderHeaderId),
	INDEX(productId),
-- second, create a "composite" primary key
	PRIMARY KEY(orderHeaderId, productId),
-- third, create the foreign keys
	FOREIGN KEY(orderHeaderId) REFERENCES orderHeader(orderHeaderId),
	FOREIGN KEY(productId) REFERENCES product(productId)
);
