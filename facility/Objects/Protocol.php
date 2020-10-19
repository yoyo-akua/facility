<?php
	class protocol{
		## Define all parameters a protocol entry has. Each protocol entry represents a patient's visit.
		private $protocol_ID;				## Database ID of this visit. 
		private $patient_ID;				 ## Database ID of the patient, who was visiting.	
		private $VisitDate;					  ## Date of patient's visit of this protocol entry.
		private $new_p;						  ## Defines, whether the patient was visiting the facility for the first time within this year or not.
		private $completed;					## Defines, whether the patient's treatment is completed or not.
		private $attendant;					 ## Name of clinic staff, who perform the diagose.
		private $referral;						## Facility's name, to which a patient is referred.
		private $ANC_ID;					## ID of Patient's ANC at the visit's date.
		private $pregnant;					 ## Defines, whether the patient is pregnant at the visit's date.
		private $surgery;					  ## Contains the name of surgery, which where performed on patient at visit's date.	
		private $protect;					   ## Defines, whether the patient's diagnosis is completed or not.
		private $CCC;						  ## Patient's CC Code at the visit's date.
		private $Expired;					  ## Defines, whether the patient's insurance is expired at the visit's date.
		private $PNC;						   ## Defines, if the patient did PNC at the patient at the visit's date.		
		private $entered;					  ## Defines, whether the status of patient's visit was entered in "NHIS Claim It".	
		private $onlylab;					   ## Defines, whether the patient visits only for lab, without any consulting.
		private $labdone;					  ## Defines, whether the lab tests have been completed for the particular visit.
		private $charge;					   ## Defines how much the patient was charged either in lab or for a minor OP.
		private $lab_number;			   ## Defines patient's lab number which is used by user to label samples (not the identifier in database).
		private $remarks;					  ## Contains remarks of the consultant on the patient's diagnosis.
		private $delivery;						## Saves the patient's maternity ID, if she delivered.

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
				$this->patient_ID = $row->patient_ID;
				$this->VisitDate = $row->VisitDate;
				$this->new_p = $row->new_p;
				$this->completed = $row->completed;
				$this->attendant = $row->attendant;
				$this->referral = $row->referral;
				$this->ANC_ID = $row->ANC_ID;
				$this->pregnant = $row->pregnant;
				$this->surgery = $row->surgery;
				$this->protect = $row->protect;
				$this->CCC = $row->CCC;
				$this->Expired = $row->Expired;
				$this->PNC = $row->PNC;
				$this->entered = $row->entered;
				$this->onlylab = $row->onlylab;
				$this->labdone = $row->labdone;
				$this->lab_number = $row->lab_number;
				$this->charge = $row->charge;
				$this->remarks = $row->remarks;
				$this->delivery = $row->delivery;
			}
			$this->protocol_ID = $protocol_ID;
		}

		/*
		## Constructor of new protocol entry.
		## Is called, if a new protocol database entry is created.
		## The data of new protocol is saved in database for some of its parameters (the others are having default values).
		## Save this data also in a new created protocol object and return this object for further actions.
		## Create a new entry in the seen table using the protocol_ID.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Protocol($patient_ID,$new_p,$CCC,$Expired){
			global $link;
			$query = "INSERT INTO `protocol`(`patient_ID`,`new_p`,`CCC`,`Expired`) VALUES ('$patient_ID','$new_p','$CCC','$Expired')";
			mysqli_query($link,$query);

			$protocol_ID = mysqli_insert_id($link);

			Seen::new_Seen($protocol_ID);

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
		## Returns the visit date of that protocol entry, on which the function is called.
		*/
		public function getVisitDate(){
			return $this->VisitDate;
		}

		/*
		## Getter function.
		## Returns the status, whether the patient was visiting the facility first time this year or more often.
		*/
		public function getnew_p(){
			return $this->new_p;
		}
		
		/*
		## Getter function.
		## Returns the treatment's status (completed or not) of the visit, on which the function is called.
		*/
		public function getcompleted(){
			return $this->completed;
		}

		/*
		## Getter function.
		## Returns the facility personal's name, who diagnosed the patient at that visit, on which the function is called.
		*/	
		public function getAttendant(){
			return $this->attendant;
		}		

		/*
		## Getter function.
		## Returns the facility's name to which a patient was referred at that visit, on which the function is called.
		*/
		public function getreferral(){
			return $this->referral;
		}		


		/*
		## Getter function.
		## Returns the patient's ANC ID of that visit, on which the function is called.
		## This ID is used to connect the patient's ANC data with the patient's visit in the facility.
		*/
		public function getANC_ID(){
			return $this->ANC_ID;
		}

		/*
		## Getter function.
		## Returns, whether the patient was pregnant at that visit, on which the function is called.
		*/
		public function getpregnant(){
			return $this->pregnant;
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
		## Returns the patient's protection status (is its diagnosis protected or not) of that visit, on which the function is called.
		*/
		public function getprotect(){
			return $this->protect;
		}	

		/*
		## Getter function.
		## Returns the patient's CC Code of that visit, on which the function is called.
		*/
		public function getCCC(){
			return $this->CCC;
		}

		/*
		## Getter function.
		## Returns whether the patient's insurance was expired at that visit, on which the function is called.
		*/
		public function getExpired(){
			return $this->Expired;
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
		## Returns the entered status in "NHIS Claim It" of that visit, on which the function is called.
		*/
		public function getEntered(){
			return $this->entered;
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
		## Getter function.
		## Returns, whether the patient's lab tests have been completed for that particular visit.
		*/
		public function getLabdone(){
			return $this->labdone;
		}		
		
		/*
		## Getter function.
		## Returns, whether the patient was charged either in lab or for a minor OP
		*/
		public function getCharge(){
			return $this->charge;
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
		## Getter function.
		## Returns any remarks that the consultant made on the patient's diagnosis.
		*/
		public function getRemarks(){
			return $this->remarks;
		}		
		
		/*
		## Getter function.
		## Returns the maternity ID of a client, that has come for delivery.
		*/
		public function getDelivery(){
			return $this->delivery;
		}	
		
		/*
		## Getter function.
		## Returns the patient ID of the client.
		*/
		public function getPatient_ID(){
			return $this->patient_ID;
		}

		/*
		## Setter function.
		## Updates the visit date of that protocol entry, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setVisitDate($var){
			global $link;
			$query = "UPDATE protocol SET VisitDate='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->VisitDate = $var;
		}

		/*
		## Setter function.
		## Updates the status, whether the patient was visiting the facility first time this year or more often, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setNew_p($var){
			global $link;
			$query = "UPDATE protocol SET new_p='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->new_p = $var;
		}

		/*
		## Setter function.
		## Updates the treatment's status (completed or not) of the visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setcompleted($var){
			global $link;
			$query = "UPDATE protocol SET completed='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->completed = $var;
		}

		/*
		## Setter function.
		## Updates the facility personal's name, who diagnosed the patient at that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setAttendant($var){
			global $link;
			$query = "UPDATE protocol SET attendant='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->attendant = $var;
		}

		/*
		## Setter function.
		## Updates the facility's name to which a patient was referred at that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setreferral($var){
			global $link;
			$query = "UPDATE protocol SET referral='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->referral = $var;
		}

		/*
		## Setter function.
		## Updates the patient's ANC ID of that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/			
		public function setANC_ID($var){
			global $link;
			$query = "UPDATE protocol SET ANC_ID='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->ANC_ID = $var;
		}

		/*
		## Setter function.
		## Updates the status, whether the patient was pregnant at that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setpregnant($var){
			global $link;
			$query = "UPDATE protocol SET pregnant='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->pregnant = $var;
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
		## Updates the patient's protection status (is its diagnosis protected or not) of that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setprotect($var){
			global $link;
			$query = "UPDATE protocol SET protect='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->protect = $var;
		}

		/*
		## Setter function.
		## Updates the patient's CCC number of that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setCCC($var){
			global $link;
			$query = "UPDATE protocol SET CCC='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->CCC = $var;
		}

		/*
		## Setter function.
		## Updates the status, whether the patient's insurance was expired at that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setExpired($var){
			global $link;
			$query = "UPDATE protocol SET Expired='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->Expired = $var;
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
		## Updates the documentation status in Account's NHIS system of that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setEntered($var){
			global $link;
			$query = "UPDATE protocol SET entered='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->entered = $var;
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
			$query = "UPDATE protocol SET onlylab='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->onlylab = $var;
		}

		/*
		## Setter function.
		## Updates the status, whether the patient's lab tests for that particular visit were completed in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setLabdone($var){
			global $link;
			$query = "UPDATE protocol SET labdone='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->labdone = $var;
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
		## Setter function.
		## Updates patient's lab number which is used by user to label samples (not the identifier in database).
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setLab_number($var){
			global $link;
			$query = "UPDATE protocol SET lab_number='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->lab_number = $var;
		}

		/*
		## Setter function.
		## Updates remarks of the consultant that were made on the patient's diagnosis.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setRemarks($var){
			global $link;
			$query = "UPDATE protocol SET remarks='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->remarks = $var;
		}		
		
		/*
		## Setter function.
		## Updates the client's maternity ID, if she has come for delivery.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setDelivery($var){
			global $link;
			$query = "UPDATE protocol SET delivery='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->delivery = $var;
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
			## Variable $html is used as buffer of HTML commands to print the list of diseases.
			## Variable $primary is used as buffer of the disease, which is marked as primary diagnose.
			## Variable $secondary is used as buffer of these diseases, which are marked as secondary diagnoses.
			*/
			$protocol_ID=$this->protocol_ID;
			$Diagnosis_IDs=Diagnosis_IDs::getDiagnosis_IDs($protocol_ID);

			$html='';
			$primary='';
			$secondary='';
			$provisional='';

			## Check, if the patient needs to reattending. If so, print a correspondent notice. 
			if(in_array(0,Diagnosis_IDs::getImportances($protocol_ID))){
				$html.='Patient is <h4>reattending</h4><br>';
			}


			/*
			## Get data from database.
			## Get all known diseases and order them by their classes.
			*/
			global $link;
			$query = "SELECT Diagnosis_ID FROM diagnoses ORDER BY DiagnosisClass";
			$result = mysqli_query($link,$query);

			/*
			## This loop is run for each disease, which was gotten from database before.
			## Check, if a disease is marked as primary or as secondary diagnose,
			## and add a notice in the correspondent buffer ($primary or $secondary).
			*/
			foreach($Diagnosis_IDs AS $Diagnosis_ID){
				$Diagnosis = new Diagnoses($Diagnosis_ID);
				$importance=Diagnosis_IDs::getImportance($protocol_ID,$Diagnosis_ID);
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

	}

?>
