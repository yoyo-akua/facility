<?php
	class protocol{
		## Define all parameters a protocol entry has. Each protocol entry represents a specific action during a patient's visit.
		private $protocol_ID;				## Database ID of this protocol entry. 
		private $visit_ID;					## Database ID of this visit.
		private $timestamp;					  ## Time of this protocol entry.
		private $staff_ID;					 ## ID of clinic staff, who perform the diagose.
		private $event;						## Verbal description of event linked to the protocol entry.
		private $surgery;					  ## Contains the name of surgery, which where performed on patient at visit's date.	
		private $PNC;						   ## Defines, if the patient did PNC at the patient at the visit's date.		
		private $charge;					   ## Defines how much the patient was charged either in lab or for a minor OP.

		/*
		## This function is called, if a new protocol object is needed for further actions.
		## Saves the information of that protocol from database (identified by protocol ID) in that new protocol object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Protocol($protocol_ID){
			global $link;
			$query = "SELECT * FROM protocol WHERE protocol_ID = $protocol_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->visit_ID = $row->visit_ID;
				$this->timestamp = $row->timestamp;
				$this->staff_ID = $row->staff_ID;
				$this->event = $row->event;
				$this->surgery = $row->surgery;
				$this->PNC = $row->PNC;
				$this->charge = $row->charge;
			}
			$this->protocol_ID = $protocol_ID;
		}

		/*
		## Constructor of new protocol entry.
		## Is called, if a new protocol database entry is created.
		## The data of new protocol is saved in database for some of its parameters (the others are having default values).
		## Inquire from the session variable who is consulting on the client and buffer this information in $staff_ID.
		## Save this data also in a new created protocol object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Protocol($visit_ID,$event){
			global $link;
			if(isset($_SESSION['staff_ID'])){
				$staff_ID=$_SESSION['staff_ID'];
			}else{
				$staff_ID='';
			}
			
			$query = "INSERT INTO `protocol`(`visit_ID`,`event`,`staff_ID`) VALUES ('$visit_ID','$event','$staff_ID')";
			mysqli_query($link,$query);

			$protocol_ID = mysqli_insert_id($link);

			$instance = new self($protocol_ID);
			return $instance;		
		}

		/*
		## Getter function.
		## Returns the ID of that protocol entry, on which the function is called.
		*/
		public function getProtocol_ID(){
			return $this->protocol_ID;
		}

		/*
		## Getter function.
		## Returns the ID of that protocol entry, on which the function is called.
		*/
		public function getVisit_ID(){
			return $this->visit_ID;
		}

		/*
		## Getter function.
		## Returns the facility personal's name, who diagnosed the patient at that visit, on which the function is called.
		*/	
		public function getStaff_ID(){
			return $this->staff_ID;
		}		


		/*
		## Getter function.
		## Returns the time of the protocol entry, on which the function is called.
		## This ID is used to connect the patient's ANC data with the patient's visit in the facility.
		*/
		public function getTimestamp(){
			return $this->timestamp;
		}

		/*
		## Getter function.
		## Returns the surgery's name, which was performed on patient at that visit, on which the function is called.
		*/
		public function getsurgery(){
			return $this->surgery;
		}			

		/*
		## Getter function.
		## Returns the patient's PNC status (performed or not) of that visit, on which the function is called.
		*/
		public function getPNC(){
			return $this->PNC;
		}
		
		/*
		## Getter function.
		## Returns, whether the patient was charged either in lab or for a minor OP
		*/
		public function getCharge(){
			return $this->charge;
		}				
	

		/*
		## Setter function.
		## Updates the time of that protocol entry, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setTimestamp($var){
			global $link;
			$query = "UPDATE protocol SET timestamp='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->timestamp = $var;
		}

		/*
		## Setter function.
		## Updates the facility personal's name, who diagnosed the patient at that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setStaff_ID($var){
			global $link;
			$query = "UPDATE protocol SET staff_ID='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->staff_ID = $var;
		}

		/*
		## Setter function.
		## Updates surgery's name, which was performed on patient at that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/			
		public function setsurgery($var){
			global $link;
			$query = "UPDATE protocol SET surgery='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->surgery = $var;
		}

		/*
		## Setter function.
		## Updates the patient's PNC status (performed or not) of that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setPNC($var){
			global $link;
			$query = "UPDATE protocol SET PNC='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->PNC = $var;
		}

		/*
		## Setter function.
		## Updates how much the patient was charged either in lab or for a Minor OP in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setCharge($var){
			global $link;
			$query = "UPDATE protocol SET charge='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->charge = $var;
		}		
		
		/*
		## Display the patient's surgery information at the date of the visit, on which this function is called.
		## Get the name of the procedure which is saved in $this->surgery 
		## and how much the patient was charged which is saved in $this->charge,
		## save both in $html.
		## Variable $html is used as buffer of HTML commands to print the surgery information later.
		*/
		public function display_surgery(){
			$procedure=$this->surgery;
			$charge=$this->charge;

			$html="
						<h4>Procedure:</h4> $procedure<br>
						<h4>Charge:</h4> $charge GhC<br>
						";	
			return $html;
		}

		/*
		## This function is used to retrieve all protocol entries related to one visit from the database. 
		## It uses the sent parameter $visit_ID to get these data and returns them.
		*/
		public function getAllByVisit_ID($visit_ID){
			global $link;
			$query="SELECT * FROM protocol WHERE visit_ID=$visit_ID";
			$result=mysqli_query($link,$query);
			return $result;
		}

	}

?>
