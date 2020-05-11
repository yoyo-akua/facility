<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Initialising variables $patient_ID and $protocol_ID by which the page is called.
	$patient_ID=$_GET['patient_ID'];
	$protocol_ID=$_GET['protocol_ID'];

	## This if-branch is called if the user confirmed his intention to delete the patient from the protocol.
	if(! empty ($_GET['delete'])){	
		/* 
		## Establish connection to database.
		## Delete patient's visit and its data from the tables protocol, disp_drugs and lab.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		*/
		$query="DELETE FROM disp_drugs WHERE protocol_ID='$protocol_ID'";
		mysqli_query($link,$query);
		$query="DELETE FROM protocol WHERE protocol_ID = $protocol_ID";
		mysqli_query($link,$query);
		$query="DELETE FROM lab WHERE protocol_ID = $protocol_ID";
		mysqli_query($link,$query);
		Diagnosis_IDs::clean($protocol_ID);
		
		## Automatically lead user to "patient_protocol.php".
		echo "<script>window.location.href=('patient_protocol.php?from=$today&to=$today&newold_column=on&insured_column=on')</script>";
	}
	
	## This if-branch is called, if there are no further parameters sent with the page, that means when the user is calling the page in the first place.
	else{
		/*
		## Request, if the user is sure about its intention to delete the patient from the protocol.
		## Depending on the answer, lead either further to continue the deleting process,
		## otherwise just lead back to previous page.
		*/
		echo'<script type="text/JavaScript">;
				if(window.confirm("Do you really want to delete this patient from the protocol?")){
					window.location.href="delete_from_protocol.php?protocol_ID='.$protocol_ID.'&delete=on";
				}else{
					window.history.back();
				}
				</script>';
	}

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");		
			
?>
