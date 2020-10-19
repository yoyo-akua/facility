<?php

	class Tests{

		## A test object contains all general data, a test has. In the following its attributes are defined.
		private $test_ID;	## Test's ID.
		private $test_name;	## Test's name.
		private $frequency;	## Test's frequency (rare, normal, frequent).
		private $sex_limit;	## Contains information, for which sex a test is able to be performed. It is empty, if no limitation exists.
		
		/*
		## This function is called, if a new test object is needed for futher actions.
		## Saves the information of that test from database (identified by test ID) in that new test object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Tests($test_ID){
			global $link;
			$query = "SELECT * FROM tests WHERE test_ID = $test_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
			    $this->test_name = $row->test_name;
				$this->frequency = $row->frequency;
				$this->sex_limit = $row->sex_limit;
			}
			$this->test_ID = $test_ID;
		}

		/*
		## Constructor of new test.
		## Is called, if a new test is created in database.
		## The data of the new test is saved in database some of its parameters (the others are having default values).
		## Save this data also in a new created test object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Test($test_name,$frequency){
			global $link;
			$query = "INSERT INTO `tests`(`test_name`,`frequency`) VALUES ('$test_name','$frequency')";
			mysqli_query($link,$query);
				
			$test_ID = mysqli_insert_id($link);
			$instance = new self($test_ID);
			return $instance;
		}

		/*
		## Getter function.
		## Returns the ID of that test, on which the function is called.
		*/
		public function getTest_ID(){
			return $this->test_ID;
		}

		/*
		## Getter function.
		## Returns the name of that test, on which the function is called.
		*/	
		public function getTest_name(){
			return $this->test_name;
		}

		/*
		## Getter function.
		## Returns the frequency of that test, on which the function is called.
		*/
		public function getFrequency(){
			return $this->frequency;
		}

		/*
		## Getter function.
		## Returns the sex limitation of that test, on which the function is called.
		*/
		public function getSex_limit(){
			return $this->sex_limit;
		}

		/*
		## Setter function.
		## Updates the name of the test in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/			
		public function setTest_name($var){
			global $link;
			$query = "UPDATE tests SET test_name='$var' WHERE test_ID = $this->test_ID";
			mysqli_query($link,$query);
			return $this->test_name = $var;
		}
	}
?>
