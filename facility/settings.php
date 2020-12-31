<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Print headline and styling element for border spacing.
	echo'
			<h1>Settings</h1>
			<div class="inputform">
			';

	/*
	## Get data from database.
	## Get list of all departments and save it in $result.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/
	$query="SELECT * FROM departments ORDER BY department";
	$result=mysqli_query($link,$query);

	## The following loop will be run once for each of the output departments from the database query.
	while($row=mysqli_fetch_object($result)){
		
		## Initialise variable with the department's name.
		$department=$row->Department;
		
		## Only if the department is activated in "defaults/DEFAULTS.php", this if-branch is called.
		if(in_array($department,$DEPARTMENTS)){
			
			/*
			## If the department is "unlocked" (with a password) or the user is trying to unlock it, display all content of the settings for the department,
			## otherwise only show the headline and hide the content.
			*/
			echo"<details";
			if(! empty($_SESSION["password_$department"]) OR ! empty($_POST["password_$department"])){
				echo" open";
			}
			echo"><summary><h1>$department</u></h1></summary>";
			
			/*
			## This if-branch is called, if there hasn't been any password for the department entered or saved in a session before.
			## Print a password request which is called upon by Settings::Password().
			*/
			if(empty ($_SESSION["password_$department"]) AND empty($_POST["password_$department"])){
				Settings::Password("password_$department");
				echo"</details>";
			}
			
			## This if-branch is called, if the user has entered a password, but it hasn't been verified yet.
			else if(empty($_SESSION["password_$department"])){
				
				## Initialise variables with the correct password ($rightpassword) for the department and the one entered by the user ($password).
				$rightpassword = $row->password;
				$password=$_POST["password_$department"];
				
				## Compare the correct password for the department and the one entered by the user.
				## If it is wrong, print a notice and show the input field for the password again.
				## Otherwise save the correct password in the $_SESSION variable which is used to store any information that apply for several pages.
				if($password !== $rightpassword){
					echo'<b><font color="firebrick">Password is wrong!</b></font><br>';
					Settings::Password("password_$department");
					echo"</details>";
				}else{
					$_SESSION["password_$department"]=$password;
				}
			}
			
			## If the correct password was entered or saved in session before, this if-branch is called.
			if(isset($_SESSION["password_$department"])){
				
				## Call this if-branch, if the user is trying to set a new password for any department.
				if(! empty($_POST["newpassword_$department"])){
					
					## Initialise variables with the user's entry for the current password and what is to be the new password and it's repetition.
					$oldpassword=$_POST['old_password'];
					$newpassword=$_POST['new_password'];
					$confirmpassword=$_POST['confirm_password'];

					/*
					## Compare the current password entered by the user with the correct password for the department. 
					## If it is not correct, show a notificiation and don't save the new password.
					*/
					if($oldpassword!==$_SESSION["password_$department"]){
						$message="You did not enter the right old password";
						Settings::Messagebox($message);
					}
					
					/*
					## Compare the new password entered by the user with the repetition for confirmation. 
					## If it is not correct, show a notificiation and don't save the new password.
					*/
					else if($newpassword!==$confirmpassword){
						$message='Please repeat the same password for "New Password" and "Confirm Password"';
						Settings::Messagebox($message);
					}
					
					/*
					## If all entries are correct, save the password in the database,
					## and update the $_SESSION variable which is used to store any information that apply for several pages.
					*/
					else{
						$query="UPDATE departments SET password='$newpassword' WHERE department like '$department'";
						mysqli_query($link,$query);
						$_SESSION["password_$department"]=$newpassword;
					}
				}
				## Call this if-branch if the user is intending to set the system colour.
				if(!empty($_GET["Change_Colour_$department"])){

					## Initialise variable $colour with the selected colour.
					$colour=$_GET['colour'];
					
					## Create database query to update the colour layout in the departments table.
					$main_query="UPDATE departments SET colour='$colour'";
					
					## In case the user is restricting the change to only one department, adapt the department.
					if(! empty($_GET['colour_department'])){
						$main_query.="WHERE Department='$department'";
					}
					
					## In any other case, update the active colour profile in the database for all departments 
					## and in the colours table itself, this affects only the colour profile of the devices
					## which are not listed in the departments table.
					else{
						$query="UPDATE colours SET active=0 WHERE layoutname!='$colour'";
						mysqli_query($link,$query);
						$query="UPDATE colours SET active=1 WHERE layoutname='$colour'";
						mysqli_query($link,$query);	
					}
					mysqli_query($link,$main_query);

						
				}	

				/*
				## Initialise variable with current IPs for the department
				## If more than one IP adress is defined, the IPs are separated by '&'.
				*/
				$IPs=$row->IP;
				
				## This if-branch is called if the user is deleting an IP address from the list of IP addresses or add one to it.
				if(! empty($_GET["Delete_IP_$department"]) OR ! empty($_GET["IP_$department"])){

					## If the user is deleting an IP address, delete it from the enumeration of IP addresses stored in $IPs.
					if(! empty($_GET["Delete_IP_$department"])){
						
						## Deleting the IP adress, if it is not placed at first position of the enumeration. Otherwise nothing happens. 
						$IPs=str_replace(' & '.$_GET["Delete_IP_$department"],'',$IPs);
						
						## Deleting the IP adress, if it is placed at the first position of the enumeration. Otherwise nothing happens.
						$IPs=str_replace($_GET["Delete_IP_$department"].' & ','',$IPs);

						## Deleting the IP adress, if it is the only IP in the "enumeration". Otherwise nothing happens.
						$IPs=str_replace($_GET["Delete_IP_$department"],'',$IPs);
					}
					
					## If the user is adding an IP address, add it to the enumeration of IP addresses stored in $IPs.
					else{
						## Adding the entered IP adress at the end of the enumeration, if one or more IP adresses are already defined for the department.
						if(! empty($IPs)){
							$IPs.=' & '.$_GET["IP_$department"];
						
							## Adding the entered IP adress at the beginning of the enumeration, because no other IP adress is already defined for the department.
						}else{
							$IPs=$_GET["IP_$department"];
						}
					}
					
					## Save the new enumeration of IP addresses stored in $IPs in the database.
					$query="UPDATE departments SET IP='$IPs' WHERE department like '$department'";
					mysqli_query($link,$query);
				}	
				
				## Call this if-branch only for the department Laboratory.
				if($department=='Laboratory'){
					
					## This if-branch is called, if the user is deleting a test from the list of tests.
					if(! empty($_GET['Delete_Test'])){
						
						## Initialising variable with test's ID, creating object of test and getting its name.
						$Test_ID=$_GET['Delete_Test'];
						$Test=new Tests($Test_ID);
						$Testname=$Test->getTest_name();
					
						## If the user didn't confirm his intention to delete the test yet, call this if-branch.
						if(empty($_GET['delete'])){
							/*
							## Request, if the user is sure about its intention to delete the test.
							## Depending on the answer, lead either further to continue the deleting process,
							## otherwise just lead back to previous page.
							*/							
							Settings::delete_request($Testname);
						}
						
						## After the user confirmed his intention to delete the test, call this if-branch.
						else{
							## Delete the test, all its parameters and entries in the lab register from the database.
							$query="DELETE FROM tests WHERE Test_ID=$Test_ID";
							mysqli_query($link,$query);
							$query="SELECT parameter_ID FROM parameters WHERE Test_ID=$Test_ID";
							$result2=mysqli_query($link,$query);
							
							while($row2=mysqli_fetch_object($result2)){
								$query="DELETE FROM lab,parameters WHERE parameter_ID=$row2->parameter_ID";
								mysqli_query($link,$query);
							}
						}
					}
					
					## This if-branch is called, if the user is editing a test from the list of diagnoses.
					if(! empty($_GET['Edit_Test'])){
						
						## Initialising variable with test's ID, creating object of test and getting its name.
						$Test_ID=$_GET['Edit_Test'];
						$Test=new Tests($Test_ID);
						$Testname=$Test->getTest_name();

						## Writing the new name of the test to database and sending message to user that change has been successful.
						if(! empty($_GET['new_name'])){
							
							$new_name=$_GET['new_name'];

							if($new_name!==$Testname){
								$Test->setTest_name($new_name);
								$message="You succesfully changed the name of the test '$Testname' to '$new_name'.";
								Settings::messagebox($message);
							}
							
						}
					}


					## Display the list of all tests and a symbol with a link for deleting each of them.
					echo"<details";
					if(! empty($_GET['Delete_Test']) OR ! empty($_GET['Edit_Test'])){
						echo " open";
					}
					echo">
								<summary>
									<h2>Delete/Edit Test</h2>
								</summary>
							<table class='onlytop' style='width:50%'>
								<tr>
									<th>
										Testname
									</th>
									<th>
									</th>
									<th>
									</th>
								</tr>
							";
					$query="SELECT * FROM tests ORDER BY frequency";
					$result2=mysqli_query($link,$query);
					while($row2=mysqli_fetch_object($result2)){
						$ID=$row2->test_ID;
						$label=".drug_$ID";
						echo"
										<tr>
											<form method='get' action='settings.php'>
												<td style='text-align:left'>
													<font  id='name$label'>$row2->test_name</font>
													<input type='text' name='new_name' id='edit$label' value='$row2->test_name' style='margin:0px;text-align:left;width:100%;max-width:1000px;display:none'>
												</td>
												<td>
													<button type='button' id='edit_button$label' onclick='edit_name(\"$label\")'><i class='fas fa-pencil-alt'></i></button>
													<button type='submit' name='Edit_Test' value='$ID' id='save_button$label' style='display:none'><i class='fas fa-save'></i></button>												
												</td>
												<td>
													<a href='settings.php?Delete_Test=$ID'><i class='fas fa-trash-alt'></i></i></a>
												</td>
											</form>
										</tr>
									";
					}
					
					## Print a link to the page, you can use for adding tests.
					echo"
								</table>
							</details>
							<details>
								<summary><h2>
									<a href='new_test.php'>Add Test</a>
								</h2></summary>
							</details>
							";
				}
				
				## Call this if-branch only for the department Store.
				else if($department=='Store'){
					
					/*
					## This if-branch is called, if the user is adding a new drug to the database.
					## It adds the drug to the database and a default entry in the store register to avoid database complications.
					## Show notification about successfull entry.
					*/
					if(! empty($_POST['Drugname'])){
						$drug=Drugs::new_Drugs($_POST['Drugname'],$_POST['Unit_of_Issue']);
						$lastID=$drug->getDrug_ID();
						$message="You successfully added ".$_POST["Drugname"];
						Settings::messagebox($message);
					}
					
					/*
					## This if-branch is called, if the user is adding a new non drug to the database.
					## It adds the non drug to the database and a default entry in the store register to avoid database complications.
					## Show notification about successfull entry.
					*/
					if(! empty($_POST['Non_Drugname'])){
						$non_drug=Non_Drugs::new_Non_Drugs($_POST['Non_Drugname'],$_POST['Receiving_Department']);
						$lastID=$non_drug->getNon_Drug_ID();
						$message="You successfully added ".$_POST["Non_Drugname"];
						Settings::messagebox($message);
					}
					
					## This if-branch is called, if the user is deleting a drug from the list of drugs.
					if(! empty($_GET['Delete_Drug'])){
						
						## Initialising variable with drug's ID, creating object of drug and getting its name.
						$Drug_ID=$_GET['Delete_Drug'];
						$Drug=new Drugs($Drug_ID);
						$Drugname=$Drug->getDrugname();
						
						## If the user didn't confirm his intention to delete the drug yet, call this if-branch.
						if(empty($_GET['delete'])){
							/*
							## Request, if the user is sure about its intention to delete the drug.
							## Depending on the answer, lead either further to continue the deleting process,
							## otherwise just lead back to previous page.
							*/
							Settings::delete_request($Drugname);
						}
						
						## After the user confirmed his intention to delete the drug, call this if-branch.
						else{
							## Delete the drug and all its entries in the dispensary, patient and store register from the database.
							$query="DELETE FROM drugs WHERE Drug_ID=$Drug_ID";
							mysqli_query($link,$query);
							$query="DELETE FROM store_drugs WHERE Drug_ID=$Drug_ID";
							mysqli_query($link,$query);
							$query="DELETE FROM disp_drugs WHERE Drug_ID=$Drug_ID";
							mysqli_query($link,$query);
						}
					}
					
					## This if-branch is called, if the user is deleting a drug from the list of non drugs.
					if(! empty($_GET['Delete_Non_Drug'])){
						
						## Initialising variable with non drug's ID, creating object of non drug and getting its name.
						$Non_Drug_ID=$_GET['Delete_Non_Drug'];
						$Non_Drug=new Non_Drugs($Non_Drug_ID);
						$Non_Drugname=$Non_Drug->getNon_Drugname();
						
						## If the user didn't confirm his intention to delete the non drug yet, call this if-branch.
						if(empty($_GET['delete'])){
							/*
							## Request, if the user is sure about its intention to delete the non drug.
							## Depending on the answer, lead either further to continue the deleting process,
							## otherwise just lead back to previous page.
							*/
							Settings::delete_request($Non_Drugname);
						}
						
						## After the user confirmed his intention to delete the non drug, call this if-branch.
						else{
							## Delete the non drug and all its entries in the dispensary, patient and store register from the database.
							$query="DELETE FROM non_drugs WHERE Non_Drug_ID=$Non_Drug_ID";
							mysqli_query($link,$query);
							$query="DELETE FROM store_non_drugs WHERE Non_Drug_ID=$Non_Drug_ID";
							mysqli_query($link,$query);
						}
					}

					## This if-branch is called, if the user is editing a drug from the list of diagnoses.
					if(! empty($_GET['Edit_Drug'])){
						
						## Initialising variable with drug's ID, creating object of drug and getting its name.
						$Drug_ID=$_GET['Edit_Drug'];
						$Drug=new Drugs($Drug_ID);
						$Drugname=$Drug->getDrugname();
						$unit=$Drug->getUnit_of_Issue();

						## Writing the new name of the drug to database and sending message to user that change has been successful.
						if(! empty($_GET['new_name']) AND ! empty($_GET['new_unit'])){
							
							$new_name=$_GET['new_name'];
							$new_unit=$_GET['new_unit'];

							if($new_name!==$Drugname OR $new_unit!==$unit){
								$Drug->setDrugname($new_name);
								$Drug->setUnit_of_Issue($new_unit);
								$message="You succesfully changed '$Drugname ($unit)' to '$new_name ($new_unit)'.";
								Settings::messagebox($message);
							}
							
						}

					}
					
					## If the user sets the in Charge of Store, update it in the database and show notification.
					if(! empty($_POST["Incharge"])){
						$incharge=$_POST["Incharge"];
						$query="UPDATE departments SET in_charge='$incharge' WHERE department like '$department'";
						mysqli_query($link,$query);
						$message="You successfully set the store's In Charge to \"".$_POST["Incharge"]."\"";
						Settings::messagebox($message);
					}
					
					/*
					## Display the list of all drugs and a symbol with a link for deleting or editing each of them.
					## When clicking the delete button a link to this page is opened which activates the if-branch deleting the item.
					## When clicking the edit button that activates the javascript function edit_name().
					*/
					echo"<details";
					if(! empty($_GET['Delete_Drug']) OR ! empty($_GET['Edit_Drug'])){
						echo " open";
					}
					echo">
								<summary>
									<h2>Delete/Edit Drug</h2>
								</summary>
							<table class='onlytop' style='width:60%'>
								<tr>
									<th>
										Drugname
									</th>
									<th>
										Unit of Issue
									</th>
									<th>
									</th>
									<th>
									</th>
								</tr>
							";
					$query="SELECT * FROM drugs ORDER BY Drugname";
					$result2=mysqli_query($link,$query);
					while($row2=mysqli_fetch_object($result2)){
						$ID=$row2->Drug_ID;
						$unit=$row2->Unit_of_Issue;
						$label=".drug_$ID";
						echo"
										<tr>
											<form method='get' action='settings.php'>
												<td style='text-align:left'>
													<font  id='name$label'>$row2->Drugname</font>
													<input type='text' name='new_name' id='edit$label' value='$row2->Drugname' style='margin:0px;text-align:left;width:100%;max-width:1000px;display:none'>
												</td>
												<td style='text-align:left'>
													<font  id='unit$label' name='unit_$ID' >$unit</font>	
													<select name='new_unit' id='unit_select$label' style='display:none'>
														<option value='ampoule' "; if($unit=='ampoule'){echo"selected='selected'";}; echo">ampoule</option>
														<option value='bag' "; if($unit=='bag'){echo"selected='selected'";}; echo">bag</option>
														<option value='bottle' "; if($unit=='bottle'){echo"selected='selected'";}; echo">bottle</option>
														<option value='capsule' "; if($unit=='capsule'){echo"selected='selected'";}; echo">capsule</option>
														<option value='container' "; if($unit=='container'){echo"selected='selected'";}; echo">container</option>
														<option value='pessary' "; if($unit=='pessary'){echo"selected='selected'";}; echo">pessary</option>
														<option value='sachet' "; if($unit=='sachet'){echo"selected='selected'";}; echo">sachet</option>
														<option value='suppository' "; if($unit=='suppository'){echo"selected='selected'";}; echo">suppository</option>
														<option value='tablet' "; if($unit=='tablet'){echo"selected='selected'";}; echo">tablet</option>
														<option value='tube' "; if($unit=='tube'){echo"selected='selected'";}; echo">tube</option>
														<option value='vial' "; if($unit=='vial'){echo"selected='selected'";}; echo">vial</option>
													</select>
												</td>
												<td>
													<button type='button' id='edit_button$label' onclick='edit_name(\"$label\")'><i class='fas fa-pencil-alt'></i></button>
													<button type='submit' name='Edit_Drug' value='$ID' id='save_button$label' style='display:none'><i class='fas fa-save'></i></button>												
												</td>
												<td>
													<a href='settings.php?Delete_Drug=$ID'><i class='fas fa-trash-alt'></i></i></a>
												</td>
											</form>
										</tr>
									";
						
					}
					echo"
								</table>
							</details>
							<details
							";
					
					## Print an input form for new drugs with name and unit of issue.
					if(! empty($_GET['Drugname'])){
						echo " open";
					}
							echo">
								<summary>
									<h2>Add Drug</h2>
								</summary>
								<form action='settings.php' method='post'>
									<div><label>Name:</label>
									<input type='text' name='Drugname'></div>
									<div><label>Unit of Issue:</label>
									<select name='Unit_of_Issue'>
										<option value=''> </option>
										<option value='ampoule'>ampoule</option>
										<option value='bag'>bag</option>
										<option value='bottle'>bottle</option>
										<option value='capsule'>capsule</option>
										<option value='container'>container</option>
										<option value='pessary'>pessary</option>
										<option value='sachet'>sachet</option>
										<option value='suppository'>suppository</option>
										<option value='tablet'>tablet</option>
										<option value='tube'>tube</option>
										<option value='vial'>vial</option>
									</select></div>
									<input type='hidden' name='token' value='$uniqueID'>
									<div><input type='submit' value='submit'></div>
								</form>
							</details>
							<details
							";
					
					## Display the list of all non drugs and a symbol with a link for deleting each of them.
					if(! empty($_GET['Delete_Non_Drug'])){
						echo " open";
					}
							echo">
										<summary>
											<h2>Delete Non-Drug</h2>
										</summary>
									<table class='onlytop'>
										<tr>
											<th>
												Non-Drugname
											</th>
											<th>
											</th>
										</tr>
									";
							$query="SELECT * FROM non_drugs ORDER BY Non_Drugname";
							$result2=mysqli_query($link,$query);
							while($row2=mysqli_fetch_object($result2)){
								echo"
										<tr>
											<td>
												$row2->Non_Drugname
											</td>
											<td>
												<a href='settings.php?Delete_Non_Drug=$row2->Non_Drug_ID'><i class='fas fa-times-circle'></i></a>
											</td>
										</tr>
										";
							}
							echo"
								</table>
							</details>
							<details
							";
					
					## Print an input form for new non drugs with name and standard receiving department.
					if(! empty($_GET['Non_Drugname'])){
						echo " open";
					}
							echo">
								<summary>
									<h2>Add Non-Drug</h2>
								</summary>
								<form action='settings.php' method='post'>
									<div><label>Name:</label>
									<input type='text' name='Non_Drugname'></div>
									<div><label>Standard Receiving Department:</label>
									<input type='text' name='Receiving_Department'></div>
									<input type='hidden' name='token' value='$uniqueID'>
									<div><input type='submit' value='submit'></div>
								</form>
							</details>
							<details
							";
					## Print an input form for setting the In Charge with a field for its name.
					if(! empty($_GET['Incharge'])){
						echo " open";
					}
							echo">
								<summary>
									<h2>Set In-Charge</h2>
								</summary>
							<form action='settings.php' method='post'>
								<div><input type='text' name='Incharge'></div>
								<div><input type='submit' value='submit'></div>
							</form>
						</details>
						";
				}
				
				## Call this if-branch only for the department Consulting.
				else if($department=='Consulting'){
					
					## This if-branch is called, if the user is deleting a disease from the list of diagnoses.
					if(! empty($_GET['Delete_Diagnosis'])){
						
						## Initialising variable with diagnosis' ID, creating object of diagnosis and getting its name.
						$Diagnosis_ID=$_GET['Delete_Diagnosis'];
						$Diagnosis=new Diagnoses($Diagnosis_ID);
						$Diagnosisname=$Diagnosis->getDiagnosisName();
						
						## If the user didn't confirm his intention to delete the diagnosis yet, call this if-branch.
						if(empty($_GET['delete'])){
							/*
							## Request, if the user is sure about its intention to delete the disease.
							## Depending on the answer, lead either further to continue the deleting process,
							## otherwise just lead back to previous page.
							*/
							Settings::delete_request($Diagnosisname);
						}
						
						## After the user confirmed his intention to delete the diagnosis, call this if-branch.
						else{
							## Delete the diagnosis and all its entries in the patient register as well as in the diagnosis register from the database.
							$query="DELETE FROM diagnoses WHERE Diagnosis_ID=$Diagnosis_ID";
							mysqli_query($link,$query);
							$query="DELETE FROM diagnosis_ids WHERE diagnosis_ID=$Diagnosis_ID";
							mysqli_query($link,$query);
						}
					}

					## This if-branch is called, if the user is editing a disease from the list of diagnoses.
					if(! empty($_GET['Edit_Diagnosis'])){
						
						## Initialising variable with diagnosis' ID, creating object of diagnosis and getting its name.
						$Diagnosis_ID=$_GET['Edit_Diagnosis'];
						$Diagnosis=new Diagnoses($Diagnosis_ID);
						$Diagnosisname=$Diagnosis->getDiagnosisName();

						## Writing the new name of the disease to database and sending message to user that change has been successful.
						if(! empty($_GET['new_name'])){
							
							$new_name=$_GET['new_name'];

							if($new_name!==$Diagnosisname){
								$Diagnosis->setDiagnosisName($new_name);
								$message="You succesfully changed the name of '$Diagnosisname' to '$new_name'.";
								Settings::messagebox($message);
							}
							
						}
					}
					/*
					## This if-branch is called, if the user is adding a new disease to the database.
					## It adds the drug to the database and shows a notification about successfull entry.
					*/
					if(! empty($_POST['DiagnosisName'])){
						Diagnoses::new_Diagnoses($_POST['DiagnosisName'],$_POST['DiagnosisClass']);
						$message="You successfully added ".$_POST["DiagnosisName"];
						Settings::messagebox($message);
					}
					
					## Print list of checkboxes for all diagnoses with the top five ones selected.
					$query="SELECT notice FROM departments WHERE Department like 'Consulting'";
					$result2=mysqli_query($link,$query);
					$object=mysqli_fetch_object($result2);
					$top_five=$object->notice;
					$top_five_array=explode(',',$top_five);
					
					echo"<details";
					
					/*
					## Display the list of all diagnoses and a symbol with a link for deleting or editing each of them.
					## When clicking the delete button a link to this page is opened which activates the if-branch deleting the item.
					## When clicking the edit button that activates the javascript function edit_name().
					*/
					if(! empty($_GET['Delete_Diagnosis']) OR ! empty($_GET['Edit_Diagnosis'])){
						echo " open";
					}
					echo">
										<summary>
											<h2>Delete/Edit Diagnosis</h2>
										</summary>
																		
									<table class='onlytop' style='width:50%;text-align:left'>
										<tr>
											<th>Diagnosisname</th>
											<th>
											</th>
											<th>
											</th>
										</tr>
									";
							$query="SELECT * FROM diagnoses WHERE DiagnosisName not like 'All other Cases' ORDER BY DiagnosisName";
							$result2=mysqli_query($link,$query);
							while($row2=mysqli_fetch_object($result2)){
								$ID=$row2->Diagnosis_ID;
								$label=".diagnosis_$ID";
								echo"
										<tr>
											<form method='get' action='settings.php'>
												<td style='text-align:left'>
												<font  id='name$label'>$row2->DiagnosisName</font>
												<input type='text' name='new_name' id='edit$label' value='$row2->DiagnosisName' style='margin:0px;text-align:left;width:100%;max-width:1000px;display:none'>
												</td>
												<td>
													<button type='button' id='edit_button$label' onclick='edit_name(\"$label\")'><i class='fas fa-pencil-alt'></i></button>
													<button type='submit' name='Edit_Diagnosis' value='$ID' id='save_button$label' style='display:none'><i class='fas fa-save'></i></button>												
												</td>
												<td>
													<a href='settings.php?Delete_Diagnosis=$ID'><i class='fas fa-trash-alt'></i></i></a>
												</td>
											</form>
										</tr>
									";
							}
							echo"
								</table>
							</details>
							<details";
					
					## Print an input form for new diagnoses, where the user can enter a name and select a class of diagnosis.
					if(! empty($_POST["DiagnosisName"])){
						echo " open";
					}
					echo">
							<summary>
								<h2>Add Diagnosis</h2>
							</summary>
								<form action='settings.php' method='post'>
									<div><label>Name:</label>
									<input type='text' name='DiagnosisName'></div>
									<div><label>Class:</label>
									<select name='DiagnosisClass' required>
										<option value=''> </option>
										<option value='communicable immunizable'>communicable immunizable</option>
										<option value='communicable non-immunizable'>communicable non-immunizable</option>
										<option value='non-communicable'>non-communicable</option>
										<option value='mental health'>mental health</option>
										<option value='specialized'>specialized</option>
										<option value='obstetric & gynaecological'>obstetric & gynaecological</option>
										<option value='reproductive tract'>reproductive tract</option>
										<option value='injuries'>injuries</option>
										<option value='others'>others</option>
									</select></div>
									<input type='hidden' name='token' value='$uniqueID'>
									<div><input type='submit' value='submit'></div>
								</form>
							</details>
						";
					
					## Only if the user is accessing the page from the server, show a link for resetting the entire database.
					$own_IP= $_SERVER['REMOTE_ADDR'];
					if($own_IP=='::1' OR $own_IP=='127.0.0.1' OR $own_IP==$_SERVER['SERVER_ADDR']){
						echo"
								<details>
									<summary><h2>
										<a href='reset_database.php'>Reset Database</a>
									</h2></summary>
								</details>
								";
					}
				}
				## Call this if-branch only for the department Administration.
				else if($department=='Administration'){

					## Call this if-branch in case the user is adding a new member of staff.
					if (! empty($_POST['staff_name'])){

						## Initialise a variable with the name of the new staff member.
						$name=$_POST['staff_name'];

						/*
						## Check if there already is a staff member by the name the user entered. 
						## If so, warn the user and stop database entry.
						*/
						if(Staff::getStaffByUsername($name)){
							$message="Entry failed! A member of staff with this username is already in the database. Please instruct the other member of staff to set a different username and try again.";
						}else{
							
							/*
							## Check if a department has been selected, 
							## if so initialise variable $department_ID and find out by which ID the department is defined. 
							## Otherwise initialise $department_ID as an empty variable.
							*/
							if(! empty($_POST['staff_department'])){
								$department_ID=Departments::getDepartmentId($_POST['staff_department']);
							}else{
								$department_ID='';
							}

							## Write the new member of staff to database and show a success message to the user. 
							Staff::new_Staff($name,$name,$_POST['qualification'],'authorised',$department_ID);
							$message="$name has been entered as a new member of staff. The initial username is \'$name\' and the initial password has been set to \'authorised\', please instruct your new co-worker to set a new individual password and username for further logins.";
						}
						Settings::messagebox($message);
					}


					echo "<details";
					
					## Print an input form for new diagnoses, where the user can enter a name and select a class of diagnosis.
					if(! empty($_POST["new_staff"])){
						echo " open";
					}
					echo">
							<summary>
								<h2>Add Staff</h2>
							</summary>
								<form action='settings.php' method='post'>
									<div>
										<label>Name:</label>
										<input type='text' name='staff_name' required><br>
									</div>
									<div>
										<label>Qualification:</label>
										<input type='text' name='qualification' required><br>
									</div>
									<div>
										<label>Department:</label>
										<select name='staff_department'>
											<option value=''></option>";
											foreach($DEPARTMENTS AS $staff_department){
												echo "<option value='$staff_department'>$staff_department</option>";
											}
											echo"
										</select>
									</div>
									<input type='hidden' name='token' value='$uniqueID'>
									<div><input type='submit' value='submit'></div>
								</form>
							</details>
						";
				}
				
				
				## The following content is displayed for every department.
				## Display the list of all IP addresses for the department and a symbol with a link for deleting each of them.
				echo"
				<details";
				if(! empty($_GET["Delete_IP_$department"])){
				echo " open";
				}
				echo">
						<summary>
							<h2>Delete IP</h2>
						</summary>
					<table class='onlytop'>
						<tr>
							<th>
								IP Adress
							</th>
							<th>
							</th>
						</tr>
					";
				$IP_array=explode(' & ',$IPs);
				foreach($IP_array AS $IP){
					echo"
							<tr>
								<td>
									$IP
								</td>
								<td>
									<a href='settings.php?Delete_IP_$department=$IP'><i class='fas fa-times-circle'></i></a>
								</td>
							</tr>
							";
				}
				echo"
						</table>
					</details>
					<details
					";

				## Print an input form for adding new IP addresses.
				if(! empty($_POST["IP_$department"])){
				echo " open";
				}
				echo">
						<summary>
							<h2>Add IP</h2>
						</summary>
						<form action='settings.php' method='get'>
							<div><input type='text' name='IP_$department' required pattern='[0-9.]{14}'>
							<input type='hidden' name='token' value='$uniqueID'>
							<input type='submit' value='submit'></div>
						</form><br>
						<div>OR
						<a class='button' href='settings.php?IP_$department=$own_IP'>this PC</a></div>
						<br>
					</details>";
				
				
				## Display a dropdown menu for the layout colour.
				echo"<details";
				if(! empty($_GET["Change_Colour_$department"])){
					echo " open";
				}
				echo">
							<summary>
								<h2>Change System Colour</h2>
							</summary>
							<form action='settings.php' method='get'>
								<select name='colour' required>
									<option value=''></option>";
									$query="SELECT layoutname FROM colours WHERE layoutname not like (SELECT colour FROM departments WHERE Department='$department')";
									$result2=mysqli_query($link,$query);
									while($row2=mysqli_fetch_object($result2)){
										$colour=$row2->layoutname;
										echo"<option value='$colour'>$colour</option>";
									}
								echo"
								</select><br>
								<input type='radio' name='colour_department' value='$department' required> $department's PCs
								<input type='radio' name='colour_department' value='' required> all PCs <br>
								<input type='submit' name='Change_Colour_$department' value='submit'><br>
							</form>
					</details>
					<details";
				
				/*
				## Print an input form for changing the password with three input fields: 
				##		- one for the current/old password,
				##		- one for the new password,
				## 		- and one to confirm the new password.
				*/
				if(! empty($_GET["newpassword_$department"])){
					echo " open";
				}
				echo">
							<summary>
								<h2>Change Password</h2>
							</summary>
							<form action='settings.php' method='post'>
								<div><label>Old Password:</label>
								<input type='password' name='old_password' required></div>
								<div><label>New Password:</label>
								<input type='password' name='new_password' title='Please use only numbers and letters and at least 6 characters' pattern='[A-Za-z0-9]{6,}' required></div>
								<div><label>Confirm Password:</label>
								<input type='password' name='confirm_password'  title='Please use only numbers and letters and at least 6 characters' pattern='[A-Za-z0-9]{6,}' required></div>
								<div><input type='submit' name='newpassword_$department' value='submit'></div>
							</form>
						</details>
					</details>
					";
			}
		}
	}
	echo'</div>';

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>
