<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Initialising variable $dir which contains the path for the database backup, which is to be created before the reset.
	$dir="/opt/lampp/htdocs/backup/backup_before_reset.sql";

	/*
	## This if-branch is called if the user confirmed his intention to reset the entire database.
	## and displays a password request.
	*/
	if(! empty ($_GET['empty'])){
		
		/* 
		## Initialising variables, that are needed for a function, that displays a password request.
		## $page describes which page should be called by the function.
		## $text is the text, that is to be displayed with the password request.
		## $hidden_array includes the parameters that should be sent along side the password request (apart from the entered password).
		*/
		$page='reset_database.php';
		$text="You need to enter the Consulting password to reset the database";
		$hidden_array=array('');
		
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
		$query="SELECT password FROM departments WHERE Department like 'Consulting'";
		$result=mysqli_query($link,$query);
		$object=mysqli_fetch_object($result);
		$rightpassword=$object->password;
		$password=$_POST['password'];
		
		## If the correct password $rightpassword and the one entered by the user $password are matching, call this if-branch.
		if($password==$rightpassword){
			
			## Initialise variables with general database information, needed for the execution of the backup.
			$database = 'facility';
			$user = 'root';
			$pass = '';
			$host = 'localhost';
			
			## Execute database backup.
			echo "backing up database to $dir...<br>";
			exec("/opt/lampp/bin/mysqldump --user={$user} --password={$pass} --host={$host} {$database} --result-file={$dir} 2>&1", $output);
			
			/*
			## This if-branch is only called, if there were no hidden warnings produced by the backup command. 
			## The assumption is, that means, that the backup was successfull.
			*/
			if(empty($output)){
				
				## $truncate is an array that describes all database tables that are to be emptied.
				$truncate=array('anc','delivery','lab','maternity','patient','protocol','store_drugs','store_non_drugs','diagnoses','disp_drugs');
				
				## Within this loop every database table in $truncate is emptied.
				foreach($truncate AS $table){
					echo "emptying table $table...<br>";
					$query="DELETE FROM $table";
					mysqli_query($link,$query);
				}
				
				## The database table "diagnoses" is filled with the standard diagnoses from the report form.
				echo "importing standard diagnoses...<br>";
				$query=file_get_contents("/opt/lampp/htdocs/backup/reset/diagnoses.sql");
				mysqli_query($link,$query);

				/*
				## Delete entered IP addresses, in Charge and notices from database table "departments", reset passwords to "authorised".
				## The content of notices differs for each department.
				## It can either be used to generate new patient numbers (OPD, Lab, Maternity),
				## to send notices about specific drugs being empty to Store and Dispensary
				## or to save the list of top five diagnoses for Consulting.
				*/
				echo "setting passwords to \"authorised\"...<br>";
				$query="UPDATE departments SET `IP`='',`password`='authorised',`in_charge`='',`notice`=''";
				mysqli_query($link,$query);
				
				## Enter the five most frequent diagnoses for top five.
				$query="UPDATE departments SET `notice`='10,14,26,37,73' WHERE Department='Consulting'";
				mysqli_query($link,$query);
				
				## Delete all passwords from current session.
				echo "deleting session...<br>";
				$_SESSION=array();
				
				## Show notification that database reset was successful.
				$message="Database reset completed";
			}
			
			## If there have been hidden warnings, show notification that reset has been prevented.
			else{
				$message="Failed backing up database, reset stopped";
			}
			
			## Function executing the notification.
			Settings::messagebox($message);
			
			## Lead the user back to the settings.
			echo "
					<script language=\"JavaScript\">
					window.location.href='settings.php'
					</script>
					";
			
		}
		
		## This if-branch is called, if the user entered the wrong password. It just shows a notification and then leads back to the settings. 
		else{
			Settings::wrongpassword();
			echo "
					<script language=\"JavaScript\">
					window.location.href='reset_database.php?empty=on'
					</script>
					";
		}
	}

	## This if-branch is called, if there are no further parameters sent with the page, that means when the user is calling the page in the first place.
	else{
		/*
		## Request, if the user is sure about its intention to reset the database.
		## Depending on the answer, lead either further to continue the resetting process,
		## otherwise just lead back to previous page.
		*/		
		echo'<script type="text/JavaScript">;
				if(window.confirm("Are you very sure you want to reset the whole database? (A backup of your current database will be saved in '.$dir.', passwords will be set to \'authorised\')")){
					window.location.href="reset_database.php?empty=on";
				}else{
					window.location.href="settings.php";
				}
			</script>';	
	}

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");	

?>