<?php

	class Complaints{

		## Define all complaints.
		private $protocol_ID;	## ID of the patient's visit.
		private $Coughing;	## Boolean indicating whether the patient is complaining about Coughing.
		private $Vomitting;	## Boolean indicating whether the patient is complaining about Vomitting.
		private $Fever;	## Boolean indicating whether the patient is complaining about Fever.
		private $Diarrhoea;	## Boolean indicating whether the patient is complaining about Diarrhoea.
		private $Others;	## Contains any other possible complains as plain text.
		
		/*
		## This function is called, if a new complaints object is needed for futher actions.
		## Saves the information of that entry from database (identified by protocol ID) in that new complaints object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Complaints($protocol_ID){
			global $link;
			$query = "SELECT * FROM complaints WHERE protocol_ID = $protocol_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->Coughing = $row->Coughing;
				$this->Vomitting = $row->Vomitting;
				$this->Fever = $row->Fever;
				$this->Diarrhoea = $row->Diarrhoea;
				$this->Others = $row->Others;
			}
			$this->protocol_ID = $protocol_ID;
		}
		
		/*
		## Constructor of new complaints entry.
		## Is called, if a complaints database entry is created.
		## The data of new complaints entry is saved in database for all its parameters.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Complaints($protocol_ID,$Coughing,$Vomitting,$Fever,$Diarrhoea,$Others){
			global $link;
			$query = "INSERT INTO `complaints`(`protocol_ID`,`Coughing`,`Vomitting`,`Fever`,`Diarrhoea`,`Others`) VALUES ('$protocol_ID','$Coughing','$Vomitting','$Fever','$Diarrhoea','$Others')";
			mysqli_query($link,$query);
			
			$protocol_ID = mysqli_insert_id($link);
			$instance = new self($protocol_ID);
			return $instance;	
		}
		
		
		/*
		## Getter function.
		## Returns whether the patient complains about coughing on that visit, on which the function is called.
		*/
		public function getCoughing(){
			return $this->Coughing;
		}

		/*
		## Getter function.
		## Returns whether the patient complains about vomitting on that visit, on which the function is called.
		*/
		public function getVomitting(){
			return $this->Vomitting;
		}

		/*
		## Getter function.
		## Returns whether the patient complains about fever on that visit, on which the function is called.
		*/
		public function getFever(){
			return $this->Fever;
		}

		/*
		## Getter function.
		## Returns whether the patient complains about diarrhoea on that visit, on which the function is called.
		*/
		public function getDiarrhoea(){
			return $this->Diarrhoea;
		}

		/*
		## Getter function.
		## Returns any further complaints on that visit, on which the function is called.
		*/
		public function getOthers(){
			return $this->Others;
		}	

		/*
		## Setter function.
		## Updates whether the patient was complaining about coughing on that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/			
		public function setCoughing($var){
			global $link;
			$query = "UPDATE complaints SET Coughing='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->Coughing = $var;
		}

		/*
		## Setter function.
		## Updates whether the patient was complaining about vomitting on that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/				
		public function setVomitting($var){
			global $link;
			$query = "UPDATE complaints SET Vomitting='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->Vomitting = $var;
		}

		/*
		## Setter function.
		## Updates whether the patient was complaining about fever on that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setFever($var){
			global $link;
			$query = "UPDATE complaints SET Fever='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->Fever = $var;
		}

		/*
		## Setter function.
		## Updates whether the patient was complaining about diarrhoea on that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/	
		public function setDiarrhoea($var){
			global $link;
			$query = "UPDATE complaints SET Diarrhoea='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->Diarrhoea = $var;
		}

		/*
		## Setter function.
		## Updates any further complaints on that visit, on which the function is called, in database.
		## Retugrns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/	
		public function setOthers($var){
			global $link;
			$query = "UPDATE complaints SET Others='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->Others = $var;
		}
		

		/*
		## This function is used to inquire whether a complaints entry exists for a patient on a particular visit or not. 
		## It checks for that information in the database and returns a boolean. 
		*/
		public function complaints_Boolean($protocol_ID){
			global $link;
			$query = "SELECT * FROM complaints WHERE protocol_ID='$protocol_ID'";
			$object=mysqli_fetch_object(mysqli_query($link,$query));
			if(! empty($object)){
				$boolean=true;
			}else{
				$boolean=false;
			}
			return $boolean;
		}

		/*
		## This function is used to display the client's complaints. 
		## It checks each category and in case any complaints have been stated adds it to the buffer $html.
		## $html is returned. 
		*/
		public function display_Complaints($protocol_ID){
			$complaints=new Complaints($protocol_ID);
			$html='';
			if(! empty($complaints->getCoughing())){
				$html.="- Coughing<br>";
			}
			if(! empty($complaints->getVomitting())){
				$html.="- Vomitting<br>";
			}
			if(! empty($complaints->getFever())){
				$html.="- Fever<br>";
			}
			if(! empty($complaints->getDiarrhoea())){
				$html.="- Diarrhoea<br>";
			}
			if(! empty($complaints->getOthers())){
				$html.="- ".$complaints->getOthers();
			}
			
			return $html;
		}
    }
?>