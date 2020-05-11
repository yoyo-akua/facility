<?php
	class maternity{
		## A maternity object represents a client's pregnancy, which has the following parameters.
		private $maternity_ID;		## Pregnancy's ID.
		private $patient_ID;		## Client's ID.
		private $conception_date;	## Client's conception date.
		private $ITN;			## Status, whether the client received a insecticide treated net.
		private $parity;		## Client's parity and gravida.
		private $occupation;		## Client's occupation.
		private $serial_number;		## Is needed to identify the client. Is given by facility itself, continuous and unique.
		private $reg_number;		## Is needed to identify the client. It is possible, that the client has already a registration number,
						## for the current pregnancy, given by another facility. That's why it is not in every case continuous/unique.
	
		
		/*
		## This function is called, if a new maternity object is needed for further actions.
		## Saves the information of that maternity from database (identified by maternity ID) in that new maternity object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function maternity($maternity_ID){
			global $link;
			$query = "SELECT * FROM maternity WHERE maternity_ID = $maternity_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->patient_ID = $row->patient_ID;
				
				$this->conception_date = $row->conception_date;
				$this->parity = $row->parity;
				$this->ITN = $row->ITN;
				$this->occupation = $row->occupation;
				$this->serial_number = $row->serial_number;
				$this->reg_number = $row->reg_number;
			}
			$this->maternity_ID = $maternity_ID;
		}

		/*
		## Constructor of new pregnancy.
		## Is called, if a new maternity database entry is created.
		## The data of new pregnancy is saved in database for all its parameters.
		## Save this data also in a new created maternity object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_maternity($patient_ID,$conception_date,$parity,$occupation,$serial_number,$reg_number){
			global $link;
			$query = "INSERT INTO `maternity`(`patient_ID`,`conception_date`,`parity`,`occupation`,`serial_number`,`reg_number`) VALUES ('$patient_ID','$conception_date','$parity','$occupation','$serial_number','$reg_number')";
			mysqli_query($link,$query);
			
			$maternity_ID = mysqli_insert_id($link);
			$instance = new self($maternity_ID);
			return $instance;		
		}
	
		/*
		## Getter function.
		## Returns the ID of a client corresponding to that pregnancy, on which the function is called.
		*/
		public function getpatient_ID(){
			return $this->patient_ID;
		}

		/*
		## Getter function.
		## Returns the ID of that pregnancy, on which the function is called.
		*/
		public function getmaternity_ID(){
			return $this->maternity_ID;
		}

		/*
		## Getter function.
		## Returns the conception date of a client corresponding to that pregnancy, on which the function is called.
		*/
		public function getconception_date(){
			return $this->conception_date;
		}

		/*
		## Getter function.
		## Returns the status, whethers a client corresponding to that pregnancy, on which the function is called, received a insectiside treated net.
		*/
		public function getITN(){
			return $this->ITN;
		}

		/*
		## Getter function.
		## Returns the parity of a client corresponding to that pregnancy, on which the function is called.
		*/
		public function getparity(){
			return $this->parity;
		}

		/*
		## Getter function.
		## Returns the occupation of a client corresponding to that pregnancy, on which the function is called.
		*/
		public function getoccupation(){
			return $this->occupation;
		}

		/*
		## Getter function.
		## Returns the serial number of a client corresponding to that pregnancy, on which the function is called.
		*/			
		public function getSerial_number(){
			return $this->serial_number;
		}

		/*
		## Getter function.
		## Returns the registration number of a client corresponding to that pregnancy, on which the function is called.
		*/			
		public function getReg_number(){
			return $this->reg_number;
		}	

		/*
		## Setter function.
		## Updates in the database the status, whethers a client corresponding to that pregnancy, 
		## on which the function is called, received a insectiside treated net.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setITN($var){
			global $link;
			$query = "UPDATE maternity SET ITN='$var' WHERE maternity_ID = $this->maternity_ID";
			mysqli_query($link,$query);
			return $this->ITN = $var;
		}

		/*
		## Setter function.
		## Updates the conception date of a client corresponding to that pregnancy, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setconception_date($var){
			global $link;
			$query = "UPDATE maternity SET conception_date='$var' WHERE maternity_ID = $this->maternity_ID";
			mysqli_query($link,$query);
			return $this->conception_date = $var;	
		}

		/*
		## Setter function.
		## Updates the parity of a client corresponding to that pregnancy, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setParity($var){
			global $link;
			$query = "UPDATE maternity SET parity='$var' WHERE maternity_ID = $this->maternity_ID";
			mysqli_query($link,$query);
			return $this->parity = $var;
		}

		/*
		## Setter function.
		## Updates the occupation of a client corresponding to that pregnancy, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/	
		public function setOccupation($var){
			global $link;
			$query = "UPDATE maternity SET occupation='$var' WHERE maternity_ID = $this->maternity_ID";
			mysqli_query($link,$query);
			return $this->occupation = $var;
		}

		/*
		## Setter function.
		## Updates the serial number of a client corresponding to that pregnancy, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/	
		public function setSerial_number($var){
			global $link;
			$query = "UPDATE maternity SET serial__number='$var' WHERE maternity_ID = $this->maternity_ID";
			mysqli_query($link,$query);
			return $this->serial_number = $var;
		}

		/*
		## Setter function.
		## Updates the registration number of a client corresponding to that pregnancy, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setReg_number($var){
			global $link;
			$query = "UPDATE maternity SET reg_number='$var' WHERE maternity_ID = $this->maternity_ID";
			mysqli_query($link,$query);
			return $this->reg_number = $var;
		}	
		
		/*
		## This function buffers the HTML commands, which are used to print the client's pregnancy data later.
		## The HTML commands are buffered in variable $html, which is returned at the end of this function.
		*/
		public function display_maternity(){
			
			## Initialise client's data.
			$occupation=$this->occupation;
			$parity=$this->parity;
			$serial_number=$this->serial_number;
			$reg_number=$this->reg_number;
			
			## Initialise object of client, get her telephone number and height.
			$patient=new Patient($this->patient_ID);
			$telephone=$patient->getTelephone();
			$height=$patient->getHeight();

			## Calculate the estimated delivery day of the client.
			$EDD=date("d.m.Y",((strtotime($this->conception_date))+(40*7*24*3600)));
			
			## Save HTML commands to print the client's data later in HTML buffer.
			$html="
					<h4>Serial Number:</h4> 
					$serial_number<br>
						
					<h4>Registration Number:</h4> 
					$reg_number<br>
			
					<h4>Occupation:</h4> 
					$occupation<br>
			
					<h4>Telephone Number:</h4>
					$telephone<br>

					<h4>Parity:</h4>
					$parity<br>

					<h4>Height of Mother:</h4>
					$height cm<br>
						
					<h4>Estimated Delivery Date:</h4>
					$EDD<br>
			";
							
			if($this->ITN==1){
				$html.="ITN has been given<br>";
			}
			
			return $html;
		}
	}
?>
