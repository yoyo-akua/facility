<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Initialise new objects of patient and visit by certain IDs, with which the page is called.
	$visit_ID=$_GET['visit_ID'];
	$visit=new Visit($visit_ID);

	$patient_ID=$visit->getPatient_ID();
	$patient = new Patient($patient_ID);
	
	## Initialise object $insurance containing the patient's visit's insurance information.
	$insurance=new Insurance($visit_ID);

	
	## This if-branch is called if the user made some changes in the patient's data and clicked "submit".
	if(! empty($_POST['submit'])){
		
		/*
		## This function saves all general patient data (like name, sex, birthdate, NHIS and OPD).
		## The timestamp of the visit date is sent with the function to calculate the age of the patient and if it is too old to use its mother's NHIS.
		*/
		$patient->setOPD_data(strtotime($visit->getCheckin_time()));
				
		## Save in the database, if "Expired?" is selected or not.
		if(! empty($_POST['Expired'])){
			$insurance->setExpired(1);
		}else{
			$insurance->setExpired(0);
		}
		
		## Update the CCC in the database, using the entry in the page.
		if(! empty($_POST['CCC'])){
			$insurance->setCCC($_POST['CCC']);
		}else{
			$insurance->setCCC('');
		}
		
		## If the date and time of visit have been changed, call this if-branch.
		if($_POST['VisitDate'].' '.$_POST['VisitTime']!==date("Y-m-d H:i",strtotime($visit->getCheckin_time()))){
			
			## Initialise variable $checkin with the original checkin time of the visit transformed into a timestamp.
			$checkin=strtotime($visit->getCheckin_time());
			
			## In case the patient already checked out, update his checkout time.
			if($visit->getCheckout_time()!=='0000-00-00 00:00:00'){

				## Initialise variable $checkin with the original checkin time of the visit transformed into a timestamp.
				$checkout=strtotime($visit->getCheckout_time());
				
				## Use the difference between $checkin and $checkout to inquire the duration of the visit and save it in $duration.
				$duration=$checkout-$checkin;

				## Update the time of checkout in the database, using the new checkin time and the duration of the stay.
				$visit->setCheckout_time(date('Y-m-d H:i:s',($new_checkin+$duration)));
			}

			## Inquire the new time of checkin the user entered in the form. 
			$new_checkin=strtotime($_POST['VisitDate'].' '.$_POST['VisitTime']);
			
			## Call the function Protocol::getAllByVisit_ID to get all protocol entries linked to this visit. 
			$result=Protocol::getAllByVisit_ID($visit->getVisit_ID());

			## Call this loop once for each protocol entry linked to the visit. 
			while($row=mysqli_fetch_object($result)){

				## Initialise new object of protocol. 
				$protocol=new Protocol($row->protocol_ID);

				## Save the timestamp of the protocol entry the variable $old_time. 
				$old_time=strtotime($protocol->getTimestamp());

				## Use the difference between $checkin and $old_time to inquire how long after the arrival the protocol entry was made.
				$duration=$checkin-$old_time;

				## Use $new_checkin and $duration to inquire the new time of the protocol entry and write it to the database.
				$new_time=date('Y-m-d H:i:s',$new_checkin+$duration);
				$protocol->setTimestamp($new_time);
			}

			## Write the new checkin time to the database.
			$visit->setCheckin_time($_POST['VisitDate'].' '.$_POST['VisitTime']);
		}
		
		/*
		## Within the first year of the introduction of the software (defined in DEFAULTS.php, saved in $YEAR), it is possible to edit the old/new status of the patient. 
		## After that, the collection of data should be sufficienty complete to calculate that reliably from the previous database entries.
		*/
		if(strstr($today,$YEAR) AND ! empty($_POST['new'])){
			$visit->setNew_p(1);
		}else{
			$visit->setNew_p(0);
		}

		## Show notification that changes have been saved and lead back to protocol.
		echo'<script type="text/JavaScript">;
				if(window.confirm("Changes have been saved successfully. Do you want to go back to the protocol?")){
					window.location.href="patient_protocol.php?from='.$today.'&to='.$today.'";
				}
				</script>
				';
	}	
	
	## Print headline and table with form, prefilled with patient's data.
	echo '
			<h1>Edit Patient</h1>
			<table>
				<tr>
					<th>
						Name
					</th>
					<th>
						OPD
					</th>
					<th>
						NHIS
					</th>
					<th>
						Birthdate
					</th>
					<th>
						Sex
					</th>
					 <th>
						Locality
					</th>
					<th>
						NHIS is <br>
						NHIS of Mother
					</th>
					<th>
						Blood Group
					</th>
				</tr>

				<tr>
					<form action="edit_patient.php?visit_ID='.$visit_ID.'" method="post">
				<td>
					<input type=text name="Name" value="'.$patient->getName().'" required>
				</td>
				<td>
					<input class="smalltext" type="text" name="OPD" value="'.$patient->getOPD().'" pattern="'.$OPD_FORMAT.'">
				</td>
				<td>
					<input class="smalltext" type="text" name="NHIS" value="'.$patient->getNHIS().'" pattern="[0-9]{8}">
				</td>
				<td>
					<input type="date" name="Birthdate" value="'.$patient->getBirthdate().'" required>
				</td>
				<td>
					';
						echo'
							<input type="radio" name="Sex" value="male"';
								if($patient->getSex()=='male'){
									echo"checked='checked'";
								}
						echo'>male </br>
							<input type="radio" name="Sex" value="female"';
								if($patient->getSex()=='female'){
									echo"checked='checked'";
								}
						echo'>female </br>
				</td>
				<td>
					<input class="smalltext" type="text" name="Locality" value="'.$patient->getLocality().'" required>
				</td>
				<td>
					<input type="checkbox" name="NHISofMother" ';
					if($patient->getNHISofMother()) echo 'checked';
					echo '>
				</td>
				<td>
					<select name="blood_group">
						<option value=""></option>
						<option value="A+">A+</option>
						<option value="A-">A-</option>
						<option value="B+">B+</option>
						<option value="B-">B-</option>
						<option value="AB+">AB+</option>
						<option value="AB-">AB-</option>
						<option value="O+">O+</option>
						<option value="O-">O-</option>
					</select>

				</tr>
				<tr class="emptytable">
					<td>
					</td>
				</tr>
				<tr>				
				';
					if(strstr($today,$YEAR)){
						echo'
							<th><div class="tooltip">
									<label>New?</label>
									<span class="tooltiptext">
										Select the checkbox, if the patient has not been here this year.
									</span>
								</div><br>
							</th>';
					}
					echo'
					<th>
						Date & Time of Arrival
					</th>
					<th>
						CC Code
					</th>
					<th>
						NHIS expired?
					</th>
				</tr>
				<tr>';
					if(strstr($today,$YEAR)){
						echo'
							<td>
								<input type="checkbox" name="new"';
								if($visit->getNew_p()==1){
									echo "checked='checkd'";
								}
								echo'>
							</td>
							';
					}
					echo'
					<td>
						<input type="date" name="VisitDate" value="'.date("Y-m-d",strtotime($visit->getCheckin_time())).'" required>
						<input type="time" name="VisitTime" value="'.date("H:i",strtotime($visit->getCheckin_time())).'" required>
					</td>
					<td>
						<input class="smalltext" type="text" pattern="[0-9]{5}" name="CCC"';
						if($insurance->getCCC()!=0){
							echo 'value="'.$insurance->getCCC().'"';
						}
						echo'>
					</td>
					<td>
						<input type="checkbox" name="Expired"';
						if($insurance->getExpired()=='1'){
							echo 'checked';
						}
						echo '>		
					</td>
					';
				
				echo"
				
				</tr>
				<tr class='emptytable'>
				<td></td>
				</tr>
				<tr class='emptytable'>
				<td></td>
				</tr>
				<tr class='emptytable'>
				<td colspan='8'>
					<div class='tooltip'>
						<button type='submit' name='submit' value='submit'><i id='submitanc' class='far fa-check-circle fa-4x'></i></button>
						<span class='tooltiptext'>
							submit
						</span>
					</div>	
				</td>
				</tr>
			</form>
			";

	## Print table bottom.
	Patient::tablebottom();
	
	## Print links to delete patients from protocol or entire database, and back to protocol.
	echo'
			<a href="delete_from_protocol.php?protocol_ID='.$visit_ID.'"><div class ="box">delete from protocol</div></a>
			<a href="delete_from_database.php?patient_ID='.$patient_ID.'"><div class ="box">delete completely from system</div></a>
			<a href="patient_protocol.php?from='.$today.'&to='.$today.'"><div class ="box">back to protocol</div></a>
			';

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");	

?>