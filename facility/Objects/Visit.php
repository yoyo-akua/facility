<?php
	class Visit{
		## Define all parameters a visit entry has. Each visit entry represents a patient's visit.
		private $visit_ID;				## Database ID of this visit. 
		private $patient_ID;				 ## Database ID of the patient, who was visiting.	
		private $new_p;						  ## Defines, whether the patient was visiting the facility for the first time within this year or not.
        private $checkin_time;					  ## Checkin time of patient's visit.
        private $checkout_time;					  ## Checkout time of patient's visit.
		private $pregnant;					 ## Defines, whether the patient is pregnant at the visit's date.
		private $protect;					   ## Defines, whether the patient's diagnosis is checkin_time or not.
		private $onlylab;					   ## Defines, whether the patient visits only for lab, without any consulting.


		/*
		## This function is called, if a new visit object is needed for further actions.
		## Saves the information of that visit from database (identified by visit ID) in that new visit object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Visit($visit_ID){
			global $link;
			$query = "SELECT * FROM visit WHERE visit_ID = $visit_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->patient_ID = $row->patient_ID;
				$this->new_p = $row->new_p;
				$this->checkin_time = $row->checkin_time;
				$this->checkout_time = $row->checkout_time;
				$this->pregnant = $row->pregnant;
				$this->protect = $row->protect;
				$this->onlylab = $row->onlylab;
			}
			$this->visit_ID = $visit_ID;
		}

		/*
		## Constructor of new visit entry.
		## Is called, if a new visit database entry is created.
		## The data of new visit is saved in database for some of its parameters (the others are having default values).
		## Save this data also in a new created visit object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Visit($patient_ID,$new_p){
			global $link;
			$query = "INSERT INTO `visit`(`patient_ID`,`new_p`) VALUES ('$patient_ID','$new_p')";
			mysqli_query($link,$query);

            $visit_ID = mysqli_insert_id($link);
            
			$instance = new self($visit_ID);
			return $instance;		
		}

		/*
		## Getter function.
		## Returns the ID of that visit entry, on which the function is called.
		*/
		public function getVisit_ID(){
			return $this->visit_ID;
		}

		/*
		## Getter function.
		## Returns the visit date of that visit entry, on which the function is called.
		*/
		public function getNew_p(){
			return $this->new_p;
		}

		/*
		## Getter function.
		## Returns the checkin time of the visit, on which the function is called.
		*/
		public function getCheckin_time(){
			return $this->checkin_time;
		}

		/*
		## Getter function.
		## Returns the facility personal's name, who diagnosed the patient at that visit, on which the function is called.
		*/	
		public function getCheckout_time(){
			return $this->checkout_time;
		}		

		/*
		## Getter function.
		## Returns, whether the patient was pregnant at that visit, on which the function is called.
		*/
		public function getPregnant(){
			return $this->pregnant;
		}	

		/*
		## Getter function.
		## Returns the patient's protection status (is its diagnosis protected or not) of that visit, on which the function is called.
		*/
		public function getProtect(){
			return $this->protect;
		}	
		
		/*
		## Getter function.
		## Returns the patient ID of the client.
		*/
		public function getPatient_ID(){
			return $this->patient_ID;
		}

		/*
		## Getter function.
		## Returns, whether the patient was only visiting for lab (not for consulting or other services) at that visit, 
		## on which the function is called.
		*/
		public function getOnlylab(){
			return $this->onlylab;
		}

		/*
		## Setter function.
		## Updates the status, whether the patient was visiting the facility first time this year or more often, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setNew_p($var){
			global $link;
			$query = "UPDATE visit SET new_p='$var' WHERE visit_ID = $this->visit_ID";
			mysqli_query($link,$query);
			return $this->new_p = $var;
		}

		/*
		## Setter function.
		## Updates the patient's checkin_time of the visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setCheckin_time($var){
			global $link;
			$query = "UPDATE visit SET checkin_time='$var' WHERE visit_ID = $this->visit_ID";
			mysqli_query($link,$query);
			return $this->checkin_time = $var;
		}

		/*
		## Setter function.
		## Updates the checkout time of that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setCheckout_time($var){
			global $link;
			$query = "UPDATE visit SET checkout_time='$var' WHERE visit_ID = $this->visit_ID";
			mysqli_query($link,$query);
			return $this->checkout_time = $var;
		}

		/*
		## Setter function.
		## Updates the status, whether the patient was pregnant at that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setPregnant($var){
			global $link;
			$query = "UPDATE visit SET pregnant='$var' WHERE visit_ID = $this->visit_ID";
			mysqli_query($link,$query);
			return $this->pregnant = $var;
		}

		/*
		## Setter function.
		## Updates the patient's protection status (is its diagnosis protected or not) of that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setProtect($var){
			global $link;
			$query = "UPDATE visit SET protect='$var' WHERE visit_ID = $this->visit_ID";
			mysqli_query($link,$query);
			return $this->protect = $var;
		}
		

		/*
		## Setter function.
		## Updates the status, whether the patient was only visiting for lab (not for consulting or other services) at that visit, 
		## on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setOnlylab($var){
			global $link;
			$query = "UPDATE visit SET onlylab='$var' WHERE visit_ID = $this->visit_ID";
			mysqli_query($link,$query);
			return $this->onlylab = $var;
		}

		/*
		## This function is used to determine whether a patient has been referred to lab during a certain visit.
		## Return the lab number, if there is a lab entry, "false" if there is not. 
		*/
		public function getLab_number(){
			global $link;
			$query= "SELECT * FROM protocol,lab_list,lab WHERE protocol.visit_ID=$this->visit_ID AND lab.lab_list_ID=lab_list.lab_list_ID AND protocol.protocol_ID=lab.protocol_ID";
			$result=mysqli_query($link,$query);
			if(mysqli_num_rows($result)!==0){
				$result=mysqli_query($link,$query);
				while($row=mysqli_fetch_object($result)){
					return $row->lab_number;
				}
			}else{
				return false;
			}
		}
	}

?>
