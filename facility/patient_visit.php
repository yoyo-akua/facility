<?php
	/*
	## Contains global variables which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");


	## Initialise new object of patient by a certain patient-ID, with which the page is called.
	$patient_ID=$_GET['patient_ID'];
	$patient=new Patient($patient_ID);

	## Initialise new object of protocol by a certain protocol-ID, with which the page is called.
	$protocol_ID=$_GET['protocol_ID'];
	$protocol= new Protocol($protocol_ID);


	/*
	## Check, whether the patient's diagnosis is protected.
	## In Case of enabling or disabling protection, the user needs to enter the password of consulting department.
	## Otherwise the user is not allowed to enable or disable the protection of a patient's diagnosis.
	## This if-branch is called, 
	##		- if user wants to protect a patient's diagnosis ($_GET['protect'] or $_POST['password'] not empty),
	##		- if a patient's diagnosis is protected ($protocol->getprotect()==1).
	*/
	if(! empty($_GET['protect']) OR ! empty($_POST['password']) OR $protocol->getprotect()==1){
		
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
				$hidden_array=array('protocol_ID'=>$protocol_ID,'patient_ID'=>$patient_ID);
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
			if($protocol->getprotect()==1){
				$protocol->setprotect(0);
			}else{
				$protocol->setprotect(1);
			}
			unset($_SESSION['protect_patient']);
		}
	}

	## Initialising variables of patient's general data and date of visit.
	$date=date('Y-m-d',strtotime($protocol->getVisitDate()));	
	if($date!==$today){
		$visitdate=date('d/m/y',strtotime($date));
	}else{
		$visitdate='today';
	}


	$name=$patient->getName();
	$sex=$patient->getSex();
	$age_exact=$patient->getAge(strtotime($protocol->getVisitDate()),'calculate');

	/*
	## Get data from database.
	## Inquire whether the patient has come to the facility before, if so initialise variable $ID_last with the ID of the previous visit.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/
	$querylast="SELECT protocol_ID FROM protocol WHERE patient_ID=$patient_ID AND protocol_ID!=$protocol_ID AND VisitDate<='$date' ORDER BY VisitDate DESC LIMIT 0,1";
	$resultlast=mysqli_query($link,$querylast);
	if(mysqli_num_rows($resultlast)!==0){
		$ID_last=mysqli_fetch_object($resultlast)->protocol_ID;
	}

	/*
	## Get data from database.
	## Inquire whether the patient has come to the facility later on, if so initialise variable $ID_next with the ID of the next visit.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/
	$querynext="SELECT protocol_ID FROM protocol WHERE patient_ID=$patient_ID AND protocol_ID!=$protocol_ID AND VisitDate>='$date' ORDER BY VisitDate ASC LIMIT 0,1";
	$resultnext=mysqli_query($link,$querynext);
	if(mysqli_num_rows($resultnext)!==0){
		$ID_next=mysqli_fetch_object($resultnext)->protocol_ID;
	}
	
	## Print patient's name as headline.
	echo"<h1>$name<br>";

	## In case the client has come before, print a link to that's visits summary.
	if(isset($ID_last)){
		echo '<a href="patient_visit.php?show=on&protocol_ID='.$ID_last.'&patient_ID='.$patient_ID.'"><i class="fa fa-caret-left" aria-hidden="true"></i></a>';
	}
	
	## Print the date o
	echo "    $visitdate    ";

	## In case the client has come again later on, print a link to that's visits summary.
	if(isset($ID_next)){
		echo '<a href="patient_visit.php?show=on&protocol_ID='.$ID_next.'&patient_ID='.$patient_ID.'"><i class="fa fa-caret-right" aria-hidden="true"></i></a>';
	}


	## Function for displaying patient's general data (like age,sex,...) is called and printed.
	echo '</h1><div class="inputform">'.$patient->display_general(strtotime($protocol->getVisitDate()));

	/*
	## Function for displaying patient's vital signs is called and printed,
	## as well as checkbox to enable or disable protection of patient's diagnosis
	## Check checkbox if patient's diagnosis is protected.
	*/
	echo "<br>
				<a href='patient_visit.php?protocol_ID=$protocol_ID&patient_ID=$patient_ID&protect=on' style='float:left'>
					<input type='checkbox'";
					if($protocol->getprotect()==1){
						echo "checked='checked'";
					}
					echo"></input> protected by password
				</a><br>
				".(new Vital_Signs($protocol_ID))->display_admission_data($patient)."
			</div>
			";
	/*
	## Initialising more variales.
	## Variable $attendant contains the name of the person, who diagnoses the patient.
	## Variable $primgiven defines, whether exactly one disease is tagged as primary disease.
	## Variable $stop prevents entries in database, if multiple primary diagnoses are selected.
	*/
	$attendant=$protocol->getAttendant();

	$primgiven=false;
	$stop=false;
	
	## Call this if-branch in case the user is submitting the selection of diagnoses.
	if(! empty($_POST['submit'])){
		
		## Delete all previously entered diagnoses from the database.
		Diagnosis_IDs::clean($protocol_ID);

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
			/*
			## This if-branch is called, if a new or updated patient's diagnoses contain primary diagnoses.
			## These diseases are tagged by suffix "(1)".
			## They are added in list of patient's diagnosed diseases ($insert)
			*/		
			if (! empty($_POST["prim_$Diagnosis_ID"])){
				## This if-branch saves the first primary diagnosis in $insert.
				if(! $primgiven){
					Diagnosis_IDs::new_Diagnosis_IDs($protocol_ID,$Diagnosis_ID,1);
					$primgiven=true;
				}

				/*
				## Check if there is more than one primary diagnosis selected.
				## If so, show warning, and by setting $stop=true, prevent database entries.
				*/
				else{
					$message="Please select only one primary diagnosis!";
					Settings::messagebox($message);
					$stop=true;
					break;
				}
			}

			/*
			## This if-branch is called, if a new or updated patient's diagnoses contain one or more secondary diagnosis.
			## These diseases are tagged by suffix "(2)".
			## They are added in list of patient's diagnosed diseases ($insert)
			*/
			else if (! empty($_POST["sec_$Diagnosis_ID"]) AND ! $stop){
				Diagnosis_IDs::new_Diagnosis_IDs($protocol_ID,$Diagnosis_ID,2);
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
			if(! empty($_POST['attendant'])){
				$attendant=$protocol->setAttendant($_POST['attendant']);
			}
		}
	}

	## In the following, print content on the left hand side of the patient's diagnosis page.
	echo "<div class='columnleft'>";

	/*
	## Show content of patient's diagnosis page if
	## 		- a new or updated diagnosis was successfully submitted,
	##		- page is called in "display-mode" that is indicated by variable $_GET['show'].
	*/
	if((! empty($_POST['submit']) OR ! empty($_GET['show'])) AND $stop==false AND empty($_POST['search'])){

		## In case of a new or updated diagnosis some information are added to database, if they are known.
		if(! empty($_POST['submit'])){
			
			## Add to database, whether the patient is pregnant or not.
			if (! empty($_POST['pregnant'])){
				$protocol->setpregnant(1);
			}else{
				$protocol->setpregnant(0);
			}
			
			## Add to database, whether the patient is referred to another hospital and the name this hospital.
			if(! empty($_POST['referral']) AND ! empty($_POST['referredto'])){
				$protocol->setreferral($_POST['referredto']);
			}else{
				$protocol->setreferral('');
			}
			
			## Add any remarks from the consultant to the diagnosis.
			if(! empty($_POST['remarks'])){
				$protocol->setRemarks($_POST['remarks']);
			}else{
				$protocol->setcompleted('');
			}
			
			## Add notice to database after the patient's treatment was tagged as completed.
			if(! empty($_POST['completed'])){
				$protocol->setcompleted(1);
			}else{
				$protocol->setcompleted(0);
			}
		}
		
		/*
		## Print result of patient's diagnosis,
		##		- if diseases (either primary or secondary) were diagnosed before,
		##		- if patient was referred to another hospital.
		## Variable $html contains all primary and secondary diseases, which were diagnosed for the patient.
		## Print also information about attendant and pregnancy, if known.
		*/
		$html=$protocol->display_diagnoses('both',$protocol_ID);
		if(!empty(Diagnosis_IDs::getDiagnosis_IDs($protocol_ID)) OR ! empty($protocol->getreferral())){
			echo "
					<details open>
						<summary>
							<h2>Diagnoses</h2>
						</summary>
					";

			if(! empty($attendant)){
				echo"<h4>Attendant:</h4> $attendant<br>";
			}
			if($protocol->getpregnant()==1){
				echo "Patient is <h4>pregnant</h4><br>";
			}
			if(! empty($protocol->getreferral())){
				$referral=$protocol->getreferral();
				echo "Patient has been <h4>referred</h4> to <h4>$referral</h4><br>";
			}
			echo $html;
		}

	}

	## Show input form in case the diagnosis still need's to be set.
	else{

		/*
		## Print "Diagnoses" headline, if the user is calling the page as a nutrition officer, hide the list of diagnoses and the consultant's information.
		## Identify last known attendant.
		## Print an input field for attendant, prefill it with the name of last known attendant.
		*/
		if(! empty($attendant)){
			$lastattendant=$attendant;
		}else{
			$query="SELECT attendant FROM protocol WHERE attendant not like '' AND attendant not like 'Midwife' ORDER BY protocol_ID DESC LIMIT 1";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			$lastattendant=$object->attendant;
		}
		echo '<details ';
		if(empty($_GET['nutrition'])){
			echo "open";
		}
		echo'
				><summary><h2>Diagnoses</h2></summary>
				<form action="patient_visit.php?patient_ID='.$patient_ID.'&protocol_ID='.$protocol_ID.'" method="post" autocomplete="off">
				<b>Attendant: <input type="text" name="attendant" 
				';
					if(! empty($_POST['attendant'])){
						echo 'value="'.$_POST['attendant'].'"';
					}else{
						echo 'value="'.$lastattendant.'"';
					}
					echo '>
				<br><br>
				';

		/*
		## Inquire, whether patient is pregnant on women within the age range of 10 to 50.
		## Print a checkbox for pregnancy, which is checked, if patient was pregnant at the time of her last visit.
		*/
		if($sex=='female' AND $age_exact>=10 AND $age_exact<=50){
			$query="SELECT pregnant FROM protocol WHERE patient_ID=$patient_ID ORDER BY VisitDate DESC LIMIT 1,1";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			
			if(! empty($object) AND $object->pregnant==1){
				$pregnant=1;
			}else{
				$pregnant=0;
			}
			
			echo"<input type='checkbox' name='pregnant'";
					if((empty($_POST['search']) AND ($pregnant==1 OR $protocol->getpregnant()==1))OR (! empty($_POST['search']) AND (! empty($_POST['pregnant'])))){
						echo"checked='checked'";
					}
					echo"> pregnant<br>";
		}

		## Check if the patient has been referred and save information in variable $referred.
		$referred=$protocol->getreferral();
		
		/*
		## Inquire, whether patient was referred to another hospital.
		## Print a checkbox for referral, which is checked, if patient was referred.
		*/
		echo "<input type='checkbox' name='referral'";
		if ($referred OR ! empty($_POST['referral'])){
			echo "checked='checked'";
		}
		echo">
				referred</b> 
				(to: <input type='text' name='referredto' ";
				if ($referred OR ! empty($_POST['referredto'])){
					if(! empty($_POST['referredto'])){
						$referral=$_POST['referredto'];
					}else{
						$referral=$protocol->getreferral();
					}
					echo "value='$referral'";
				}
		
		## Print checkbox for reattendance, which is checked if it was previously defined to be a review case. 
		echo"style='margin:0px'>)<br>
		
				<input type='checkbox' name='reattendance' ";
				if (in_array(0,Diagnosis_IDs::getImportances($protocol_ID)) OR ! empty($_POST['reattendance'])){
					echo "checked='checked''";
				}
		
		## Print input field and icon for searching a diagnosis as well as submit button for the diagnoses.
		echo"> <b>reattendance</b><br><br>
				<div><input type='text' name='search' id='autocomplete' placeholder='search diagnosis' class='autocomplete'>
				<button type='submit' name='submitsearch'><i class='fas fa-search smallsearch'></i></button></div>

				<input type='submit' name='submit' value='submit diagnoses'>
				";
		
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
				Diagnoses::diagnoses_tablerow($protocol_ID,$Diagnosis_ID);
			}
			$searchpara='';
		}
		
		## This else-branch is called, if the user is searching for certain diseases and printing those that match his search.
		else{
			$query="SELECT * FROM diagnoses WHERE DiagnosisName like '%".$_POST['search']."%' ORDER BY DiagnosisName";
			$result=mysqli_query($link,$query);
			while($row=mysqli_fetch_object($result)){
				Diagnoses::diagnoses_tablerow($protocol_ID,$row->Diagnosis_ID);
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
				Diagnoses::diagnoses_tablerow($protocol_ID,$Diagnosis_ID);
			}
			$lastClass=$Class;
		}
		
		/*
		## Print also a checkbox to tag a patient's treatment as completed
		## and close the input form for diagnoses.
		*/
		echo'		
						</table>
					</details>
					Remarks / further specification of diagnosis:<br>
					<textarea name="remarks" maxlength="1000" style="min-width:500px">';
					if(! empty($protocol->getRemarks())){
						echo $protocol->getRemarks();
					}
					echo'</textarea><br>
					<input type="checkbox" name="completed" ';
					if($protocol->getCompleted()!=0){
						echo "checked='checked'";
					}
					echo'> <b>treatment in clinic completed</b>
				</form>
				';
	}
	
	/*
	## If page is not shown in "display-mode" (indicated by variable $_GET['show']), 
	## this branch prints a link to the list of patients in consulting.
	*/
	if(empty($_GET['show'])){
		echo '<a href="current_patients.php"><div class ="box">patients in consulting</div></a><br>';
	}
	echo "</details>";


	## If client has come for ANC, print a summary of her pregnancy's data and this particular ANC visit.
	$ANC_ID=$protocol->getANC_ID();
	if(! empty($ANC_ID)){
		$ANC=new ANC($ANC_ID);
		$maternity_ID=$ANC->getmaternity_ID();
		$maternity=new Maternity($maternity_ID);
		echo"<details>
					<summary>
						<h2>ANC</h2>
					</summary>
				";
		echo
					$maternity->display_maternity().
					$ANC->display_ANC($protocol_ID,'date off')."
					<a href=\"complete_pregnancy.php?maternity_ID=$maternity_ID\"><div class ='box'>Pregnancy Overview</div></a>
				";
		
		/*
		## If page is not shown in "display-mode" (indicated by variable $_GET['show']), 
		## this branch prints a link, that can be used to edit the ANC visit, 
		## and one that leads to the list of patients in maternity.
		*/
		if(empty($_GET['show'])){
			echo"
					<a href=\"anc.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID&edit=$ANC_ID\"><div class ='box'>edit ANC</div></a>
					<a href='maternity_patients.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID'><div class ='box'>patients in maternity</div></a>
					";
		}
		echo"</details>";
	}

	## If the client came for delivery, print the delivery record and a link for editing it.
	if($protocol->getDelivery()!=0){
		$maternity_ID=$protocol->getDelivery();
		echo"
				<details>
					<summary>
						<h2>Delivery</h2>
					</summary>
					".Delivery::display_delivery($maternity_ID,$protocol_ID);
		if(empty($_GET['show'])){
			echo "<a href=\"delivery.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID&maternity_ID=$maternity_ID&edit=on\"><div class ='box'>edit Delivery</div></a>";
		}
		echo "</details>";
	}

	/*
	## This if-branch is called, if patient attended postnatal care.
	## In this case there is just a short note displayed.
	*/
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


	/*
	## This if-branch is called, if information about a surgery exists.
	## In this case, information about procedure and charge are printed.
	*/
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

	## The following content is displayed n the right side of the page.
	echo"
			</div>
			<div class='columnright'>
			";

	/*
	## Check, if the patient has been referred for lab investigations.
	## If so, print the lab number and the test results.
	## If not in "display-mode" (indicated by $_GET['show']), print a links for adding more tests and editing the results.
	*/
	$lab_number=$protocol->getLab_number();
	if(! empty($lab_number)){
		$html=Lab::display_results($protocol_ID,'tooltips on');
		echo"
				<details>
					<summary>
						<h2>Tests</h2>
					</summary>
					<h4>Lab number: <u>$lab_number</u></h4><br>
					$html
				"; 
		if(empty($_GET['show'])){
			echo"
					<a href=\"order_tests.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID\"><div class =\"box\">add tests</div></a>
					<a href=\"lab.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID&reset=on\"><div class =\"box\">reset test results</div></a>
					";
		}
		echo "</details>";
	}

	/*
	## Check, if the patient has been referred for lab investigations.
	## If so, print the lab number and the test results.
	## If not in "display-mode" (indicated by $_GET['show']), print a links for adding more tests and editing the results.
	*/
	$lab_number=$protocol->getLab_number();
	if(! empty($_GET['nutrition'])){
		$html=Lab::display_results($protocol_ID,'tooltips on');
		echo"
				<details open>
					<summary>
						<h2>Nutrition Treatment</h2>
					</summary>
					<h3>Measurements</h3>
					<table>
						<tr>
							<th style='border-left:none'>
								MUAC
							</th>
							<th>
								Height
							</th>
							<th>
								Weight
							</th>
							<th>
								<div class='tooltip'>
									BMI
									<span class='tooltiptext' id='BMIflag' style='display:none'>
									</span>
								</div>
							</th>
						</tr>
						<tr>
							<td style='border-left:none'>
								<input type='number' name='MUAC' min='0' step='0.1'> cm
							</td>
							<td>
								<input type='number' name='height' min='0' step='0.1' oninput='PrefillBMI()'> cm
							</td>
							<td>
								<input type='number' name='weight' min='0' step='0.1' oninput='PrefillBMI()'> kg
							</td>
							<td id='BMI'>
							</td>
						</tr>
					</table>
				"; 
		if(empty($_GET['show'])){
			echo"
					";
					# Hier Link zu nutrition patients Seite @todo
		}else{
			# Hier Übersicht über Nutrition Daten @todo
		}
		echo "</details>";
	}

	/*
	## If there were any, print a list of all prescribed drugs with information about drug name, amount, unit and dosage recommendation.
	## If not in "display-mode" (indicated by $_GET['show']), print a link for adding more drugs.
	*/
	$drugs_prescribed=Disp_Drugs::drugs_prescribed($protocol_ID);

	if(! empty($drugs_prescribed)){
		echo"
				<details>
					<summary>
						<h2>Prescribed Drugs</h2>
					</summary>
				".Disp_Drugs::display_disp_drugs($protocol_ID,'print');
		
		if(empty($_GET['show'])){
			echo"
					<a href=\"prescribe_drugs.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID\"><div class =\"box\">add prescriptions</div></a>
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
			echo "<a href='prescribe_drugs.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID'><div class ='box'>prescribe drugs</div></a>";
		}
		
		/*
		## This if-branch is called, 
		## 		- if patient is female and in the age, in which a pregnancy is possible,
		##		- if no ANC for patient already exists,
		##		- and the maternity department is not deactivated in the DEFAULTS.php
		## If pregnancy is between week 32 and 45 (and no delivery records are available), a button to add a delivery is printed.
		*/
		if($sex=='female' AND $age_exact>=10 AND $age_exact<=50 AND empty($ANC_ID) AND in_array('Maternity',$DEPARTMENTS)){
			echo "<a href='anc.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID'><div class ='box'>add ANC</div></a>";
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
						echo "<a href='delivery.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID&maternity_ID=$maternity_ID'><div class ='box'>add Delivery</div></a>";
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
			echo "<a href='order_tests.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID'><div class ='box'>order tests</div></a>";
		}
		
		## If there hasn't been performed any surgery/procedure, print a link to do so.
		if(empty($protocol->getsurgery()) AND in_array('Surgery/Procedure',$DEPARTMENTS)){
			echo "<a href='surgery.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID'><div class ='box'>add surgery/procedure</div></a>";
		}
	}

	## If you are in "display-mode" or just submitted the diagnosis, there is a link printed to exit the "display-mode" and edit diagnoses again.
	if(! empty($_GET['show']) OR ! empty($_POST['submit'])){
		echo"<a href='patient_visit.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID'><div class ='box'>edit results</div></a></div>";
	}

	## Prints a button to create a pdf document of patient's diagnosis.
	echo"
			</div>
			<div class='tableright'><a href='patient_visit_pdf.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID'>create pdf</a></div>
			";

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");		

?>
