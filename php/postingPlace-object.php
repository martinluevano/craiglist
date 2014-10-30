<?php

// in order to create the  object, we need to first require the class file
require_once("PostingPlace.php");

// how to store a password! first, generate the salt & authentication tokens
	//actual code
	//$authToken = bin2hex(openssl_random_pseudo_bytes(16));
	//$salt      = bin2hex(openssl_random_pseudo_bytes(32));

	//second, hash the cleartext password using PBKDF2
	//actual code
	//$clearTextPassword = "ChedGeek5";
	//pbkdf2Hash			= hash_pbkdf2("sha512", $clearTextPassword, $salt, 2048 128);


// now that PHP knows what to build, we use the new keyword to create an object
// the new keyword automatically runs the __construct method
$postingPlace = new PostingPlace(null, 1, "postingTitle", "location", "zip code", "postingBody", "compensation");

//pesky mysqli doesn't throw exceptions by default - this will override this and throw exceptions!
mysqli_report(MYSQLI_REPORT_STRICT);

//OK, now we can *try* connecting to my SQL - get it? it's a pun
try {
	//parameters: hostname, username, password, database
	//never show this stuff.  malicious users do not need to see this

	$mysqli = new mysqli("localhost", "store_martin","deepdive", "store_martin");
} catch(mysqli_sql_exception $splException) {
	echo "unable to connect to mySQL: " . $splException->getMessage();
}
//if we got here, we did connect!  so we'll go ahead and insert this object
$postingPlace->insert($mysqli);
//var_dump to view object
var_dump($postingPlace);
?>