<?php
	## Contains global variables and functions which are needed within this page.
	include("setup.php");
	
	
	## Initialise new object of patient by a certain patient-ID, with which the page is called
	$patient_ID=$_GET['patient_ID'];
	$patient=new Patient($patient_ID);

	## Initialise new object of protocol by a certain protocol-ID, with which the page is called
	$protocol_ID=$_GET['protocol_ID'];
	$protocol= new Protocol($protocol_ID);


	## This if-branch is called, if a patient's diagnosis is protected ($protocol->getprotect()==1) and the password hasn't been saved before.
	if(!(isset($_SESSION['password_Consulting'])) AND $protocol->getprotect()==1){
		$consultingpassword=Settings::passwordrequest('Consulting');

		/*
		## This if-branch is called, 
		##		-if there hasn't been entered a passwored so far
		##		-or the entered password is wrong
		*/
		if(empty($_POST['password']) OR $_POST['password']!==$consultingpassword){
			
			## Include the HTML/CSS structure to style the password request.
			include('HTMLParts/HTML_HEAD.php');

			## If wrong password was entered, offer retry.
			if(! empty($_POST['password'])){
				Settings::wrongpassword();
			}

			## Show password request and stop any further execution of the page.
			$text='This patient\'s data are password protected. Please enter the Consulting password to confirm your authorisation to access them';
			$hidden_array=array('protocol_ID'=>$protocol_ID,'patient_ID'=>$patient_ID);
			Settings::popupPassword($thispage,$text,$hidden_array);
			exit();
		}
		
		## If the entered password is correct, it is saved in the $_SESSION variable to prevent further password requests.
		else{
			$_SESSION['password_consulting']=$_POST['password'];
		}
	}


	## Variable $style contains any styling attributes that should apply for certain elements in the pdf file.
	$style='th{font-weight:bold}';

	## Initialising variables of patient's name and date of visit.
	$VisitDate=date("d/m/y",strtotime($protocol->getVisitDate()));
	$name = $patient->getName();

	/*
	## In $patient_data all the data, that will be displayed on the right side of the letter head of the pdf file, are stored.
	## That includes the name, date of visit and general data of the person (like age,sex,...), 
	## which are called upon with the function $patient->display_general().
	*/
	$patient_data="
							<h1>$name</h1><br>
							<b>Visit Date:</b> $VisitDate<br>
							<br>
							".$patient->display_general(strtotime($protocol->getVisitDate()));
	
	/*
	## The function Settings::pdf_header() is used to create the letter head of the pdf file, which includes the facility's data and the logo.
	## Below that comes a headline and the patient's vital signs, which are called upon with the function display_admission_data().
	## Variable $html is used to buffer any content that is to be displayed in the pdf file.
	*/
	$html = Settings::pdf_header($style,$patient_data)
				.'<h1 style="text-align:center"><u>Results</u></h1>'.
				(new Vital_Signs($protocol_ID))->display_admission_data();

	## Check if patient is pregnant, if so, add that information to $html.
	if($protocol->getpregnant()==1){
		$html.="<br>Patient is <b>pregnant</b><br>";
	}

	## Check if patient was referred, if so, add that information to $html.
	if(! empty($protocol->getreferral())){
		$referral=$protocol->getreferral();
		$html.="<br>Patient has been <b>referred</b> to <b>$referral</b><br>";
	}

	## Check if patient was diagnosed, if so add the attending medical officer and the list of primary and secondary diagnoses to $html.
	if(! empty(Diagnosis_IDs::getDiagnosis_IDs($protocol_ID))){
		$html.='
					<h2>Diagnoses</h2>
					<b>Attendant:</b> '.$protocol->getattendant().'<br>
					'.$protocol->display_diagnoses('both');	
	}

	## Initialising variable $ANC_ID, which indicates, if the client has come for ANC, for delivery or neither of them.
	$ANC_ID=$protocol->getANC_ID();
	
	/*
	## If the client has come for ANC ($ANC_ID not empty) and not delivery,
	## call upon function $maternity->display_maternity(), which puts a summary of her general maternity data out and
	## call upon function $ANC->display_ANC(), which puts a summary of this particular ANC visit's records out
	## and add them to $html.
	*/
	if(! empty($ANC_ID)){	
		$ANC=new ANC($ANC_ID);
		$maternity=new Maternity($ANC->getmaternity_ID());
		$html.="<h2>ANC</h2>".
					$maternity->display_maternity().
					$ANC->display_ANC($protocol_ID,'date off');
	}
	
	/*
	## This if-branch is called if the client delivered on that day. 
	## If so, the function Delivery::display_Delivery() is used add the records about the delivery to $html.
	*/
	if($protocol->getDelivery()!=0){
		$maternity_ID=$protocol->getDelivery();
		$html.="<h2>Delivery</h2>".Delivery::display_Delivery($maternity_ID,$protocol_ID);
	}

	## Check if client came for PNC, if so, add that information to $html.
	if($protocol->getPNC()==1){
		$html.="<h2>PNC</h2>Client attended Postnatal Care";
	}

	## Check if patient came for surgery, if so, the function Protocol::display_surgery() is used to add the particulars of the procedure to $html.
	$surgery=$protocol->getsurgery();
	if(! empty($surgery)){
		$html.="<h2>Surgery</h2>".$protocol->display_surgery();
	}

	## Check, if the patient has been referred for lab investigations, if so if-branch is opened.
	if(! empty($protocol->getLab_number())){
		## Add the table head for the following table to $html.
		$html.="<h2>Lab Tests</h2>".Lab::result_tablehead();
		$last_test='';
		/*
		## Get data from database.
		## Get all the tests, that were performed on the patient and their results entered.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		$query="SELECT * FROM lab WHERE protocol_ID=$protocol_ID AND test_results not like ''";
		$result=mysqli_query($link,$query);
		
		## The following loop will be run once for each of the output tests from the database query.
		while($row=mysqli_fetch_object($result)){
			## Initialise object of lab by lab-ID.
			$lab=new Lab($row->lab_ID);
			
			/*
			## The function $lab->result_table() adds the table of results to $html. 
			## Variable $last_test is used to determine, whenever parameters for a new test are beginning, so that the new test can be displayed as a headline.
			*/
			$html.=$lab->result_table($last_test);
			
			/*
			##These variables are defined for the only purpose of setting $last_test, 
			## which will be needed in the next run of the loop in the function $lab->result_table().
			*/
			$parameter_ID=$row->parameter_ID;

			$parameter= new Parameters($parameter_ID);
			$test_ID=$parameter->getTest_ID();

			$test=new Tests($test_ID);
			$test_name=$test->getTest_name();

			$last_test=$test_name;
		}
	}

	/*
	## If there were any, add a list of all prescribed drugs 
	## with information about drug name, amount, unit and dosage recommendation to $html.
	## Format this list, to a more pdf adapted style.
	*/
	$drugs_prescribed=Disp_Drugs::drugs_prescribed($protocol_ID);

	if(! empty($drugs_prescribed)){
		$drugs="<h2>Prescribed Drugs</h2>".Disp_Drugs::display_disp_drugs($protocol_ID,'print');
		$drugs=str_replace('<u>','',$drugs);
		$drugs=str_replace('</u>','',$drugs);
		$html.=$drugs;
	}

	## Initialise variables for the name of the pdf and it's page format.
	$pdfName = "Results-$name-$VisitDate.pdf";
	$size='A4';

	## This function is creating the pdf file, using the data stored in $html as content.
	Settings::pdf_execute($pdfName,$size,$html);

?>
