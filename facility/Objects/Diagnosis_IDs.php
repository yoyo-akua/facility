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
		## Returns an array of the patient's diagnosis IDs of that visit, of which the function is called.
		*/
		public function getDiagnosis_IDs($visit_ID){
			global $link;
			$query = "SELECT d.diagnosis_ID FROM diagnosis_ids d, protocol p WHERE d.protocol_ID = p.protocol_ID AND p.visit_ID = $visit_ID";
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
		*/
		public function getImportance($visit_ID,$diagnosis_ID){
			global $link;
			$query = "SELECT d.importance FROM diagnosis_ids d, protocol p WHERE d.protocol_ID = p.protocol_ID AND p.visit_ID = $visit_ID AND d.diagnosis_ID=$diagnosis_ID";
			$result=mysqli_query($link,$query);
			
			$result=mysqli_fetch_object($result);
			if(! empty($result)){
				$importance=$result->importance;
			}else{
				$importance='';
			}
			
			return $importance;
		}
		
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
		
		## Deletes all diagnosis entries of a specific patient's visit
		public function clean($protocol_ID){
			global $link;
			$query = "DELETE FROM diagnosis_ids WHERE protocol_ID=$protocol_ID";
			mysqli_query($link,$query);
		}
			
	}
?>