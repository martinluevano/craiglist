<?php
// first require the SimpleTest framework
require_once("/usr/lib/php5/simpletest/autorun.php");

//then require class under scrutiny
require_once("../php/PostingPlace.php");

//the UserTest for all our testsclass PostingPlaceTest extends UnitTestCase {
class PostingPlaceTest extends UnitTestCase {
	//variable to hold the test database row
	private $mysqli =null;
	//variable to hold the test database row
	private $postingPlace = null;

	//the rest of the "global" variables used to create test data
	private $contactId = 1;
	private $postingTitle = "This is first";
	private $location = "Albuquerque";
	private $zipCode = "12345";
	private $postingBody = "This is where we start to learn";
	private $compensation = "$100.00";

	//setUp () is the first step in unit testing and is a method to run before each test
	//here it connects to mySQL but it can also perform other calculations i.e. salt and hash
	public function setUp () {
		//connect to mySQL
		mysqli_report(MYSQLI_REPORT_STRICT);
		$this->mysqli = new mysqli("localhost", "store_martin", "deepdive", "store_martin");
	}
	//Teardown (), a method to delete the test record and disconnect from mySQL
	public function tearDown() {
		// delete the user if we can
		if($this->postingPlace !== null) {
			$this->postingPlace->delete($this->mysqli);
			$this->postingPlace = null;
		}
		// disconnect from mySQL
		if($this->mysqli !== null) {
			$this->mysqli->close();
		}
	}

	// test creating a new posting Id and inserting it to mySQL
	public function testInsertNewPostingId() {
		// first, verify mySQL connected OK
		$this->assertNotNull($this->mysqli);
		// second, create a user to post to mySQL
		$this->postingPlace = new PostingPlace(null, $this->contactId, $this->postingTitle, $this->location, $this->zipCode,
																$this->postingBody, $this->compensation);

		// third, insert the posting Id to mySQL
		$this->postingPlace->insert($this->mysqli);

		// finally, compare the fields
		$this->assertNotNull($this->postingPlace->getPostingId());
		$this->assertTrue($this->postingPlace->getPostingId() > 0);
		$this->assertIdentical($this->postingPlace->getContactId(),               	$this->contactId);
		$this->assertIdentical($this->postingPlace->getPostingTitle(),            	$this->postingTitle);
		$this->assertIdentical($this->postingPlace->getLocation(),                	$this->location);
		$this->assertIdentical($this->postingPlace->getZipCode(),					 	$this->zipCode);
		$this->assertIdentical($this->postingPlace->getPostingBody(),					$this->postingBody);
		$this->assertIdentical($this->postingPlace->getCompensation(),					$this->compensation);
	}

	// test updating a Posting Place in mySQL
	public function testUpdatePostingId() {
		// verify mySQL connected OK
		$this->assertNotNull($this->mysqli);

		// create a posting Id to post to mySQL
		$this->postingPlace = new PostingPlace(null, $this->contactId, $this->postingTitle, $this->location, $this->zipCode,
																$this->postingBody, $this->compensation);

		// third, insert the posting id to mySQL
		$this->postingPlace->insert($this->mysqli);

		// fourth, update the posting id and post the changes to mySQL
		$newZipCode = "49494";
		$this->postingPlace->setZipCode($newZipCode);
		$this->postingPlace->update($this->mysqli);

		// finally, compare the fields
		$this->assertNotNull($this->postingPlace->getPostingId());
		$this->assertTrue($this->postingPlace->getPostingId() > 0);
		$this->assertIdentical($this->postingPlace->getContactId(),               	$this->contactId);
		$this->assertIdentical($this->postingPlace->getPostingTitle(),            	$this->postingTitle);
		$this->assertIdentical($this->postingPlace->getLocation(),                	$this->location);
		$this->assertIdentical($this->postingPlace->getZipCode(),					 	$newZipCode);
		$this->assertIdentical($this->postingPlace->getPostingBody(),					$this->postingBody);
		$this->assertIdentical($this->postingPlace->getCompensation(),					$this->compensation);
	}

	// test deleting a Posting Id
	public function testDeletePostingId() {
		// first, verify mySQL connected OK
		$this->assertNotNull($this->mysqli);

		// second, create a Posting Id to post to mySQL
		$this->postingPlace = new PostingPlace(null, $this->contactId, $this->postingTitle, $this->location, $this->zipCode,
																$this->postingBody, $this->compensation);

		// third, insert the Posting Id to mySQL
		$this->postingPlace->insert($this->mysqli);

		// fourth, verify the Posting Id was inserted
		$this->assertNotNull($this->postingPlace->getPostingId());
		$this->assertTrue($this->postingPlace->getPostingId() > 0);

		// fifth, delete the posting id
		$deletedPostingId = $this->postingPlace->getPostingId();
		$this->postingPlace->delete($this->mysqli);
		$this->postingPlace = null;

		// finally, try to get the posting id and assert we didn't get a thing
		$hopefulPostingId = PostingPlace::getUserByPostingId($this->mysqli, $deletedPostingId);
		$this->assertNull($hopefulPostingId);
	}

	// test grabbing a posting id from mySQL
	public function testGetUserByPostingId()
	{
		// first, verify mySQL connected OK
		$this->assertNotNull($this->mysqli);

		// second, create a posting Id to post to mySQL
		$this->postingPlace = new PostingPlace(null, $this->contactId, $this->postingTitle, $this->location, $this->zipCode,
			$this->postingBody, $this->compensation);

		// third, insert the user to mySQL
		$this->postingPlace->insert($this->mysqli);

		// fourth, get the user using the static method
		$staticPostingId = PostingPlace::getUserByPostingId($this->mysqli, $this->postingPlace->getPostingId());

		// finally, compare the fields
		$this->assertNotNull($staticPostingId->getPostingId());
		$this->assertTrue($staticPostingId->getPostingId() > 0);
		$this->assertIdentical($staticPostingId->getPostingId(), $this->postingPlace->getPostingId());
		$this->assertIdentical($staticPostingId->getContactId(), $this->contactId);
		$this->assertIdentical($staticPostingId->getPostingTitle(), $this->postingTitle);
		$this->assertIdentical($staticPostingId->getLocation(), $this->location);
		$this->assertIdentical($staticPostingId->getZipCode(), $this->zipCode);
		$this->assertIdentical($staticPostingId->getPostingBody(), $this->postingBody);
		$this->assertIdentical($staticPostingId->getCompensation(), $this->compensation);
	}
}