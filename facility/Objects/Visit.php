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
			$query= "SELECT * FROM lab_list WHERE visit_ID=$this->visit_ID";
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

		/*
		## Display all diagnoses, which are known within the system.
		## Identify, which of them is marked as primary disease and which as secondary disease(s) for the patient's visit, 
		## on which this function is called.
		## The sent parameter $circumference defines, whether only primary, only secondary or both - primary and secondary - diseases are displayed.
		*/
		public function display_diagnoses($circumference){

			/*
			## Initialise variables.
			## $Diagnosis_IDs contains all known diseases ordered by their classes
			## Variable $html is used as buffer of HTML commands to print the list of diseases.
			## Variable $primary is used as buffer of the disease, which is marked as primary diagnose.
			## Variable $secondary is used as buffer of these diseases, which are marked as secondary diagnoses.
			*/
			$Diagnosis_IDs=Diagnosis_IDs::getDiagnosis_IDs($this->visit_ID);
			$html='';
			$primary='';
			$secondary='';
			$provisional='';

			## Check, if the patient needs to reattending. If so, print a correspondent notice. 
			if(in_array(0,Diagnosis_IDs::getImportances($this->visit_ID))){
				$html.='Patient is <h4>reattending</h4><br>';
			}

			/*
			## This loop is run for each disease, which was gotten from database before.
			## Check, if a disease is marked as primary or as secondary diagnose,
			## and add a notice in the correspondent buffer ($primary or $secondary).
			*/
			foreach($Diagnosis_IDs AS $Diagnosis_ID){
				$Diagnosis = new Diagnoses($Diagnosis_ID);
				$importance=Diagnosis_IDs::getImportance($this->visit_ID,$Diagnosis_ID);
				$DiagnosisName=$Diagnosis->getDiagnosisName();
				if ($importance==1){
					$primary.="-$DiagnosisName<br>";
				}else if ($importance==2){
					$secondary.="-$DiagnosisName<br>";
				}else if($importance==3){
					$provisional.="-$DiagnosisName<br>";
				}
			}

			/*	
			## Add the as primary marked diagnoses to HTML buffer for printing it later. 
			## Add also a headline for primary diagnosis, if both - primary and secondary diagnoses - are printed.
			*/ 
			if(! empty($primary) AND $circumference!=='secondary' AND $circumference!=='provisional'){
				if($circumference!=='primary'){
				$html.="<h3>primary diagnosis:</h3>";
				}
				$html.=$primary;
			}

			/*
			## Add all as secondary marked diagnoses to HTML buffer for printing them later. 
			## Add also a headline for secondary diagnosis, if both - primary and secondary diagnoses - are printed.
			*/ 
			if(! empty($secondary) AND $circumference!=='primary' AND $circumference!=='provisional'){
				if($circumference!=='secondary'){
				$html.="<h3>secondary diagnosis:</h3>";
				}
				$html.=$secondary;
			}

			/*
			## Add all as provisional marked diagnoses to HTML buffer for printing them later. 
			## Add also a headline for provisional diagnosis, if both - primary and secondary diagnoses - are printed.
			*/ 
			if(! empty($provisional) AND $circumference!=='primary' AND $circumference!=='secondary'){
				if($circumference!=='provisional'){
				$html.="<h3>provisional diagnosis:</h3>";
				}
				$html.=$provisional;
			}
			
			## If it is not empty and both - secondary and primary diagnoses - are printed, also print the consultant's remarks on the diagnosis.
			if($circumference=='both' AND ! empty ($this->remarks)){
				$html.="<h3>Remarks:</h3>".$this->remarks;
			}
			
			return $html;
		}

	}

?>
