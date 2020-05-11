<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Initialise new object of protocol by a certain protocol-ID, with which the page is called.
	$protocol_ID=$_GET['protocol_ID'];
	$protocol= new Protocol($protocol_ID);

	## Initialise new object of patient by a certain patient-ID, with which the page is called
	$patient_ID=$_GET['patient_ID'];
	$patient=new Patient($patient_ID);

	/*
	## Get data from database.
	## Get all the dispensary's drug register entries for the patient.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
	*/
	$query="SELECT * FROM disp_drugs WHERE protocol_ID='$protocol_ID'";
	$drug_result=mysqli_query($link,$query);

	## If the user submitted any changes by clicking one of the submit buttons (labeled with treatment completed and treatment not completed), call this if-branch.
	if(!empty($_POST['completed']) OR ! empty($_POST['not_completed'])){
		
		## Depending on which submit button the user clicked, set "completed" or "not completed" in the database.
		if(!empty($_POST['completed'])){
			$protocol->setcompleted(1);
		}else{
			$protocol->setcompleted(0);
		}
		
		## This loop is run once for each prescribed drug of the patient.
		while($row=mysqli_fetch_object($drug_result)){
	
			/*
			## Initialising some more variables, which are needed within this loop.
			## Variable $amount contains the amount the current drug.
			## Variable $Counts contains the amount of a drug in Dispensary corresponding to the current Dispensary register entry.		
			## Variable $radio saves the selection of the user in the input form for this particular drug.
			*/
			$ID=$row->Disp_Drugs_ID;
			$Disp_Drug=new Disp_Drugs($ID);
			$Drug_ID=$Disp_Drug->getDrug_ID();
			$Counts=$Disp_Drug->getCounts();
			$amount=$Disp_Drug->getPrescribed();
			$radio=$_POST["radio_$ID"];
			
			## If the user changed the status of the drug from "write to buy" to "issue", call this if-branch.
			if($radio=='issued' AND $Counts==''){
				
				/*
				## Get physical stock at hand in the dispensary and store and save in variables.
				## Add the two variables to get the physical stock at hand in the entire facility.
				*/
				$disp_stock=Disp_Drugs::getLastCounts($Drug_ID,time());
				$store_stock=Store_Drugs::getAmount($Drug_ID,time());
				$available=$store_stock+$disp_stock;

				## Set variable with new available amount in the dispensary (after issuing the drug to the patient).
				$Counts=$disp_stock-$amount;
				
				/*
				## If the drug was prescribed more than available (in store and dispensary together), 
				## first issue the remaining drugs to the patient, then set $amount to what is left for the patient to buy outside
				## and set $Counts as an empty variable, which will indicate for future reference, that the drug was not issued by this facility.
				## Show notification about partial (or impossible) issuing).
				*/
				if($available<$amount){
					
					## Initialise variable with the name of the drug. 
					$drug_name=(new Drugs($Drug_ID))->getDrugname();
					
					if($available!==0){
						$Counts=$disp_stock-$available;
						$Disp_Drug->setPrescribed($available);
						Disp_Drugs::new_Disp_Drugs("$Drug_ID","0",$amount-$available,'',$recom,$protocol_ID);
						$amount=$amount-$available;
						$message="You can only issue $drug_name partially (there is not enough available to issue all)";
					}else{
						$message="You can not issue $drug_name to the patient, because it is not available";
						$Counts='';
					}
					Settings::Messagebox($message);
				}
				
				## Update the information (stock at hand and issued.status) in the dispensary.
				$Disp_Drug->setCounts($Counts);
			}
			
			## If the user changed the status of the drug from "issue" to "write to buy", update the information in the dispensary.
			else if($radio=='non_issued' AND $Counts!==''){
				$Disp_Drug->setCounts('');
			}
		}

		echo "<script>window.location.href=('disp_patients.php')</script>";
		;
	}

	## This if-branch is called, when the user opens the page and before he takes further ations.
	else{

		
		## Initialise variable with patient's name.
		$Name=$patient->getName();
		
		## Print headline, the beginning of the form and the table head. 
		echo"
				<h1>prescribed drugs on $Name</h1>
				<div class='inputform'>
				<form method='post' action='patient_drugs.php?protocol_ID=$protocol_ID&patient_ID=$patient_ID'>
					<table>
						<tr>
							<th style=border-left:none>
							</th>
							<th>
								Issue
							</th>
							<th>
								Write to buy
							</th>
						</tr>
				";
				


		## This loop is run once for each prescribed drug of the patient.
		while($row=mysqli_fetch_object($drug_result)){

			/*
			## Initialising some more variables, which are needed within this loop.
			## Variable $unit contains the unit of issue of the current drug.
			## Variable $amount contains the amount the current drug.
			## Variable $unit contains the unit of issue of the current drug.	
			## Variable $dosage_recommendation contains dosage recommendations of the current drug.
			## Variable $Counts contains the amount of a drug in Dispensary corresponding to the current Dispensary register entry.		
			*/
			$ID=$row->Disp_Drugs_ID;
			$Disp_Drug=new Disp_Drugs($ID);
			$Drug_ID=$Disp_Drug->getDrug_ID();
			$Drug=new Drugs($Drug_ID);
			$Drugname=$Drug->getDrugname();
			$amount=$Disp_Drug->getPrescribed();
			$unit=$Drug->getUnit_of_Issue();
			$dosage_recommendation=$Disp_Drug->getdosage_recommendation();
			$Counts=$Disp_Drug->getCounts();
			
			/*
			## Get physical stock at hand in the dispensary and store and save in variables.
			## Add the two variables to get the physical stock at hand in the entire facility.
			*/
			$disp_stock=Disp_Drugs::getLastCounts($Drug_ID,time());
			$store_stock=Store_Drugs::getAmount($Drug_ID,time());
			$available=$store_stock+$disp_stock;

			/*
			## Depending on the drug's availability in Dispensary corresponding to the current Dispensary register entry,
			## the drug, its prescribed amount and dosage recommendation is printed:
			##		- if the drug's amount in Dispensary is empty, print a flag notice to get from store,
			##		- print checkboxes for "issue" and "write to buy" for each drug.
			*/
			echo
					'<tr>
						<td style=border-left:none>
						';
							if($Counts<0 OR $disp_stock<$amount AND $store_stock!==0){
								echo'
										<div class="tooltip">
											<b>'.$Drugname.': </b>
												<span class="tooltiptext">
													get from store
												</span>
										</div>
										';
							}else{
								echo '<b>'.$Drugname.': </b>';		
							}
							echo
							$amount.' '.$unit.'s ';
							if(! empty($dosage_recommendation)){
								echo'('.$dosage_recommendation.')';
							}
							echo"
						</td>
						<td>
							<input type='radio' name='radio_$ID' value='issued'";
							if($Counts!=='' OR $amount<=$available){
								echo"checked='checked'";
							}
							echo"
							>
						</td>
						<td>
							<input type='radio' name='radio_$ID' value='non_issued'";
							if($amount>$available){
								echo"checked='checked'";
							}
							echo"
							>
						</td>
					</tr>
					";
			
		}
		## Print button for "treatment completed" and link to list of patients in dispensary.
		echo
				"
					</table>
					<br>
					<input type='submit' name='completed' value='treatment completed'>
					<input type='submit' name='not_completed' value='treatment not completed'>
				</form>
				<br>
				<a href='prescribe_drugs.php?protocol_ID=$protocol_ID&patient_ID=$patient_ID'><div class ='box'>edit patient's prescriptions</div></a>
				<a href='disp_patients.php'><div class ='box'>patients in dispensary</div></a>
				";	
	}

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");	

?>
