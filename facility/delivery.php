<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	/*
	## Initialise new objects of patient, maternity, visit
	## and maternity (which contains general data of the client's pregnancy).
	*/

	$visit_ID=$_GET['visit_ID'];
	$visit=new Visit($visit_ID);

	$maternity_ID=$_GET['maternity_ID'];
	$maternity=new maternity($maternity_ID);

	$patient_ID=$visit->getPatient_ID();
	$patient=new Patient($patient_ID);

	/*
	## Inquire whether the user is editing the delivery and there are any data as to a previous entry,
	## save this as a status in $edit.
	*/
	if(! empty($_GET['edit'])){
		$query="SELECT * FROM delivery WHERE maternity_ID=$maternity_ID AND del_category_ID=1";
		$edit_result=mysqli_query($link,$query);
		$object=mysqli_fetch_object($edit_result);
		if(! empty($object)){
			$edit=true;
			$del_protocol_ID=$object->protocol_ID;
			$del_protocol=new Protocol($del_protocol_ID);
		}else{
			$edit=false;
		}
	}else{
		$edit=false;
	}

	## If the user is submitting the delivery entry form, call this if-branch.
	if(! empty($_GET['submit'])){

		## In case the user is not only editing the delivery data, create a new protocol entry and save the ID in $protocol_ID.
		if(! $edit){
			$del_protocol=protocol::new_Protocol($visit_ID,'client delivered');
			$del_protocol_ID=$del_protocol->getProtocol_ID();
		}


		## If the user referred the client in labour, call this if-branch.
		if(! empty($_GET['refer'])){

			/*
			## Check if the client has been marked as referred before, 
			## if so overwrite this information with the now entered specifics, 
			## otherwise create a new referral entry and a corresponding protocol entry.
			*/
			if(! Referral::checkReferral($visit_ID)){
				Referral::new_Referral($protocol_ID,$_GET['referredto'],$_GET['refer_reason']);
				protocol::new_Protocol($visit_ID,'client referred in labour');
			}else{
				$referral=new Referral(Referral::checkReferral($visit_ID));
				$referral->setDestination($_GET['referredto']);
				$referral->setReason($_GET['refer_reason']);
			}
			$message="Client has been referred";
		
			## If the user indicated a reason for the referral, add it to $referred and $message.
			if(! empty($_GET['refer_reason'])){
				$reason=$_GET['refer_reason'];
				$message.=" because of $reason";
			}
			
			## Display the message box.
			Settings::messagebox($message);
						
			## Set the client's treatment completed.
			$visit->setCheckout_time(date('Y-m-d H:i:s',time()));
			
			## Automatically forward the user back to the maternity client list.
			echo "
					<script language=\"JavaScript\">
					window.location.href='maternity_patients.php'
					</script>
					";
		}else{

			## In case the user unselected a referral, delete that information from database.
			if(Referral::checkReferral($visit_ID)){
				$referral_ID=Referral::checkReferral($visit_ID);
				Referral::delete_Referral($referral_ID);
				Protocol::delete_Protocol($referral_ID);
			}
		}
		
		/*
		## Get data from database.
		## Get list of all delivery parameters and their meta data.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		*/
		$query="SELECT * FROM delivery_categories";
		$result=mysqli_query($link,$query);
		
		## This loop will be run once for each of the output delivery parameters from the database query.
		while($row=mysqli_fetch_object($result)){
			
			## Initialise variable with the parameter's ID.
			$ID=$row->del_category_ID;
			
			## If the user activated the twin delivery, the loop is run twice for some of the parameters.
			if(! empty($_GET['twins']) AND($ID==1 OR ($ID>=6 AND $ID<=20) OR ($ID>=23 AND $ID<=26) OR $ID==30)){
				for($i=1;$i<=2;$i++){
					
					/*
					## If the parameter deals with a time, transfer the entered times in the input fields to American time format,
					## which can be interpreted by the system.
					## Combine the date and time information to be saved in the database.
					*/
					if(! empty($_GET["time_$ID-$i"])){
						$date=date("Y-m-d",strtotime($_GET[$ID]));
						$time=date("H:i",strtotime($_GET["time_$ID"]));
						$Outcome=$date.' '.$time;

						## Save the time of delivery also in the protocol table.
						if($ID==6 AND $i==2){
							$del_protocol->setTimestamp($Outcome);
						}
					}
					
					## If the parameter is the APGAR-score, combine the score after one and after ten min to be inserted in the database.
					else if($ID==9){
						$Outcome=$_GET["APGAR_1-$ID-$i"].'/10, '.$_GET["APGAR_5-$ID-$i"].'/10';
					}
					
					## In any other case just buffer the value entered by the user to be saved in the database.
					else{
						$Outcome=$_GET["$ID-$i"];
					}
					
					## Complete the information to be entered in the database by the "number of baby" for which it applies.
					$Outcome="Baby $i: $Outcome";
					
					/*
					## In case the user is editing the delivery entry, update the information in the corresponding entry of the delivery register.
					## Otherwise create a new entry.
					*/
					if($edit){
						$query="UPDATE delivery SET result='$Outcome' WHERE del_category_ID='$ID' AND result like '%Baby $i:%' AND maternity_ID=$maternity_ID";
						mysqli_query($link,$query);
					}else{
						Delivery::new_delivery($ID,$Outcome,$maternity_ID,$del_protocol_ID);
					}
				}
			}
			
			## If the user didn't activate the twin delivery and/or the parameter doesn't belong to those who can differ for each twin, call this if-branch.
			else{
				
				/*
				## If the parameter deals with a time, transfer the entered times in the input fields to American time format,
				## which can be interpreted by the system.
				## Combine the date and time information to be saved in the database.
				*/
				if(! empty($_GET["time_$ID"])){
					$date=date("Y-m-d",strtotime($_GET[$ID]));
					$time=date("H:i",strtotime($_GET["time_$ID"]));
					$Outcome=$date.' '.$time;

					## Save the time of delivery also in the protocol table.
					if($ID==6){
						$del_protocol->setTimestamp($Outcome);
					}else if($ID==32){
						$discharge_protocol->setTimestamp($Outcome);
						$maternity->setDelivery_date($date);
					}
				}

				## If the parameter is the APGAR-score, combine the score after one and after ten min to be inserted in the database.
				else if($ID==9){
					$Outcome=$_GET["APGAR_1-$ID"].'/10, '.$_GET["APGAR_5-$ID"].'/10';
				} 

				## In any other case just buffer the value entered by the user to be saved in the database.
				else{
					$Outcome=$_GET["$ID"];
				}
				
				
				## Enter the vital signs of the mother at discharge also in the vital signs table. 
				if($ID==26){
					if($edit){
						
						$query="SELECT * FROM vital_signs v, delivery d WHERE d.protocol_ID=v.protocol_ID AND d.del_category_ID=26 AND maternity_ID=$maternity_ID ";
						$result2=mysqli_query($link,$query);
						$object=mysqli_fetch_object($result2);
						$del_protocol_ID=$object->protocol_ID;
						$vitals_discharge=new Vital_Signs($del_protocol_ID);
						$vitals_discharge->settemperature($_GET['26']);
						$vitals_discharge->setBP($_GET['27']);
						$vitals_discharge->setpulse($_GET['28']);
						
					}else{
						$protocol=protocol::new_Protocol($visit_ID,'vital signs taken');
						$del_protocol_ID=$protocol->getProtocol_ID();
						$vital_signs=Vital_Signs::new_Vital_Signs($del_protocol_ID,$_GET['27'],'',$_GET['28'],$_GET['26'],'');
					}
				}
				
				## Create a new protocol entry for the discharge of the client.
				else if ($ID==29){
					if($edit){
						$query="SELECT protocol_ID FROM delivery WHERE maternity_ID=$maternity_ID AND del_category_ID=29";
						$result2=mysqli_query($link,$query);
						$object=mysqli_fetch_object($result2);
						$del_protocol_ID=$object->protocol_ID;
						$discharge_protocol=new protocol($del_protocol_ID);
					}else{
						$discharge_protocol=Protocol::new_Protocol($visit_ID,'delivery client discharged');
						$del_protocol_ID=$discharge_protocol->getProtocol_ID();
					}
				}
				

				/*
				## In case the user is editing the delivery entry, update the information in the corresponding entry of the delivery register.
				## Otherwise create a new entry.
				*/
				if(isset($Outcome)){
					if($edit){
						$query="UPDATE delivery SET result='$Outcome' WHERE del_category_ID='$ID' AND maternity_ID=$maternity_ID";
						mysqli_query($link,$query);
					}else{
						Delivery::new_delivery($ID,$Outcome,$maternity_ID,$del_protocol_ID);
					}
				}

				
			}
		}
		
		## If the user selected the "treatment in clinic completed" checkbox, set the treatment as completed in the protocol.
		if(! empty($_GET['completed'])){
			$visit->setCheckout_time(date('Y-m-d H:i:s',time()));
		}else{
			$visit->setCheckout_time('0000-00-00 00:00:00');
		}
		
		## Set the client as pregnant in the protocol (at least when she arrived in the facility she still was pregnant).
		$visit->setPregnant(1);
		
		if(!$edit){
			## Set the diagnosis of the client to "All other Cases".
			$query="SELECT Diagnosis_ID FROM diagnoses WHERE DiagnosisName like 'All other Cases'";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			Diagnosis_IDs::new_Diagnosis_IDs($del_protocol_ID,$object->Diagnosis_ID,1,'',0);
		}
		
		
		
		## The patient's vital signs are added/updated in the system, if they were defined in browser.
		if(! empty($_GET['BP']) OR ! empty($_GET['Weight']) OR ! empty($_GET['Pulse']) OR ! empty($_GET['Temperature'])){
			if($edit){
				if(! empty($_GET['vitals_ID'])){
					$vitals_ID=$_GET['vitals_ID'];
					$vital_signs=new Vital_Signs($vitals_ID);

					if(! empty($_GET['Weight'])){
						$vital_signs->setweight($_GET['Weight']);
					}
					if(! empty($_GET['Temperature'])){
						$vital_signs->settemperature($_GET['Temperature']);
					}
					if(! empty($_GET['BP'])){
						$vital_signs->setBP($_GET['BP']);
					}
					if(! empty($_GET['Pulse'])){
						$vital_signs->setpulse($_GET['Pulse']);
					}
				}
				
			}else{
				$protocol=protocol::new_Protocol($visit_ID,'vital signs taken');
				$protocol_ID=$protocol->getProtocol_ID();
				$vital_signs=Vital_Signs::new_Vital_Signs($protocol_ID,$_GET['BP'],$_GET['Weight'],$_GET['Pulse'],$_GET['Temperature'],'');
			}
		}

		## Print links to edit the delivery, the client's pregnancy overview and back to the list of Maternity clients.
		echo"
				<a href='delivery.php?visit_ID=$visit_ID&maternity_ID=$maternity_ID&edit=on'><div class='box'>Edit Delivery</div></a>
				<a href='complete_pregnancy.php?maternity_ID=$maternity_ID'><div class='box'>Pregnancy Overview</div></a>
				<a href='maternity_patients.php'><div class='box'>Clients in Maternity</div></a>
				";
	}
	
	## This if-branch is called, if the user enters the pages and has not clicked on the submit button yet.
	else{

		## Initialise variables with the name and information about a referral of the client.
		$name=$patient->getName();

		echo "
			<h1>Delivery of $name</h1>
			<table class='invisible' style='margin-left:20px' id='anc_table'>
				<tr>
					<td>
						<h3>General Information</h3>
				";

		/*
		## Depending on the selection of the client or, if available any previous data, 
		## use variable $twins to set a "twin status".
		*/
		if(! empty($_GET['twins']) OR ($edit AND mysqli_num_rows($edit_result)>40)){
			$twins=true;
		}else{
			$twins=false;
		}
		
		/*
		## Depending on the delivery being a twin or a single delivery, print a button to change this status, 
		## provided the user has not previously entered the delivery data and is merely editing them now.
		*/
		if(! $edit){
			if($twins){
				echo"  
				<div class='tooltip'>
					<a href='delivery.php?visit_ID=$visit_ID&maternity_ID=$maternity_ID'>
						<i class='fas fa-baby fa-3x'></i>
						<i class='fas fa-baby fa-3x greened'></i>
					</a>
					<span class='tooltiptext'>
						switch back to no twins
					</span>
				</div>
				";
			}else{
				echo"  
				<div class='tooltip'>
					<a href='delivery.php?visit_ID=$visit_ID&maternity_ID=$maternity_ID&twins=on'>
						<i class='fas fa-baby fa-3x'></i>
						<i class='fas fa-baby fa-3x greyed'></i>
					</a>
					<span class='tooltiptext'>
						switch to twins
					</span>
				</div>
				";
			}
		}

		## Check whether the client has been marked as referred before. 
		$referred=Referral::checkReferral($visit_ID);
		
		## Print the headline and the input form for the referral information. 
		## In case information about any referral are available, prefill the form with those.
		echo"
				<br><br>
				<form action='delivery.php' method='get'>
				<table class='invisible'>
					<tr>
						<td style='vertical-align:top'>
				";
		
			/*
			## Inquire, whether patient was referred to another hospital.
			## Print a checkbox for referral, which is checked, if patient was referred.
			## If so, also display the input field for destination and reason of the referral.
			*/
			echo "<input type='checkbox'  id='unfold_item' onClick='unfold()' name='refer'";
			if ($referred OR ! empty($_POST['referral'])){
				echo "checked='checked'";
			}
			echo">
				<i class='fas fa-share fa-2x'></i> refer
				</td>
				<td id='unfold_content'";
				if (!$referred AND empty($_POST['referral'])){
					echo "style='display:none'";
				}
				echo"'>
				<div>
				<i>destination:</i> <br>
				<input type='text' maxlength='200' name='referredto' ";
				if ($referred OR ! empty($_POST['referredto'])){
					if(! empty($_POST['referredto'])){
						$destination=$_POST['referredto'];
					}else{
						$referral=new Referral($referred);
						$destination=$referral->getDestination();
					}
					echo "value='$destination'";
				}
				echo"style='width:300px'><br>
				<i>reason for referral:</i> <br>
				<textarea name='refer_reason' maxlength='1000'> ";
				if ($referred OR ! empty($_POST['refer_reason'])){
					if(! empty($_POST['refer_reason'])){
						echo $_POST['refer_reason'];
					}else{
						$referral=new Referral($referred);
						echo $referral->getReason();
					}
				}
				echo"</textarea></div>
				</td>
				</tr>
				</table>
				";
		


	
		
		## Print a form and prefill it with the clien's vital signs.
		echo '<h3>Vital Signs at admission</h3>';
		$query="SELECT * FROM vital_signs v, protocol p WHERE p.protocol_ID=v.protocol_ID AND p.visit_ID=$visit_ID AND p.protocol_ID NOT LIKE (SELECT v.protocol_ID FROM vital_signs v, delivery d WHERE d.protocol_ID=v.protocol_ID AND d.del_category_ID=26 AND maternity_ID=$maternity_ID) ORDER BY p.protocol_ID DESC LIMIT 0,1";
		$result=mysqli_query($link,$query);
		$object=mysqli_fetch_object($result);
		if (! empty($object)){
			$protocol_ID=$object->protocol_ID;
			echo '<input type="hidden" name="vitals_ID" value="'.$protocol_ID.'">';
		}
		Vital_Signs::display_editable_Vitals($protocol_ID);
		

		/*
		## Get data from database.
		## Get list of all delivery parameters and their meta data.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		*/
		$query="SELECT * FROM delivery_categories ORDER BY del_category_ID ASC";
		$result=mysqli_query($link,$query);

		## This loop will be run once for each of the output delivery parameters from the database query.
		while($row=mysqli_fetch_object($result)){
			
			## Initialise variables with the parameter's name, it's possible outcomes and its ID.
			$Category=$row->category_name;
			$Outcome=$row->outcomes;
			$ID=$row->del_category_ID;
			
			if($ID==1){
				echo '<h3>Stage 1-3</h3>';
			}
			if($ID==7){
				echo '</td><td><h3>State of the Newborn</h3>';
			}
			if($ID==18){
				echo '</td><td><h3>Newborn Care</h3>';
			}
			if($ID==21){
				echo '<h3>Stage 4</h3>';
			}
			if($ID==26){
				echo '</td><td><h3>Discharge</h3>';
			}

			echo '<div style="margin-top: 10px">';
			## If the user activated the twin delivery, the loop is run twice for some of the parameters.
			if($twins AND ($ID==1 OR ($ID>=6 AND $ID<=20) OR ($ID>=23 AND $ID<=25) OR $ID==29 OR $ID==31)){
				for($i=1;$i<=2;$i++){
					
					## Call this if-branch, if the user is editing the delivery.
					if($edit){
						
						/*
						## Get data from database.
						## Get the previously entered data for this entry of the delivery register and store them in $results,
						## which will later be used to prefill the input field.
						## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
						*/
						$query="SELECT * FROM delivery WHERE delivery.del_category_ID=$ID AND delivery.maternity_ID=$maternity_ID";
						$query.=" AND result LIKE '%Baby $i:%'";
						$result2=mysqli_query($link,$query);
						$object=mysqli_fetch_object($result2);
						$results=$object->result;
						$results=str_replace("Baby $i: ",'',"$results");
					}
					
					## If the user is not editing, set $results as an empty variable.
					else{
						$results='';
					}
					
					## Print the name of the parameter and the "number of the baby".
					echo"<div>Baby $i:<label>$Category</label><br>";
					
					## Call a function which is printing the input field for the parameter.
					Delivery::print_form($results,$edit,$ID,"$ID-$i",$Outcome);
				}
			}
			
			## If the user didn't activate the twin delivery and/or the parameter doesn't belong to those who can differ for each twin, call this if-branch.
			else{
				
				## Print the name of the parameter.
				echo"<div><label>$Category</label><br>";

				## Call this if-branch, if the user is editing the delivery.
				if($edit){

					/*
					## Get data from database.
					## Get the previously entered data for this entry of the delivery register and store them in $results which will later be used to prefill the input field.
					## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
					*/
					$query="SELECT * FROM delivery WHERE delivery.del_category_ID=$ID AND delivery.maternity_ID=$maternity_ID";
					$result2=mysqli_query($link,$query);
					$object=mysqli_fetch_object($result2);
					$results=$object->result;
				}
				
				## If the user is not editing, set $results as an empty variable.
				else{
					$results='';
				}
				
				## Call a function which is printing the input field for the parameter.
				Delivery::print_form($results,$edit,$ID,$ID,$Outcome);
				
				echo '</div>';
			}
		}
		
		## In case the twin delivery is activated, send this parameter along with the form.
		if($twins){
			echo"<input type='hidden' name='twins' value='on'>";
		}
		
		## Print the "treatment in clinic completed" checkbox and the close of the form.
		echo"
				<div><input type='checkbox' name='completed'";
				if($edit AND $visit->getCheckout_time()!=='0000-00-00 00:00:00'){
					echo "checked='checked'";
				}
				echo"> <label>treatment in clinic completed</label></div>
				<br>
				<input type='hidden' name='visit_ID' value='$visit_ID'>
				<input type='hidden' name='maternity_ID' value='$maternity_ID'>
				<input type='hidden' name='token' value='$uniqueID'>
				";
				
				## In case the user is editing, send this parameter along with the form.
				if($edit){
					echo "<input type='hidden' name='edit' value='on'>";
				}
				echo"
				<div class='tooltip'>
					<button type='submit' name='submit' value='submit'><i id='submitanc' class='far fa-check-circle fa-4x'></i></button>
					<span class='tooltiptext'>
						submit
					</span>
				</div>
				</form>
				</td>
				</tr>
				</table>
				";
	}

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>