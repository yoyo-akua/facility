<?php
	/*
	## Contains global variables which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Initialise new object of visit by a certain visit ID, with which the page is called.
	$visit_ID=$_GET['visit_ID'];
	$visit= new Visit($visit_ID);

	## Initialise new object of patient by a certain patient-ID, with which the page is called.
	$patient_ID=$visit->getPatient_ID();
	$patient=new Patient($patient_ID);

	## Inquire whether the patient has received nutrition management for this visit.
	$nutrition=Nutrition::nutritionBoolean($visit_ID);

	/*
	## Check, whether the patient's diagnosis is protected.
	## In Case of enabling or disabling protection, the user needs to enter the password of consulting department.
	## Otherwise the user is not allowed to enable or disable the protection of a patient's diagnosis.
	## This if-branch is called, 
	##		- if user wants to protect a patient's diagnosis ($_GET['protect'] or $_POST['password'] not empty),
	##		- if a patient's diagnosis is protected ($visit->getprotect()==1).
	*/
	if(! empty($_GET['protect']) OR ! empty($_POST['password']) OR $visit->getProtect()==1){
		
		## Tag, that the user wants to enable or disable the protection of a patient's diagnosis by a session named $_SESSION['protect_patient'].
		if(! empty($_GET['protect'])){
			$_SESSION['protect_patient']='active';
		}
		
		/*
		## This if-branch is called,
		## 		- after entering a the password of consulting department, 
		## 		- or if the correct password was already entered before.
		*/
		if(empty($_POST['submit']) OR !(isset($_SESSION['password_consulting']))){
			$consultingpassword=Settings::passwordrequest('Consulting');
			
			/*
			## This if-branch is called, if an empty or wrong password was entered.
			## If the password is wrong, a notification is printed and the user gets the chance to try it again.
			*/
			if(empty($_POST['password']) OR $_POST['password']!==$consultingpassword){
				if(! empty($_POST['password'])){
					Settings::wrongpassword();
				}
				
				## This if-branch is only called during the process of enabling or disabling the protection ($_SESSION['protect_patient']).
				if(isset($_SESSION['protect_patient'])){
					$text='Please enter the Consulting password to authenticate your authorisation to set or unset protection for this patient';
				}
				
				
				## Otherwise, the user is asked for the password of consulting department to see the data.
				else{
					$text='This patient\'s data are password protected. Please enter the Consulting password to confirm your authorisation to access them';
				}
				
				## Is needed to remember IDs of protocol and patient during the authorisation process.
				$hidden_array=array('visit_ID'=>$visit_ID,'patient_ID'=>$patient_ID);
				Settings::popupPassword($thispage,$text,$hidden_array);
				exit();
			}
			
			/*
			## This if-branch is called, if the entered password is correct.
			## A new session is started, so that the user does not need to enter the password again for furhter actions.
			*/
			else{
				$_SESSION['password_consulting']=$_POST['password'];
			}
		}
		
		## Change the patient's protection status in datatbase, after the user is authorised and was changing it. 
		if(isset($_SESSION['password_consulting']) AND isset($_SESSION['protect_patient'])){
			if($visit->getProtect()==1){
				$visit->setProtect(0);
			}else{
				$visit->setProtect(1);
			}
			unset($_SESSION['protect_patient']);
		}
	}

	## Initialising variables of patient's general data and date of visit.
	$date=date('Y-m-d',strtotime($visit->getCheckin_time()));	
	if($date!==$today){
		$visitdate=date('d/m/y',strtotime($date));
	}else{
		$visitdate='today';
	}


	$name=$patient->getName();
	$sex=$patient->getSex();
	$age_exact=$patient->getAge(strtotime($visit->getCheckin_time()),'calculate');

	/*
	## Get data from database.
	## Inquire whether the patient has come to the facility before, if so initialise variable $ID_last with the ID of the previous visit.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/
	$querylast="SELECT visit_ID FROM visit WHERE patient_ID=$patient_ID AND checkout_time<='$date 00:00:00' ORDER BY checkout_time DESC LIMIT 0,1";
	$resultlast=mysqli_query($link,$querylast);

	if(mysqli_num_rows($resultlast)!==0){
		#$ID_last=mysqli_fetch_object($resultlast)->protocol_ID;
		$ID_last=mysqli_fetch_object($resultlast)->visit_ID;
	}

	/*
	## Get data from database.
	## Inquire whether the patient has come to the facility later on, if so initialise variable $ID_next with the ID of the next visit.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/
	$querynext="SELECT visit_ID FROM visit WHERE patient_ID=$patient_ID AND checkin_time>='$date 23:59:59' ORDER BY checkin_time ASC LIMIT 0,1";
	$resultnext=mysqli_query($link,$querynext);

	if(mysqli_num_rows($resultnext)!==0){
		#$ID_next=mysqli_fetch_object($resultnext)->protocol_ID;
		$ID_next=mysqli_fetch_object($resultnext)->visit_ID;
	}
	
	## Print patient's name as headline.
	echo"<h1>$name<br>";

	## In case the client has come before, print a link to that's visits summary.
	if(isset($ID_last)){
		echo '<a href="patient_visit.php?show=on&visit_ID='.$ID_last.'"><i class="fa fa-caret-left" aria-hidden="true"></i></a>';
	}
	
	## Print the date o
	echo "    $visitdate    ";

	## In case the client has come again later on, print a link to that's visits summary.
	if(isset($ID_next)){
		echo '<a href="patient_visit.php?show=on&visit_ID='.$ID_next.'"><i class="fa fa-caret-right" aria-hidden="true"></i></a>';
	}


	## Function for displaying patient's general data (like age,sex,...) is called and printed.
	echo '</h1><div class="inputform">'.$patient->display_general(strtotime($visit->getCheckin_time()));

	/*
	## Printing checkbox to enable or disable protection of patient's diagnosis
	## Check checkbox if patient's diagnosis is protected.
	*/	
	echo "<br>
			<a href='patient_visit.php?visit_ID=$visit_ID&patient_ID=$patient_ID&protect=on' style='float:left'>
				<input type='checkbox'";
				if($visit->getProtect()==1){
					echo "checked='checked'";
				}
				echo"></input> protected by password
			</a><br>
			</div>
		";



	/*
	## Initialising more variables.
	## Variable $primgiven defines, whether exactly one disease is tagged as primary disease.
	## Variable $stop prevents entries in database, if multiple primary diagnoses are selected.
	*/

	$primgiven=false;
	$stop=false;

	## Call this if-branch in case the user is submitting the selection of diagnoses.
	if(! empty($_POST['submit'])){

		## Create a new protocol entry to document the submitted diagnosis
		$protocol=Protocol::new_Protocol($visit_ID, "new diagnoses entered");
		$protocol_ID = $protocol->getProtocol_ID();

		## Initialise variables with submitted patient's complaints
		if(! empty($_POST['coughing'])){
			$Coughing = 1;
		}else{
			$Coughing = 0;
		}
		if(! empty($_POST['vomitting'])){
			$Vomitting = 1;
		}else{
			$Vomitting = 0;
		}
		if(! empty($_POST['fever'])){
			$Fever = 1;
		}else{
			$Fever = 0;
		}
		if(! empty($_POST['diarrhoea'])){
			$Diarrhoea = 1;
		}else{
			$Diarrhoea = 0;
		}
		if(! empty($_POST['others'])){
			$Others=$_POST['others'];
		}else{
			$Others='';
		}
		
		/*
		## Check, if already patient's complaints exist. 
		## If so, update these complaints with submitted diagnosis data.
		## Otherwise create a new patient's complaints record in database.
		*/
		If (Complaints::complaints_exist($visit_ID)){

			## Initialise variable containing object with patient's complaints on that visit.
			$complaints=new Complaints($visit_ID);

			## Set all the complaints to database as entered by the user. 
			$complaints->setCoughing($Coughing);
			$complaints->setVomitting($Vomitting);
			$complaints->setFever($Fever);
			$complaints->setDiarrhoea($Diarrhoea);
			$complaints->setOthers($Others);
		}
		else {
			$complaints=Complaints::new_Complaints($protocol_ID,$Coughing,$Vomitting,$Fever,$Diarrhoea,$Others);
		}

		/*
		## Get data from database.
		## Get all diseases, which are known within the system.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		*/
		$query = "SELECT Diagnosis_ID FROM diagnoses";
		$result = mysqli_query($link,$query);

		## The following loop will be run once for each of the output diseases from the database query.
		while($row = mysqli_fetch_object($result)){
			$Diagnosis_ID=$row->Diagnosis_ID;
			$Diagnosis = new Diagnoses($Diagnosis_ID);

			## This if-branch is called, if the current disease has been selected as a diagnosis (either primary, secondary or provisional).
			if (! empty($_POST["$Diagnosis_ID"]) OR ! empty($_POST["prov_$Diagnosis_ID"])){
				
				/*
				## Variable $importance is initialised with a figural value symbolising the importance of the diagnosis:
				## 		- 1 for primary diagnoses
				##		- 2 for secondary diagnoses
				##		- 3 for provisional diagnoses.
				*/
				if(! empty($_POST["$Diagnosis_ID"])){
					$importance=$_POST["$Diagnosis_ID"];
				}else{
					$importance=3;
				}
				

				/*
				## This if-branch is used to make sure only one primary diagnosis is selected. 
				## It creates the database entry for the diagnosis using the function new_Diagnosis_IDs.
				## Within this if-branch it is also checked whether the diagnosis was also selected as provisional diagnosis and if so, stored as such in database.
				*/
				if(! $primgiven OR $importance!==1){
					Diagnosis_IDs::new_Diagnosis_IDs($protocol_ID,$Diagnosis_ID,$importance);
					$protocol->setStaff_ID($_SESSION['staff_ID']);
					if($importance==1){
						$primgiven=true;
					}
				}

				/*
				## Check if there is more than one primary diagnosis selected.
				## If so, show warning and break the loop to prevent further database entries.
				*/
				else{
					$message="Please select only one primary diagnosis!";
					Settings::messagebox($message);
					$stop=true;
					break;
				}
			}
		}

		## In list of patient's diagnosed diseases is also defined, if a patient is coming for review of previous diagnosis.
		if(! empty($_POST['reattendance'])){
			Diagnosis_IDs::new_Diagnosis_IDs($protocol_ID,0,0);
		}

		/*
		## This if-branch is called, if the user is submitting a new or updated patient's diagnosis,
		## If the attendant is known, his or her name is documented in database
		*/
		if ($stop==false){
			$protocol->setStaff_ID($_SESSION['staff_ID']);
		}
	}

	## In the following, print content on the left hand side of the patient's diagnosis page.
	echo "<div class='columnleft'>";

	$vital_signs=Vital_Signs::display_admission_data($visit_ID);	
	if($vital_signs){
		echo "
			<details>
				<summary>
					<h2>Vital Signs</h2>
				</summary>
				$vital_signs
			</details>
			";
	}
	

	/*
	## Show content of patient's diagnosis page if
	## 		- a new or updated diagnosis was successfully submitted,
	##		- page is called in "display-mode" that is indicated by variable $_GET['show'].
	*/
	if((! empty($_POST['submit']) OR ! empty($_GET['show'])) AND $stop==false AND empty($_POST['search'])){

		/*
		## Check if any complaints for the patient have been stated on that visit.
		## If so, display them, using the function display_Complaints().
		*/
		$complaints=Complaints::display_Complaints($visit_ID);
		if(! empty($complaints)){
			echo "
				<details>
					<summary>
						<h2>Complaints</h2>
					</summary>
					$complaints
				</details>
				";
		}

		## In case of a new or updated diagnosis some information are added to database, if they are known.
		if(! empty($_POST['submit'])){
			
			## Add to database, whether the patient is pregnant or not.
			if (! empty($_POST['pregnant'])){
				$visit->setPregnant(1);
			}else{
				$visit->setPregnant(0);
			}
			
			
			## ???
			##TODO Flo
			##Muss auf visit_ID umgestellt werden 
			##Bleibt solange auskommentiert
			/*
			if(! empty($_POST['referral'])){
				if(! Referral::checkReferral($visit_ID)){
					Referral::new_Referral($protocol_ID,$_POST['referredto'],$_POST['refer_reason']);
				}else{
					$referral=new Referral(Referral::checkReferral($visit_ID));
					$referral->setDestination($_POST['referredto']);
					$referral->setReason($_POST['refer_reason']);
				}
			}else{
				if(Referral::checkReferral($visit_ID)){
					Referral::delete_Referral(Referral::checkReferral($visit_ID));
				}
			}
			*/

			/*
			## Check if the user selected the checkbox for referring a patient for nutrition management.
			## In case he did and the patient has no such entry for this visit yet, add an empty nutrition entry for the user.
			## In case the user is trying to delete an existing nutrition entry, jump into the else branch.
			*/
			if(! empty($_POST["refer_nutrition"]) AND ! $nutrition){
				$protocol=protocol::new_Protocol($visit_ID,'nutrition management');
				$protocol_ID=$protocol->getProtocol_ID();
				Nutrition::new_Nutrition($protocol_ID);
			}else if(empty($_POST['refer_nutrition']) AND $nutrition){

				## Initialise variable $nutrition_management with an object of the class Nutrition corresponding to the patient's visit.
				$nutrition_management=new Nutrition($protocol_ID);

				/*
				## Check if any data have been entered already for nutrition management, if so alert the user and prevent the deletion of the entry.
				## Otherwise delete the nutrition entry from the database.
				*/
				
				if(! empty($nutrition_management->getManagement()) OR ! empty($nutrition_management->getNutrition_remarks())){
					echo"
						<script>
							alert('Nutrition data have already been entered. Clean them manually first, if you are sure you want to delete them.')
						</script>
						";
				}else{
					$query="DELETE FROM nutrition WHERE protocol_ID=$protocol_ID";
					mysqli_query($link,$query);
					$nutrition=false;
				}
			}
			
			
			## Add any remarks from the consultant to the diagnosis.
			## TODO Flo: muss noch umgebaut werden
			## ToDo Flo: HIER HABE ICH AUFGEHÖRT
			## es muss in die Diagnosis_IDs.php eine neue Funktion
			## aufgenommen werden für get und set remarks
			## Diese Funktion muss dann hier zum Schreiben
			## Und weiter unten zum Lesen aufgerufen werden
			## bleibt solange auskommentiert
			if(! empty($_POST['remarks'])){
				#$protocol->setRemarks($_POST['remarks']);
				Diagnosis_IDs::setRemarks($protocol_ID, $_POST['remarks']);
			#}else{
			#	$protocol->setRemarks('');
			}
			

			## Add notice to database after the patient's treatment was tagged as completed.
			if(! empty($_POST['completed'])){
				$visit->setCheckout_time(date('Y-m-d H:i:s',time()));
			}else{
				$visit->setCheckout_time('0000-00-00 00:00:00');
			}
		}

		
		
		/*
		## Print result of patient's diagnosis,
		##		- if diseases (either primary or secondary) were diagnosed before,
		##		- if patient was referred to another hospital.
		## Variable $html contains all primary and secondary diseases, which were diagnosed for the patient.
		## Print also information about attendant and pregnancy, if known.
		*/
		if(!empty(Diagnosis_IDs::getDiagnosis_IDs($visit_ID)) OR Referral::checkReferral($visit_ID)){
			echo "
					<details open>
						<summary>
							<h2>Diagnoses</h2>
						</summary>
					";

			## TODO Flo: muss noch umgebaut werden
			## bleibt solange auskommentiert
			/*
			if(! empty($protocol->getStaff_ID())){
				$staff=new Staff($protocol->getStaff_ID());
				$staff_name=$staff->getName();
				echo"<h4>Attendant:</h4> ".$staff_name."<br><br>";
			}
			*/
			if($visit->getPregnant()==1){
				echo "Patient is <h4>pregnant</h4><br>";
			}
			if(Referral::checkReferral($visit_ID)){
				$referral=new Referral(Referral::checkReferral($visit_ID));
				echo "Patient has been <h4>referred</h4> to <b>".$referral->getDestination()."</b> because of ".$referral->getReason()."<br>";
			}
			$html=$visit->display_diagnoses('both',$visit_ID);
			echo $html;
		}

	}

	## Show input form in case the diagnosis still need's to be set.
	else{
		/*
		## Display a table head containing the most common complaints (coughing, vomitting, fever, diarrhoea) and a general category "others" for further complaints.
		## In a table row display checkboxes for the most common complaints and an input field for "others".
		## These input fields are to be prefilled with previously entered values, if available. 
		*/
		echo "
			<details open>
				<summary>
					<h2>Complaints</h2>
				</summary>
					<table>
						<tr>
							<th style='border-left:none'>
								Coughing
							</th>
							<th>
								Vomitting
							</th>
							<th>
								Fever
							</th>
							<th>
								Diarrhoea
							</th>
							<th>
								Others
							</th>
						</tr>";
		
						/*
						## Inquire whether complaits have already been noted.
						## If so, initialise variable $complaints with these previously stated complaints,
						*/
						if(Complaints::complaints_exist($visit_ID)==true){
							$complaints=new Complaints($visit_ID);
							echo "
								<tr>
									<form action='patient_visit.php?visit_ID=$visit_ID' method='post' autocomplete='off'>
										<td style='border-left:none'>
											<input type='checkbox' name='coughing' value='1'";
											if($complaints->getCoughing()==1){
												echo" checked='checked'";
											}
											echo">			
										</td>
										<td>
											<input type='checkbox' name='vomitting' value='1'";						
											if($complaints->getVomitting()==1){
												echo" checked='checked'";
											}
											echo">
										</td>
										<td>
											<input type='checkbox' name='fever' value='1'";
											if($complaints->getFever()==1){
												echo" checked='checked'";
											}
											echo">
										</td>
										<td>
											<input type='checkbox' name='diarrhoea' value='1'";
											if($complaints->getDiarrhoea()==1){
												echo" checked='checked'";
											}
											echo">
										</td>
										<td>
											<textarea name='others' length='1000' style=width:90px;height:25px>";	
											if(! empty($complaints->getOthers())){
												echo $complaints->getOthers();
											}
											echo"</textarea>
										</td>
								</tr>
							";

						}
						else{
							echo"
								<tr>
									<form action='patient_visit.php?patient_ID=$patient_ID&visit_ID=$visit_ID' method='post' autocomplete='off'>
									<td style='border-left:none'>
										<input type='checkbox' name='coughing' value='1'>			
									</td>
									<td>
										<input type='checkbox' name='vomitting' value='1'>
									</td>
									<td>
										<input type='checkbox' name='fever' value='1'>
									</td>
									<td>
										<input type='checkbox' name='diarrhoea' value='1'>
									</td>
									<td>
										<textarea name='others' length='1000' style=width:90px;height:25px></textarea>
									</td>
								</tr>
							";
						}
						echo "
							</table>
							</details>
						";
		
		## Print the name of the attendant.
		echo '<details ';
		if(empty($_GET['nutrition'])){
			echo "open";
		}
		echo'><summary><h2>Diagnoses</h2></summary>';
		if(! empty($_SESSION['staff_name'])){
			echo'<h4>Attendant:</h4> '.$_SESSION['staff_name'].'<br><br>';
		}

		/*
		## Inquire, whether patient is pregnant on women within the age range of 10 to 50.
		## Print a checkbox for pregnancy, which is checked, if patient was pregnant at the time of her last visit.
		*/
		if($sex=='female' AND $age_exact>=10 AND $age_exact<=50){
			$query="SELECT pregnant FROM visit WHERE patient_ID=$patient_ID ORDER BY checkin_time DESC LIMIT 1,1";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			
			if(! empty($object) AND $object->pregnant==1){
				$pregnant=1;
			}else{
				$pregnant=0;
			}
			
			echo"<input type='checkbox' name='pregnant'";
					if((empty($_POST['search']) AND ($pregnant==1 OR $visit->getPregnant()==1))OR (! empty($_POST['search']) AND (! empty($_POST['pregnant'])))){
						echo"checked='checked'";
					}
					echo"> pregnant<br>";
		}

		## Check if the patient has been referred and save information in variable $referred.
		$referred=Referral::checkReferral($visit_ID);
		
		/*
		## Inquire, whether patient was referred to another hospital.
		## Print a checkbox for referral, which is checked, if patient was referred.
		## If so, also display the input field for destination and reason of the referral.
		*/
		echo "<input type='checkbox'  id='unfold_item' onClick='unfold()' name='referral'";
		if ($referred OR ! empty($_POST['referral'])){
			echo "checked='checked'";
		}
		echo">
				refer
				<div id='unfold_content' style='margin-left:70px;";
				if (!$referred AND empty($_POST['referral'])){
					echo "display:none";
				}
				echo"'>
				<i>destination:</i> <br>
				<input type='text' maxlength='200' name='referredto' ";
				if ($referred OR ! empty($_POST['referredto'])){
					if(! empty($_POST['referredto'])){
						$destination=$_POST['referredto'];
					}else{
						$referral=new Referral($referred);
						$destination=$referral->getDestination();
					}
					echo "value='$destination'";
				}
				echo"style='width:300px'><br>
				<i>reason for referral:</i> <br>
				<textarea name='refer_reason' maxlength='1000'> ";
				if ($referred OR ! empty($_POST['refer_reason'])){
					if(! empty($_POST['refer_reason'])){
						echo $_POST['refer_reason'];
					}else{
						$referral=new Referral($referred);
						echo $referral->getReason();
					}
				}
				echo"</textarea>
				</div><br>";
		/*
		## Inquire, whether patient was referred for nutrition management.
		## Print a checkbox for referral, which is checked, if patient was referred.
		*/
		echo "<input type='checkbox' name='refer_nutrition'";
		if ($nutrition OR ! empty($_POST['refer_nutrition'])){
			echo "checked='checked'";
		}
		echo">
				nutrition management<br>";
		
		## Print checkbox for reattendance, which is checked if it was previously defined to be a review case. 
		echo"
				<input type='checkbox' name='reattendance' ";
				if (in_array(0,Diagnosis_IDs::getImportances($visit_ID)) OR ! empty($_POST['reattendance'])){
					echo "checked='checked''";
				}
		
		## Print input field and icon for searching a diagnosis as well as submit button for the diagnoses.
		echo"> reattendance<br><br>
				<div><input type='text' name='search' id='autocomplete' placeholder='search diagnosis' class='autocomplete'>
				<button type='submit' name='submitsearch'><i class='fas fa-search smallsearch'></i></button></div>
				<button type='submit' name='submit' value='submit'><i id='submitconsult' class='far fa-check-circle fa-4x'></i></button>";
		
		##Print tablehead for following table.
		Diagnoses::diagnoses_tablehead();
		
		/*
		## Get data from database.
		## Get the most frequent diseases.
		## Print these top five diseases.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		*/
		$query="SELECT top FROM departments WHERE Department like 'Consulting'";
		$result=mysqli_query($link,$query);
		$object=mysqli_fetch_object($result);
		$top_five=$object->top;
		$top_five_array=explode(',',$top_five);
		if(empty ($_POST['search'])){
			foreach($top_five_array AS $Diagnosis_ID){
				Diagnoses::diagnoses_tablerow($visit_ID,$Diagnosis_ID);
			}
			$searchpara='';
		}
		
		## This else-branch is called, if the user is searching for certain diseases and printing those that match his search.
		else{
			$query="SELECT * FROM diagnoses WHERE DiagnosisName like '%".$_POST['search']."%' ORDER BY DiagnosisName";
			$result=mysqli_query($link,$query);
			while($row=mysqli_fetch_object($result)){
				Diagnoses::diagnoses_tablerow($visit_ID,$row->Diagnosis_ID);
			}
			
			/*
			## $searchpara is set to exclude all the found search results from the following data-list,
			## so that the other diagnoses can be displayed below the search results.
			## (This is necessary to prevent a reset of all previously selected diseases.)
			*/
			$searchpara=" AND DiagnosisName not like '%".$_POST['search']."%'";
		}

		
		/*
		## Print all diagnosed diseases of a patient excepting these ones, which where searched before, to prevent double checkboxes (that would all have to be selected).
		## $lastClass indicates when a new class of diagnoses is started in the list, so that a new headline and table can be printed.
		## Variable $first is used to prevent the diagnosis-part of the page from being closed too early.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		*/
		$lastClass=0;
		$first=true;
		$query="SELECT * FROM diagnoses WHERE Diagnosis_ID not like '0' $searchpara ORDER BY DiagnosisClass,Diagnosis_ID";
		$result=mysqli_query($link,$query);
		while($row=mysqli_fetch_object($result)){
			$Class=$row->DiagnosisClass;
			$Name=$row->DiagnosisName;
			$Diagnosis_ID=$row->Diagnosis_ID;

			if($Class!==$lastClass){
				echo "</table>";
				if(! $first){
					echo"</details>";
				}else{
					$first=false;
				}
				echo"<details>
						<summary>
							<h4>$Class</h4>
						</summary>
						<br>
						";
				Diagnoses::diagnoses_tablehead();
			}
			if(! (in_array($Diagnosis_ID,$top_five_array)) OR ! empty($_POST['search'])){
				Diagnoses::diagnoses_tablerow($visit_ID,$Diagnosis_ID);
			}
			$lastClass=$Class;
		}
		
		/*
		## Print also a checkbox to tag a patient's treatment as completed
		## Furthermore print remarks of patient's diagnoses, if exist and
		## finally close the input form for diagnoses.
		*/
		echo'		
						</table>
					</details>
					Remarks / further specification of diagnosis:<br>
					<textarea name="remarks" maxlength="1000" style="min-width:500px">';
					$remarks = Diagnosis_IDs::getRemarks($visit_ID);
					if(! empty($remarks)){
						echo $remarks;
					}
					echo'</textarea><br>
					<input type="checkbox" name="completed" ';
					if($visit->getCheckout_time()!=='0000-00-00 00:00:00'){
						echo "checked='checked'";
					}
					echo"> <b>treatment in clinic completed</b>
				</form>
				";
	}
	
	/*
	## If page is not shown in "display-mode" (indicated by variable $_GET['show']), 
	## this branch prints a link to the list of patients in consulting.
	*/
	if(empty($_GET['show'])){
		echo '<a href="current_patients.php"><div class ="box">patients in consulting</div></a><br>';
	}
	echo "</details>";

	## The following content is displayed n the right side of the page.
	echo"
	</div>
	<div class='columnright'>
	";
	## If client has come for ANC, print a summary of her pregnancy's data and this particular ANC visit.

	$ANC_ID=ANC::check_ANC($visit_ID);
	if($ANC_ID){
		$ANC=new ANC($ANC_ID);
		$maternity_ID=$ANC->getmaternity_ID();
		$maternity=new Maternity($maternity_ID);
		echo"<details>
					<summary>
						<h2>ANC</h2>
					</summary>
				";
		echo
					$maternity->display_maternity('complete').
					$ANC->display_ANC('date off')."</details>
					<a href=\"complete_pregnancy.php?maternity_ID=$maternity_ID\"><div class ='box'>Pregnancy Overview</div></a>
				";
		
		/*
		## If page is not shown in "display-mode" (indicated by variable $_GET['show']), 
		## this branch prints a link, that can be used to edit the ANC visit, 
		## and one that leads to the list of patients in maternity.
		*/
		
		if(empty($_GET['show'])){
			echo"
					<a href=\"anc.php?ANC_ID=$ANC_ID&maternity_ID=$maternity_ID&visit_ID=$visit_ID\"><div class ='box'>edit ANC</div></a>
					<a href='maternity_patients.php'><div class ='box'>patients in maternity</div></a><br><br>
					";
		}
		echo"</details>";
	}
	

	## If the client came for delivery, print the delivery record and a link for editing it.
	$maternity_ID=Delivery::check_delivery($visit_ID);

	if($maternity_ID){
		echo"
				<details>
					<summary>
						<h2>Delivery</h2>
					</summary>
					".Delivery::display_delivery($maternity_ID,$visit_ID,'without vitals');
		if(empty($_GET['show'])){
			echo "<a href=\"delivery.php?visit_ID=$visit_ID&maternity_ID=$maternity_ID&edit=on\"><div class ='box'>edit Delivery</div></a>";
		}
		echo "</details>";
	}

	/*
	## This if-branch is called, if patient attended postnatal care.
	## In this case there is just a short note displayed.
	*/
	##TODO Flo: muss erst noch durch Liebste umgebaut werden
	## Bleibt solange noch auskommentiert
	/* 
	if($protocol->getPNC()==1){
		echo"
				<details>
					<summary>
						<h2>PNC</h2>
					</summary>
					Client attended Postnatal Care
				</details>
				";
	}
	*/

	/*
	## This if-branch is called, if information about a surgery exists.
	## In this case, information about procedure and charge are printed.
	*/
	##TODO Flo: muss erst noch umgebaut werden
	## Allerdings bislang unklar, wie es genau aussehen soll
	## Infos aus Ghana noch benötigt
	## Bleibt solange noch auskommentiert
	/* 
	$surgery=$protocol->getsurgery();
	if(! empty ($surgery)){
		$html=$protocol->display_surgery();
		echo"	
				<details>
					<summary>
						<h2>Surgery/Procedure</h2>
					</summary>
						$html
				</details>
				";
	}
	*/

	

	/*
	## Check, if the patient has been referred for lab investigations.
	## If so, print the lab number and the test results.
	## If not in "display-mode" (indicated by $_GET['show']), print a links for adding more tests and editing the results.
	*/
	$lab_number=$visit->getLab_number();
	if(! empty($lab_number)){
		$html=Lab::display_results($lab_number,'tooltips on');
		echo"
				<details>
					<summary>
						<h2>Tests</h2>
					</summary>
					<h4>Lab number: <u>$lab_number</u></h4><br>
					$html
				"; 
		

		## In case a file has been attached which contains lab information, display a link to this file. 
		$upload_array=Uploads::getUploadArray($visit_ID);
		if($upload_array){
			foreach($upload_array AS $ID){
				$upload=new Uploads($ID);
				if(! empty($upload->getFilename())){
					$upload_name=$upload->getFilename();
					if($upload->getDepartment_ID()==Departments::getDepartmentId('Laboratory')){
						echo '
						<br>
						<div class="tooltip">
							<a href="./uploads/'.$upload_name.'">
								<i class="fas fa-paperclip fa-2x"></i>
							</a>
							<span class="tooltiptext" style="line-height:normal">
								uploaded file:<br>
								'.$upload_name.'
							</span>
						</div>
						';
					}	
				}
			}
		}
		
		if(empty($_GET['show'])){
			echo"
					<a href=\"order_tests.php?patient_ID=$patient_ID&visit_ID=$visit_ID\"><div class =\"box\">add tests</div></a>
					<a href=\"lab.php?patient_ID=$patient_ID&visit_ID=$visit_ID&reset=on\"><div class =\"box\">reset test results</div></a>
					";
		}
		
		echo "</details>";
	}

	/*
	## Inquire whether any nutrition data are requested or saved for the patient.
	## If so call the function containing the corresponding code (Vital_Signs::nutrition_visit()).
	*/
	if($nutrition){
		$nutrition_entry=new Nutrition($nutrition);
		if(! empty($_GET['nutrition']) OR ($nutrition AND ((! empty($nutrition_entry->getNutrition_remarks()) OR ! empty($nutrition_entry->getManagement())) OR ! empty($_GET['edit'])))){
			Vital_Signs::nutrition_visit($nutrition, $age_exact);
		}
	}
	
	

	/*
	## If there were any, print a list of all prescribed drugs with information about drug name, amount, unit and dosage recommendation.
	## If not in "display-mode" (indicated by $_GET['show']), print a link for adding more drugs.
	*/

	
	$drugs_prescribed=Disp_Drugs::drugs_prescribed($visit_ID);

	if(! empty($drugs_prescribed)){
		echo"
				<details>
					<summary>
						<h2>Prescribed Drugs</h2>
					</summary>
				".Disp_Drugs::display_prescribed_drugs($visit_ID,'print','both');
		
		if(empty($_GET['show'])){
			echo"
					<a href=\"prescribe_drugs.php?visit_ID=$visit_ID\"><div class =\"box\">add prescriptions</div></a>
					";
		}
		echo "</details>";
	}
	echo "
			</div>
			<div class='fullscreen'>
			";

	## This if branch is called if you are not in "display-mode".
	if(empty($_GET['show'])){
		
		## If there haven't been prescribed any drugs so far, print link to prescribe drugs.
		if(empty($disp_drug_IDs)){
			echo "<a href='prescribe_drugs.php?patient_ID=$patient_ID&visit_ID=$visit_ID'><div class ='box'>prescribe drugs</div></a>";
		}
		
		/*
		## This if-branch is called, 
		## 		- if patient is female and in the age, in which a pregnancy is possible,
		##		- if no ANC for patient already exists,
		##		- and the maternity department is not deactivated in the DEFAULTS.php
		## If pregnancy is between week 32 and 45 (and no delivery records are available), a button to add a delivery is printed.
		*/
		if($sex=='female' AND $age_exact>=10 AND $age_exact<=50 AND empty($ANC_ID) AND in_array('Maternity',$DEPARTMENTS)){
			
			
			/*
			## Get client's pregnancy data from database.
			## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
			## If client has visited maternity in this facility, call the if-branch.
			*/
			$query="SELECT * FROM maternity WHERE patient_ID=$patient_ID ORDER BY maternity_ID DESC LIMIT 1";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			if(! empty($object)){
				$maternity_ID=$object->maternity_ID;
				echo "<a href=\"anc.php?maternity_ID=$maternity_ID&visit_ID=$visit_ID\"><div class ='box'>add ANC</div></a>";
			}else{
				echo "<a href=\"new_maternity_client.php?visit_ID=$visit_ID\"><div class ='box'>add ANC</div></a>";
			}

			$query="SELECT * FROM maternity WHERE patient_ID=$patient_ID ORDER BY maternity_ID DESC";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			if(! empty($object)){
				if(strtotime($object->conception_date)<=(time()-(3600*24*7*32)) AND strtotime($object->conception_date)>=(time()-(3600*24*7*45))){
					$maternity_ID=$object->maternity_ID;
					$query="SELECT * FROM delivery WHERE maternity_ID=$maternity_ID";
					$result=mysqli_query($link,$query);
					$object=mysqli_fetch_object($result);
					if(empty($object)){
						echo "<a href=\"delivery.php?maternity_ID=$maternity_ID&visit_ID=$visit_ID\"><div class ='box'>add Delivery</div></a>";

					}
				}
			}
		}
		
		/*
		## This if-branch is called, 
		##		-  if there haven't been ordered any lab tests so far
		##		- and the lab department is not deactivated in the DEFAULTS.php.
		## In this case link to order tests is printed.
		*/
		if(! $lab_number AND in_array('Laboratory',$DEPARTMENTS)){
			echo "<a href='order_tests.php?patient_ID=$patient_ID&visit_ID=$visit_ID'><div class ='box'>order tests</div></a>";
		}
		
		## If there hasn't been performed any surgery/procedure, print a link to do so.
		## ToDo Flo: muss erst noch umgebaut werden
		## Bleibt solange auskommentiert
		/*
		if(empty($protocol->getsurgery()) AND in_array('Surgery/Procedure',$DEPARTMENTS)){
			echo "<a href='surgery.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID'><div class ='box'>add surgery/procedure</div></a>";
		}
		*/
	}

	## If you are in "display-mode" or just submitted the diagnosis, there is a link printed to exit the "display-mode" and edit diagnoses again.
	if(! empty($_GET['show']) OR ! empty($_POST['submit'])){
		echo"<a href='patient_visit.php?patient_ID=$patient_ID&visit_ID=$visit_ID&edit=on'><div class ='box'>edit results</div></a></div>";
	}

	## Prints a button to create a pdf document of patient's diagnosis.
	echo"
			</div>
			<div class='tableright'>
				<a href='patient_visit_pdf.php?patient_ID=$patient_ID&visit_ID=$visit_ID'>create pdf</a>";

				if(isset($upload_array)){
					## In case a file has been attached which contains lab information, display a link to this file. 
					foreach($upload_array AS $ID){
						$upload=new Uploads($ID);
						if(! empty($upload->getFilename())){
							$upload_name=$upload->getFilename();
							if (strstr($upload_name,'pdf')){
								echo '<br>
									<br>
									<div class="tooltip" >
										<a href="./uploads/'.$upload_name.'" id="linkbutton">
											<i class="fas fa-file"></i>
										</a>
										<span class="tooltiptext " style="line-height:normal;">
											uploaded file:<br>
											'.$upload_name.'
										</span>
									</div>';
							}
						}
					}
				}
				echo"
			</div>
			";

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");		

?>
