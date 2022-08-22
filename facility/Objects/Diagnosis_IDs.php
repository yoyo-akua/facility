<?php

	class Diagnosis_IDs{

		## Define all parameters necessary to link a patient's visit to its diagnoses.
		private $protocol_ID;		## ID of the patient's visit.
		private $diagnosis_ID;		## ID of the disease/diagnosis.
		private $importance;		## Either primary or secondary diagnosis.
		
		/*
		## This function is called, if a client's diagnosis object is needed for further actions.
		## Saves the information of that client's diagnosis from database (identified by client's protocol ID) in that diagnosis object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Diagnosis_IDs($diagnosis_entry_ID){
			global $link;
			$query = "SELECT * FROM diagnosis_ids WHERE diagnosis_entry_ID = '$diagnosis_entry_ID'";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->protocol_ID = $row->protocol_ID;
				$this->diagnosis_ID = $row->diagnosis_ID;
				$this->importance = $row->importance;
				$this->remarks = $row->remarks;
				$this->reattendance = $row->reattendance;

			}
			$this->diagnosis_entry_ID = $diagnosis_entry_ID;
		}

		/*
		## Constructor of new diagnosis entry.
		## Is called, if a new diagnosis database entry is created.
		## The data of new diagnosis is saved in database for all its parameters.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Diagnosis_IDs($protocol_ID,$diagnosis_ID,$importance,$remarks,$reattendance){
			global $link;
			$query = "INSERT INTO `diagnosis_ids`(`protocol_ID`,`diagnosis_ID`,`importance`,`remarks`,`reattendance`) VALUES ('$protocol_ID','$diagnosis_ID','$importance','$remarks','$reattendance')";
			mysqli_query($link,$query);
		}
		/*
		## Getter function.
		## Returns the remarks on a diagnosis, on which the function is called.
		*/
		public function getRemarks(){
			return $this->remarks;
		}

		/*
		## Getter function.
		## Returns the information whether the patient came for review, on which the function is called.
		*/
		public function getReattendance(){
			return $this->reattendance;
		}

		/*
		## Getter function.
		## Returns the protocol ID of a diagnosis, on which the function is called.
		*/
		public function getProtocol_ID(){
			return $this->protocol_ID;
		}

		/*
		## Getter function.
		## Returns an array of the patient's diagnosis IDs of that visit, 
		## of which the function is called.
		## Thereby only the patient's newest diagnoses (which belong to the newest protocol entry),
		## are observed.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function getDiagnosis_IDs($visit_ID){
			global $link;
			$query = "SELECT diagnosis_ID FROM diagnosis_ids,protocol WHERE protocol.visit_ID=$visit_ID AND diagnosis_ids.protocol_ID=protocol.protocol_ID ORDER BY diagnosis_ids.protocol_ID DESC";
			$result=mysqli_query($link,$query);
			$diagnoses=array();
			while($row=mysqli_fetch_object($result)){
				$diagnoses[]=$row->diagnosis_ID;
			}
			
			return $diagnoses;
		}
		
		/*
		## Getter function.
		## Returns whether a specific diagnosis is primary or secondary.
		## Only the diagnosis of the newest protocol entry.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function getImportance(){
			return $this->importance;
		}
		
		/*
		## Getter function.
		## Returns all importances of all diagnoses of a certain patient's visit.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function getImportances($visit_ID){
			global $link;
			$query = "SELECT d.importance FROM diagnosis_ids d, protocol p WHERE d.protocol_ID = p.protocol_ID AND p.visit_ID = $visit_ID";
			$result=mysqli_query($link,$query);
			
			$importances=array();
			while($row=mysqli_fetch_object($result)){
				$importances[]=$row->importance;
			}
			
			return $importances;
		}
		
		/*
		## Deletes all diagnosis entries of a specific patient's visit
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function clean($protocol_ID){
			global $link;
			$query = "DELETE FROM diagnosis_ids WHERE protocol_ID=$protocol_ID";
			mysqli_query($link,$query);
		}	

		/*
		## Setter function.
		## Writes remarks of a certain patient's diagnosis into database.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setRemarks($var){
			global $link;
			$query = "UPDATE `diagnosis_ids` SET remarks='$var' WHERE diagnosis_entry_ID=$this->diagnosis_entry_ID";
			mysqli_query($link,$query);

			return $this->remarks=$var;
		}

		/*
		## Setter function.
		## Writes remarks of a certain patient's diagnosis into database.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setImportance($var){
			global $link;
			$query = "UPDATE `diagnosis_ids` SET importance='$var' WHERE diagnosis_entry_ID=$this->diagnosis_entry_ID";
			mysqli_query($link,$query);

			return $this->importance=$var;
		}

		/*
		## Checks whether a certain patient's diagnosis contains any remarks,
		## if so returns the ID of this entry, if not, returns "false".
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function check_Remarks($visit_ID){
			global $link;
			$query = "SELECT * FROM diagnosis_ids WHERE remarks NOT LIKE '' AND protocol_ID IN (SELECT protocol_ID FROM protocol WHERE visit_ID=$visit_ID)";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			
			if(! empty($object)){
				$ID=$object->diagnosis_entry_ID;
			}else{
				$ID=false;
			}		
			return $ID;
		}

		/*
		## Checks whether a certain patient's diagnosis contains any remarks,
		## if so returns the ID of this entry, if not, returns "false".
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function check_Reattendance($visit_ID){
			global $link;
			$query = "SELECT * FROM diagnosis_ids WHERE reattendance LIKE '1' AND protocol_ID IN (SELECT protocol_ID FROM protocol WHERE visit_ID=$visit_ID)";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			
			if(! empty($object)){
				$ID=$object->diagnosis_entry_ID;
			}else{
				$ID=false;
			}
			return $ID;
		}		

		/*
		## Setter function.
		## Writes remarks of a certain patient's diagnosis into database.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function delete_Diagnosis_IDs($diagnosis_entry_ID){
			global $link;
			$query = "DELETE FROM diagnosis_ids WHERE diagnosis_entry_ID=$diagnosis_entry_ID";
			mysqli_query($link,$query);
		}		

		/*
		## This function is used whether a client has received the diagnosis of a particular disease throughout a visit to the facility. 
		## The sent parameter $visit_ID is used to identify the visit at the facility. 
		## The function returns the ID of the entry, if the diagnosis already exists, otherwise false. 
		*/
		public function check_Diagnosis($visit_ID,$diagnosis_ID){
			global $link; 
			$query="SELECT * FROM diagnosis_ids d, protocol p WHERE p.protocol_ID=d.protocol_ID AND visit_ID=$visit_ID AND diagnosis_ID=$diagnosis_ID";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			if(! empty($object)){
				$given=$object->diagnosis_entry_ID;
			}else{
				$given=false;
			}
			return $given;
		}

	
		## This function is used to check whether there are any diagnoses in the database linked to a specific protocol entry.
		public function check_protocol($protocol_ID){
			global $link; 
			$query="SELECT * FROM diagnosis_ids WHERE protocol_ID=$protocol_ID";
			$result=mysqli_query($link,$query);
			if(mysqli_num_rows($result)!==0){
				$protocol_ID=true;
			}else{
				$protocol_ID=false;
			}
			return $protocol_ID;
		}
	}
?>