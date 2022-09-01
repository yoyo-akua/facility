<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Initialising variable $patient_ID by which the page is called.
	if(! empty($_GET['patient_ID'])){
		$patient_ID=$_GET['patient_ID'];
	}else{
		$patient_ID=$_POST['patient_ID'];
	}
	
	/*
	## This if-branch is called if the user confirmed his intention to delete the patient completely from the database
	## and displays a password request.
	*/
	if(! empty ($_GET['delete'])){
		/* 
		## Initialising variables, that are needed for a function, that displays a password request.
		## $page describes which page should be called by the function.
		## $text is the text, that is to be displayed with the password request.
		## $hidden_array includes the parameters that should be sent along side the password request (apart from the entered password). 
		*/
		$page='delete_from_database.php';
		$text="You need to enter the OPD password to delete this patient completely from the database";
		$hidden_array=array('patient_ID'=>"$patient_ID");
		
		## This is the function calling the password request.
		Settings::popupPassword($page,$text,$hidden_array);
		
	}

	## This if-branch is called, if a password has been entered by the user. 
	else if(! empty($_POST['password'])){
		
		/*
		## Get the correct password for the OPD department.
		## Save this in $rightpassword.
		## Save the password entered by the user in $password.
		*/
		$rightpassword=Settings::passwordrequest('OPD');
		$password=$_POST['password'];
		
		## If the correct password $rightpassword and the one entered by the user $password are matching, call this if-branch.
		if($password==$rightpassword){
			
			## Initialise empty variable $query which is used later on to create the database querys which delete the datasets.
			$query='';

			/*
			## Create $tables_array as an array containing all tables 
			## in which the dataset linked to the patient can be deleted with basically the same query.
			## That query is created by linking all the datasets to the patient via protocol_ID.
			## That includes the tables: 
			##		- anc
			##		- vital_signs
			##		- complaints
			##		- uploads
			##		- nutrition
			##		- diagnosis_ids
			*/
			$tables_array=array('anc','vital_signs','complaints','uploads','nutrition','diagnosis_ids');

			## Use the $tables_array to create a deletion query for each of the tables in $table_array and write these in $query.
			foreach ($tables_array AS $table){
				$query.="DELETE x 
						FROM visit v 
						INNER JOIN protocol p 
							ON v.visit_ID = p.visit_ID 
						INNER JOIN $table x 
							ON p.protocol_ID = x.protocol_ID 
						WHERE patient_ID=$patient_ID ; ";
			}

			/*
			## Add querys to $query which are supposed to delete datasets from the tables disp_drugs and lab
			## which are linked to the patient via 2 different protocol_IDs.
			## Therefore the queries could not be created with the previous foreach loop.
			*/
			$query.="DELETE x 
						FROM visit v 
						INNER JOIN protocol p 
							ON v.visit_ID = p.visit_ID 
						INNER JOIN disp_drugs x 
							ON x.prescription_protocol_ID=p.protocol_ID 
							OR x.given_protocol_ID=p.protocol_ID 
						WHERE patient_ID=$patient_ID ; 
					DELETE x 
						FROM visit v 
						INNER JOIN protocol p 
							ON v.visit_ID = p.visit_ID 
						INNER JOIN lab x 
							ON x.protocol_ID_ordered=p.protocol_ID 
							OR x.protocol_ID_results=p.protocol_ID
						WHERE patient_ID=$patient_ID ; ";
			
			/*
			## Create $tables_array as an array containing all tables 
			## in which the datasets linked to the patient can be deleted with basically the same query.
			## That query is created by linking all the datasets to the patient via visit_ID.
			## That includes the tables: 
			##		- insurance
			##		- lab_list
			##		- protocol
			*/
			$tables_array=array('insurance','lab_list','protocol');

			## Use the $tables_array to create a deletion query for each of the tables in $table_array and write these in $query.
			foreach($tables_array AS $table){
				$query.="DELETE x 
						FROM visit v
						INNER JOIN $table x
							ON v.visit_ID=x.visit_ID
						WHERE v.patient_ID=$patient_ID ; ";
			}

			## Add the query to delete the delivery data of the patient which is linked via maternity_ID.
			$query.="DELETE x
						FROM maternity m
						INNER JOIN delivery x
							ON m.maternity_ID=x.maternity_ID
						WHERE m.patient_ID=$patient_ID ; ";

			/*
			## Create $tables_array as an array containing all tables 
			## in which the datasets linked to the patient can be deleted with basically the same query.
			## That query is created by linking all the datasets to the patient via patient_ID.
			## That includes the tables: 
			##		- maternity
			##		- visit
			##		- patient
			*/
			$tables_array=array('maternity','visit','patient');

			## Use the $tables_array to create a deletion query for each of the tables in $table_array and write these in $query.
			foreach($tables_array AS $table){
				$query.="DELETE FROM $table WHERE patient_ID=$patient_ID ; ";
			}
			
			/* 
			## Establish connection to database.
			## Delete patient and its data from all the tables using $query.
			## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
			*/
			mysqli_multi_query($link,$query);

			## Depending on the user's last visited page, forward him back.
			if($_SERVER['HTTP_REFERER']=="search_patient.php"){
				#echo '<script>window.location.href=("search_patient.php")</script>';
			}else{
				#echo '<script>window.location.href=("patient_protocol.php?from='.$today.'&to='.$today.'&newold_column=on&insured_column=on")</script>';
			}
			
		}
		
		/*
		## This if-branch is called, if the user's password was wrong. 
		## In this case it leads to the password request again.
		*/
		else{
			Settings::wrongpassword();
			echo'<script type="text/JavaScript">window.location.href="delete_from_database.php?delete=on&patient_ID='.$patient_ID.'"</script>';
		}
	}
	
	## This if-branch is called, if there are no further parameters sent with the page, that means when the user is calling the page in the first place.
	else{
		/*
		## Request, if the user is sure about its intention to delete the patient.
		## Depending on the answer, lead either further to continue the deleting process,
		## otherwise just lead back to previous page.
		*/
		echo'<script type="text/JavaScript">;
				if(window.confirm("Do you really want to delete this patient from the database?")){
					window.location.href="delete_from_database.php?delete=on&patient_ID='.$patient_ID.'";
				}else{
					if(document.referrer.indexOf("edit_patient.php")>-1){
						window.history.back();
					}else{
						history.go(-2);
					}
				}
			</script>';	
	}

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");		

?>