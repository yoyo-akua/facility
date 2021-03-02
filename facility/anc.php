<?php
	/*
	## Contains global variables which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Initialise new object of visit by a certain visit ID, with which the page is called.
	$visit_ID=$_GET['visit_ID'];
	$visit=new Visit($visit_ID);

	## Create an object of maternity by the ID of that pregnancy data set, with which the page is called.
	$maternity_ID=$_GET['maternity_ID'];
	$maternity=new maternity($maternity_ID);

	## Initialise new object of client by a certain client ID, retreived from the visit object.
	$patient_ID=$visit->getPatient_ID();
	$patient=new Patient($patient_ID);

	## Initialise variable with client's name.
	$name=$patient->getName();

	## Write to database that the patient is pregnant during this visit. 
	$visit->setPregnant(1);

	## This if-branch is called after the user clicks on submit to save client's ANC visit data.
	if(! empty ($_GET['submit'])){
		
		## If the user is just editing (not creating) the ANC data, set the ANC entry's data like the ones the user entered.
		if(! empty($_GET['ANC_ID'])){
			$lastANC=new ANC($_GET['ANC_ID']);
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

			## Create a new protocol entry describing that the client visited ANC.
			$protocol=protocol::new_Protocol($visit_ID,'ANC data entered');
			$protocol_ID=$protocol->getProtocol_ID();

			## Save the ANC data to the database.
			$ANC=ANC::new_ANC($maternity_ID,$protocol_ID,$_GET['FHt'],$_GET['fetal_heart'],$_GET['SP'],$_GET['TT'],$_GET['remarks'],$_GET['visitnumber']);
			$ANC_ID=$ANC->getANC_ID();

			## Add "All other Cases" as diagnosis to client's protocol (this is necessary for the Morbidity report), and set the midwife as client's attendant.
			$query="SELECT Diagnosis_ID FROM diagnoses WHERE DiagnosisName like 'All other Cases'";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			Diagnosis_IDs::new_Diagnosis_IDs($protocol_ID,$object->Diagnosis_ID,1);

		

		}
		
		
		## The patient's vital signs are added to the system, if they were defined in browser.
		if(! empty($_GET['BP']) OR ! empty($_GET['Weight']) OR ! empty($_GET['Pulse']) OR ! empty($_GET['Temperature'])){
			$protocol=protocol::new_Protocol($visit_ID,'vital signs taken');
			$protocol_ID=$protocol->getProtocol_ID();
			$vital_signs=Vital_Signs::new_Vital_Signs($protocol_ID,$_GET['BP'],$_GET['Weight'],$_GET['Pulse'],$_GET['Temperature'],'');
		}
		

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
			$conception_date=date("Y-m-d",(strtotime($visit->getCheckin_time())-$days));
			
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
			$visit->setCheckout_time(date('Y-m-d H:i:s',time()));
			echo '<script>window.location.href=("maternity_patients.php")</script>';
		}
		
		## Print link for editing this ANC visit and the general pregnancy data.
		echo"<a href=\"anc.php?maternity_ID=$maternity_ID&ANC_ID=$ANC_ID&visit_ID=$visit_ID\"><div class ='box'>edit ANC</div></a>";
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
			if(! empty($_GET['ANC_ID'])){
				$lastANC_ID=$_GET['ANC_ID'];
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

		
		## Check if the client received an Insectecite Treated Net (ITN).
		$ITN=$maternity->getITN();
		
		## Print the headline and whether the user is editing the ANC (or not).	
		if(! empty($_GET['ANC_ID'])){
			echo "<h1>Edit ANC of $name</h1>";
		}else{
			echo "<h1>$name in ANC</h1>";
		}

		## Open the input form and print soem general information on the client and her pregnancy. 
		echo"
			<div class='inputform'>
			<form action='anc.php' method='get'>
				<h2>General Pregnancy Data</h2>
				<table class='invisible'>
					<tr>
						<td>".
							$maternity->display_maternity('without EDD');
					echo"</td>
						<td style='font-size:155px;color:lightgrey;font-weight: 100'>
							}	
						</td>
						<td>
							<div class='tooltip'>
								<a href='new_maternity_client.php?maternity_ID=$maternity_ID&edit=on&visit_ID=$visit_ID' class='grey'><i class='fas fa-pencil-alt fa-2x'></i></a>
								<span class='tooltiptext'>
									Edit pregnancy data
								</span>
							</div>
						</td>
						<td style='padding-left:50px'>
							<div class='tooltip'>
								<a href='new_maternity_client.php?maternity_ID=$maternity_ID&new_ANC=on&visit_ID=$visit_ID' '><i class='fas fa-plus-square fa-2x'></i></a>
								<span class='tooltiptext'>
									Add new pregnancy for this client
								</span>
							</div>
						</td>
					<tr>
					</table>
					<h2>ANC Data</h2>
					<table class='invisible' id='anc_table'>
						<tr>
							<td>
								<h3>Vital Signs</h3>
							</td>
							<td colspan='3'>
								<h3>ANC specifics</h3>
							</td>
						</tr>
						<tr>
							<td>
							";
				## Inquire whether vitals have been taken for this visit, if so, save the protocol ID of this database entry in $vitals_ID.
				$vitals_ID=Vital_Signs::get_last_vitals($visit_ID);

				## In case vitals have been taken for this visit, call this if-branch.
				if($vitals_ID){

					## Initialise new Vital Signs object using the protocol ID saved in $vitals_ID.
					$vital_signs=new Vital_Signs($vitals_ID);
					
					## For each vitals parameter (blood pressure, weight, pulse and temperature) create an array, containing
					##		- the name of the parameter
					##		- the previously saved values
					##		- the unit of the parameter
					##		- the type of the variable and further specifications.
					$BP=array('BP',$vital_signs->getBP(),'mmHg',"type='text' pattern='[0-9]{2,}[//]{1}[0-9]{2,}'");
					$Weight=array('Weight',$vital_signs->getweight(),'kg',"type='number' step='0.1' min='0' max='500'");
					$Pulse=array('Pulse',$vital_signs->getpulse(),'bpm',"type='number' min='0' max='200'");
					$Temperature=array('Temperature',$vital_signs->gettemperature(),'&#176C',"type='number' min='30' max='45' step='0.1'");
				}
				
				## In case the vitals haven't been taken before, create the same arrays as listed above, but without the previously saved values.
				else{
					$BP=array('BP','','mmHg',"type='text' pattern='[0-9]{2,}[//]{1}[0-9]{2,}'");
					$Weight=array('Weight','','kg',"type='number' step='0.1' min='0' max='500'");
					$Pulse=array('Pulse','','bpm',"type='number' min='0' max='200'");
					$Temperature=array('Temperature','','&#176C',"type='number' min='30' max='45' step='0.1'");
				}

				## Create an array containing all vital signs parameter arrays created above.
				$vital_parameters=array($BP,$Weight,$Pulse,$Temperature);
				

				/*
				## Run this loop once for each of the vital signs parameters. 
				## Use the information saved in the arrays to display the vital signs parameter as plain text.
				## Print an editing symbol after that,
				## if this is clicked, there appears an input field instead of the plain text
				## which enables the user to edit the value. 
				## This functionality is described in the javascript function edit_value.
				*/
				foreach($vital_parameters AS $para){
					$title=$para[0];
					$value=$para[1];
					$unit=$para[2];
					$specifications=$para[3];

					echo"
						<div>
							<label>$title:</label><br>
							
							<div style='display:none' id='".$title."_div'>
								<input $specifications id='".$title."_input' class='smalltext' name='$title'> $unit
							</div>
		
							<font  id='".$title."_text'>";
								if(! empty($value)){
									echo"$value $unit";
								}else{
									echo"not entered";
								}
								echo"
							</font>
		
							<button type='button' id='edit_".$title."' onclick='edit_value(\"$title\",\"$value\")' class='grey'>
								<i class='fas fa-pencil-alt'></i>
							</button>
		
						</div>";
				}
							
							
				
						## Initialise variable $visitnumber. Use the last vistnumber to calculate it.
						if(!empty($_GET['ANC_ID'])){
							$visitnumber=$lastvisitnumber;
						}else{
							$visitnumber=$lastvisitnumber+1;
						}
		
						/*
						## Display the number of visit as plain text.
						## Print an editing symbol after that,
						## if this is clicked, there appears an input field instead of the plain text
						## which enables the user to edit the value. 
						## This functionality is described in the javascript function edit_value.
						*/
						echo"
						</td>
						<td>
						<div>
							<label>Number of Visit:</label><br>
							
							<div style='display:none' id='number_div'>
								<input type='number' id='number_input' name='visitnumber' min='1' value='$visitnumber'>
							</div>
		
							<font  id='number_text'>
								$visitnumber
							</font>
		
							<button type='button' id='edit_number' onclick='edit_value(\"number\",\"$visitnumber\")' class='grey'>
								<i class='fas fa-pencil-alt'></i>
							</button>
		
						</div>";
		
						## Calculate the gestational age and save it in $weeks and $anddays.
						$days=(strtotime($visit->getCheckin_time())-(strtotime($maternity->getconception_date())))/(24*3600);
						$weeks=floor($days/7);
						$anddays=floor($days-($weeks*7));
		
						/*
						## Display the gestational age as plain text.
						## Print an editing symbol after the number of weeks and after the number of days,
						## if this is clicked, there appears an input field instead of the plain text
						## which enables the user to edit the value. 
						## This functionality is described in the javascript function edit_value.
						*/
						echo"
							<div>
								<label>Gestational Age:</label><br>
								
								<div style='display:none' id='weeks_div'>
									<input type='number'  id='weeks_input' class='smalltext' name='pregnancyweeks' min='0'> weeks and
								</div>
			
								<font  id='weeks_text'>
									$weeks weeks and
								</font>
		
								<button type='button' id='edit_weeks' onclick='edit_value(\"weeks\",\"$weeks\")' class='grey'>
									<i class='fas fa-pencil-alt'></i>
								</button>
		
		
								<div style='display:none' id='days_div'>
									<input type='number' id='days_input' class='smalltext' name='pregnancydays' min='0'> days
								</div>
			
								<font  id='days_text'>
									$anddays days
								</font>
		
								<button type='button' id='edit_days' onclick='edit_value(\"days\",\"$anddays\")' class='grey'>
									<i class='fas fa-pencil-alt'></i>
								</button>
			
							</div>";
		
						## Calculate the estimated delivery date on the basis of the conception date saved in the database.
						$EDD=date("Y-m-d",((strtotime($maternity->getconception_date()))+(40*7*24*3600)));
						$EDDstring=date("d/m/y",strtotime($EDD));
		
						/*
						## Display the estimated delivery date as plain text.
						## Print an editing symbol after that,
						## if this is clicked, there appears an input field instead of the plain text
						## which enables the user to edit the value. 
						## This functionality is described in the javascript function edit_value.
						*/
						echo"
							<div>
								<label>Estimated Delivery Date:</label><br>
								
								<div style='display:none' id='EDD_div'>
									<input type='date' id='EDD_input' name='EDD'>
								</div>
			
								<font  id='EDD_text'>
									$EDDstring
								</font>
			
								<button type='button' id='edit_EDD' onclick='edit_value(\"EDD\",\"$EDD\")' class='grey'>
									<i class='fas fa-pencil-alt'></i>
								</button>
			
							</div>";


				
				
				echo"
				</td>
				<td>
				<div><label>Sulfadoxine Pyrimethamine:</label><br>
				<select name='SP'>
					<option value=''";if((empty($_GET['ANC_ID']) AND $lastSP==5) OR (! empty($_GET['ANC_ID']) AND $lastSP==0)){echo'selected';}echo"> </option>
					<option value='1'";if((empty($_GET['ANC_ID']) AND $lastSP==0) OR (! empty($_GET['ANC_ID']) AND $lastSP==1)){echo'selected';}echo">1st</option>
					<option value='2'";if((empty($_GET['ANC_ID']) AND $lastSP==1) OR (! empty($_GET['ANC_ID']) AND $lastSP==2)){echo'selected';}echo">2nd</option>
					<option value='3'";if((empty($_GET['ANC_ID']) AND $lastSP==2) OR (! empty($_GET['ANC_ID']) AND $lastSP==3)){echo'selected';}echo">3rd</option>
					<option value='4'";if((empty($_GET['ANC_ID']) AND $lastSP==3) OR (! empty($_GET['ANC_ID']) AND $lastSP==4)){echo'selected';}echo">4th</option>
					<option value='5'";if((empty($_GET['ANC_ID']) AND $lastSP==4) OR (! empty($_GET['ANC_ID']) AND $lastSP==5)){echo'selected';}echo">5th</option>
				</select> dosis</div>

				<div><label>Tetanus Diphteria vaccine:</label><br>
				<select name='TT'>
					<option value=''";if((empty($_GET['ANC_ID']) AND $lastTT==5) OR (! empty($_GET['ANC_ID']) AND $lastTT==0)){echo'selected';}echo"> </option>
					<option value='1'";if((empty($_GET['ANC_ID']) AND $lastTT==0) OR (! empty($_GET['ANC_ID']) AND $lastTT==1)){echo'selected';}echo">1st</option>
					<option value='2'";if((empty($_GET['ANC_ID']) AND $lastTT==1) OR (! empty($_GET['ANC_ID']) AND $lastTT==2)){echo'selected';}echo">2nd</option>
					<option value='3'";if((empty($_GET['ANC_ID']) AND $lastTT==2) OR (! empty($_GET['ANC_ID']) AND $lastTT==3)){echo'selected';}echo">3rd</option>
					<option value='4'";if((empty($_GET['ANC_ID']) AND $lastTT==3) OR (! empty($_GET['ANC_ID']) AND $lastTT==4)){echo'selected';}echo">4th</option>
					<option value='5'";if((empty($_GET['ANC_ID']) AND $lastTT==4) OR (! empty($_GET['ANC_ID']) AND $lastTT==5)){echo'selected';}echo">5th</option>
				</select> dosis</div>

				<div><label>Fundal Height:</label><br>
				<input type='number' name='FHt' min='0' max='45' ";if(! empty($_GET['ANC_ID'])){echo "value=".$lastANC->getFHt();}echo"> cm </div>

				<div><label>Fetal Heartrate:</label><br>
				<input type='number' name='fetal_heart'";if(! empty($_GET['ANC_ID'])){echo "value=".$lastANC->getFetal_heart();}echo"> bpm</div>

				</td>
				<td>

				<div><input type='checkbox' name='ITN'";if($ITN==1){echo'checked="checked"';}echo"> <label>ITN</label> given</div>

				<div><label>Remarks:</label><br>
				<input type='text' name='remarks' ";if(! empty($_GET['ANC_ID'])){echo "value='".$lastANC->getRemarks()."'";}echo"></div>

				<input type='checkbox' name='completed'> <label>treatment in clinic completed</label><br>
				";
				if(! empty($_GET['ANC_ID'])){
					echo"<input type='hidden' name='ANC_ID' value='".$_GET['ANC_ID']."'>";
				}

				echo"
				<input type='hidden' name='patient_ID' value='$patient_ID'>
				<input type='hidden' name='maternity_ID' value='$maternity_ID'>
				<input type='hidden' name='visit_ID' value='$visit_ID'>
				<input type='hidden' name='token' value='$uniqueID'>
				<br>
				<div class='tooltip'>
					<button type='submit' name='submit' value='submit'><i id='submitanc' class='far fa-check-circle fa-4x'></i></button>
					<span class='tooltiptext'>
						submit
					</span>
				</div>	
				</td></tr></table>
			</form>
			</div>
			";
	}
	
	## Print links to pregnancy overview, for ordering tests, diagnosing, prescribing drugs and back to the list of potential maternity clients.
	echo"<a href=\"complete_pregnancy.php?maternity_ID=$maternity_ID\"><div class ='box'>pregnancy overview</div></a>
		<a href='order_tests.php?patient_ID=$patient_ID&visit_ID=$visit_ID'><div class='box'>request tests</div></a>
		<a href='patient_visit.php?patient_ID=$patient_ID&visit_ID=$visit_ID'><div class='box'>diagnose</div></a>
		<a href='prescribe_drugs.php?patient_ID=$patient_ID&visit_ID=$visit_ID'><div class='box'>prescribe drugs</div></a>
		<a href='maternity_patients.php?patient_ID=$patient_ID&visit_ID=$visit_ID'><div class='box'>back to maternity clients</div></a>";

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>
