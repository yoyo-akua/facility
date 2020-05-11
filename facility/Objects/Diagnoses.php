<?php

	class Diagnoses{

		## Define all parameters a disease/diagnosis has. This object represents a certain disease/diagnosis.
		private $Diagnosis_ID;		## ID of the disease/diagnosis.
		private $DiagnosisName;		## Name of the disease/diagnosis.
		private $DiagnosisClass;		## Class/Category of disease/diagnosis.
		
		/*
		## This function is called, if a new diagnosis object is needed for further actions.
		## Saves the information of that disease/diagnosis from database (identified by diagnosis ID) in that new diagnosis object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Diagnoses($Diagnosis_ID){
			global $link;
			$query = "SELECT * FROM diagnoses WHERE Diagnosis_ID = $Diagnosis_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
			    $this->DiagnosisName = $row->DiagnosisName;
			    $this->DiagnosisClass = $row->DiagnosisClass;
			}
			$this->Diagnosis_ID = $Diagnosis_ID;
		}
		
		/*
		## Constructor of new diagnosis.
		## Is called, if a new diagnosis database entry is created.
		## The data of new diagnosis is saved in database for all its parameters.
		## Save this data also in a new created diagnosis object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Diagnoses($DiagnosisName,$DiagnosisClass){
			global $link;
			$query = "INSERT INTO `diagnoses`(`DiagnosisName`,`DiagnosisClass`) VALUES ('$DiagnosisName','$DiagnosisClass')";
			mysqli_query($link,$query);
			
			$Diagnosis_ID = mysqli_insert_id($link);
			$instance = new self($Diagnosis_ID);
			return $instance;
		}

		/*
		## Getter function.
		## Returns the name of that disease/diagnosis, on which the function is called.
		*/
		public function getDiagnosisName(){
			return $this->DiagnosisName;
		}

		/*
		## Getter function.
		## Returns the class's name of that disease/diagnosis, on which the function is called.
		*/
		public function getDiagnosisClass(){
			return $this->DiagnosisClass;
		}	
		
		
		## Print a head of a table to display diseases with three columns: primary disease, secondary disease and name of disease.		
		public function diagnoses_tablehead(){
			echo"<table>
						<tr>
							<th style='border-left:none'>
								primary
							</th>
							<th>
								secondary
							</th>
							<th>
								disease
							</th>
						</tr>
					";
		}	
		
		
		/*
		## Print a single table row to display a disease, with checkboxes in both columns, the first and the second one.
		## Check, if that disease is defined as primary or secondary disease and activate the correspondent checkbox.
		## Print the disease's name in the third column.
		*/
		public function diagnoses_tablerow($protocol_ID,$Diagnosis_ID){
			$Diagnosis_Name=(new Diagnoses($Diagnosis_ID))->getDiagnosisname();
			echo"
					<tr>
						<td style='border-left:none'>
							<input type='checkbox' name='prim_$Diagnosis_ID'";
							if(Diagnosis_IDs::getImportance($protocol_ID,$Diagnosis_ID)==1 OR ! empty($_POST["prim_$Diagnosis_ID"])){
								echo "checked='checked'";
							}
							echo">
						</td>
						<td>
							<input type='checkbox' name='sec_$Diagnosis_ID'";
							if(Diagnosis_IDs::getImportance($protocol_ID,$Diagnosis_ID)==2 OR ! empty($_POST["sec_$Diagnosis_ID"])){
								echo "checked='checked'";
							}
							echo">
						</td>
						<td>
							$Diagnosis_Name
						</td>
					</tr>
					";
		}
		
		
	}
?>