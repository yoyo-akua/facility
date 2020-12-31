<?php
	class ANC{

		## Define all parameters an ANC object has. Each ANC object represents a client's ANC visit.
		private $ANC_ID;		## ID of client's ANC visit.
		private $maternity_ID;		## ID of corresponding client's pregnancy entry.
		private $FHt;			## Client's fundal height.
		private $fetal_heart;		## Pulse of client's fetus.
		private $SP;			## Number of Malaria prophylaxis given to client.
		private $TT;			## Number of Tetanus/Diphteria vaccine given to client.
		private $remarks;		## Remarks to client's ANC visit.
		private $visitnumber;		## Client's ANC visit number (the how many client's ANC visit is it?).

		/*
		## This function is called, if a new client's ANC visit object is needed for futher actions.
		## Saves the information of that client's ANC visit from database (identified by client's ANC visit ID) in that new client's ANC visit object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function ANC($ANC_ID){
			global $link;
			$query = "SELECT * FROM anc WHERE ANC_ID = $ANC_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->maternity_ID = $row->maternity_ID;
				$this->FHt = $row->FHt;
				$this->fetal_heart = $row->fetal_heart;
				$this->SP = $row->SP;
				$this->TT = $row->TT;
				$this->remarks = $row->remarks;
				$this->visitnumber = $row->visitnumber;
			}
			$this->ANC_ID = $ANC_ID;
		}

		/*
		## Constructor of new client's ANC visit.
		## Is called, if a new client's ANC visit is created in database.
		## The data of new client's ANC visit is saved in database for all its parameters.
		## Save this data also in a new created client's ANC visit object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_ANC($maternity_ID,$FHt,$fetal_heart,$SP,$TT,$remarks,$visitnumber){
			global $link;
			$query = "INSERT INTO `anc`(`maternity_ID`,`FHt`,`fetal_heart`,`SP`,`TT`,`remarks`,`visitnumber`) VALUES ('$maternity_ID','$FHt','$fetal_heart','$SP','$TT','$remarks','$visitnumber')";
			mysqli_query($link,$query);
		
			$ANC_ID = mysqli_insert_id($link);
			$instance = new self($ANC_ID);
			return $instance;		
		}

		/*
		## Getter function.
		## Returns the number of Malaria prophylaxis given to client at the time of that client's ANC visit, on which the function is called.
		*/
		public function getSP(){
			return $this->SP;
		}

		/*
		## Getter function.
		## Returns the number of Tetanus/Diphteria vaccine given to client at the time of that client's ANC visit, on which the function is called.
		*/
		public function getTT(){
			return $this->TT;
		}

		/*
		## Getter function.
		## Returns the Client's fundal height of that client's ANC visit, on which the function is called.
		*/
		public function getFHt(){
			return $this->FHt;
		}
		
		/*
		## Getter function.
		## Returns the Pulse of client's fetus at the time of that client's ANC visit, on which the function is called.
		*/	
		public function getFetal_heart(){
			return $this->fetal_heart;
		}

		/*
		## Getter function.
		## Returns the ID of client's ANC visit, on which the function is called.
		*/
		public function getANC_ID(){
			return $this->ANC_ID;
		}

		/*
		## Getter function.
		## Returns the visitnumber of client's ANC visit, on which the function is called.
		*/
		public function getvisitnumber(){
			return $this->visitnumber;
		}

		/*
		## Getter function.
		## Returns remarks to that client's ANC visit, on which the function is called.
		*/
		public function getremarks(){
			return $this->remarks;
		}

		/*
		## Getter function.
		## Returns the ID of the corresponding pregnancy entry to client's ANC visit, on which the function is called.
		*/
		public function getmaternity_ID(){
			return $this->maternity_ID;
		}

		/*
		## Setter function.
		## Updates the client's fundal height of that client's ANC visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setFHt($var){
			global $link;
			$query = "UPDATE anc SET FHt='$var' WHERE ANC_ID = $this->ANC_ID";
			mysqli_query($link,$query);
			return $this->FHt = $var;
		}

		/*
		## Setter function.
		## Updates the Pulse of client's fetus at the time of that client's ANC visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setFetalHeart($var){
			global $link;
			$query = "UPDATE anc SET fetal_heart='$var' WHERE ANC_ID = $this->ANC_ID";
			mysqli_query($link,$query);
			return $this->fetal_heart = $var;
		}	

		/*
		## Setter function.
		## Updates the number of Malaria prophylaxis given to client at the time of that client's ANC visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setSP($var){
			global $link;
			$query = "UPDATE anc SET SP='$var' WHERE ANC_ID = $this->ANC_ID";
			mysqli_query($link,$query);
			return $this->SP = $var;
		}	

		/*
		## Setter function.
		## Updates the number of Tetanus/Diphteria vaccine given to client at the time of that client's ANC visit, 
		## on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setTT($var){
			global $link;
			$query = "UPDATE anc SET TT='$var' WHERE anc_ID = $this->ANC_ID";
			mysqli_query($link,$query);
			return $this->TT = $var;
		}

		/*
		## Setter function.
		## Updates remarks to that client's ANC visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setRemarks($var){
			global $link;
			$query = "UPDATE anc SET remarks='$var' WHERE anc_ID = $this->ANC_ID";
			mysqli_query($link,$query);
			return $this->remarks = $var;
		}

		/*
		## Setter function.
		## Updates the visitnumber of client's ANC visit, on which the function is called.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setVisitnumber($var){
			global $link;
			$query = "UPDATE anc SET visitnumber='$var' WHERE anc_ID = $this->ANC_ID";
			mysqli_query($link,$query);
			return $this->visitnumber = $var;
		}	

		/*
		## The function is responsible for displaying a certain client's ANC visit and its information.
		## For that, the necessary HTML commands are added to a HTML buffer, to print the information later.
		## The sent parameters have the following meaning:
		##		- $protocol_ID contains the ID of that client's visit, which is corresponding to the current client's ANC visit. 
		##		  This is necessary to print the visit date of the current client's ANC visit. 
		##		- $date contains the information, whether the ANC visit's date is going to be displayed or not. 
		## This function returns the HTML buffer $html.
		*/	
		public function display_ANC($protocol_ID,$date){
			
			/*
			## Initialise variables, which are needed within this function.
			## Variable $days, $weeks and $anddays are used to display the gestational age.
			*/
			$visitnumber=$this->visitnumber;		
			$remarks=$this->remarks;
			$SP=$this->SP;
			$TT=$this->TT;
			$FHt=$this->FHt;
			$fetal_heart=$this->fetal_heart;
			
			$protocol=new Protocol($protocol_ID);
			$visit_ID=$protocol->getVisit_ID();
			$visitdate=(new Visit($visit_ID))->getCheckin_time();
			
			$maternity=new Maternity($this->maternity_ID);
			
			$days=(strtotime($visitdate)-(strtotime($maternity->getconception_date())))/(24*3600);
			$weeks=floor($days/7);
			$anddays=floor($days-($weeks*7));
			
			/*
			## Print client's ANC visit information, if they are known.
			## Add date to HTML buffer, if $date is set.
			*/
			$html="<h3>$visitnumber. visit</h3>";
			
			
			if($date=='date on'){
				$visitdate=date("d.m.Y",strtotime($visitdate));
				$html.="<h4>$visitdate</h4><br>";
			}
			
			$html.="<h4>Gestational Age:</h4> $weeks weeks and $anddays days<br>";
			
			if($SP!=='' AND $SP!=0){
				$html.="<h4>Sulfadoxine Pyrimethamine:</h4>
				".$SP.". dose<br>";
			}
			
			if($TT!=='' AND $TT!=0){
				$html.="<h4>Tetanus Diphteria vaccine:</h4>
				".$TT.". dose<br>";
			}
			
			if($FHt!=='' AND $FHt!=0){
				$html.="<h4>Fundal Height:</h4>
				$FHt cm<br>";
			}
			if($fetal_heart!==''){
				$html.="<h4>Fetal Heartrate:</h4>
				$fetal_heart cm<br>";
			}
			
			if($this->remarks!==''){
				$html.="<h4>Remarks:</h4>
				$remarks<br>";
			}
			return $html;
		}
	}

?>
