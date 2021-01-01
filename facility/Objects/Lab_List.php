<?php
	class Lab_List{
		## Define all parameters a lab list entry has. Each lab list entry represents a specific action during a patient's visit.
		private $lab_list_ID;				## Database ID of this lab list entry. 
		private $lab_done;					  ## Defines, whether the lab tests have been completed for the particular visit.
		private $lab_number;			   ## Defines patient's lab number which is used by user to label samples (not the identifier in database).
		private $visit_ID;				## Database ID of the patient's visit (to the facility, not lab).

		/*
		## This function is called, if a new lab list object is needed for further actions.
		## Saves the information of that lab list from database (identified by lab list ID) in that new lab list object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Lab_List($visit_ID){
			global $link;
			$query = "SELECT * FROM lab_list WHERE visit_ID = $visit_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->lab_done = $row->lab_done;
				$this->lab_number = $row->lab_number;
				$this->lab_list_ID = $row->lab_list_ID;
			}
			$this->visit_ID = $visit_ID;
		}

		/*
		## Constructor of new lab list entry.
		## Is called, if a new lab list database entry is created.
		## The data of new lab list is saved in database for some of its parameters (the others are having default values).
		## Save this data also in a new created lab list object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Lab_List($lab_number,$visit_ID){
			global $link;
			$query = "INSERT INTO `lab_list`(`lab_number`,`visit_ID`) VALUES ('$lab_number','$visit_ID')";
			mysqli_query($link,$query);

			$instance = new self($visit_ID);
			return $instance;		
		}

		/*
		## Getter function.
		## Returns the ID of that lab list entry, on which the function is called.
		*/
		public function getLab_List_ID(){
			return $this->lab_list_ID;			
		}

		/*
		## Getter function.
		## Returns, whether the patient's lab tests have been completed for that particular visit.
		*/
		public function getLab_done(){
			return $this->lab_done;
		}					
		
		/*
		## Getter function.
		## Returns patient's lab number which is used by user to label samples (not the identifier in database).
		## If this field is empty that implies, that no tests were ordered for the patient.
		*/
		public function getLab_number(){
			return $this->lab_number;
		}
		
		/*
		## Setter function.
		## Updates the status, whether the patient's lab tests for that particular visit were completed in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setLab_done($var){
			global $link;
			$query = "UPDATE lab_list SET lab_done='$var' WHERE lab_list_ID = $this->lab_list_ID";
			mysqli_query($link,$query);
			return $this->lab_done = $var;
		}		

		/*
		## Setter function.
		## Updates patient's lab number which is used by user to label samples (not the identifier in database).
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setLab_number($var){
			global $link;
			$query = "UPDATE lab_list SET lab_number='$var' WHERE lab_list_ID = $this->lab_list_ID";
			mysqli_query($link,$query);
			return $this->lab_number = $var;
		}

	}

?>
