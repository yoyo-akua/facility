<?php
	## Is refreshing the current patient site every 10 minutes.
	header("Refresh:600");

	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	echo "<h1>Current Patients</h1>";
	/*
	## $searchpara is the variable on which the search is based. 
	## The function Patient::simple_search() prints a simple input form for name and OPD number to search in the list.
	## If the user used the search, the function also adds the parameters to $searchpara.
	*/
	$searchpara=Patient::simple_search('current_patients.php');

	/*
	## Get data from database.
	## Get all patients, 
	##		- who are visiting today ($today is defined in HTML_HEAD.php),
	##		- whose treatment is not finished (completed like 0),
	##		- who match to current search parameters 'OPD Number' or 'Name' in case these search parameters are used
	##		- who didn't come only for lab investigations.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	## Save all data from database in $result.
	*/
	$query="SELECT * FROM patient,visit,protocol WHERE protocol.visit_ID=visit.visit_ID AND patient.patient_ID=visit.patient_ID AND checkout_time like '0000-00-00 00:00:00' AND onlylab=0 $searchpara GROUP BY visit.visit_ID ORDER BY checkin_time ASC";
	$result = mysqli_query($link,$query);

	/*
	## If search result is empty and no patient found, print button 'search patient' in browser.
	## After click on this button the user is forwarded to OPD Search.
	*/
	If (mysqli_num_rows($result) == 0){
		echo'
			<a href="search_patient.php"><div class ="box">search patient</div></a>
		';
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
		## In the following this table head is called and completed by two additional columns.
		*/
		Patient::currenttablehead();  
		echo"
			<th>
				diagnose
			</th>
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
			
			## Create a new patient object.
			$patient = new Patient($row->patient_ID);
			
			## Print a new table row for created patient object.
			$patient->currenttablerow($row->protocol_ID);
			
			## Create a new visit object;
			$visit= new Visit($row->visit_ID);
			
			/*
			## Complete table row with additional information.
			## First a hyperlink to Diagnosis for each patient.
			## Second a hyperlink to Laboratory for each patient, provided department Laboratory is activated in defaults/DEFAULTS.php.
			## Third a hyperlink to Dispensary for each patient.
			## Fourth a hyperlink to Surgery/Procedure of each patient, provided "Surgery/Procedure" is selected in defaults/DEFAULTS.php.
			*/
			echo"
				<td>
					<a href=\"patient_visit.php?patient_ID=$row->patient_ID&visit_ID=$row->visit_ID\">Diagnosis</a>
				</td>
				";
			
			##Check if the Lab Department is disabled in defaults/DEFAULTS.php
			if(in_array('Laboratory',$DEPARTMENTS)){
				echo"
					<td>
						<a href=\"order_tests.php?patient_ID=$row->patient_ID&visit_ID=$row->visit_ID\">";
						## If tests are already ordered, label the link "Add tests", otherwise "Laboratory".
						if(! $visit->getLab_number()){
							echo 'Laboratory';
						}else{
							echo 'Add Tests';
						}
						echo"</a>
					</td>
					";
			}
			echo"
				<td>
					<a href=\"prescribe_drugs.php?patient_ID=$row->patient_ID&protocol_ID=$row->protocol_ID\">Dispensary</a>
				</td>
				";
			
			if(in_array('Surgery/Procedure',$DEPARTMENTS)){
				echo"
						<td>
							<a href=\"surgery.php?patient_ID=$row->patient_ID&protocol_ID=$row->protocol_ID\">Surgery/Procedure</a>
						</td>
						</tr>
						";
			}
		}
	}
	
	/*
	## Print the table bottom.
	## Is the same bottom as in every patient table.
	## That's why the table bottom itself is defined in object 'Patient' (see folder 'Objects -> Patient.php') and called as in the following.
	*/
	Patient::tablebottom();
	
	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>
