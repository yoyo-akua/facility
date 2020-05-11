<?php
	/*
	## Contains global variables which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Initialise new object of protocol by a certain protocol-ID, with which the page is called.
	$protocol_ID=$_GET['protocol_ID'];
	$protocol=new Protocol($protocol_ID);

	## Initialise new object of client by a certain client ID, with which the page is called.
	$patient_ID=$_GET['patient_ID'];
	$patient=new Patient($patient_ID);

	## Initialise variable with client's name.
	$name=$patient->getName();
	
	/*
	## Get data from database.
	## Get last pregnancy entry for the client.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	## Save the entry in $object and use it to check, if a pregnancy has to be "initialised" for the client first.
	*/
	$query="SELECT maternity_ID FROM maternity WHERE patient_ID=$patient_ID ORDER BY maternity_ID DESC LIMIT 1";
	$result=mysqli_query($link,$query);
	$object=mysqli_fetch_object($result);

	/*
	## Call this if-branch, if
	##		- there has no pregnancy been entered in the system before.
	##		- the user clicked on the button "new pregnancy" for creating a new pregnancy entry.
	##		- the user wants to edit the client's pregnancy data (and has not submitted the edited client's pregnancy data yet).
	*/
	if(empty ($object) OR ! empty($_GET['new_ANC']) OR (! empty($_GET['edit']) AND empty($_GET['submit']))){
		
		## If the user wants to edit the pregnancy data or create a new pregnancy entry, initialise object with previous maternity data.
		if(! empty($_GET['edit']) OR ! empty($_GET['new_ANC'])){
			$last_maternity_ID=$object->maternity_ID;
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
				$conception_date=date("Y-m-d",(strtotime($protocol->getVisitDate())-$days));
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
				if(empty($_GET['edit'])){
					echo"<h2>client created</h2>";
				}
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
				echo "<h1>edit pregnancy data</h1>";
			}else{
				echo"<h1>create client</h1>";
			}
			echo"
					<div class='inputform'>
					<form action='anc.php' method='get'>
						<div><label>Name:</label><br>
						$name</div>".
						
						$patient->display_general(strtotime($protocol->getVisitDate()))."

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

						<div><label>Estimated Week of Pregnancy:</label><br>
						<input type='number' name='pregnancy_week' min='0' max='43'></div>
						
						<div>OR<br><label>Date of last Menstruation:</label><br>
						<input type='date' name='lastmensis' max='$today' ";
						if(! empty($_GET['edit'])){
							echo"value=".$last_maternity->getconception_date();
						}
						echo"></div>


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
					
						if(!empty($_GET['new_ANC'])){
							echo"<input type='hidden' name='new_ANC' value='on'>";
						}
			
						if(! empty($_GET['edit'])){
							echo"<input type='hidden' name='edit' value='".$_GET['edit']."'>";
						}
						echo"
						<input type='hidden' name='protocol_ID' value='$protocol_ID'>
						<input type='hidden' name='patient_ID' value='$patient_ID'>
						<input type='hidden' name='token' value='$uniqueID'>
						<div><input type='submit' name='create' value='submit'></div>
						</div>
						";
		}
	}
	
	/*
	## This if-branch is called, if 
	## 		- the client already has an entry with the general pregnancy data,
	##		- and the user does not want to edit this data set. 
	## Creates an object of maternity by the ID of that pregnancy data set.
	*/
	else{
		$maternity_ID=$object->maternity_ID;
		$maternity=new maternity($maternity_ID);
	}

	## If the maternity ID is set (either because there already was an entry for the entire pregnancy or because the user created a new general pregnancy data set), call this if-branch.
	if(isset($maternity_ID)){

		
		## This if-branch is called after the user clicks on submit to save client's ANC visit data.
		if(! empty ($_GET['submit'])){
			
			## If the user is just editing (not creating) the ANC data, set the ANC entry's data like the ones the user entered.
			if(! empty($_GET['edit'])){
				$lastANC=new ANC($_GET['edit']);
				$lastANC->setFHt($_GET['FHt']);
				$lastANC->setFetalHeart($_GET['fetal_heart']);
				$lastANC->setSP($_GET['SP']);
				$lastANC->setTT($_GET['TT']);
				$lastANC->setRemarks($_GET['remarks']);
				$lastANC->setVisitnumber($_GET['visitnumber']);
				$ANC=$lastANC;
			}
			## If the user was creating a new ANC data set, save it to database and update the client's protocol data.
			else{
				$ANC=ANC::new_ANC($maternity_ID,$_GET['FHt'],$_GET['fetal_heart'],$_GET['SP'],$_GET['TT'],$_GET['remarks'],$_GET['visitnumber']);
				
				## Add "All other Cases" as diagnosis to client's protocol (this is necessary for the Morbidity report), and set the midwife as client's attendant.
				$query="SELECT Diagnosis_ID FROM diagnoses WHERE DiagnosisName like 'All other Cases'";
				$result=mysqli_query($link,$query);
				$object=mysqli_fetch_object($result);
				Diagnosis_IDs::new_Diagnosis_IDs($protocol_ID,$object->Diagnosis_ID,1);
				$protocol->setAttendant('Midwife');
			}
			
			
			## The patient's vital signs are added to the system, if they were defined in browser.
			if(Vital_Signs::already_set($protocol_ID)){
				(new Vital_Signs($protocol_ID))->setVital_signs('get');
			}else{
				$vital_signs=Vital_Signs::new_Vital_Signs($protocol_ID,$_GET['BP'],$_GET['weight'],$_GET['pulse'],$_GET['temperature'],'');
			}
			
			$protocol->setANC_ID($ANC->getANC_ID());
			$protocol->setpregnant(1);

			## Save status about receipt of ITN (inisecticide treated net) to database.
			if(! empty($_GET['ITN'])){
				$maternity->setITN(1);
			}else{
				$maternity->setITN(0);
			}
			
			/*
			## If known, calculate the estimated conception date either from the days or the weeks of pregnancy,
			## depending on what was entered by the user.
			## Update this information in the client's pregnancy data set.
			*/
			if(! empty($_GET['pregnancyweeks'])){
				$days=7*3600*24*($_GET['pregnancyweeks']);
				if(! empty($_GET['pregnancydays'])){
					$days+=3600*24*($_GET['pregnancydays']);
				}
				$conception_date=date("Y-m-d",(strtotime($protocol->getVisitDate())-$days));
				
				/*
				## $conception_date can be calculated from the EDD (estimated delivery date) or the gestational age.
				## To prevent prefilled input fields from resetting changes, this if-branch is necessary.
				## It "empties" the input field of EDD.
				*/
				if($conception_date!==$maternity->getconception_date()){
					$_GET['EDD']='';
					$maternity->setconception_date($conception_date);
				}
			}
			
			/*
			## If known, calculate the conception date from the estimated delivery date.
			## Update this information in the client's pregnancy data set.
			*/
			if(! empty($_GET['EDD'])){
				$conception_date=date("Y-m-d",((strtotime($_GET['EDD']))-(40*7*24*3600)));
				$maternity->setconception_date($conception_date);
			}
			
			/*
			## The client's pregnancy is marked as completed.
			## The client's protocol data is updated.
			## The user is forwarded to the list of all potential maternity clients.
			*/
			if(! empty($_GET['completed'])){
				$protocol->setcompleted(1);
				echo '<script>window.location.href=("maternity_patients.php")</script>';
			}
			
			## Print link for editing this ANC visit and the general pregnancy data.
			echo"<a href=\"anc.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID&edit=".$protocol->getANC_ID()."\"><div class ='box'>edit ANC</div></a>";
		}
		
		## This if-branch is called before the user clicks on submit to save the ANC visit data.
		else{
			/*
			## Get data from database.
			## Get data of the last ANC visit of the client (if there was one) and save it in $object.
			## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
			*/
			$query="SELECT ANC_ID FROM anc WHERE maternity_ID=$maternity_ID ORDER BY ANC_ID DESC LIMIT 1";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);

			/*
			## Call this if-branch to initialise the data of the last ANC visit, if there was one.
			## These are used for prefilling the form with this ANC visit's data.
			*/
			if(! empty($object)){
				
				/*
				## If the user is only editing the ANC visit, use the edited ANC visit as "last visit".
				## Otherwise use the last found ANC visit from database.
				*/
				if(! empty($_GET['edit'])){
					$lastANC_ID=$_GET['edit'];
				}else{
					$lastANC_ID=$object->ANC_ID;
				}
				
				/*
				## Initialise object of ANC for the last visit and use it to set variables with that visit's data.
				## Variable $lastSP contains the number of Malaria prophylaxis (max. 5 possible) at the time of that ANC visit.
				## Variable $lastTT contains the number of Tetanus/Diphteria vaccine (max. 5 possible) at the time of that ANC visit.
				## The client's ANC visits are counted. The variable $lastvisitnumber contains the number of the last ANC visit.
				*/
				$lastANC=new ANC($lastANC_ID);
				$lastSP=$lastANC->getSP();
				$lastTT=$lastANC->getTT();
				$lastvisitnumber=$lastANC->getvisitnumber();
				
				/*
				## If the client didn't take Malaria prophylaxis during her last visit, 
				## check if she has taken before and set $lastSP to the last dose's number.
				*/
				if($lastSP==0){
					$query="SELECT * FROM anc WHERE maternity_ID=$maternity_ID AND SP not like '0' ORDER BY ANC_ID DESC LIMIT 1";
					$result=mysqli_query($link,$query);
					$object=mysqli_fetch_object($result);
					if(!empty($object)){
						$lastSP=$object->SP;
					 }
				}
				
				/*
				## If the client didn't take Tetanus/Diphteria vaccine during her last visit, 
				## check if she has taken before and set $lastTT to the last dose's number.
				*/
				if($lastTT==0){
					$query="SELECT * FROM anc WHERE maternity_ID=$maternity_ID AND TT not like '0' ORDER BY ANC_ID DESC LIMIT 1";
					$result=mysqli_query($link,$query);
					$object=mysqli_fetch_object($result);
					if(!empty($object)){
						$lastTT=$object->TT;
					}
				}
			}

			## If there was no previous ANC visit, just set the data of the "last ANC visit" to zero.
			else{
				$lastSP=0;
				$lastTT=0;
				$lastvisitnumber=0;
			}
			
			## Initialise variables with the client's vital signs, entered at the admission.
			$vital_signs=new Vital_Signs($protocol_ID);
			
			$currentBP=$vital_signs->getBP();
			$currentweight=$vital_signs->getweight();
			$currentpulse=$vital_signs->getpulse();
			$currenttemperature=$vital_signs->gettemperature();

			## Calculate the gestational age and save it in $weeks and $anddays.
			$days=(strtotime($protocol->getVisitDate())-(strtotime($maternity->getconception_date())))/(24*3600);
			$weeks=floor($days/7);
			$anddays=floor($days-($weeks*7));
			
			## Check if the client received an Insectecite Treated Net (ITN).
			$ITN=$maternity->getITN();
			
			## Calculate the estimated delivery date and the number of visit.
			$EDD=date("Y-m-d",((strtotime($maternity->getconception_date()))+(40*7*24*3600)));
			$visitnumber=$lastvisitnumber+1;
			
			## Print headline and form with input fields, which are prefilled with data that are calculated from the last visit's data and the general pregnancy data.
			
			if(! empty($_GET['edit'])){
				echo "edit ANC of $name";
			}else{
				echo "<h1>$name in ANC</h1>";
			}
			echo"
				<div class='inputform'>
				<form action='anc.php' method='get'>".
					$maternity->display_maternity()."

					<div><label>Gestational Age:</label><br>
					<input type='number' name='pregnancyweeks' value='$weeks' min='0' max='45'> weeks and 
					<input type='number' name='pregnancydays' value='$anddays' min='0' max='6'> days</div>

					<div><label>Estimated Delivery Date:</label><br>
					<input type='date' name='EDD' value='$EDD'></div>

					<div><label>Number of Visit:</label><br>
					<input type='number' name='visitnumber' value='";if(!empty($_GET['edit'])){echo $lastvisitnumber;}else{echo $visitnumber;}echo"' min='1' required></div>

					<div><label>BP:</label><br>
					<input type='text' class='smalltext' name='BP' ";if($currentBP!=='0'){echo"value='$currentBP'";}echo"> mmHg</div>

					<div><label>Weight:</label><br>
					<input type='number'step='0.1' name='weight' ";if($currentweight!=0){echo"value='$currentweight'";}echo" min='30.0' max='150.0'> kg</div>

					<div><label>Pulse:</label><br>
					<input type='number' name='pulse' min='0' max='200' ";if($currentpulse!=='0'){echo"value='$currentpulse'";}echo"> bpm</div>

					<div><label>Temperature:</label><br>
					<input type='number' name='temperature' min='0' max='45' step='0.1'";if($currenttemperature!=='0.0'){echo"value='$currenttemperature'";}echo"> &#176C</div>
					<div><label>Sulfadoxine Pyrimethamine:</label><br>
					<select name='SP'>
						<option value=''";if((empty($_GET['edit']) AND $lastSP==5) OR (! empty($_GET['edit']) AND $lastSP==0)){echo'selected';}echo"> </option>
						<option value='1'";if((empty($_GET['edit']) AND $lastSP==0) OR (! empty($_GET['edit']) AND $lastSP==1)){echo'selected';}echo">1st</option>
						<option value='2'";if((empty($_GET['edit']) AND $lastSP==1) OR (! empty($_GET['edit']) AND $lastSP==2)){echo'selected';}echo">2nd</option>
						<option value='3'";if((empty($_GET['edit']) AND $lastSP==2) OR (! empty($_GET['edit']) AND $lastSP==3)){echo'selected';}echo">3rd</option>
						<option value='4'";if((empty($_GET['edit']) AND $lastSP==3) OR (! empty($_GET['edit']) AND $lastSP==4)){echo'selected';}echo">4th</option>
						<option value='5'";if((empty($_GET['edit']) AND $lastSP==4) OR (! empty($_GET['edit']) AND $lastSP==5)){echo'selected';}echo">5th</option>
					</select> dosis</div>

					<div><label>Tetanus Diphteria vaccine:</label><br>
					<select name='TT'>
						<option value=''";if((empty($_GET['edit']) AND $lastTT==5) OR (! empty($_GET['edit']) AND $lastTT==0)){echo'selected';}echo"> </option>
						<option value='1'";if((empty($_GET['edit']) AND $lastTT==0) OR (! empty($_GET['edit']) AND $lastTT==1)){echo'selected';}echo">1st</option>
						<option value='2'";if((empty($_GET['edit']) AND $lastTT==1) OR (! empty($_GET['edit']) AND $lastTT==2)){echo'selected';}echo">2nd</option>
						<option value='3'";if((empty($_GET['edit']) AND $lastTT==2) OR (! empty($_GET['edit']) AND $lastTT==3)){echo'selected';}echo">3rd</option>
						<option value='4'";if((empty($_GET['edit']) AND $lastTT==3) OR (! empty($_GET['edit']) AND $lastTT==4)){echo'selected';}echo">4th</option>
						<option value='5'";if((empty($_GET['edit']) AND $lastTT==4) OR (! empty($_GET['edit']) AND $lastTT==5)){echo'selected';}echo">5th</option>
					</select> dosis</div>

					<div><label>Fundal Height:</label><br>
					<input type='number' name='FHt' min='0' max='45' ";if(! empty($_GET['edit'])){echo "value=".$lastANC->getFHt();}echo"> cm </div>

					<div><label>Fetal Heartrate:</label><br>
					<input type='number' name='fetal_heart'";if(! empty($_GET['edit'])){echo "value=".$lastANC->getFetal_heart();}echo"> bpm</div>

					<div><input type='checkbox' name='ITN'";if($ITN==1){echo'checked="checked"';}echo"> <label>ITN</label> given</div>

					<div><label>Remarks:</label><br>
					<input type='text' name='remarks' ";if(! empty($_GET['edit'])){echo "value='".$lastANC->getRemarks()."'";}echo"></div>

					<input type='checkbox' name='completed'> <label>treatment in clinic completed</label><br>
					";
					if(! empty($_GET['edit'])){
						echo"<input type='hidden' name='edit' value='".$_GET['edit']."'>";
					}

					echo"
					<input type='hidden' name='protocol_ID' value='$protocol_ID'>
					<input type='hidden' name='patient_ID' value='$patient_ID'>
					<input type='hidden' name='token' value='$uniqueID'>
					<div><input type='submit' name='submit' value='submit'></div>
				</form>
				<br><br><br>
					OR:<a href='anc.php?protocol_ID=$protocol_ID&patient_ID=$patient_ID&new_ANC=on'>new pregnancy</a>
				</div>
				";
		}
		
		## Print links to pregnancy overview, for ordering tests, diagnosing, prescribing drugs and back to the list of potential maternity clients.
		echo"<a href=\"complete_pregnancy.php?maternity_ID=$maternity_ID\"><div class ='box'>pregnancy overview</div></a>
			<a href='order_tests.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID'><div class='box'>request tests</div></a>
			<a href='patient_visit.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID'><div class='box'>diagnose</div></a>
			<a href='prescribe_drugs.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID'><div class='box'>prescribe drugs</div></a>
			<a href='maternity_patients.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID'><div class='box'>back to maternity clients</div></a>";
	}

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>
