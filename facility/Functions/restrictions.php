<?php
	include("variables.php");
	include("Objects/Departments.php");
	/*
	## Transfer own URL to a format which can be used for the comparison with the page names which 
	## are stored within the database for each department for the purpose of limiting some pages to some departments.
	## Store this information in $self.
	*/
	$self=str_replace("/facility/","",$_SERVER['PHP_SELF']);


	$own_department=Departments::getDepartment();
	
	/*
	## Call this if-branch, if the user is not accessing the application from the server.
	## It is used to limit the access and/or send messages to specific departments.
	## The server should neither belong to a specific department nor have limited access.
	*/
	if(! in_array('Server',$own_department)){
		
		/*
		## Get data from database.
		## Inquire whether the PC which the user is using to access the page belongs to a department which is permitted to access the page.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		$query="SELECT * FROM departments WHERE IP like '%$own_IP%' AND pages like '%$self%'";
		$result=mysqli_query($link,$query);
		
		## Call this if-branch, if the user is using a computer which is not permitted by default to access the page.
		if(empty(mysqli_fetch_object($result))){
			
			/*
			## Get data from database.
			## Get a list of all departments which are permitted to access the page by default.
			## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
			*/			
			$query="SELECT * FROM departments WHERE pages like '%$self%'";
			$result=mysqli_query($link,$query);
			
			/*
			## Initialise variables.
			## $passwordset is used to determine whether there has a password been entered for any of the departments,
			## which are allowed to access the page.
			## $first is used to avoid an "or" at the beginning of the enumeration of those departments.
			*/
			$passwordset=false;
			$first=true;
			
			## This loop will be run once for each of the output departments with access permission for this page from the database query.
			while($row=mysqli_fetch_object($result)){
				
				## Set variables with name and password of the department.
				$department=$row->Department;
				$password=$row->password;
				
				/*
				## If the password for the department of this particular run of the loop is already saved in the session,
				## change the status of $passwordset and break the loop.
				*/
				if(! empty($_SESSION["password_$department"])){
					$passwordset=true;
					break;
				}
				
				/*
				## If the password for the department is not set,
				##		- use the variable $alloweddepartments to create an enumeration of departments, 
				##		  which are permitted to access this page, which will be displayed for the user in the password request.
				##		- use the array $allowedpasswords to save all passwords (and the correspondent departments),
				##		  which are legit for proving the access permission for this page
				##		  (this means all allowed department's passwords).
				*/
				else if ($first){
					$alloweddepartments="$department";
					$allowedpasswords=array($department=>$password);
					$first=false;
				}else{
					$alloweddepartments.=" or $department";
					$allowedpasswords[$department]=$password;
				}
			}

			## Call this if-branch only if the correct password for any of the permitted departments has not been saved yet.
			if(! $passwordset){
				
				## Call this if-branch, if the user entered a password for any of the departments (, but it is not confirmed/saved yet).
				if(! empty($_POST['password'])){
					
					/*
					## If the entered password is correct for any of the departments which are permitted to access the page, 
					## save it for all of those departments which have this password in the correspondent session variables.
					## Change the status of $passwordset.
					*/
					if(in_array($_POST['password'],$allowedpasswords)){
						foreach($allowedpasswords AS $department=>$password){
							if($_POST['password']==$password){
								$_SESSION["password_$department"]=$password;
							}
							$passwordset=true;
						}
					}
					
					/*
					## If the password is not correct, call a function which warns the user and offers a retry, 
					## if the user does not select that option, lead to the start page (index.php).
					*/
					else{
						Settings::wrongpassword();
					}
				}

				## If the user didn't enter a password yet or entered a wrong password, print a password request, using $alloweddepartments.
				if(empty($_POST['password']) OR ! $passwordset){
					$text="Please enter the $alloweddepartments password to confirm your access allowance";
					Settings::popupPassword($thispage,$text,array(''));
				}
			}
		}
	}

	## This if-branch is called when the user is not logged in 
	## and trying to open a page, he should only open being logged in. 
	## In this case, forward him to the login page.
	if(strstr($thispage,'patient_visit.php') AND ! isset($_SESSION['staff_ID'])){
		echo'<script type="text/JavaScript">;
				if(window.confirm("Please log in to continue.")){
					window.location.href="login.php";
				}else{
					window.history.back();
				}
			</script>';
	}
?>