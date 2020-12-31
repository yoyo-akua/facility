<?php
	## Automatic refresh every 5 minutes.
	header("Refresh:300");

	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Print a headline.
	echo "<h1>Lab Patients</h1>";

	/*
	## $searchpara is the variable on which the search is based. 
	## The function Patient::simple_search() prints a simple input form for name and OPD number to search in the list.
	## If the user used the search, the function also adds the parameters to $searchpara.
	*/
	$searchpara=Patient::simple_search('lab_patients.php');

	/*
	## Get data from database.
	## Get all patients and their lab register entries,
	##		- who were visiting within the last two weeks ($today is defined in HTML_HEAD.php),
	##		- whose tests are not finished (lab_done like 0),
	##		- who match to current search parameters, saved in $searchpara.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	## Save all data from database in $result.
	*/
	$query="SELECT * FROM protocol,patient,visit,lab_list,lab WHERE lab_list.lab_list_ID=lab.lab_list_ID AND lab.protocol_ID=protocol.protocol_ID AND patient.patient_ID=visit.patient_ID AND visit.visit_ID=protocol.visit_ID AND lab_done=0 $searchpara AND checkin_time>(DATE_SUB('$today',INTERVAL 14 DAY)) GROUP BY lab_list.lab_number ORDER BY SUBSTRING(lab_list.lab_number FROM -2 FOR 2), lab_list.lab_number";
	$result = mysqli_query($link,$query);

	/*
	## If search result is empty and no patient found, print button 'search patient' in browser.
	## After click on this button the user is forwarded to current patients list of consulting department.
	*/
	If (mysqli_num_rows($result) == 0){
		echo'<a href="current_patients.php"><div class ="box">consulting patients</div></a>';
	}

	/*
	## If search result is not empty and patients are found,
	## Print a table with all found patients.
	*/
	else{
		
		## Print table head.
		echo"
				<table>
				<tr>
				<th style=border-left:none>
					Visit Date
				</th>
				<th>
					OPD
				</th>
				<th>
					Name
				</th>
				<th>
					Age
				</th>
				 <th>
					Sex
				</th>
				 <th>
					Locality
				</th>
				<th>
					lab number
				</th>
				<th>
					Select
				</th>
			</tr>  
			";
		
		## Initialise variable $previous, which is used to determine, whenever the date is changing in the patient list.
		$previous='';
		
		## This loop will be run once for each of the output patients from the database query.
		while($row = mysqli_fetch_object($result)){
			
			## Initialise objects of patient and protocol by their IDs.
			$patient = new Patient($row->patient_ID);
			$protocol=new Protocol($row->protocol_ID);
			$visit=new Visit($row->visit_ID);
			
			## Print the patient's data in the table and in the last column a link for entering their test results.
			echo"
					<tr>
						<td style=border-left:none>
							";
							$visitdate=date("d/m/y",strtotime($visit->getCheckin_time()));
							if($visitdate!==$previous){
								echo $visitdate;
								$previous=$visitdate;
							}
							echo"
						<td>
						   ". $patient->getOPD()."
						</td>
						<td>
							".$patient->getName()."
						</td>	
						<td>
							"
							.$patient->getAge(time(),'print')."
						</td>
						<td>
							".$patient->getSex()."
						</td>			
						<td>
							".$patient->getLocality()."
						</td>
						<td>
							$row->lab_number
						</td>
						<td>
							<a href=\"lab.php?patient_ID=$row->patient_ID&visit_ID=$row->visit_ID\">Select</a>
						</td>
					</tr>
					";				
		}

		/*
		## Print the table bottom.
		## Is the same bottom as in every patient table.
		## That's why the table bottom itself is defined in object 'Patient' (see folder 'Objects -> Patient.php') and called as in the following.
		*/
		Patient::tablebottom();
	}
	
	/*
	## Show a button for adding a self-paying client in the upper right corner.
	## Those clients are marked in the database and are not appearing in the department's patient lists (apart from lab).
	*/
	echo "
			<div class='tableright'>
			<a href='search_patient.php?onlylab=on'>Add self-paying Client</a>
			</div>
			";

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>