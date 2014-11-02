<?php
/**
 * Created by PhpStorm.
 * User: Martin
 * Date: 11/1/2014
 * Time: 10:22 PM
 * Create new Contact Id class for mySQL
 **/
class ContactInfo {
	//contact Id for the ContactId class; this is the foreign key
	private $contactId;
	//email for the ContactId class; this is the unique field
	private $email;
	//phone number for the ContactId class
	private $phone;
	//name of contact for the ContactId class
	private $contactName;
	/**  constructor for the ContactId
	 * @param mixed $newContactId contact ID(or null if new object)
	 * @param string $newEmail users email address
	 * @param string $newPhone users phone numbers
	 * @param string $newContactName users contact information
	 * @throws UnexpectedValueException when a parameter is of the wrong type
	 * @throws RangeException when a parameter is invalid
	 **/
	public function __construct($newContactId, $newEmail, $newPhone, $newContactName) {
		try {
			$this->setContactId($newContactId);
			$this->setEmail($newEmail);
			$this->setPhone($newPhone);
			$this->setContactName($newContactName);
		} catch(UnexpectedValueException $unexpectedValue) {
			//rethrow to caller
			throw(new UnexpectedValueException ("Unable to construct User", 0, $unexpectedValue));
		} catch(RangeException $range) {
			//rethrow to caller
			throw(new RangeException("Unable to construct User", 0, $range));
		}
	}
	/**
	 * gets the value of contactId
	 *
	 * @return mixed contact Id (or null if new object)
	 **/
	public function getContactId() {
		return($this->contactId);
	}

	/**
	 * sets the value of contact id
	 *
	 * @param mixed $newContactId posting id (or null if new object)
	 * @throws UnexpectedValueException if not an integer or null
	 * @throws RangeException if user id isn't positive
	 **/
	public function setContactId($newContactId) {
		//set allow the contact id to be null if a new object
		if($newContactId=== null) {
			$this->contactId = null;
			return;
		}
		//first, ensure the contact id is an integer
		if(filter_var($newContactId, FILTER_VALIDATE_INT) === false) {
			throw(new UnexpectedValueException("posting id $newContactId is not numeric"));
		}

		// second, convert the contact id to an integer and enforce it's positive
		$newContactId = intval($newContactId);
		if($newContactId <=0) {
			throw(new RangeException ("Posting Id $newContactId is not positive"));
		}
		//now it is ok to assign and remove contact Id from quarantine
		$this->contactId = $newContactId;
	}
	/**
	 * gets the value of email
	 *
	 * @return string value of email
	 **/
	public function getEmail() {
		return($this->email);
	}

	/**
	 * sets the value of email
	 *
	 * @param string $newEmail email
	 * @throws UnexpectedValueException if the input doesn't appear to be an Email
	 **/
	public function setEmail($newEmail) {
		// sanitize the Email as a likely Email
		$newEmail = trim($newEmail);
		if(($newEmail = filter_var($newEmail, FILTER_SANITIZE_EMAIL)) == false) {
			throw(new UnexpectedValueException("email $newEmail does not appear to be an email address"));
		}

		// then just take email out of quarantine
		$this->email = $newEmail;
	}
	/** get characters for phone number
	 *
	 * @ return string value for phone number
	 **/
	public function getPhone () {
		return($this->phone);
	}
	//verify phone number is a string
	public function setPhone($newPhone) {
		$newPhone = trim($newPhone);
		if(filter_var($newPhone, FILTER_SANITIZE_STRING) === false) {
			throw(new UnexpectedValueException("posting title $newPhone is not a string"));
		}
		//verify phone number is less than 24 characters
		$newPhoneLength = strlen($newPhone);
		if($newPhoneLength > 23) {
			throw(new RangeException("posting title $newPhone is longer than 250 characters"));
		}
		//assign phone number and take out of quarantine
		$this->phone = $newPhone;
	}
	/** get characters for the contact name
	 *
	 * @ return string value for contact name
	 **/
	public function getContactName () {
		return ($this->contactName);
	}
	//verify contact name is a string
	public function setContactName($newContactName) {
		$newContactName = trim($newContactName);
		if(filter_var($newContactName, FILTER_SANITIZE_STRING)=== false)  {
			throw(new UnexpectedValueException ("location $$newContactName is not a string"));
		}
		//verify contact name is less than 65 characters
		$newContactNameLength = strlen($newContactName);
		if($newContactNameLength > 64){
			throw(new RangeException("location $newContactName is longer than 64 characters"));
		}
		//assign location and take out of quarantine
		$this->location = $newContactName;
	}

	/**
	 * Insert ContactInfo  into mySQL
	 *
	 * @param resource $mySQLi to connect to mySQL connection, by reference
	 * @throws mysqli_sql_exception when mySQL related errors occur
	 **/

	public function insert(&$mysqli) {
		//handle degenerate cases
		if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli") {
			throw(new mysqli_sql_exception("input is not a mysqli object"));
		}
		//ensures the ContactId  is null (does not already exist)
		if($this->contactId !== null) {
			throw(new mysqli_sql_exception("not a new user"));
		}

		//create query template
		$query = "INSERT INTO contactInfo(email, phone, contactName) VALUES (?,?,?)";
		$statement = $mysqli->prepare($query);
		if($statement === false) {
			throw(new mysqli_sql_exception("Unable to prepare statements"));
		}
		//bind the member variables to the place holders in the template
		$wasClean = $statement->bind_param("sss", $this->email, $this->phone, $this->contactName);
		if($wasClean === false) {
			throw(new mysqli_sql_exception("Unable to bind statement"));
		}
		//execute the statement
		if($statement->execute() === false) {
			throw(new mysqli_sql_exception ("Unable to execute mySQL statement"));
		}
		//update the null postingId with SQL information
		$this->contactId = $mysqli->insert_id;
	}
	/**
	 * deletes this User from mySQL
	 *
	 * @param resource $mysqli pointer to mySQL connection, by reference
	 * @throws mysqli_sql_exception when mySQL related errors occur
	 **/
	public function delete(&$mysqli)
	{
		//handle degenerate cases
		if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli") {
			throw(new mysqli_sql_exception("input is not a mysqli object"));
		}
		//enforces the user is not null or don't delete a user that hasn't been inserted
		if($this->contactId === null) {
			throw(new mysqli_sql_exception("unable to delete a user that does not exist"));
		}
		//create query template
		$query = "DELETE FROM contactInfo WHERE contactId = ?";
		$statement = $mysqli->prepare($query);
		if($statement === false) {
			throw(new mysqli_sql_exception("unable to prepare statement"));
		}
		//bind the member variables to the place holder in the template
		$wasClean = $statement->bind_param("i", $this->contactId);
		if($wasClean === false) {
			throw(new mysqli_sql_exception("unable to bind parameters"));
		}

		//execute the statement
		if($statement->execute() === false) {
			throw(new mysqli_sql_exception("unable to execute mySQL statement"));
		}
	}
	/**
	 * updates this contactInfo in mySQL
	 *
	 * @param resource $mysqli pointer to mySQL connection, by reference
	 * @throws mysqli_sql_exception when mySQL related errors occur
	 **/
	public function update(&$mysqli) {
		//handle degenerate cases
		if(gettype($mysqli) !=="object" || get_class($mysqli) !== "mysqli"){
			throw(new mysqli_sql_exception("input is not a mysqli object"));
		}
		//enforce contact Id is not null/  don't update a user that does not exist
		if($this->postingId === null) {
			throw (new mysqli_sql_exception ("Unable to update because user does not exist"));
		}
		//create query template
		$query		= "UPDATE contactInfo SET email = ?, phone = ?, contactName = ?";
		$statement	= $mysqli->prepare($query);
		if($statement === false) {
			throw(new mysqli_sql_exception("unable to prepare statement"));
		}
		//bind the member variables to the place holders in the template
		$wasClean = $statement->bind_param("sss", $this->email, $this->phone, $this->contactName);
		if($wasClean === false) {
			throw(new mysqli_sql_exception("unable to bind parameters"));
		}

		//execute the statement
		if($statement->execute () === false)  {
			throw(new mysqli_sql_exception ("unable to execute mySQL statement"));
		}
	}
	/**
	 * Get the contactInfo by  contactId
	 *
	 * @param resource $mysqli pointer to mySQL connection, by reference
	 * @param string $contactId   contactId to search for
	 * @return mixed contactInfo found or null if not found
	 * @throws mysqli_sql_exception when mySQL related errors occur
	 **/
	public static function getUserByContactId(&$mysqli, $contactId) {
		//handle degenerate cases
		if(gettype($mysqli) !=="object" || get_class($mysqli) !== "mysqli") {
			throw(new mysqli_sql_exception("input is not a mysqli object"));
		}
		//sanitize the contactId before searching
		$contactId = filter_var($contactId, FILTER_VALIDATE_INT);
		if($contactId === null) {
			throw(new mysqli_sql_exception("input is null"));
		}

		//Create query template
		$query		="SELECT contactId, email, phone, contactName FROM contactInfo WHERE contactId = ?";
		$statement	=$mysqli->prepare($query);
		if($statement === false) {
			throw(new mysqli_sql_exception ("unable to prepare statement"));
		}

		//bind the contact Id to the place holder in the template
		$wasClean = $statement->bind_param("i", $contactId);
		if($wasClean === false) {
			throw(new mysqli_sql_exception("unable to bind parameters"));
		}
		//execute the statement
		if($statement->execute() === false) {
			throw(new mysqli_sql_exception("unable to execute mySQL statement"));
		}
		// get result from the SELECT query
		$result = $statement->get_result ();
		if($result === false) {
			throw(new mysqli_sql_exception("unable to get result set"));
		}
		//primary key can only one of two things null or integer
		//if there's a result, we can show it
		//if not error code 404
		$row = $result->fetch_assoc ();

		//covert the associative array to a postingId
		if($row !==null) {
			try {
				$contactId = new contactInfo($row["contactId"], $row["email"], $row["phone"], $row["contactName"]);
			}
			catch(Exception $exception) {
				//rethrow
				throw(new mysqli_sql_exception ("unable to convert row to contactInfo", 0, $exception));

			}
			//if we get a postingId I'm lucky and show it
			return($contactId);
		} else {
			//404 User not found
			return(null);
		}
	}
}