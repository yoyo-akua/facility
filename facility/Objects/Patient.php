<?php
	class Patient{
		## define all parameters a patient has.
		private $ID;
		private $OPD;
		private $name;
		private $NHIS;
		private $Birthdate;
		private $NHISofMother;
		private $Sex;
		private $Locality;
		private $blood_group;
		private $telephone;
		private $height;
		
		/*
		## This function is called, if a new patient object is needed for further actions.
		## Saves the information of that patient from database (identified by patient ID) in that new patient object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Patient($ID){
			global $link;
			$query = "SELECT * FROM patient WHERE patient_ID = $ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->name = $row->Name;
				$this->OPD = $row->OPD;
				$this->NHIS = $row->NHIS;
				$this->Birthdate = $row->Birthdate;
				$this->NHISofMother = $row->NHISofMother;
				$this->Sex = $row->Sex;
				$this->Locality = $row->Locality;
				$this->blood_group = $row->blood_group;
				$this->telephone = $row->telephone;
				$this->height = $row->height;
			}
			$this->ID = $ID;
		}
		
		/*
		## Constructor of new patient.
		## Is called, if a new patient database entry is created.
		## The data of new patient is saved in database for all its parameters.
		## Save this data also in a new created patient object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Patient($name,$OPD,$NHIS,$Birthdate,$Sex,$Locality,$NHISofMother,$blood_group){
			global $link;
			$query = "INSERT INTO `patient`(`Name`, `OPD`, `NHIS`,`Birthdate`,`NHISofMother`,`Sex`,`Locality`,`blood_group`) VALUES ('$name','$OPD','$NHIS','$Birthdate','$NHISofMother','$Sex','$Locality','$blood_group')";
			mysqli_query($link,$query);
			
			$ID = mysqli_insert_id($link);
			$instance = new self($ID);
			return $instance;
		}
				
		/*
		## Getter function.
		## Returns the ID of a patient, on which the function is called.
		*/
		public function getPatient_ID(){
			return $this->ID;
		}
		/*
		## Getter function.
		## Returns the name of a patient, on which the function is called.
		*/
		public function getName(){
			return $this->name;
		}
		
		/*
		## Getter function.
		## Returns the OPD number of a patient, on which the function is called.
		*/
		public function getOPD(){
			return $this->OPD;
		}
		
		/*
		## Getter function.
		## Returns the NHIS of a patient, on which the function is called.
		*/
		public function getNHIS(){
			return $this->NHIS;
		}
		
		/*
		## Getter function.
		## Returns the birthdate of a patient, on which the function is called.
		*/
		public function getBirthdate(){
			return $this->Birthdate;
		}
		
		/*
		## Getter function.
		## Returns the information, whether a patient's NHIS is the NHIS of its mother.
		*/
		public function getNHISofMother(){
			return $this->NHISofMother;
		}
		
		/*
		## Getter function.
		## Returns the sex of a patient, on which the function is called.
		*/
		public function getSex(){
			return $this->Sex;
		}
		
		/*
		## Getter function.
		## Returns the locality of a patient, on which the function is called.
		*/
		public function getLocality(){
			return $this->Locality;
		}
		
		/*
		## Getter function.
		## Returns the blood groupt of a patient, on which the function is called.
		*/
		public function getblood_group(){
			return $this->blood_group;
		}
				
		/*
		## Getter function.
		## Returns the telephone number of a patient, on which the function is called.
		*/
		public function getTelephone(){
			return $this->telephone;
		}

		/*
		## Getter function.
		## Returns the body hight of a patient, on which the function is called.
		*/	
		public function getHeight(){
			return $this->height;
		}

		/*
		## Setter function.
		## Updates the name of a patient (identified by patient's ID), on which the function is called, in database.
		## Returns the updated name of this patient.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setName($var){
			global $link;
			$query = "UPDATE patient SET Name='$var' WHERE patient_ID = $this->ID";
			mysqli_query($link,$query);
			return $this->name = $var;
		}
		
		/*
		## Setter function.
		## Updates the OPD number of a patient (identified by patient's ID), on which the function is called, in database.
		## Returns the updated OPD number of this patient.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setOPD($var){
			global $link;
			$query = "UPDATE patient SET OPD='$var' WHERE patient_ID = $this->ID";
			mysqli_query($link,$query);
			return $this->OPD = $var;
		}
		
		/*
		## Setter function.
		## Updates the NHIS of a patient (identified by patient's ID), on which the function is called, in database.
		## Returns the updated NHIS of this patient.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setNHIS($var){
			global $link;
			$query = "UPDATE patient SET NHIS='$var' WHERE patient_ID = $this->ID";
			mysqli_query($link,$query);
			return $this->NHIS = $var;
		}
		
		/*
		## Setter function.
		## Updates the birthdate of a patient (identified by patient's ID), on which the function is called, in database.
		## Returns the updated birthdate of this patient.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setBirthdate($var){
			global $link;
			$query = "UPDATE patient SET Birthdate='$var' WHERE patient_ID = $this->ID";
			mysqli_query($link,$query);
			return $this->Birthdate = $var;
		}
		
		/*
		## Setter function.
		## Updates the information, whether the NHIS of a patient, identified by patient's ID, is the NHIS of its mother,
		## and writes Update to database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setNHISofMother($var){
			global $link;
			$query = "UPDATE patient SET NHISofMother='$var' WHERE patient_ID = $this->ID";
			mysqli_query($link,$query);
			return $this->NHISofMother = $var;
		}
		
		/*
		## Setter function.
		## Updates the sex of a patient (identified by patient's ID), on which the function is called, in database.
		## Returns the updated sex of this patient.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setSex($var){
			global $link;
			$query="UPDATE patient SET Sex='$var' WHERE patient_ID=$this->ID";
			mysqli_query($link,$query);
			return $this->Sex = $var;
		}
		
		/*
		## Setter function.
		## Updates the locality of a patient (identified by patient's ID), on which the function is called, in database.
		## Returns the updated locality of this patient.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public  function setLocality($var){
			global $link;
			$query="UPDATE patient SET Locality='$var' WHERE patient_ID=$this->ID";
			mysqli_query($link,$query);
			return $this->Locality = $var;
		}
		
		/*
		## Setter function.
		## Updates the blood group of a patient (identified by patient's ID), on which the function is called, in database.
		## Returns the updated blood group of this patient.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public  function setblood_group($var){
			global $link;
			$query="UPDATE patient SET blood_group='$var' WHERE patient_ID=$this->ID";
			mysqli_query($link,$query);
			return $this->blood_group = $var;
		}

		/*
		## Setter function.
		## Updates the telephone number of the patient, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setTelephone($var){
			global $link;
			$query = "UPDATE patient SET telephone='$var' WHERE patient_ID = $this->ID";
			mysqli_query($link,$query);
			return $this->telephone = $var;
		}

		/*
		## Setter function.
		## Updates the the body hight of a patient, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/	
		public function setHeight($var){
			global $link;
			$query = "UPDATE patient SET height='$var' WHERE patient_ID = $this->ID";
			mysqli_query($link,$query);
			return $this->height = $var;
		}

		/*
		## Prints a table head to browser with patient's information.
		## Is used to display list of patients in protocol.
		## Within the table head are the patient's parameters: date of last visit, OPD number, name,
		## age, sex and locality.
		## Optional and depending on which columns are requested within variable $columns,
		## more columns (new or old patient, insured or uninsured, NHIS,
		## CC Code, tests and its results, surgery information, 
		## prescribed drugs, provisional, primary and secondary diagnosis, 
		## as well as information about ANC) are printed within the table head.
		## At the end two further columns for Editing a patient and patient's results are printed.
		*/
		public static function shorttablehead($columns){
			echo '
				<table>
					<tr>
						<th style=border-left:none>
							Date of Visit
						</th>
						<th>
							OPD
						</th>
						<th>
							Name
						</th>
						<th>
							Age
						</th>
						<th>
							Sex
						</th>
						<th>
							Locality
						</th>
						';
						if($columns['birthdate']=='on'){
							echo"
								<th>
									Birthdate
								</th>
							";
						}
						if($columns['newold']=='on'){
							echo"
								<th>
									old/new
								</th>
							";
						}
						if($columns['insured']=='on'){
							echo"
								<th>
									insured?
								</th>
							";
						}
						if($columns['NHIS']=='on'){
							echo"
								<th>
									NHIS Number
								</th>
							";
						}
						if($columns['CCC']=='on'){
							echo"
								<th>
									CC Code
								</th>
							";
						}
						if($columns['tests']=='on'){
							echo"
								<th>
									tests & results
								</th>
							";
						}
						if($columns['surgery']=='on'){
							echo"
								<th>
									surgery
								</th>
							";
						}
						if($columns['drugs']=='on'){
							echo"
								<th>
									prescribed drugs
								</th>
							";
						}
						if($columns['primary']=='on'){
							echo"
								<th>
									primary diagnoses
								</th>
							";
						}
						if($columns['secondary']=='on'){
							echo"
								<th>
									secondary diagnoses
								</th>
							";
						}
						if($columns['provisional']=='on'){
							echo"
								<th>
									provisional diagnoses
								</th>
							";
						}
						if($columns['ANC']=='on'){
							echo"
								<th>
									ANC
								</th>
							";
						}
						if($columns['entered']=='on'){
							echo"
								<th>
									Entered in NHIS Claim It?
								</th>
							";
						}
						if($columns['nutrition']=='on'){
							echo"
								<th>
									Nutrition Management
								</th>
							";
						}
					echo'
						<th>
							Edit
						</th>
						<th>
							Results
						</th>
						</tr>
					';
		}
		
		/*
		## Prints a table row to browser with patient's information for each patient, on which this function is called.
		## Is used to display list of patients in protocol.
		## Within the table row are the following patient's parameters printed: 
		## OPD number, name, age, sex and locality.
		## Optional and depending on variable $previous the date of patient's last visit is printed.
		## Variable $previous is used to print the visit date only for the first of all patients, which were visiting a certain day.
		## For all following patients, which were visiting the same day, the date of last visit is not printed again. 
		## Thereby, the patient's list should be more clearly.
		## Optional and depending on which columns are requested within variable $columns,
		## more columns are printed within the table: new or old patient, insured or uninsured patient, NHIS,
		## CC Code, tests and its results, surgery information, prescribed drugs, 
		## provisional, primary and secondary diagnosis, as well as information about ANC.
		## At the end two further columns are printed for Editing a patient by forwarding the user to edit_patient.php,
		## and for patient's results by forwarding the user to patient_visit.php.
		*/
		public function shorttablerow($visit_ID,$previous,$columns){

			#$Protocol=new Protocol($protocol_ID);
			/*
			## Initialise variables, which are needed within this function.
			*/
			$visit=new Visit($visit_ID);
			#$protocol=new Protocol()
			#$visit=new Visit($Protocol->getVisit_ID());

			$insurance=new Insurance($visit_ID);
			#$insurance=new Insurance($Protocol->getVisit_ID());

			$patient_ID=$this->ID;
			
			echo"
				<tr>
				<td style=border-left:none>
			";
			
			## Prints last visit date.
			$VisitDate=date("d/m/y",strtotime($visit->getCheckin_time()));
			if($previous!==$VisitDate){
				echo $VisitDate;
			}
			
			## Prints patient's OPD number,name, age, sex and locality.
			echo"
				</td>
				<td>
					$this->OPD
				</td>
				<td>
					$this->name
				</td>	
				<td>
				".$this->getAge(strtotime($visit->getCheckin_time()),'print')."
				</td>
				<td>
					$this->Sex
				</td>
				<td>
					$this->Locality
				</td>
			";
			
			## Prints optional and depending on which columns are requested, the Birthdate of a patient.
			if($columns['birthdate']=='on'){
				$birthdate=date("d/m/y",strtotime($this->Birthdate));
				echo"<td>$birthdate</td>";
			}
			
			## Prints optional and depending on which columns are requested the information, whether a patient is new or not.
			if($columns['newold']=='on'){
				$new_p=$visit->getNew_p();
				if($new_p=='1'){
					echo"<td>new</td>";
				}
				else{
					echo"<td>old</td>";
				}
			}

			## Prints optional and depending on which columns are requested the information, whether a patient is insured or not.
			if($columns['insured']=='on'){
				$exp=$insurance->getExpired();
				if($exp=='0' AND $this->NHIS){
					echo"<td>yes</td>";
				}
				else{
					echo"<td>no</td>";
				}
			}
			
			## Prints optional and depending on which columns are requested the patient's NHIS.
			if($columns['NHIS']=='on'){
				echo"<td>$this->NHIS</td>";
			}
			
			## Prints optional, depending on which columns are requested and only if existing the patient's CCC information (insurance code).
			if($columns['CCC']=='on'){
				$CCC=$insurance->getCCC();
				if(! empty($CCC)){
					echo "<td>$CCC</td>";
				}
				else{
					echo "<td></td>";
				}
			}
			
			## Prints optional and depending on which columns are requested the patient's tests and its results.
			if($columns['tests']=='on'){
				$lab_number=$visit->getLab_number();
				$html=Lab::display_results($lab_number,'tooltips off');
				echo "<td style=text-align:left><h4><u>$lab_number</u></h4><br>$html</td>";
			}
						
			##TODO: Surgery soll noch in eine eigene Tabelle. 
			## Dann ist das hier mit anzupassen.
			## Solange bleibt der Code auskommentiert. 
			## Prints optional, depending on which columns are requested and only if existing the patient's surgery information.
			/*
			f($columns['surgery']=='on'){
				echo"<td>";
				$surgery=$Protocol->getsurgery();
				if(! empty($surgery)){
					echo $Protocol->display_surgery();
				}
				echo"</td>";
			}
			*/
			
			## Prints optional, depending on which columns are requested and only if existing the prescribed drugs of a patient.
			if($columns['drugs']=='on'){
				if(Disp_Drugs::drugs_prescribed($visit_ID)){
					$html=Disp_Drugs::display_disp_drugs($visit_ID,'print');
					echo"<td style=text-align:left>$html</td>";
				}else{
					echo '<td></td>';
				}
			}

			## TODO: display_diagnoses Funktion muss aus dem Protocol Objekt herausgezogen
			## und in das Visit-Objekt verschieben
			## @Flo: Das noch mit erledigen beim aktuellen Umbau
			## Solange bleibt der Code erstmal auskommentiert.
			## Prints optional, depending on which columns are requested and only if existing the patient's primary and secondary diagnoses.
			/*
			if($columns['primary']=='on'){
				$html=$Protocol->display_diagnoses('primary');
				echo "<td style='text-align:left'>$html</td>";
			}
			if($columns['secondary']=='on'){
				$html=$Protocol->display_diagnoses('secondary');
				echo "<td style='text-align:left'>$html</td>";
			}
			if($columns['provisional']=='on'){
				$html=$Protocol->display_diagnoses('provisional');
				echo "<td style='text-align:left'>$html</td>";
			}
			*/
			
			##TODO: Hier muss erst noch die Protocol ID in die ANC Tabelle
			## Danach kann die getANC_ID in das ANC Object verschoben werden
			## Anschließend kann in der get Methode über die Visit-ID und die Protocol ID
			## nach der ANC-ID gesucht werden.
			## Bis zum Abschluss dieses Umbaus bleibt der nachstehende Code auskommentiert.
			## Prints optional, depending on which columns are requested and only if existing the patient's ANC information.
			/*
			if($columns['ANC']=='on'){
				$ANC_ID=$Protocol->getANC_ID();
				if(! empty($ANC_ID)){
					if(strstr($ANC_ID,"delivered")){
						$ANC="DELIVERY";
					}
					else{
						$ANC=new ANC($ANC_ID);
						$ANC=$ANC->display_ANC($protocol_ID,'date off');
					}
				}
				else{
					$ANC='';
				}
				echo"<td style='text-align:left'>$ANC</td>";
			}
			*/

			##Prints optional, depending on which columns are requested the information, whether patient has been entered in Claim It or not
			if($columns['entered']=='on'){
				global $thispage;
				if(! empty($_GET['entered']) AND $_GET['entered']==$visit_ID){
					if($insurance->getEntered()==1){
						$insurance->setEntered(0);
					}else{
						$insurance->setEntered(1);
					}
				}
				/*
				## Variables $from and $to are initialised which are used to set the time frame for the search.
				## Depending on, if the page was called by an external link or the submission of the search form, 
				## these dates are retrieved either from the URL or the global variable $_POST.
				*/
				if(! empty($_GET['from'])){
					$from=$_GET['from'];
				}else{
					$from=$_POST['from'];
				}

				if(! empty($_GET['to'])){
					$to=$_GET['to'];
				}else{
					$to=$_POST['to'];
				}

				echo"
						<td><a href='$thispage?from=$from&to=$to$&entered=$visit_ID'>
						<input type='checkbox'";
				if($insurance->getEntered()==1){
					echo"checked='checked'";
				}
				echo"></a></td>";
			}

			## TODO: Vital_Signs::print_nutrition($visit_ID) muss noch umgeschrieben werden
			## Vorher wurde hier die protocol_ID übergeben
			## @Flo: selber umbauen
			## Solange bleibt der Code noch auskommentiert.
			## Prints optional, depending on which columns are requested and only if existing the nutrition management of a patient.
			/*
			if($columns['nutrition']=='on'){
				$html=Vital_Signs::print_nutrition($visit_ID);
				echo "<td style='text-align:left'>$html</td>";
			}
			*/

			## Prints links for editing patient as well as for show its results in patient diagnoses overview.
			echo"
				<td>
					<a href=\"edit_patient.php?patient_ID=$this->ID&visit_ID=$visit_ID\">See/Edit</a>
				</td>
				<td>
					<a href=\"patient_visit.php?show=on&pvisit_ID=$visit_ID&patient_ID=$patient_ID\">Results</a>
				</td>
			";
		}
		
		/*
		## Prints a table head to browser with patient's information.
		## Is used to display the list of patients in maternity, laboratory and consulting.
		## Within the table head are the following patient's parameters printed: OPD number, name, age, sex and locality.
		*/
		public static function currenttablehead(){
			echo '
				<table>
				<tr>
				<th style=border-left:none>
					OPD
				</th>
				<th>
					Name
				</th>
				<th>
					Age
				</th>
				 <th>
					Sex
				</th>
				 <th>
					Locality
				</th>				
			';
		}	
		
		/*
		## Prints a table row to browser with patient's information for each patient, on which this function is called.
		## Is used to display the list of patients in maternity, laboratory and consulting.
		## Within the table row are the following patient's parameters printed: OPD number, name, age, sex and locality.
		*/
		public function currenttablerow(){
			echo"
				<tr>
				<td style=border-left:none>
					$this->OPD
				</td>
				<td>
					$this->name
				</td>	
				<td>
					".$this->getAge(time(),'print')."
				</td>
				<td>
					$this->Sex
				</td>			
				<td>
					$this->Locality
				</td>
			";
		}
		
		/*
		## Prints a table head to browser with patient's information.
		## Is used for updating patient's information.
		## Within the table head are the following patient's parameters printed: name, OPD number, NHIS, expiring status, birthdate, 
		## information, whether the NHIS is like the NHIS of patient's mother, sex, locality and CCC information.
		## Further, at the end is another column for saving updates on patient's information.
		*/
		public static function tablehead(){
			echo "
				<table>
				<tr>
				<th style=border-left:none>
					Name
				</th>
				<th>
					OPD
				</th>
				<th>
					NHIS
				</th>
				<th>
					Expired?
				</th>
				<th>
					Birthdate
				</th>
				 <th>
					NHIS is NHIS <br> of Mother
				</th>
				 <th>
					Sex
				</th>
				 <th>
					Locality
				</th>	
				<th>
					CCC
				</th>
				";
			/*
			## Within the first year that the programme is running, add a column for the old/new status of the patient.
			## Also print a tooltip to explain the meaning of the column.
			## After the first year, manual editing of that shouldn't be necessary again, because data collection should be sufficient to enter that automatically.
			*/
			global $today;
			global $YEAR;
			if(strstr($today,$YEAR)){
				echo"
						<th>
							<div class='tooltip'>
								New?
								<span class='tooltiptext'>
									Select the checkbox, if the patient has not been here this year.
								</span>
							</div>
						</th>
						";
			}
			echo"
				<th>
					Save
				</th>
				</tr>
			";
		} 

		/*
		## Prints a table row to browser with patient's information for each patient, on which this function is called.
		## Is used for updating patient's information.
		## Within the table row are input field regarding the following patient's parameters printed: name, OPD number, NHIS, expiring status, birthdate, 
		## information, whether the NHIS is like the NHIS of patient's mother, sex, locality and CCC information.
		## Further, at the end is another column for saving updates on patient's information.
		*/
		public function tablerow(){
			global $OPD_FORMAT;
			
			echo'
				<tr>
				<form action="search_patient.php" method="post">
					<td style=border-left:none>
						<input type=text name="Name" value="'.$this->name.'" required>
					</td>
					<td>
						<input class="smalltext" type="text" name="OPD" value="'.$this->OPD.'" pattern="'.$OPD_FORMAT.'">
					</td>
					<td>
						<input class="smalltext" type="text" name="NHIS" value="'.$this->NHIS.'" pattern="[0-9]{8}">
					</td>
					<td>
						<input type="checkbox" name="Expired">					
					</td>
					<td>
						<input type="date" name="Birthdate" value="'.$this->Birthdate.'" max="'.date("Y-m-d",time()).'" min="1900-01-01" required>
					</td>
					<td>						
						<input type="checkbox" name="NHISofMother" 
			';
			
			if($this->NHISofMother){
				echo 'checked';
			}
			
			echo 
				'>
				</td>
				<td>
			';
			
			echo'
				<input type="radio" name="Sex" value="male"
			';

			if($this->Sex=='male'){
				echo"checked='checked'";
			}
			
			echo'
				>male </br>
				<input type="radio" name="Sex" value="female"
			';
			
			if($this->Sex=='female'){
				echo"checked='checked'";
			}

			echo'
				required>female </br>
				</td>
				<td>
					<input class="smalltext" type="text" name="Locality" value="'.$this->Locality.'" required>
				</td>
				<td>
					<input class="smalltext" type="text" pattern="[0-9]{5}" name="CCC">
				</td>
				<td>
				';
			
			/*
			## Within the first year that the programme is running, add a checkbox to edit the old/new status of the patient.
			## Precheck the checkbox, if there already is an entry for the patient in this year.
			## After the first year, manual editing of that shouldn't be necessary again, because data collection should be sufficient to enter that automatically.
			*/
			global $today;
			global $YEAR;
			if(strstr($today,$YEAR)){
				global $link;
				$new_p=1;
				$query="SELECT * FROM visit WHERE patient_ID='$this->ID' ORDER BY checkin_time DESC LIMIT 1";
				$result=mysqli_query($link,$query);
				$object=mysqli_fetch_object($result);
				if(! empty($object)){
					$VisitYear=date("Y",strtotime($object->checkin_time));
					if($VisitYear==date("Y",time())){
						$new_p=0;
					}
				}
				echo'
					<input type="checkbox" name="new_p"';
						if($new_p==1){
							echo"checked='checked'";
						}
					echo">
					</td>
					<td>
					";		
			}
				if(! empty($_GET['onlylab'])){
					echo'<input type="hidden" name="onlylab" value="on">';
				}
				echo'
					<input type="submit" name="patientAction" value="save">
					<input type=hidden name="ID" value="'.$this->ID.'" readonly>
				</td>			
				</form>
				<td style="border:none">
					<a href="delete_from_database.php?patient_ID='.$this->ID.'">&#8594;delete</a>
				</td>
				</tr>
			';
		}
		
		## Is necessary to close the printed table.
		public static function tablebottom(){
			echo'</table>';
		}
		
		## Display patient's general data by this function.
		public function display_general($timestamp){
			$html="
						<h4>OPD:</h4> $this->OPD<br>
						<h4>Age:</h4> ".$this->getAge($timestamp,'print')."<br>
						<h4>Sex:</h4> $this->Sex<br>
						<h4>Locality:</h4> $this->Locality<br>
						";
			if(! empty($this->blood_group)){
				$html.="<h4>Blood Group:</h4> $this->blood_group<br>";
			}
			return $html;
		}
		
		## Set all patient's general data at once with this function.
		public function setOPD_data($timestamp){
			$this->setName($_POST['Name']);
			$this->setOPD($_POST['OPD']);
			$this->setNHIS($_POST['NHIS']);
			$this->setBirthdate($_POST['Birthdate']);
			If (! empty($_POST['NHISofMother'])){
				if($_POST['Birthdate']<date("Y-m-d",($timestamp-(30*24*3600*3)))){
					$message="Only infants below the age of three months can use their mother's NHIS";
					Settings::messagebox($message);
					$this->setNHISofMother(0);
				}else{
					$this->setNHISofMother(1);
				}
			}else{
				$this->setNHISofMother(0);
			}
			$this->setSex($_POST['Sex']);
			$this->setLocality($_POST['Locality']);
			if(! empty($_POST['blood_group'])){
				$this->setblood_group($_POST['blood_group']);
			}
		}
		
		## Adapt a search parameter for a simple search with name or OPD number.
		public static function simple_search($page){
			$searchpara="";
			if(! empty($_POST['OPD']) OR ! empty($_POST['Name'])){
				if(! empty($_POST['OPD'])){
					$var = $_POST['OPD'];
					$searchpara .= " AND OPD like '$var'";
				}
				if(! empty($_POST['Name'])){
					$var = $_POST['Name'];
					$searchpara .= " AND NAME like '%$var%'";
				}
			}
			echo'
					<div class="inputform">
						<form action="'.$page.'" method="post">

							<div><label>OPD Number:</label><br>
							<input type="text" name="OPD"><br></div>

							<div><label>Name:</label><br>
							<input type=text name="Name"  id="autocomplete" class="autocomplete" autocomplete="off"><br></div>

							<button type="submit" name="submit_simple"><i class="fas fa-search" id="department_search"></i></button>
						</form>
					</div>
					';
			return $searchpara;
		}
		/*
		## Caluculates the patient's age in months, if the patient is younger than one year.
		## Otherwise the age is calculated in years.
		## If no patient's birthdate is known, 'unknown' is returned.
		*/
		public function getAge($timestamp,$function){
			if($this->Birthdate){
				$date=strtotime($this->Birthdate);
				$age=($timestamp-$date)/(3600*24*365.25);
				if($age<1){
					$agemonths=floor(($timestamp-$date)/(3600*24*30.4375));
					$html= "$agemonths months";
				}
				else{
					$ageyears=floor($age);
					$html= "$ageyears years";
				}
			}
			if($function=='print'){
				return $html;
			}else{
				return $age;
			}
		}		
	}
?>
