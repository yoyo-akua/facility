<?php
	class protocol{
		## Define all parameters a protocol entry has. Each protocol entry represents a specific action during a patient's visit.
		private $protocol_ID;				## Database ID of this protocol entry. 
		private $visit_ID;					## Database ID of this visit.
		private $timestamp;					  ## Time of this protocol entry.
		private $staff_ID;					 ## ID of clinic staff, who perform the diagose.
		private $event;						## Verbal description of event linked to the protocol entry.
		private $ANC_ID;					## ID of Patient's ANC at the visit's date.
		private $surgery;					  ## Contains the name of surgery, which where performed on patient at visit's date.	
		private $PNC;						   ## Defines, if the patient did PNC at the patient at the visit's date.		
		private $charge;					   ## Defines how much the patient was charged either in lab or for a minor OP.
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
				$this->visit_ID = $row->visit_ID;
				$this->timestamp = $row->timestamp;
				$this->staff_ID = $row->staff_ID;
				$this->event = $row->event;
				$this->ANC_ID = $row->ANC_ID;
				$this->surgery = $row->surgery;
				$this->PNC = $row->PNC;
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
		## Returns the patient's ANC ID of that visit, on which the function is called.
		## This ID is used to connect the patient's ANC data with the patient's visit in the facility.
		*/
		public function getANC_ID(){
			return $this->ANC_ID;
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
