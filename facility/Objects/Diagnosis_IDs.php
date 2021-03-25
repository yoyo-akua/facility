<?php

	class Diagnosis_IDs{

		## Define all parameters necessary to link a patient's visit to its diagnoses.
		private $protocol_ID;		## ID of the patient's visit.
		private $diagnosis_ID;		## ID of the disease/diagnosis.
		private $importance;		## Either primary or secondary diagnosis.
		
		/*
		## Constructor of new diagnosis entry.
		## Is called, if a new diagnosis database entry is created.
		## The data of new diagnosis is saved in database for all its parameters.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Diagnosis_IDs($protocol_ID,$diagnosis_ID,$importance){
			global $link;
			$query = "INSERT INTO `diagnosis_ids`(`protocol_ID`,`diagnosis_ID`,`importance`) VALUES ('$protocol_ID','$diagnosis_ID','$importance')";
			mysqli_query($link,$query);
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
			$query = "SELECT diagnosis_ID FROM diagnosis_ids WHERE protocol_ID = (SELECT protocol_ID FROM protocol WHERE visit_ID = $visit_ID ORDER BY protocol_ID DESC LIMIT 1)";
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
		## Only the diagnosis of the newest protocol entry, 
		## which belongs to the current patient's visit, is observed.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function getImportance($visit_ID,$diagnosis_ID){
			global $link;
			$query = "SELECT importance FROM diagnosis_ids WHERE protocol_ID = (SELECT protocol_ID FROM protocol WHERE  visit_ID = $visit_ID ORDER BY protocol_ID DESC LIMIT 1) AND diagnosis_ID=$diagnosis_ID";
			$result=mysqli_query($link,$query);
			
			$result=mysqli_fetch_object($result);
			if(! empty($result)){
				$importance=$result->importance;
			}else{
				$importance='';
			}
			
			return $importance;
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
		public static function setRemarks($protocol_ID, $remarks){
			global $link;
			$query = "INSERT INTO `diagnosis_ids`(`protocol_ID`,`remarks`) VALUES ('$protocol_ID','$remarks')";
			mysqli_query($link,$query);
		}

		/*
		## Getter function.
		## Gets remarks of a certain patient's diagnosis.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function getRemarks($visit_ID){
			global $link;
			$query = "SELECT remarks FROM diagnosis_ids WHERE remarks NOT LIKE '' AND protocol_ID IN (SELECT protocol_ID FROM protocol WHERE visit_ID=$visit_ID)";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			
			if(! empty($object)){
				$remarks=$object->remarks;
			}else{
				$remarks='';
			}		
			return $remarks;
		}

		/*
		## This function is used whether a client has received the diagnosis of a particular disease throughout a visit to the facility. 
		## The sent parameter $visit_ID is used to identify the visit at the facility. 
		## The function returns a boolean with the values true or false, depending on the existnece of a previous diagnosis or not. 
		*/
		public function check_Diagnosis($visit_ID,$diagnosis_ID){
			global $link; 
			$query="SELECT * FROM diagnosis_ID d, protocol p WHERE p.protocol_ID=d.protocol_ID AND visit_ID=$visit_ID AND diagnosis_ID=$diagnosis_ID";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			if(! empty($object)){
				$given=true;
			}else{
				$given=false;
			}
			return $given;
		}
	}
?>