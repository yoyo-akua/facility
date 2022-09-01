<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Initialising variables $patient_ID and $protocol_ID by which the page is called.
	$visit_ID=$_GET['visit_ID'];

	## This if-branch is called if the user confirmed his intention to delete the patient from the protocol.
	if(! empty ($_GET['delete'])){	
		/* 
		## Establish connection to database.
		## Delete patient's visit and its data from all the tables which contain data which is related to this specific visit.
		## That includes the tables: 
		## 		- disp_drugs
		##		- anc
		##		- vital_signs
		##		- complaints
		##		- uploads
		##		- nutrition
		##		- lab
		##		- diagnosis_ids
		##		- insurance
		##		- lab_list
		##		- protocol
		##		- visit
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		*/
		$query="DELETE p,x FROM protocol p 
					JOIN disp_drugs x
						ON x.prescription_protocol_ID=p.protocol_ID 
						OR x.given_protocol_ID=p.protocol_ID 
					WHERE p.visit_ID=$visit_ID ;
				DELETE p,x FROM protocol p 
					JOIN anc x
						ON x.protocol_ID=p.protocol_ID 
					WHERE p.visit_ID=$visit_ID ;
				DELETE p,x FROM protocol p 
					JOIN vital_signs x
						ON x.protocol_ID=p.protocol_ID 
					WHERE p.visit_ID=$visit_ID ;
				DELETE p,x FROM protocol p 
					JOIN complaints x
						ON x.protocol_ID=p.protocol_ID 
					WHERE p.visit_ID=$visit_ID ;
				DELETE p,x FROM protocol p 
					JOIN uploads x
						ON x.protocol_ID=p.protocol_ID 
					WHERE p.visit_ID=$visit_ID ;
				DELETE p,x FROM protocol p 
					JOIN nutrition x
						ON x.protocol_ID=p.protocol_ID 
					WHERE p.visit_ID=$visit_ID ;
				DELETE p,x FROM protocol p 
					JOIN lab x
						ON x.protocol_ID_ordered=p.protocol_ID 
						OR x.protocol_ID_results=p.protocol_ID
					WHERE p.visit_ID=$visit_ID ;
				DELETE p,x FROM protocol p 
					JOIN diagnosis_ids x
						ON x.protocol_ID=p.protocol_ID 
					WHERE p.visit_ID=$visit_ID ;

				DELETE FROM insurance WHERE visit_ID=$visit_ID ;
				DELETE FROM lab_list WHERE visit_ID=$visit_ID ;
				DELETE FROM protocol WHERE visit_ID=$visit_ID ;
				DELETE FROM visit WHERE visit_ID=$visit_ID ;";

		mysqli_multi_query($link,$query);
		
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
					window.location.href="delete_from_protocol.php?visit_ID='.$visit_ID.'&delete=on";
				}else{
					window.history.back();
				}
				</script>';
	}

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");		
			
?>
