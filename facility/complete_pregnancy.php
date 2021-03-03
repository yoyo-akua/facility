<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	/*
	## Initialise new object of maternity by a certain maternity-ID, with which the page is called.
	## This includes general functions and data for the pregnancy, that apply for each of the ANC visits.
	*/
	$maternity_ID=$_GET['maternity_ID'];
	$maternity= new Maternity($maternity_ID);

	## Initialise new object of client by patient-ID that is stored in $maternity.
	$patient_ID=$maternity->getpatient_ID();
	$patient=new Patient($patient_ID);

	## Initialise variable with client's name.
	$name=$patient->getName();
	
	/*
	## Print headline and client's general data (like age,sex,...) with function $patient->display_general()
	## followed by the client's general pregnancy data (like EDD,parity,...), which are called with $maternity->display_maternity().
	*/
	echo"
			<h1>Pregnancy Overview</h1><h2 style='text-align:center'>of $name</h2>
			<div class='inputform'>
			<h2>General Data</h2>
			". $patient->display_general(time()).'<br>'.
			$maternity->display_maternity('complete')."
			<h2>ANC</h2>";


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
		
		## Print this particular visit's vital signs and ANC data.
		echo $ANC->display_ANC($protocol_ID,'date on');

		$vitals=Vital_Signs::display_admission_data($visit_ID);
		if($vitals){
			echo '<br><h4>Vital Signs</h4><div style="margin-left:10px">'.$vitals.'</div>';
		}
		
		/*
		## Check, if the patient has been referred for lab investigations.
		## If so, print the test results.
		*/
		if(! empty($visit->getLab_number())){
			echo"<h4>Test results</h4><div style='margin-left:10px'>".
					Lab::display_results($visit->getLab_number(),'tooltips on').'</div>';
		}
		
		## Print link to results of that day's visit.
		echo "<br><a href='patient_visit.php?show=on&visit_ID=$visit_ID'><i class='fas fa-external-link-alt'></i> complete visit summary</a>";
	}

	echo"<br>";

	/*
	## Get data from database.
	## Get the protocol-ID for the delivery's ANC visit if it exists.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/
	$query="SELECT * FROM protocol,delivery WHERE delivery.maternity_ID like '$maternity_ID' AND protocol.protocol_ID=delivery.protocol_ID";
	$result=mysqli_query($link,$query);
	$object=mysqli_fetch_object($result);

	## Call this if-branch, if the client delivered.
	if(! empty ($object)){
		
		## Initialising object of protocol by protocol-ID.
		$protocol_ID=$object->protocol_ID;
		$protocol=new Protocol($protocol_ID);

		## Initialising variable with date of delivery.
		$date=date("d/m/y",(strtotime($protocol->getTimestamp())));
		
		## Print the delivery's records and a link to results of that day's visit.
		echo"<h2>$date - Delivery</h2>".
				Delivery::display_delivery($maternity_ID,$protocol_ID).
				"<br><a href='patient_visit.php?show=on&patient_ID=$patient_ID&protocol_ID=$protocol_ID'>show complete visit summary</a>";
	}
	
	## Print link for creating a pdf file with the pregnancy overview.
	echo"
			</div>
			<div class='tableright'>
				<a href='complete_pregnancy_pdf.php?maternity_ID=$maternity_ID' id='linkbutton'>
					<div class='tooltip'>
						<i id='submitbutton' class='fas fa-file-download fa-2x'></i>
						<span class='tooltiptext'>
							create pdf
						</span>
					</div>
				</a>
			</div>
			";

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>
