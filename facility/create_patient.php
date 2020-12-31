<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	/*
	## This if-branch is called, if the form for adding a new patient is submitted
	*/
	if(! empty($_GET['submit'])){

		## Call this if-branch, if the OPD number was entered.
		if(! empty($_GET['OPD'])){
			/*
			## Get data from database.
			## Get OPD-number, which the user has filled by creating the new patient.
			## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
			## Save all data from database in $object.
			*/
			$query="SELECT OPD FROM patient WHERE OPD like '".$_GET['OPD']."'";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);

			## Check, if the filled OPD-number of the new patient is already used for another patient.
			if(! empty($object)){
				$message="This OPD Number is already used";
				exit($message);
			}
		}
		
		/*
			## Only infants below the age of three months can use their mother's NHIS.
			## Give the user a hint, if he tries to use NHIS of mother for an infant older than three months.
		*/
		if(! empty($_GET['Birthdate']) AND ! empty($_GET['NHISofMother'])){
			if($_GET['Birthdate']<date("Y-m-d",(time()-(30*24*3600*3)))){
				$message="Only infants below the age of three months can use their mother's NHIS";
				exit($message);
			}
		}
		
		/*
		## Initialising variables.
		## Set asdefault NHIS of mother ($nom = "0") and expired status ($exp = "0") to false,
		## As well as an empty blood group.
		*/
		$nom = "0";
		$exp = "0";
		$blood_group="";
		
		/*
		## NHIS of mother, expired status and blood group are optional data.
		## Override default values, if a parameter is defined by user.
		*/
		if(! empty($_GET['NHISofMother'])){
			$nom = "1";
		}
		if(! empty($_GET['Expired'])){ 
			$exp = "1";
		}
		if(! empty($_GET['blood_group'])){
			$blood_group=$_GET['blood_group'];
		}
		
		/*
		## Save all patient's data.
		## Variable $new_p = 0, if the checkbox 'Not new?' in browser is checked.
		## That means, the patient was already visiting the clinic in the past.
		## Variable $CCC (for CC Code) is stored in the protocol, because it differs with each of the patient's visits.		
		*/
		$patient = Patient::new_Patient(
			$_GET['Name'],
			$_GET['OPD'],
			$_GET['NHIS'],
			$_GET['Birthdate'],
			$_GET['Sex'],
			$_GET['Locality'],
			$nom,
			$blood_group
		);
		
		if(! empty($_GET['notnew'])){
			$new_p="0";
		}else{
			$new_p="1";
		}
		
		if(! empty($_GET['CCC'])){
			$CCC=$_GET['CCC'];
		}else{
			$CCC="";
		}
		
		## If there was an OPD number entered, update it in the database, so that the next one can be proposed correctly.
		if(! empty ($_GET['OPD'])){
			Settings::set_new_number('OPD',$_GET['OPD'],'');
		}
		
		/*
		## Initialise variable with the new patient's patient ID and use it to
		## create new protocol entry for the patient's visit, provided the patient is really present and the entry is not only serving the purpose of updating the archive files.
		## $_GET['onlylab'] checks if the patient comes only for (self-payed) lab tests.
		## If $_GET['onlylab'] is active, leads directly to order_tests.php, otherwise leads automatically back to search patient page.
		*/
		$patient_ID=$patient->getPatient_ID();
		
		if (empty ($_GET['notpresent'])){
			$visit = Visit::new_Visit($patient_ID,$new_p);
			$visit_ID= $visit->getVisit_ID();

			protocol::new_Protocol($visit_ID,'admission');

			if(! empty($_GET['CCC']) OR ! empty($_GET['Expired'])){
				$insurance=Insurance::new_Insurance($visit_ID,$CCC,$exp);
			}
		
			
			if(! empty($_GET['onlylab'])){
				$visit->setOnlylab(1);
				echo '<script>window.location.href=("order_tests.php?patient_ID='.$patient_ID.'&visit_ID='.$visit_ID.'")</script>';
			}else{
				echo '<script>window.location.href=("search_patient.php")</script>';
			}
		}
	}
	
	/*
	## This if-branch is called, if the user wants to create a new patient,
	## And before the user is able to fill the new patient's data into the formular fields in the browser.
	*/
	else{		
		## Initialising variable which calculates the next OPD number, using a function.
		$OPD_number=Settings::new_number('OPD','');
		
		/*
		## Print headline and search input fields for name, OPD-number, NHIS, expired status, Birthdate,
		## Sex, locality, NHIS of mother, blood group and not new status
		## CC Code as well as search symbol to browser.
		*/
		echo'
				<h1>Create Patient</h1>
			<div class="inputform">
				<form action="create_patient.php" method="get">
					<div><label>Full Name:</label><br>
					<input type=text name="Name" required></div>
					
					<div><label>OPD Number:</label><br>
					<input type="text" name="OPD" value="'.$OPD_number.'" pattern="'.$OPD_FORMAT.'"></div>
					
					<div><label>NHIS:</label><br>
					<input type="text" name="NHIS" pattern="[0-9]{8}"></div>
					
					<div><label>Expired?</label><br>
					<input type="checkbox" name="Expired"></div>			
					
					<div><label>Birthdate:</label><br>
					<input type="date" name="Birthdate" min="1901-01-01" max="'.$today.'" required></div>
					
					<div><label>Sex:</label><br>
					<input type="radio" name="Sex" value="male" required>Male
					<input type="radio" name="Sex" value="female" required>Female</div>   
				   
					<div><label>Locality:</label><br>
					<input type="text" name="Locality" required></div>			
					
					<div><label>NHIS is NHIS of mother?</label><br>
					<input type="checkbox" name="NHISofMother"></div>
					
					<div><label>Blood Group (if known):</label><br>
					<select name="blood_group">
						<option value=""></option>
						<option value="A+ Rh positive">A Rh positive</option>
						<option value="A Rh negative">A Rh negative</option>
						<option value="B Rh positive">B Rh positive</option>
						<option value="B Rh negative">B Rh negative</option>
						<option value="AB Rh positive">AB Rh positive</option>
						<option value="AB Rh negative">AB Rh negative</option>
						<option value="O Rh positive">O Rh positive</option>
						<option value="O Rh negative">O Rh negative</option>
					</select>
					</div>
					';
				if(strstr($today,$YEAR)){
					echo'
						<div><div class="tooltip">
								<label>Not New?</label>
								<span class="tooltiptext">
									Select the checkbox, if the patient has been here this year.
								</span>
							</div><br>
						<input type="checkbox" name="notnew"></div>';
				}
				echo'
					<div><div class="tooltip" style="line-height:normal">
							<label>Entering folder from archive?</label>
							<span class="tooltiptext" style="text-align:left">
								Select the checkbox, when adding old folder for a non-present patient,
								<br> which you are not using - it will not be displayed in the protocol,
								<br>(therefore it will rarely be possible to edit any of the patient\'s data afterwards
								<br> &rarr; please check twice before submitting).
							</span>
						</div><br>
					<input type="checkbox" name="notpresent"></div>
					<div><label>CCC:</label></div>
					<input class="smalltext" type="text" pattern="[0-9]{5}" name="CCC"></div>
					';
					if(! empty($_GET['onlylab'])){
						echo'<input type="hidden" name="onlylab" value="on">';
					}
					echo'
					<input type="hidden" name="token" value="'.$uniqueID.'">
					<div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="submit" value="Submit"></div>
				<form>
			</div>
		';
	}
	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");
	
?>
