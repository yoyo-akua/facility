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
		## This function is called, if a new vital signs object is needed for further actions.
		## Saves the information of that entry from database (identified by protocol ID) in that new vital signs object.
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
		public function display_admission_data($visit_ID){

			## Initialise variables.
			## 		- $thispage is used to inquire whether the user is opening a pdf file.
			## 		- $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
			global $thispage;
			global $link;

			## Initialise variable $html which is used to buffer the text which is to be displayed. 
			$html='';

			## Call all vital signs entries saved for this visit of this patient. 
			$query="SELECT * FROM vital_signs,protocol WHERE vital_signs.protocol_ID=protocol.protocol_ID AND protocol.visit_ID='$visit_ID'";
			$result=mysqli_query($link,$query);

			## This loop is run once for each vital signs entry saved for this visit. 
			while($row=mysqli_fetch_object($result)){
				
				## Inquire the protocol ID linked to this vital signs entry.
				$protocol_ID=$row->protocol_ID;

				## Initialise new objects of vital signs and protocol using the protocol ID.
				$vitals=new self($protocol_ID);
				$protocol=new Protocol($protocol_ID);
			
				## Save the time of the protocol entry linked to this vital signs entry in $time.
				$time=date('d/m/Y H:i',strtotime($protocol->getTimestamp()));

				## Buffer $time as a headline in $html. 
				$html.="<h5>$time</h5><br>
						<div style='margin-left:20px;'>";

				## If the blood pressure has been entered, call this if-branch.
				if(! empty($vitals->getBP())){

					## In case this function is called within a pdf file, skip this if-branch. 
					if(!strstr($thispage,'pdf')){
					
						## Call the function last_BPs() to inquire the patient's last 5 measured blood pressures as reference values.
						$BP_last=Vital_Signs::last_BPs($visit_ID,$protocol_ID);

						/*
						## In case previously measured blood pressures have been entered, call this if-branch. 
						## It is used to buffer the last BP values as a tooltip, which is displayed when hovering over the current BP value. 
						*/
						if(!empty($BP_last)){
							$html.="<b>Blood Pressure:</b>
								<div class='tooltip' style='line-height:normal'>
									".$vitals->getBP()." mmHg
										<span class='tooltiptext' style='text-align:left'>
											$BP_last
										</span>
								</div><br>";
						}
						
						## If no previous BPs have been recorded, just buffer the current one in $html. 
						else{
							$html.="<b>Blood Pressure:</b> ".$vitals->getBP()." mmHg<br>";
						}
					}
					
					## If the function is called within a pdf file, just buffer the current blood pressure in $html. 
					else{
						$html.="<b>Blood Pressure:</b> ".$vitals->getBP()." mmHg<br>";
					}
				}

				## In case it is set, buffer the pulse in $html.
				if(! empty($vitals->getpulse())){
					$html.="<b>Pulse:</b> ".$vitals->getpulse()." bpm<br>";
				}

				## In case it is set, buffer the weight in $html.
				if($vitals->getweight()!=0.0){
					$html.="<b>Weight:</b> ".$vitals->getweight()." kg<br>";
				}

				## In case it is set, buffer the temperature in $html.
				if($vitals->gettemperature()!=0.0){
					$html.="<b>Temperature:</b> ".$vitals->gettemperature().' °C<br>';
				}

				## In case it is set, buffer the MUAC in $html. 
				if($vitals->getMUAC()!=0.0){
					$html.="<b>MUAC:</b> ".$vitals->getMUAC()." cm<br>";
				}	

				$html.='</div>';
			}

			## In case any vital signs have been buffered in $html, return this variable, otherwise return "false".
			if (! empty($html)){
				return $html.'<br>';
			}else{
				return false;
			}
			
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
		
	
		public static function get_last_vitals($visit_ID){
			global $link;
			$query="SELECT p.protocol_ID FROM vital_signs v,protocol p WHERE p.protocol_ID=v.protocol_ID AND p.visit_ID=$visit_ID ORDER BY p.protocol_ID DESC LIMIT 1";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			
			if(! empty($object)){
				$protocol_ID=$object->protocol_ID;
			}else{
				$protocol_ID=false;
			}
			
			return $protocol_ID;
		}

		## Check whether the vital signs for a specific patient's specific visit (identified by $visit_ID) have been entered.
		public static function already_set($visit_ID){
			global $link;
			$query="SELECT * FROM vital_signs,protocol WHERE protocol.protocol_ID=vital_signs.protocol_ID AND protocol.visit_ID=$visit_ID";
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
		## The sent parameter $visit_ID is used to link to the patient and his visit's data.
		## $function is indicating whether the vital signs for the patient are supposed to be added or edited:
		##		- if $functions equals "addVitals" that means that there is no vital signs entry to be excluded from the search (when adding)
		##		- if $function is a figure that is a protocol ID which is used to exclude the vitals with this specific ID from the search (when editing)
		## Return $BP_last.
		*/
		public static function last_BPs($visit_ID,$function){
			global $link;

			if($function=='addVitals'){
				$para='';
			}else{
				$para="AND protocol.protocol_ID!=$function";
			}
			$querylast="SELECT * FROM protocol,vital_signs,visit WHERE visit.visit_ID=protocol.visit_ID AND visit.patient_ID=(SELECT visit.patient_ID FROM visit WHERE visit_ID=$visit_ID) AND vital_signs.protocol_ID=protocol.protocol_ID AND vital_signs.BP NOT LIKE '' $para ORDER BY checkin_time DESC LIMIT 0,5";
			$resultlast=mysqli_query($link,$querylast);
			if(mysqli_num_rows($resultlast)!==0){
				$BP_last="last visits' BPs <br>";
				while($row_last=mysqli_fetch_object($resultlast)){
					$date=date("d/m/y",strtotime($row_last->timestamp));
					$BP_last.=$date.": ".$row_last->BP."<br>";
				}
			}else{
				$BP_last='';
			}
			return $BP_last;
		}

		/*
		## This function is used to display the input fields for entering the data of nutrition management.
		## The following parameters are sent along: 
		## - $protocol_ID contains the protocol ID which is used to identify the protocol entry of the particular visit 
		##   on which the patient received the treatment,
		## - $age contains the patient's age as a decimal figure, 
		##   which is used to limit the option of entering the MUAC only to children upt ot five years. 
		*/
		public function nutrition_visit($protocol_ID,$age){

			/*
			## Get data from database. 
			## Get the vital signs which were previously taken for the patient on that visit.
			## Variable $link contains credentials to connect with database and is defined in DB.php.
			*/
			global $link;
			$query="SELECT * FROM vital_signs WHERE protocol_ID=$protocol_ID";
			$result=mysqli_query($link,$query);

			/*
			## Initialise variable $vitals with the values of the previously entered vital signs in case they were entered before,
			## otherwise create a new Vital Signs entry and assign the value to $vitals.
			*/
			if(mysqli_num_rows($result)!==0){
				$vitals=new self($protocol_ID);
			}else{
				$vitals=Vital_Signs::new_Vital_Signs($protocol_ID,'','','','','');
			}

			/*
			## Inquire whether there already existis a nutrition entry, if not create a new one.
			## Assign the previously entered (or newly created, empty) values to $nutrition. 
			*/
			if(Nutrition::nutritionBoolean($protocol_ID)){
				$nutrition=new Nutrition($protocol_ID);
			}else{
				$nutrition=Nutrition::new_Nutrition($protocol_ID);
			}

			## Print the headline.
			echo"
				<details ";
				if (empty($_GET['show'])){
					echo "open";
				}
				echo">
					<summary>
						<h2>Nutrition Management</h2>
					</summary>";
			## In case the user is not in "display mode" and is either editing or newly entering the nutrition data, display the corresponding input fields.
			if(empty($_GET['show']) AND ((! empty($_GET["nutrition"]) AND $_GET["nutrition"]=='enter') OR ! empty($_GET['edit']))){
				
				/*
				## Initialise variable $management which contains the data which were previously entered as type of management.
				## This information is required for preselecting the correct type of management in the select box, when the user has entered this before. 
				*/
				$management=$nutrition->getManagement();

				/*
				## Print the form for the nutrition data.
				## This includes 
				## - a number field with the patient's height,
				## - in case the patient is below 6 years old a checkbox, inquiring whether the height was measured with an infantometer or not,
				## - an inout field for the patient's weight, 
				## - an automatically filled (but not editable) cell with the BMI which is calculated and classified by the javascript function prefillBMI(),
				## - in case the client is below 6 years another input field for the MUAC of the child,
				## - a selection field for the type of management performed on the patient,
				## - a large textbox where to enter any further information to the treatment. 
				## All fields are prefillled with previously entered values for this visit, if available.
				*/
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
									<a href='BMI_links.php'>BMI</a>
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
					<br><h3>Management</h3>
					<h4>Type:</h4><br>
					<select name='management' style='margin-left:20px'>
						<option value=''";if($management==''){echo 'selected';}echo"></option>
						<option value='Diet Management'";if($management=='Diet Management'){echo 'selected';}echo">Diet Management</option>
						<option value='Physical Management'";if($management=='Physical Management'){echo 'selected';}echo">Physical Management</option>
						<option value='Pharmaceutical Management'";if($management=='Pharmaceutical Management'){echo 'selected';}echo">Pharmaceutical Management</option>
						<option value='Therapeutic Management'";if($management=='Therapeutic Management'){echo 'selected';}echo">Therapeutic Management</option>
					</select><br>
					<h4>Remarks:</h4><br>
					<textarea name='nutrition_remarks' maxlength='1000' style='min-width:500px; margin-left:20px; min-height:100px'>".$nutrition->getNutrition_remarks()."</textarea>
					<br>
					<input type='checkbox' name='completed'> treatment in clinic completed<br>
					<input type='submit' name='nutrition' value='submit' style='margin-left:0px'><br>
					<input type='hidden' name='protocol_ID' value='$protocol_ID'>
					"; 
			}
			
			## Call this if-branch after the user submitted the nutrition data or in case he is in "display mode". 
			else{

				/*
				## Call this if-branch in case the user submitted the input form. 
				## It is used to write the entered data to the database. 
				*/
				if(! empty($_GET["nutrition"]) AND $_GET["nutrition"]=='submit'){

					$vitals->setHeight($_GET["height"]);
					if(! empty($_GET['infantometer'])){
						$vitals->setInfantometer(1);
					}else{
						$vitals->setInfantometer(0);
					}
					$vitals->setweight($_GET["weight"]);

					if(! empty($_GET['MUAC'])){
						$vitals->setMUAC($_GET["MUAC"]);
					}

					$nutrition->setManagement($_GET["management"]);
					$nutrition->setNutrition_remarks($_GET["nutrition_remarks"]);

					$BMI=$vitals->getweight()/pow(($vitals->getheight()/100),2);
					$nutrition->setBMI_classification(Nutrition::classify_BMI($protocol_ID,$BMI));

					$protocol=new Protocol($protocol_ID);
					$visit=new Visit($protocol->getVisit_ID());
					if(! empty($_GET['completed'])){
						$visit->setCheckout_time(date('Y-m-d H:i:s',time()));
					}else{
						$visit->setCheckout_time('0000-00-00 00:00:00');
					}
				}

				## Print the nutrition data for the patient on that visit on which the function is called (not editable).
				echo Vital_Signs::print_nutrition($protocol_ID);
			}

			## In case the user is not in "display mode", print a link to the overview with all nutrition patients. 
			if(empty($_GET['show'])){
				echo "<a href='nutrition_patients.php'><div class ='box'>patients in nutrition office</div></a><br>";
			}
			echo "</details>";
		}

		/*
		## This function is used to print the nutrition data for a patient on a particular visit (not editable).
		## The sent parametr $protocol_ID is used to link to this particular visit's data in the database. 
		*/
		public static function print_nutrition($protocol_ID){
			
			/*
			## Initialise variable $vitals containing the vital signs of that visit. 
			## Use this to initialise variables with the height, weight, infantometer status and MUAC of the client. 
			*/
			$vitals=new self($protocol_ID);
			
			$height=$vitals->height;
			$weight=$vitals->weight;
			$infantometer=$vitals->infantometer;
			$MUAC=$vitals->MUAC;

			## Calculate the BMI, based on the patient's height and weight.
			if($height!=0 AND $weight!=0){
				$BMI=number_format($weight/pow(($height/100),2),2);
			}

			/*
			## Initialise variable $nutrition containing the nutrition data for the patient on that visit. 
			## Use it to initialise variables with the management type, the classification of the BMI and further remarks on the treatment.
			*/
			$nutrition=new Nutrition($protocol_ID);
			$remarks=$nutrition->getNutrition_remarks();
			$management=$nutrition->getManagement();
			$classification=$nutrition->getBMI_classification();

			## Initilialise variable $html as an empty variable whihc will be used to buffer all data which are to be printed.
			$html='';
			
			## Add all the nutrition data and vitals to $html.
			if($height!=0 OR $weight!=0 OR $MUAC!=0){
				$html.='<h3>Measurements</h3>';
				if($height!=0){
					$html.="<h4>Height:</h4> $height cm";
					if($infantometer!=0){
						$html.=" (measured with infantometer)";
					}
					$html.="<br>";
				}
				if($weight!=0){
					$html.="<h4>Weight:</h4> $weight kg<br>";
				}
				if(isset($BMI)){
					$html.="<h4>BMI:</h4> $BMI kg/m&#xB2; ($classification)<br>";
				}
				if($MUAC!=0){
					$html.="<h4>MUAC:</h4> $MUAC cm<br>";
				}
			}
			if(! empty($remarks) OR ! empty($management)){
				$html.="<h3>Management</h3>";
				if(! empty($management)){
					$html.="<h4>Type:</h4> $management<br>";
				}
				if(! empty($remarks)){
					$html.="<h4>Remarks:</h4> $remarks";
				}
			}

			## Return $html.
			return $html;
		}

		public static function display_editable_Vitals($vitals_ID){
			

			## In case vitals have been taken for this visit, call this if-branch.
			if($vitals_ID){

				## Initialise new Vital Signs object using the protocol ID saved in $vitals_ID.
				$vital_signs=new Vital_Signs($vitals_ID);
				
				## For each vitals parameter (blood pressure, weight, pulse and temperature) create an array, containing
				##		- the name of the parameter
				##		- the previously saved values
				##		- the unit of the parameter
				##		- the type of the variable and further specifications.
				$BP=array('BP',$vital_signs->getBP(),'mmHg',"type='text' pattern='[0-9]{2,}[//]{1}[0-9]{2,}'");
				$Weight=array('Weight',$vital_signs->getweight(),'kg',"type='number' step='0.1' min='0' max='500'");
				$Pulse=array('Pulse',$vital_signs->getpulse(),'bpm',"type='number' min='0' max='200'");
				$Temperature=array('Temperature',$vital_signs->gettemperature(),'&#176C',"type='number' min='30' max='45' step='0.1'");
			}
			
			## In case the vitals haven't been taken before, create the same arrays as listed above, but without the previously saved values.
			else{
				$BP=array('BP','','mmHg',"type='text' pattern='[0-9]{2,}[//]{1}[0-9]{2,}'");
				$Weight=array('Weight','','kg',"type='number' step='0.1' min='0' max='500'");
				$Pulse=array('Pulse','','bpm',"type='number' min='0' max='200'");
				$Temperature=array('Temperature','','&#176C',"type='number' min='30' max='45' step='0.1'");
			}

			## Create an array containing all vital signs parameter arrays created above.
			$vital_parameters=array($BP,$Weight,$Pulse,$Temperature);
			

			/*
			## Run this loop once for each of the vital signs parameters. 
			## Use the information saved in the arrays to display the vital signs parameter as plain text.
			## Print an editing symbol after that,
			## if this is clicked, there appears an input field instead of the plain text
			## which enables the user to edit the value. 
			## This functionality is described in the javascript function edit_value.
			*/
			foreach($vital_parameters AS $para){
				$title=$para[0];
				$value=$para[1];
				$unit=$para[2];
				$specifications=$para[3];

				echo"
					<div>
						<label>$title:</label><br>
						
						<div style='display:none' id='".$title."_div'>
							<input $specifications id='".$title."_input' class='smalltext' name='$title'> $unit
						</div>
	
						<font  id='".$title."_text'>";
							if(! empty($value)){
								echo"$value $unit";
							}else{
								echo"not entered";
							}
							echo"
						</font>
	
						<button type='button' id='edit_".$title."' onclick='edit_value(\"$title\",\"$value\")' class='grey'>
							<i class='fas fa-pencil-alt'></i>
						</button>
	
					</div>";
			}
		}
	}
?>