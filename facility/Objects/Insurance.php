<?php
	class Insurance{
		## Define all parameters an insurance entry has. Each insurance entry is linked to a patient's visit.
		private $visit_ID;					## Database ID of this visit.
		private $CCC;						  ## Patient's CC Code at the visit's date.
		private $expired;					  ## Defines, whether the patient's insurance is expired at the visit's date.
		private $entered;					  ## Defines, whether the status of patient's visit was entered in "NHIS Claim It".	

		/*
		## This function is called, if a new insurance object is needed for further actions.
		## Saves the information of that insurance from database (identified by visit ID) in that new insurance object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Insurance($visit_ID){
			global $link;
			$query = "SELECT * FROM insurance WHERE visit_ID = $visit_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->CCC = $row->CCC;
				$this->expired = $row->expired;
				$this->entered = $row->entered;
			}
			$this->visit_ID = $visit_ID;
		}

		/*
		## Constructor of new insurance entry.
		## Is called, if a new insurance database entry is created.
		## The data of new insurance entry is saved in database for some of its parameters (the others are having default values).
		## Save this data also in a new created insurance object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Insurance($visit_ID,$CCC,$expired){
			global $link;
			$query = "INSERT INTO `insurance`(`visit_ID`,`CCC`,`expired`) VALUES ('$visit_ID','$CCC','$expired')";
            
            mysqli_query($link,$query);            
			$instance = new self($visit_ID);
			return $instance;		
        }
        
		/*
		## Getter function.
		## Returns the ID of that insurance entry, on which the function is called.
		*/
		public function getVisit_ID(){
			return $this->visit_ID;
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
			return $this->expired;
		}

		/*
		## Getter function.
		## Returns the entered status in "NHIS Claim It" of that visit, on which the function is called.
		*/
		public function getEntered(){
			return $this->entered;
		}


		/*
		## Setter function.
		## Updates the patient's CCC number of that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setCCC($var){
			global $link;
			$query = "UPDATE insurance SET CCC='$var' WHERE visit_ID = $this->visit_ID";
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
			$query = "UPDATE insurance SET expired='$var' WHERE visit_ID = $this->visit_ID";
			mysqli_query($link,$query);
			return $this->expired = $var;
		}

		/*
		## Setter function.
		## Updates the documentation status in Account's NHIS system of that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setEntered($var){
			global $link;
			$query = "UPDATE insurance SET entered='$var' WHERE visit_ID = $this->visit_ID";
			mysqli_query($link,$query);
			return $this->entered = $var;
        }
        
        /*
        ## Getter function.
        ## Inquires whether a client has insurance at a certain visit (defined by visit ID) and returns that information;.
        */
        public function checkInsurance($visit_ID){
            global $link;
			$query = "SELECT * FROM insurance WHERE visit_ID='$visit_ID'";
            $result=mysqli_query($link,$query);
            $object=mysqli_fetch_object($result);
            if(! empty($object)){
                return true;
            }else{
                return false;
            }
            
        }

	}

?>
