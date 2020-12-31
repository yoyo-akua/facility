<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");
	
	/*
	## Initialising variables.
	## Variable $first is needed, if user is searching for more than one search parameter.
	## In that case it helps to identify the first search parameter.
	## This is necessary for creating a correct database query.
	## Variable $searchpara represents all search parameters a current search is based on.
	*/
	$first = true;
	$searchpara = "";

	/*
	## This if-branch is called, if the user is clicking on save button of a found patient.
	## That patient is added to system for treatment.
	*/
	if(! empty($_POST['patientAction'])){
		
		/*
		## Get data from database.
		## Get the patient for which the save button was clicked by user before.
		## This patient is identified by $patient_ID.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		## Save all data from database in $result.
		## Variable $treatmentStarted is true, when a patient was already admitted today some time before.
		## $new_p describes whether the patient has already come to the facility within this year.
		*/
		$patient_ID=$_POST['ID'];
		$query="SELECT * FROM visit WHERE patient_ID='$patient_ID' ORDER BY checkin_time DESC LIMIT 1";
		$result=mysqli_query($link,$query);
		$treatmentStarted=false;
		$new_p=1;
		
		/*
		## Identify the date of patient's last visit.
		## Verify if the patient was already visiting today.
		## In that case reset his treatment status (shows, that its treatment isn't finished yet).
		## Variable $today is defined in HTML_HEAD.php as global variable.
		## If the last visit of the patient was within this year, set $new_p to 0, to indicate it is an "old" patient.
		*/
		$object=mysqli_fetch_object($result);
		if(! empty($object)){
			$visit_ID=$object->visit_ID;
			$visit=new Visit($visit_ID);
			$VisitDate=date("Y-m-d",strtotime($visit->getCheckin_time()));
			
			if ($VisitDate==$today){
				$message="This patient has already been entered today. You can continue the treatment in Consulting";
				Settings::messagebox($message);
				
				$visit->setCheckout_time('0000-00-00 00:00:00');
				$treatmentStarted=true;
			}
			if(strstr($VisitDate,date("Y",time()))){
				$new_p=0;
			}
		}
		
		/*
		## This if-branch is called, if the patient is admitting for the first time today.
		## Add patient for treatment to system with all its data, which is shown in search patient table.
		## Variable $CCC (for CC Code) is stored in the protocol, because it differs with each of the patient's visits.
		## $_GET['onlylab'] checks if the patient comes only for (self-payed) lab tests.
		## If $_GET['onlylab'] is active, leads directly to order_tests.php.
		*/
		if(! $treatmentStarted){
			$patient = new Patient($patient_ID);
			
			$patient->setOPD_data(time());

			$patient_ID=$_POST['ID'];
			
			if(! empty($_POST['CCC'])){
				$CCC=$_POST['CCC'];
			}else{
				$CCC='';
			}
			if(! empty($_POST['Expired'])){
				$Expired=1;
			}else{
				$Expired=0;
			}
			if(strstr($today,$YEAR)){
				if(! empty($_POST['new_p'])){
					$new_p=1;
				}else{
					$new_p=0;
				}
			}
			$visit=Visit::new_Visit($patient_ID,$new_p);
			$protocol = Protocol::new_Protocol($visit->getVisit_ID(),'admission');
			if(! empty($_POST['onlylab'])){
				$visit->setOnlylab(1);
				echo "<script>window.location.href=('order_tests.php?patient_ID=$patient_ID&protocol_ID=$protocol->getProtocol_ID()')</script>";
			}
		}
	}

	## Call this if-branch if the user submitted a search.
	if(empty($_POST['patientAction'])){
		/*
		## This if-branch is called, if the user is searching a patient by its name.
		## The patient's name is added to variable $searchpara. 
		*/
		if(! empty($_POST['Name'])){
			$var = $_POST['Name'];
			if($first){
				$searchpara .= " NAME like '%$var%'";
				$first = false;
			}else{
			   $searchpara .= " AND NAME like '%$var%'"; 
			}
		}

		/*
		## This if-branch is called, if the user is searching a patient by its OPD-number.
		## The patient's OPD-number is added to variable $searchpara.
		*/
		if(! empty($_POST['OPD'])){
			$var = $_POST['OPD'];
			if($first){
				$searchpara .= " OPD like '%$var%'";
				$first = false;
			}else{
			   $searchpara .= " AND OPD like '%$var%'"; 
			}
		}

		/*
		## This if-branch is called, if the user is searching a patient by its NHIS.
		## The patient's NHIS is added to variable $searchpara.
		*/
		if(! empty($_POST['NHIS'])){
			$var = $_POST['NHIS'];
			if($first){
				$searchpara .= " NHIS like '%$var%'";
				$first = false;
			}else{
			   $searchpara .= " AND NHIS like '%$var%'"; 
			}
		}
	}

	## Print search input fields for name, OPD-number and NHIS as well as search symbol to browser.
	echo "<h1>Search Patient</h1>";
	echo'
		<div class="inputform">
		<form action="search_patient.php" method="post">
			<div><label>Name:</label><br>
			<input type="text" id="autocomplete" name="Name" class="autocomplete" autocomplete="off"><br></div>
				
			<div><label>OPD Number:</label><br>
			<input type="text" name="OPD"><br></div>
				
			<div><label>NHIS:</label><br>       
			<input type="text" name="NHIS"><br></div>
			';
			if(! empty($_GET['onlylab'])){
				echo'<input type="hidden" name="onlylab" value="on">';
			}
			echo'
			<button type="submit" name="search"><i class="fas fa-search" id="department_search"></i></button>
		</form>
		</div>
	';
	
	/*
	## This if-branch is called, if the user is searching a patient with one or more active search parameters,
	## that means, one or more checkboxes are checked.
	*/
	if (! $first){
		
		/*
		## Get data from database.
		## Get all patients regarding the search parameters in variable $searchpara.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		## Save all data from database in $result.
		*/
		$query = "SELECT `patient_ID` FROM `patient` WHERE $searchpara";
		$result = mysqli_query($link,$query);
		/*
		## If search result is not empty and patients are found,
		## Print a table with all found patients in browser.
		*/
		If (!mysqli_num_rows($result) == 0){
			Patient::tablehead();
						
			while($row = mysqli_fetch_object($result)){
				$patient = new Patient($row->patient_ID);
				
				/*
				## Only infants below the age of three months can use their mother's NHIS.
				## In that case, by printing patient's data in browser the NHIS of the mother will be deleted,
				## And the checkbox 'NHIS is NHIS of mother' will be unchecked.
				*/
				if(($patient->getAge(time(),'calculate'))>0.25 AND $patient->getNHISofMother()==1){
					$patient->setNHISofMother(0);
					$patient->setNHIS("");
				}
				$patient->tablerow();
			}
			Patient::tablebottom();
		}	
		
		## Print button 'create this patient' in browser.
		echo '<a href="create_patient.php';
			if(! empty($_POST['onlylab'])){
				echo'?onlylab=on';
			}
			echo'"><div class ="box">create patient</div></a><br>';
	}

	## contains HTML/CSS structure, which styles the graphical user interface in the browser
	include("HTMLParts/HTML_BOTTOM.php");		

?>
