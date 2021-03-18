<?php
	/*
	## Contains global variables which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Initialise new object of visit by a certain visit ID, with which the page is called.
	$visit_ID=$_GET['visit_ID'];
	$visit=new Visit($visit_ID);
	
	## Initialise new object of client by a certain client ID, retreived from the visit object.
	$patient_ID=$visit->getPatient_ID();
	$patient=new Patient($patient_ID);

	## Initialise variable with client's name.
	$name=$patient->getName();

	## If the user wants to edit the pregnancy data or create a new pregnancy entry, initialise object with previous maternity data.
	if(! empty($_GET['edit']) OR ! empty($_GET['new_ANC'])){
		$last_maternity_ID=$_GET['maternity_ID'];
		$last_maternity=new Maternity($last_maternity_ID);
	}
	
	## This if-branch is called, if the user submitted the page for entering the general pregnancy data. That means the user has created or updated a patient's pregnancy data set and is saving this.
	if(! empty($_GET['create'])){
		
		/*
		## Calculate the estimated conception date either from the date of the last menstruation or the week of pregnancy,
		## depending on what was entered by the user.
		*/
		if(! empty($_GET['lastmensis'])){
			$conception_date=$_GET['lastmensis'];
		}else if (! empty($_GET['pregnancy_week'])){
			$pregnancy_week=$_GET['pregnancy_week'];
			$days=7*3600*24*$pregnancy_week;
			$conception_date=date("Y-m-d",(strtotime($visit->getCheckin_time())-$days));
		}
		
		## If the user is just editing (not creating) the pregnancy data, set the pregnancy entry's data in the database like the ones the user entered.
		if(! empty($_GET['edit'])){
			$patient->setTelephone($_GET['telephone']);
			$last_maternity->setconception_date($conception_date);
			$last_maternity->setParity($_GET['parity']);
			$patient->setHeight($_GET['height']);
			$last_maternity->setOccupation($_GET['occupation']);
			$last_maternity->setSerial_number($_GET['serial_number']);
			$last_maternity->setReg_number($_GET['reg_number']);
			$maternity_ID=$last_maternity_ID;
			$maternity=$last_maternity;

			echo '<script>history.go(-2)</script>';
		}
		
		/*
		## If the user is creating (not editing) a new pregnancy data set, add a new entry to the database and create an object of maternity with these data.
		## Provided the proposed serial and registration number match the ones entered by the user,
		## update the last numbers in the database, so that the next ones can be proposed correctly.
		*/
		else{
			$maternity=maternity::new_maternity($_GET['patient_ID'],$conception_date,$_GET['parity'],$_GET['occupation'],$_GET['serial_number'],$_GET['reg_number']);
			$patient->setTelephone($_GET['telephone']);
			$patient->setHeight($_GET['height']);
			$maternity_ID=$maternity->getmaternity_ID();

			if(! empty ($_GET['serial_number']) AND $_GET['serial_number']==Settings::new_number('Maternity','serial_number') AND empty($_GET['edit'])){
				Settings::set_new_number('Maternity',Settings::new_number('Maternity','serial_number'),'serial_number');
			}
			if(! empty ($_GET['reg_number']) AND $_GET['reg_number']==Settings::new_number('Maternity','reg_number') AND empty($_GET['edit'])){
				Settings::set_new_number('Maternity',Settings::new_number('Maternity','reg_number'),'reg_number');
			}

			Protocol::new_Protocol($visit_ID,'registered as maternity client');
			$message='Maternity client created';
			Settings::messagebox($message);
			echo'<script>window.location.href="anc.php?maternity_ID='.$maternity_ID.'&visit_ID='.$visit_ID.'"</script>';
		}
	}
	
	## This if-branch is called, if the user is yet to enter the general pregnancy data.
	else{
		
		/*
		## Print headline, a styling element for border spacing, the beginning of the form and the client's general data.
		## Below that, print an input form for all the general pregnancy data. 
		## Propose the next serial and registration number, calculated from the system, to the user.
		## If the client has been for ANC with another pregnancy before or the user just wants to edit the data, prefill the input fields with previous data.
		*/
		if(! empty($_GET['edit'])){
			echo "<h1>Edit Pregnancy Data</h1><h2 style='text-align:center'>of $name</h2>";
		}else{
			echo"<h1>Create Maternity Client</h1><h2 style='text-align:center'>$name</h2>";
		}
		echo"
				<div class='inputform'>
				<form action='new_maternity_client.php' method='get'>".
					
					$patient->display_general(strtotime($visit->getCheckin_time()))."

					<div><label>Serial Number:</label><br>
					<input type='text' name='serial_number' ";
					if(! empty($_GET['edit'])){
						echo"value=".$last_maternity->getSerial_number();
					}else{
						$serial_number=Settings::new_number('Maternity','serial_number');

						echo"value='$serial_number' pattern='[0-9]{1,}[//]{1}[0-9]{2}'";
					}
					echo"></div>

					<div><label>Registration Number:</label><br>
					<input type='text' name='reg_number' ";
					if(! empty($_GET['edit'])){
						echo"value=".$last_maternity->getReg_number();
					}else{
						$reg_number=Settings::new_number('Maternity','reg_number');

						echo"value='$reg_number' pattern='[0-9]{1,}[//]{1}[0-9]{2}'";
					}
					echo"></div>

					<div><label>Occupation:</label><br>
					<input type='text' name='occupation' ";
					if(! empty($_GET['edit']) OR ! empty($_GET['new_ANC'])){
						echo"value=".$last_maternity->getoccupation();
					}
					echo"></div>
					
					<div><label>Parity:</label><br>
					<input type='text' name='parity' ";
					if(! empty($_GET['edit'])){
						echo"value=".$last_maternity->getparity();
					}
					echo"></div>

					<table style='margin-left:0px;'>
						<tr>
							<td style='text-align:left;padding-left:0px;border:none'>
								<div>
									<label>Date of last Menstruation:</label><br>
									<input type='date' name='lastmensis' max='$today' ";
									if(! empty($_GET['edit'])){
										echo"value=".$last_maternity->getconception_date();
									}
									echo">
								</div>
					
							</td>
							<td style='text-align:left;padding-left:20px;border:none'>
								<div>
									If not known - Estimated Week of Pregnancy:<br>
									<input type='number' name='pregnancy_week' min='0' max='43'>
								</div>
							</td>
						</tr>
					</table>

					<div><label>Phone Number:</label><br>
					<input type='text' name='telephone' ";
					if(! empty($_GET['edit']) OR ! empty($_GET['new_ANC'])){
						echo"value='".$patient->getTelephone()."'";
					}
					echo" pattern='[0-9]{10}'></div>

					<div><label>Height:</label><br>
					<input type='number' name='height' min='100' max='200' ";
					if(! empty($_GET['edit']) OR ! empty($_GET['new_ANC'])){
						echo"value=".$patient->getHeight();
					}
					echo"> cm</div>";
				
		
					if(! empty($_GET['edit']) OR ! empty($_GET['new_ANC'])){
						echo "<input type='hidden' name='maternity_ID' value='$last_maternity_ID'>";
						if(! empty($_GET['edit'])){
							echo"<input type='hidden' name='edit' value='on'>";
						}else if(! empty($_GET['new_ANC'])){
							echo"<input type='hidden' name='new_ANC' value='on'>";
						}
					}
					echo"
					<input type='hidden' name='visit_ID' value='$visit_ID'>
					<input type='hidden' name='patient_ID' value='$patient_ID'>
					<input type='hidden' name='token' value='$uniqueID'>
					<div><input type='submit' name='create' value='submit'></div>
					</div>
					</form>
					";
					
	}


	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>