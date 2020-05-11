<?php 	
	class Non_Drugs{
		
		## Define all parameters a non drug has.
		private $Non_Drug_ID;
		private $Non_Drugname;
		private $Receiving_Department;
		
		
		/*
		## This function is called, if a new non drug object is needed for further actions.
		## Saves the information of that non drug from database (identified by non drug ID) in that new non drug object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Non_Drugs($Non_Drug_ID){
			global $link;
			$query = "SELECT * FROM non_drugs WHERE Non_Drug_ID = $Non_Drug_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
		    		$this->Non_Drugname = $row->Non_Drugname;
				$this->Receiving_Department = $row->Receiving_Department;
			}	
			$this->Non_Drug_ID = $Non_Drug_ID;
	    	}

		/*
		## Constructor of new non drug.
		## Is called, if a new non drug database entry is created.
		## The data of new non drug is saved in database for all its parameters.
		## Save this data also in a new created non drug object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Non_Drugs($Non_Drugname,$Receiving_Department){
			global $link;
			$query = "INSERT INTO `non_drugs`(`Non_Drugname`,`Receiving_Department`) VALUES ('$Non_Drugname','$Receiving_Department')";
			mysqli_query($link,$query);
		
			$Non_Drug_ID = mysqli_insert_id($link);
			$instance = new self($Non_Drug_ID);
			return $instance;
	    	}

		/*
		## Getter function.
		## Returns the ID of non drug, on which the function is called.
		*/
		public function getNon_Drug_ID(){
			return $this->Non_Drug_ID;
		}

		/*
		## Getter function.
		## Returns the name of non drug, on which the function is called.
		*/
		public function getNon_Drugname(){
			return $this->Non_Drugname;
		}

		/*
		## Getter function.
		## Returns the department's name, which is receiving that non drug, on which the function is called.
		*/
		public function getReceiving_Department(){
			return $this->Receiving_Department;
		}
	}
?>
