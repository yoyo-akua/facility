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
		## Get data from database.
		## Get the password for the consulting department.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		## Save this in $rightpassword.
		## Save the password entered by the user in $password.
		*/
		$query="SELECT password FROM departments WHERE Department like 'OPD'";
		$result=mysqli_query($link,$query);
		$object=mysqli_fetch_object($result);
		$rightpassword=$object->password;
		$password=$_POST['password'];
		
		## If the correct password $rightpassword and the one entered by the user $password are matching, call this if-branch.
		if($password==$rightpassword){
			/* 
			## Establish connection to database.
			## Delete patient and its data from the tables patient, maternity, anc, protocol and lab.
			## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
			*/
			$query="DELETE FROM patient WHERE patient_ID = $patient_ID";
			mysqli_query($link,$query);
			$query="SELECT maternity_ID FROM maternity WHERE patient_ID=$patient_ID";
			$result=mysqli_query($link,$query);
			while($row=mysqli_fetch_object($result)){
				$maternity_ID=$row->maternity_ID;
				$query="DELETE FROM anc WHERE maternity_ID=$maternity_ID";
				mysqli_query($link,$query);
				$query="DELETE FROM maternity  WHERE maternity_ID=$maternity_ID";
				mysqli_query($link,$query);
			}
			$query=" SELECT protocol_ID FROM protocol WHERE patient_ID=$patient_ID";
			$result=mysqli_query($link,$query);
			while($row=mysqli_fetch_object($result)){
				$protocol_ID=$row->protocol_ID;
				$query="DELETE FROM protocol WHERE protocol_ID = $protocol_ID";
				mysqli_query($link,$query);
				$query="SELECT * FROM lab WHERE protocol_ID=$protocol_ID";
				mysqli_query($link,$query);
				Diagnosis_IDs::clean($protocol_ID);
			}

			## Depending on the user's last visited page, forward him back.
			if($_SERVER['HTTP_REFERER']=="search_patient.php"){
				echo '<script>window.location.href=("search_patient.php")</script>';
			}else{
				echo '<script>window.location.href=("patient_protocol.php?from='.$today.'&to='.$today.'&newold_column=on&insured_column=on")</script>';
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