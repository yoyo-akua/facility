<?php

	## Contains global variables and functions which are needed within this page.
	include("setup.php");

	/*
	## Initialise new object of maternity by a certain maternity-ID, with which the page is called.
	## This includes general functions and data for the pregnancy, that apply for each of the ANC visits.
	*/
	$maternity_ID=$_GET['maternity_ID'];
	$maternity=new maternity($maternity_ID);
	
	## Initialise new object of patient by patient-ID that is stored in $maternity.
	$patient_ID=$maternity->getpatient_ID();
	$patient=new Patient($patient_ID);
	

	## Variable $style contains any styling attributes that should apply for certain elements in the pdf file.
	$style='
			h1{text-align:center}
			';

	## Initialising variable with client's name.
	$name=$patient->getName();

	/*
	## In $patient_data all the data, that will be displayed on the right side of the letter head of the pdf file, are stored.
	## That includes the name, date of visit and general data of the person (like age,sex,...), 
	## which are called upon with the function $patient->display_general().
	*/
	$patient_data='
							<h1>'.$name.'</h1>'.
							$patient->display_general(time());
	/*
	## The function Settings::pdf_header() is used to create the letter head of the pdf file, which includes the facility's data and the logo.
	## Below that come the client's general pregnancy data (like EDD,parity,...), which are called with $maternity->display_maternity().
	## Variable $html is used to buffer any content that is to be displayed in the pdf file.
	*/
	$html = 
				Settings::pdf_header($style,$patient_data)
				.'<h1>Pregnancy Overview</h1>'.
				$maternity->display_maternity('complete')."
				<h2>ANC</h2>
				";

	/*
	## Get data from database.
	## Get all ANC visits that belong to this pregnancy.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/
	$query="SELECT ANC_ID FROM anc WHERE maternity_ID=$maternity_ID";
	$result=mysqli_query($link,$query);

	## The following loop will be run once for each of the output ANC visits from the database query.
	while($row=mysqli_fetch_object($result)){
		
		## Initialising object of ANC by ANC-ID.
		$ANC_ID=$row->ANC_ID;
		$ANC=new ANC($ANC_ID);

		/*
		## Get data from database.
		## Get protocol-ID for this particular ANC visit.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		*/
		$query2="SELECT * FROM protocol,anc WHERE anc.protocol_ID=protocol.protocol_ID AND anc.ANC_ID=$ANC_ID";
		$result2=mysqli_query($link,$query2);
		$object2=mysqli_fetch_object($result2);

		## Initialising object of visit by visit-ID.
		$visit_ID=$object2->visit_ID;
		$visit=new Visit($visit_ID);

		## Initialise variable $protocol_ID.
		$protocol_ID=$object2->protocol_ID;		

		## Add this particular visit's vital signs and ANC data to $html.		
		$html.=$ANC->display_ANC($protocol_ID,'date on').'
				<br><h4><u>Vital Signs</u></h4><br>'.(Vital_Signs::display_admission_data($visit_ID));

		
		## Function visit::() is called to check, if the patient has been referred for lab investigations, if so if-branch is opened.
		if($visit->getLab_number()){
			## Add the table head for the following table to $html.
			$html.="<h4><u>Lab Tests</u></h4><br>".Lab::result_tablehead();
			$last_test='';

			/*
			## Get data from database.
			## Get all the tests, that were performed on the patient and their results entered.
			## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
			*/			
			$query="SELECT * FROM lab,protocol WHERE visit_ID=$visit_ID AND protocol.protocol_ID=lab.protocol_ID_ordered AND test_results not like ''";
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
	}

	$html.="<br>";

	/*
	## Get data from database.
	## Get the protocol-ID for the delivery's ANC visit, if it exists.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/		
	$query="SELECT * FROM protocol,delivery WHERE delivery.maternity_ID like '$maternity_ID' AND protocol.protocol_ID=delivery.protocol_ID";
	$result=mysqli_query($link,$query);
	$object=mysqli_fetch_object($result);

	## Check if client has delivered, if so, call if-branch.
	if(! empty($object)){

		## Initialising object of protocol by protocol-ID.
		$protocol_ID=$object->protocol_ID;
		$protocol=new Protocol($protocol_ID);

		## Initialising variable with date of delivery.
		$date=date("d.m.Y",(strtotime($protocol->getTimestamp())));
		
		## Add the delivery's records to $html.
		$html.='<h2>'.$date.' - Delivery</h2>'.
					Delivery::display_Delivery($maternity_ID,$protocol_ID);
	}

	## Initialise variables for the name of the pdf and it's page format.
	$pdfName = "Pregnancy-Overview($name).pdf";
	$size='A4';

	## This function is creating the pdf file, using the data stored in $html as content.
	Settings::pdf_execute($pdfName,$size,$html);

?>
