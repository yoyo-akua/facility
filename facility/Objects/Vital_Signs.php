<?php

	class Vital_Signs{

		## Define all vital signs.
		private $protocol_ID;	## ID of the patient's visit.
		private $BP;	## The patient's blood pressure.
		private $weight;	## The patient's weight.
		private $pulse;	## The patient's pulse.
		private $temperature;	## The patient's temperature.
		private $MUAC;	## The patient's Mid-upper arm circumference.
		private $height; ## The patient's body height.
		private $infantometer; ## Boolean indicating if an infantometer was used to measure the patient's height
		
		/*
		## This function is called, if a new protocol object is needed for futher actions.
		## Saves the information of that protocol from database (identified by protocol ID) in that new protocol object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Vital_Signs($protocol_ID){
			global $link;
			$query = "SELECT * FROM vital_signs WHERE protocol_ID = $protocol_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->BP = $row->BP;
				$this->weight = $row->weight;
				$this->pulse = $row->pulse;
				$this->temperature = $row->temperature;
				$this->MUAC = $row->MUAC;
				$this->height = $row->height;
				$this->infantometer = $row->infantometer;
			}
			$this->protocol_ID = $protocol_ID;
		}
		
		/*
		## Constructor of new vital signs entry.
		## Is called, if a new vital signs database entry is created.
		## The data of new vital signs entry is saved in database for all its parameters.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Vital_Signs($protocol_ID,$BP,$weight,$pulse,$temperature,$MUAC){
			global $link;
			$query = "INSERT INTO `vital_signs`(`protocol_ID`,`BP`,`weight`,`pulse`,`temperature`,`MUAC`) VALUES ('$protocol_ID','$BP','$weight','$pulse','$temperature','$MUAC')";
			mysqli_query($link,$query);
			
			$protocol_ID = mysqli_insert_id($link);
			$instance = new self($protocol_ID);
			return $instance;	
		}
		
		
		/*
		## Getter function.
		## Returns the patient's blood pressure of that visit, on which the function is called.
		*/
		public function getBP(){
			return $this->BP;
		}

		/*
		## Getter function.
		## Returns the patient's weight on that visit, of which the function is called.
		*/
		public function getweight(){
			return $this->weight;
		}

		/*
		## Getter function.
		## Returns the patient's pulse on that visit, of which the function is called.
		*/
		public function getpulse(){
			return $this->pulse;
		}

		/*
		## Getter function.
		## Returns the patient's temperature of that visit, on which the function is called.
		*/
		public function gettemperature(){
			return $this->temperature;
		}

		/*
		## Getter function.
		## Returns the patient's MUAC of that visit, on which the function is called.
		*/
		public function getMUAC(){
			return $this->MUAC;
		}	

		/*
		## Getter function.
		## Returns the patient's body height at that visit, on which the function is called.
		*/
		public function getHeight(){
			return $this->height;
		}	

		/*
		## Getter function.
		## Returns the boolean indicating whether the patient's height was measured with an infantometer or not.
		*/
		public function getInfantometer(){
			return $this->infantometer;
		}	

		/*
		## Setter function.
		## Updates the patient's blood pressure of that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/			
		public function setBP($var){
			global $link;
			$query = "UPDATE vital_signs SET BP='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->BP = $var;
		}

		/*
		## Setter function.
		## Updates the patient's weight on that visit, of which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/				
		public function setweight($var){
			global $link;
			$query = "UPDATE vital_signs SET weight='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->weight = $var;
		}

		/*
		## Setter function.
		## Updates the patient's pulse on that visit, of which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/		
		public function setpulse($var){
			global $link;
			$query = "UPDATE vital_signs SET pulse='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->pulse = $var;
		}

		/*
		## Setter function.
		## Updates the patient's temperature of that visit, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/	
		public function settemperature($var){
			global $link;
			$query = "UPDATE vital_signs SET temperature='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->temperature = $var;
		}

		/*
		## Setter function.
		## Updates the patient's MUAC of that visit, on which the function is called, in database.
		## Retugrns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/	
		public function setMUAC($var){
			global $link;
			$query = "UPDATE vital_signs SET MUAC='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->MUAC = $var;
		}

		/*
		## Setter function.
		## Updates the patient's height on that visit, of which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/				
		public function setHeight($var){
			global $link;
			$query = "UPDATE vital_signs SET height='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->height = $var;
		}

		/*
		## Setter function.
		## Updates the information whether the height was measured with an infantometer, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/				
		public function setInfantometer($var){
			global $link;
			$query = "UPDATE vital_signs SET infantometer='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->infantometer = $var;
		}

		/*
		## If known, display the patient's admission information.
		## Variable $html is used as buffer of HTML commands to print the patient's admission information later.
		## Add all known patient's admission information and their unit to that HTML buffer.
		*/
		public function display_admission_data(){
			$html='';
			global $thispage;
			
			if(! empty($this->BP)){
				if(!strstr($thispage,'pdf')){
				
					$BP_last=Vital_Signs::last_BPs($this->protocol_ID);
					if(!empty($BP_last)){
						$html.="<h4>Blood Pressure:</h4>
							<div class='tooltip' style='line-height:normal'>
								".$this->BP." mmHg
									<span class='tooltiptext' style='text-align:left'>
										$BP_last
									</span>
							</div><br>";
					}else{
						$html.="<h4>Blood Pressure:</h4> ".$this->BP." mmHg<br>";
					}
				}else{
					$html.="<h4>Blood Pressure:</h4> ".$this->BP." mmHg<br>";
				}
			}
			if(! empty($this->pulse)){
				$html.="<h4>Pulse:</h4> ".$this->pulse." bpm<br>";
			}
			if($this->weight!=0.0){
				$html.="<h4>Weight:</h4> ".$this->weight." kg<br>";
			}
			if($this->temperature!=0.0){
				$html.="<h4>Temperature:</h4> ".$this->temperature.' °C<br>';
			}
			if($this->MUAC!=0.0){
				$html.="<h4>MUAC:</h4> ".$this->MUAC." cm<br>";
			}	
			return '<br>'.$html.'<br>';
		}

		/*
		## Setter function.
		## Updates the patient's vital signs of that visit, on which the function is called, in database by calling each setter function.
		## Vital signs contain information about blood pressure, pulse, weight, temperature and MUAC.
		*/	
		public function setVital_signs($variable){
			if($variable=='post'){
				if(! empty($_POST['BP'])){
					$this->setBP($_POST['BP']);
				}
				if(! empty($_POST['pulse'])){
					$this->setpulse($_POST['pulse']);
				}
				if(! empty($_POST['weight'])){
					$this->setweight($_POST['weight']);
				}
				if(! empty($_POST['temperature'])){
					$this->settemperature($_POST['temperature']);
				}
				if(! empty($_POST['MUAC'])){
					$this->setMUAC($_POST['MUAC']);	
				}
			}else if($variable=='get'){
				if(! empty($_GET['BP'])){
					$this->setBP($_GET['BP']);
				}
				if(! empty($_GET['pulse'])){
					$this->setpulse($_GET['pulse']);
				}
				if(! empty($_GET['weight'])){
					$this->setweight($_GET['weight']);
				}
				if(! empty($_GET['temperature'])){
					$this->settemperature($_GET['temperature']);
				}
				if(! empty($_GET['MUAC'])){
					$this->setMUAC($_GET['MUAC']);	
				}	
			}
		}
		
		/*
		## This function is used to determine whether a patient's vital signs have already been entered.
		## The sent parameter $protocol_ID is used as a link to the patient's visit's entry.
		*/
		public function already_set($protocol_ID){
			global $link;
			$query="SELECT * FROM vital_signs WHERE protocol_ID=$protocol_ID";
			$result=mysqli_query($link,$query);
			
			if(mysqli_num_rows($result)==0){
				$set=false;
			}else{
				$set=true;
			}
			
			return $set;
		}
		/*	
		## This function is used to inquire whether the patient has come to the facility before, 
		## if so initialise variable $BP_last with the BP of the 5 previous visits.
		## The sent parameters $protocol_ID and $patient_ID are used to link to the patient and his visit's data.
		## Return $BP_last.
		*/
		public function last_BPs($protocol_ID){
			global $link;
			global $today;
			$querylast="SELECT * FROM protocol,vital_signs WHERE patient_ID=(SELECT patient_ID FROM protocol WHERE protocol_ID=$protocol_ID) AND vital_signs.protocol_ID=protocol.protocol_ID AND protocol.protocol_ID!=$protocol_ID AND VisitDate<='$today' ORDER BY VisitDate DESC LIMIT 0,5";
			$resultlast=mysqli_query($link,$querylast);
			if(mysqli_num_rows($resultlast)!==0){
				$BP_last="last visits' BPs <br>";
				while($row_last=mysqli_fetch_object($resultlast)){
					$date=date("d/m/y",strtotime($row_last->VisitDate));
					$BP_last.=$date.": ".$row_last->BP."<br>";
				}
			}else{
				$BP_last='';
			}
			return $BP_last;
		}

		public function nutrition_visit($protocol_ID,$age){
			$vitals=new self($protocol_ID);
			$nutrition=new Nutrition($protocol_ID);

			echo"
				<details open>
					<summary>
						<h2>Nutrition Treatment</h2>
					</summary>";
			if(empty($_GET['show']) AND $_GET["nutrition"]=='enter'){
				echo"
					<h3>Measurements</h3>
					<form action='patient_visit.php' method='get'>
					<table>
						<tr>
							<th style='border-left:none'>
								Height
							</th>";
							if($age<6){
								echo "<th>Infantometer?</th>";
							}
							echo"
							<th>
								Weight
							</th>
							<th>
								<div class='tooltip'>
									BMI
									<span class='tooltiptext' id='BMIflag' style='display:none'>
									</span>
								</div>
							</th>";
							if($age<6){
								echo "<th>MUAC</th>";
							}
							echo"
						</tr>
						<tr>
							<td style='border-left:none'>
								<input type='number' name='height' min='0' step='0.1' oninput='PrefillBMI()' value='$vitals->height'> cm
							</td>";
							if($age<6){
								echo "<td><input type='checkbox' name='infantometer'";
								if($vitals->infantometer=='1'){
									echo "checked='checked'";
								}
								echo"></td>";
							}
							echo"
							<td>
								<input type='number' name='weight' min='0' step='0.1' oninput='PrefillBMI()' value='$vitals->weight'> kg
							</td>
							<td id='BMI'>
							</td>
							";
							if($age<6){
								echo "<td><input type='number' name='MUAC' min='0' step='0.1' value='$vitals->MUAC'> cm</td>";
							}
							echo"
						</tr>
					</table>
					<br><h3>Remarks:</h3>
					<textarea name='nutrition_remarks' maxlength='1000' style='min-width:500px; margin-left:20px; min-height:100px'>".$nutrition->getNutrition()."</textarea>
					<br><br>
					<input type='submit' name='nutrition' value='submit' style='margin-left:0px'><br>
					<input type='hidden' name='protocol_ID' value='$protocol_ID'>
					<a href='nutrition_patients.php'><div class ='box'>patients in nutrition office</div></a><br>"; 
			}else{
				if($_GET["nutrition"]=='submit'){
					$vitals->setHeight($_GET["height"]);
					if(! empty($_GET['infantometer'])){
						$vitals->setInfantometer(1);
					}else{
						$vitals->setInfantometer(0);
					}
					$vitals->setweight($_GET["weight"]);
					$vitals->setMUAC($_GET["MUAC"]);
					$nutrition->setNutrition($_GET["nutrition_remarks"]);
				}
				echo Vital_Signs::print_nutrition($protocol_ID);
			}
			echo "</details>";
		}

		public function print_nutrition($protocol_ID){
			
			$vitals=new self($protocol_ID);
			
			$height=$vitals->height;
			$weight=$vitals->weight;
			$infantometer=$vitals->infantometer;
			$MUAC=$vitals->MUAC;
			$BMI=$weight/($height/100)^2;


			$nutrition=(new Nutrition($protocol_ID))->getNutrition();

			$html='';
			
			if(! empty($height) OR ! empty($weight) OR ! empty($MUAC)){
				$html.='<h3>Measurements</h3>';
				if(! empty($height)){
					$html.="<h4>Height:</h4> $height cm";
					if(! empty($infantometer)){
						$html.=" (measured with infantometer)";
					}
					$html.='<br>';
				}
				
			}

			return $html;
		}
	}
?>