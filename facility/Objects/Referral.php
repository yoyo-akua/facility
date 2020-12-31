<?php
	class Referral{
		## Define all parameters a protocol entry has. Each protocol entry represents a specific action during a patient's visit.
		private $protocol_ID;				## Database ID of this protocol entry. 
		private $destination;				## The destination to which the patient has been referred.
		private $reason;					## Reason of the referral. 
	
		/*
		## This function is called, if a new referral object is needed for further actions.
		## Saves the information of that referral entry from database (identified by protocol ID) in that new referral object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Referral($protocol_ID){
			global $link;
			$query = "SELECT * FROM referral WHERE protocol_ID = $protocol_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->destination = $row->destination;
				$this->reason = $row->reason;
			}
			$this->protocol_ID = $protocol_ID;
		}

		/*
		## Constructor of new referral entry.
		## Is called, if a new referral database entry is created.
		## The data of new referral is saved in database for some of its parameters (the others are having default values).
		## Save this data also in a new created referral object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Referral($protocol_ID,$destination,$reason){
			global $link;
			$query = "INSERT INTO `referral`(`protocol_ID`,`destination`,`reason`) VALUES ('$protocol_ID','$destination','$reason')";
			mysqli_query($link,$query);

			$protocol_ID = mysqli_insert_id($link);

			$instance = new self($protocol_ID);
			return $instance;		
		}


		/*
		## This function is called to delezte a certain referral entry defined by $protocol_ID.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function delete_Referral($protocol_ID){
			global $link;
			$query = "DELETE FROM `referral` WHERE protocol_ID=$protocol_ID";
			mysqli_query($link,$query);
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
		public function getDestination(){
			return $this->destination;
		}

		/*
		## Getter function.
		## Returns the facility personal's name, who diagnosed the patient at that visit, on which the function is called.
		*/	
		public function getReason(){
			return $this->reason;
        }		
        
		/*
		## Setter function.
		## Updates the time of that protocol entry, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setReason($var){
			global $link;
			$query = "UPDATE referral SET reason='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->reason = $var;
		}

		/*
		## Setter function.
		## Updates the facility personal's name, who diagnosed the patient at that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setDestination($var){
			global $link;
			$query = "UPDATE referral SET destination='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->staff_ID = $var;
		}

		public function checkReferral($visit_ID){
			global $link;
			$query = "SELECT * FROM referral, protocol WHERE protocol.protocol_ID=referral.protocol_ID AND visit_ID=$visit_ID";
			$result = mysqli_query($link,$query);
			if(mysqli_num_rows($result)!==0){
				while($row = mysqli_fetch_object($result)){
					$referral=$row->protocol_ID;
				}
			}else{
				$referral=false;
			}
			return $referral;
		}

	}

?>
