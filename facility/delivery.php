<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	/*
	## Initialise new objects of patient, 
	## protocol (which represents the client visit on which the function is called),
	## and maternity (which contains general data of the client's pregnancy).
	*/
	$patient_ID=$_GET['patient_ID'];
	$patient=new Patient($patient_ID);

	$protocol_ID=$_GET['protocol_ID'];
	$protocol=new Protocol($protocol_ID);

	$maternity_ID=$_GET['maternity_ID'];
	$maternity=new maternity($maternity_ID);


	/*
	## Inquire whether the user is editing the delivery and there are any data as to a previous entry,
	## save this as a status in $edit.
	*/
	if(! empty($_GET['edit'])){
		$query="SELECT * FROM delivery WHERE maternity_ID=$maternity_ID";
		$edit_result=mysqli_query($link,$query);
		$object=mysqli_fetch_object($edit_result);
		if(! empty($object)){
			$edit=true;
		}else{
			$edit=false;
		}
	}else{
		$edit=false;
	}

	## If the user is submitting the delivery entry form, call this if-branch.
	if(! empty($_GET['submit'])){
		
		## If the user referred the client in labour, call this if-branch.
		if(! empty($_GET['refer']) AND ! empty($_GET['referredto'])){
			
			/*
			## Initialise variables.
			## $referred saves the information about the referral which is to be stored in the database.
			## $message buffers the text which is to be displayed in a message box later on.
			*/
			$referred=$_GET['referredto']." when in labour";
			$message="Client has been referred";
			
			## If the user indicated a reason for the referral, add it to $referred and $message.
			if(! empty($_GET['reason'])){
				$reason=$_GET['reason'];
				$referred.="(because of: $reason)";
				$message.=" because of $reason";
			}
			
			## Display the message box.
			Settings::messagebox($message);
			
			## Set the client's treatment completed.
			$protocol->setcompleted(1);
			
			## Save the referral information in the database.
			$protocol->setreferral($referred);
			
			## Automatically forward the user back to the maternity client list.
			echo "
					<script language=\"JavaScript\">
					window.location.href='maternity_patients.php'
					</script>
					";
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
						Delivery::new_delivery($ID,$Outcome,$maternity_ID);
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
				}

				## If the parameter is the APGAR-score, combine the score after one and after ten min to be inserted in the database.
				else if($ID==9){
					$Outcome=$_GET["APGAR_1-$ID"].'/10, '.$_GET["APGAR_5-$ID"].'/10';
				}

				## In any other case just buffer the value entered by the user to be saved in the database.
				else{
					$Outcome=$_GET["$ID"];
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
						Delivery::new_delivery($ID,$Outcome,$maternity_ID);
					}
				}
			}
		}
		
		## If the user selected the "treatment in clinic completed" checkbox, set the treatment as completed in the protocol.
		if(! empty($_GET['completed'])){
			$protocol->setcompleted(1);
		}
		
		## Set the client as pregnant in the protocol (at least when she arrived in the facility she still was pregnant).
		$protocol->setpregnant(1);
		
		## Update the protocol with the client's maternityID which will indicate the client delivered and connect this delivery with the client.
		$protocol->setDelivery($maternity_ID);
		
		## Set the diagnosis of the client to "All other Cases".
		$query="SELECT Diagnosis_ID FROM diagnoses WHERE DiagnosisName like 'All other Cases'";
		$result=mysqli_query($link,$query);
		$object=mysqli_fetch_object($result);
		Diagnosis_IDs::new_Diagnosis_IDs($protocol_ID,$object->Diagnosis_ID,1);
		
		## Set the client's attendant to "Midwife".
		$protocol->setAttendant('Midwife');
		
		## The patient's vital signs are added/updated in the system, if they were defined in browser.
		if(Vital_Signs::already_set($protocol_ID)){
			(new Vital_Signs($protocol_ID))->setVital_signs('get');
		}else{
			$vital_signs=Vital_Signs::new_Vital_Signs($protocol_ID,$_GET['BP'],$_GET['weight'],$_GET['pulse'],$_GET['temperature'],$_GET['MUAC']);
		}

		## Print links to edit the delivery, the client's pregnancy overview and back to the list of Maternity clients.
		echo"
				<a href='delivery.php?patient_ID=$patient_ID&maternity_ID=$maternity_ID&protocol_ID=$protocol_ID'><div class='box'>Edit Delivery</div></a>
				<a href='complete_pregnancy.php?patient_ID=$patient_ID&maternity_ID=$maternity_ID'><div class='box'>Pregnancy Overview</div></a>
				<a href='maternity_patients.php'><div class='box'>Clients in Maternity</div></a>
				";
	}
	
	## This if-branch is called, if the user enters the pages and has not clicked on the submit button yet.
	else{
		$vital_signs=new Vital_Signs($protocol_ID);
		
		## Initialise variables with the vital signs of the client.
		$currentBP=$vital_signs->getBP();
		$currentweight=$vital_signs->getweight();
		$currentpulse=$vital_signs->getpulse();
		$currenttemperature=$vital_signs->gettemperature();
		
		## Initialise variables with the name and information about a referral of the client.
		$name=$patient->getName();
		$referral=$protocol->getreferral();
		
		## Print the headline and the input form for the referral information. 
		## In case information about any referral are available, prefill the form with those.
		echo"
				<h1>Delivery of $name</h1>
				<div class='inputform'>
				<form action='delivery.php' method='get'>
				<input type='checkbox' name='refer'";
				if(! empty($referral)){
					echo "checked='checked'";
				}
				echo" > <h4>referred in Labour</h4>
				to:<input type='text' name='referredto'";
				
				## If client was referred, inquire the referral destination and the referral reason.
				if(! empty($referral)){
					$referral=str_replace("when in labour",'',$referral);
					
					## Inquire referral destination.
					if(strstr($referral,'(because of:')){
						$start=0;
						$length=strpos($referral,'(')-1;
						$referredto=substr($referral,$start,$length);
					}else{
						$referredto=$referral;
					}
					echo "value='$referredto'";
				}
				echo">
				(reason:<input type='text' name='reason'";
				
				## Inquire referral reason.
				if(! empty($referral) AND strstr($referral,'(because of:')){
					$start=$length+1;
					$reason=substr($referral,$start);
					$reason=str_replace("(because of:","",$reason);
					$reason=str_replace(")","",$reason);
					echo "value='$reason'";
				}
				echo">)
				<input type='hidden' name='protocol_ID' value='$protocol_ID'>
				<input type='hidden' name='patient_ID' value='$patient_ID'>
				<input type='hidden' name='maternity_ID' value='$maternity_ID'>
				";
				
				## In case the user is editing, send this parameter along with the form.
				if($edit){
					echo"<input type='hidden' name='edit' value='on'>";
				}
				echo"
				<input type='submit' name='submit' value='refer'>
				</form><br>
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
				echo"<a class='button' href='delivery.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID&maternity_ID=$maternity_ID'>no twins</a>";
			}else{
				echo"<a class='button' href='delivery.php?patient_ID=$patient_ID&protocol_ID=$protocol_ID&maternity_ID=$maternity_ID&twins=on'>twins</a>";
			}
		}
		
		## Print a form and prefill it with the clien's vital signs.
		echo"
			<br><br>
			<form action='delivery.php' method='get'>

				<div><label>BP:</label><br>
				<input type='text' class='smalltext' name='BP' ";if($currentBP!==''){echo"value='$currentBP'";}echo"> mmHg</div>

				<div><label>Weight:</label><br>
				<input type='number'step='0.1' name='weight' ";if($currentweight!=0){echo"value='$currentweight'";}echo" min='30.0' max='150.0'> kg</div>

				<div><label>Pulse:</label><br>
				<input type='number' name='pulse' min='0' max='200' ";if($currentpulse!=='0'){echo"value='$currentpulse'";}echo"> bpm</div>

				<div><label>Temperature:</label><br>
				<input type='number' name='temperature' min='30' max='45' step='0.1'";if($currenttemperature!=0){echo"value='$currenttemperature'";}echo"> &#176C</div>";			

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
			
			## If the user activated the twin delivery, the loop is run twice for some of the parameters.
			if($twins AND ($ID==1 OR ($ID>=6 AND $ID<=20) OR ($ID>=23 AND $ID<=26) OR $ID==30)){
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
			}
		}
		
		## In case the twin delivery is activated, send this parameter along with the form.
		if($twins){
			echo"<input type='hidden' name='twins' value='on'>";
		}
		
		## Print the "treatment in clinic completed" checkbox and the close of the form.
		echo"
				<div><input type='checkbox' name='completed'";
				if($edit AND $protocol->getCompleted()==1){
					echo "checked='checked'";
				}
				echo"> <label>treatment in clinic completed</label></div>
				<br>
				<input type='hidden' name='protocol_ID' value='".$_GET['protocol_ID']."'>
				<input type='hidden' name='patient_ID' value='$patient_ID'>
				<input type='hidden' name='maternity_ID' value='$maternity_ID'>
				<input type='hidden' name='token' value='$uniqueID'>
				";
				
				## In case the user is editing, send this parameter along with the form.
				if($edit){
					echo "<input type='hidden' name='edit' value='on'>";
				}
				echo"
				<input type='submit' name='submit' value='submit'>
				</form>
				</div>
				";
	}

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>