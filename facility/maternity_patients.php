<?php
	## Automatic refresh every 5 minutes.
	header("Refresh:300");
	
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Initialising variables $ageto and $agefrom to set an age frame of 10-50 years and add it to $searchpara on which the search is based.
	$ageto=date("Y-m-d",(time()-(50*365.25*3600*24)));
	$agefrom=date("Y-m-d",(time()-(10*365.25*3600*24)));
	$searchpara=  "AND Birthdate BETWEEN '$ageto' AND '$agefrom'";

	## If the user selected PNC for a client, the following if-branch sets this in the protocol and adds the diagnosis "All other Cases" to the diagnoses.
	if(! empty($_GET['PNC'])){
		$protocol=new Protocol($_GET['PNC']);
		if($protocol->getPNC()==0){
			$protocol->setPNC(1);
			$query="SELECT Diagnosis_ID FROM diagnoses WHERE DiagnosisName like 'All other Cases'";
			$result=mysqli_query($link,$query);
			$object=mysqli_fetch_object($result);
			Diagnosis_IDs::new_Diagnosis_IDs($_GET['PNC'],$object->Diagnosis_ID,1);
		}else{
			$protocol->setPNC(0);
		}
	}

	
	## Print a headline.
	echo'<h1>Maternity Clients</h1>';

	/*
	## The function Patient::simple_search() prints a simple input form for name and OPD number to search in the list.
	## If the user used the search, the function also adds the parameters to $searchpara.
	*/
	$searchpara.=Patient::simple_search('maternity_patients.php');

	/*
	## Get data from database.
	## Get all patients, 
	##		- who are visiting today ($today is defined in HTML_HEAD.php),
	##		- whose treatment is not finished (completed like 0),
	##		- who match to current search parameters, saved in $searchpara
	##		- who didn't come only for lab investigations.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	## Save all data from database in $result.
	*/
	$query="SELECT * FROM patient,visit WHERE patient.patient_ID=visit.patient_ID AND checkout_time like '0000-00-00 00:00:00'  AND onlylab=0 and Sex like 'female' $searchpara ORDER BY checkin_time ASC";
	$result = mysqli_query($link,$query);

	/*
	## If search result is empty and no patient found, print button 'search patient' in browser.
	## After click on this button the user is forwarded to OPD Search.
	*/
	If (mysqli_num_rows($result) == 0){
		echo'<a href="search_patient.php"><div class ="box">search patient</div></a>';
	}

	/*
	## If search result is not empty and patients are found,
	## Print a table with all found patients.
	*/
	else{
		
		/*
		## Table head.
		## Is the same head as in every patient table.
		## That's why the table head itself is defined in object 'Patient' (see folder 'Objects -> Patient.php').
		## In the following this table head is called and completed with an additional column.
		*/
		Patient::currenttablehead();  
		echo"
						<th colspan='3'>
							Go to
						</th>
					</tr>  
					";
		
		/*
		## For each found patient a new table row is printed.
		## Therefore first a new patient object is created by the patiend_ID of each search result.
		## Afterwards for this created patient object a new table row is printed.
		## At least this table row is completed by additional information.
		*/		
		while($row = mysqli_fetch_object($result)){
			
			## Initialise variable with the patient's ID and use it to create a new patient object.
			$patient_ID=$row->patient_ID;
			$patient = new Patient($patient_ID);

			## Initialise variable with the patient's visit ID.
			$visit_ID=$row->visit_ID;
			
			## Print a new table row for created patient object.
			$patient->currenttablerow();
			
			## Get the patient's age and save it in a variable.
			$age=$patient->getAge(time(),'calculate');
			
			/*
			## Complete table row with additional information.
			## A hyperlink to Diagnosis for adding diagnoses.
			## A hyperlink to Laboratory for adding tests
			*/
			echo"
					<td>
						<a href=\"patient_visit.php?visit_ID=$visit_ID\">Diagnosis</a>
					</td>
					<td>
						<a href=\"order_tests.php?patient_ID=$patient_ID&visit_ID=$visit_ID\">Laboratory</a>
					</td>
				";
			
			
			
				
			/*
			## Get client's pregnancy data from database.
			## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
			## If client has visited maternity in this facility, call the if-branch.
			*/
			$query2="SELECT * FROM maternity WHERE patient_ID=$patient_ID ORDER BY maternity_ID DESC LIMIT 1";
			$result2=mysqli_query($link,$query2);
			$object=mysqli_fetch_object($result2);
			if(! empty($object)){

				## Initialise variable $maternity ID, containing the ID of the maternity register entry.
				$maternity_ID=$object->maternity_ID;
				$maternity= new maternity($maternity_ID);

				/*
				## Check in the database, whether the client has already come for ANC during this visit to the facility. 
				## If so, print a link for editing the ANC visit.
				## If not, print a link for adding an ANC visit.
				*/
				$query3="SELECT * FROM visit,anc,protocol WHERE visit.visit_ID=$visit_ID AND visit.visit_ID=protocol.visit_ID AND anc.protocol_ID=protocol.protocol_ID AND anc.maternity_ID=$maternity_ID";
				$result3=mysqli_query($link,$query3);
				$object3=mysqli_fetch_object($result3);
				if(! empty($object3)){
					echo"
						<td>
							<a href=\"anc.php?ANC_ID=$object3->ANC_ID&maternity_ID=$maternity_ID&visit_ID=$visit_ID\">edit ANC</a>
						</td>
					";
				}else if($maternity->getDelivery_date()=='0000-00-00'){
					echo"
						<td>
							<a href=\"anc.php?maternity_ID=$maternity_ID&visit_ID=$visit_ID\">ANC</a>
						</td>
					";
				}

				## Initialise variable with the client's conception date to calculate the week of pregnancy.
				$conception_stamp=strtotime($object->conception_date);
				
				## If the client reached week 32 of pregnancy, call this if-branch.
				if($conception_stamp<=(time()-(3600*24*7*32))){

					## If the client hasn't "reached" week 45 of pregnancy yet, call this if-branch.
					if($conception_stamp>=(time()-(3600*24*7*45))){

						/*
						## Check if a delivery has been recorded for the client. 
						## If not, display a link for recording the delivery, 
						## otherwise check if the client has delivered during this visit. 
						## In that case, print a link for editing the delivery data.
						*/
						if($maternity->getDelivery_date()=='0000-00-00'){
							echo"
								<td>
									<a href=\"delivery.php?maternity_ID=$maternity_ID&visit_ID=$visit_ID\">Delivery</a>
								</td>
								";
						}else{
							echo"
								<td>
									<a href=\"delivery.php?maternity_ID=$maternity_ID&visit_ID=$visit_ID&edit=on\">edit Delivery</a>
								</td>
								";
						}
					}
						/*???
								echo"
										<td>
											<a href=\"maternity_patients.php?PNC=$row->visit_ID\">
												<input type='checkbox'";
												if((new Protocol($row->protocol_ID))->getPNC()==1){
													echo "checked='checked'";
												}
												echo" readonly> PNC</a>
										</td>
									";
							*/
					
				}

				## Print a link to the client's pregnancy overview
				echo"
					<td>
						<a href=\"complete_pregnancy.php?maternity_ID=$maternity_ID\">Pregnancy Overview</a>
					</td>
				";
			}
			
			## In case the client hasn't been registered in maternity yet, print a link for registering her. 
			else{
				echo"
					<td>
						<a href=\"new_maternity_client.php?patient_ID=$patient_ID&visit_ID=$visit_ID\">new client</a>
					</td>
				";
			}
		}
		
		/*
		## Print the table bottom.
		## Is the same bottom as in every patient table.
		## That's why the table bottom itself is defined in object 'Patient' (see folder 'Objects -> Patient.php') and called as in the following.
		*/
		Patient::tablebottom();
	}

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>
