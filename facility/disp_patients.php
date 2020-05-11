<?php
	## Automatic refresh every 5 minutes.
	header("Refresh:300");
	
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	## Print a headline.
	echo "<h1>Patients in Dispensary</h1>";

	/*
	## $searchpara is the variable on which the search is based. 
	## The function Patient::simple_search() prints a simple input form for name and OPD number to search in the list.
	## If the user used the search, the function also adds the parameters to $searchpara.
	*/
	$searchpara=Patient::simple_search('disp_patients.php');

	/*
	## Get data from database.
	## Get all patients, 
	##		- whose treatment is not finished (completed like 0),
	##		- who match to current search parameters, saved in $searchpara
	##		- who didn't come only for lab investigations,
	## 		- who were prescribed drugs to (their protocol ID appears in the disp_drugs table),
	##		- who were visiting within the last two weeks ($today is defined in HTML_HEAD.php).
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	## Save all data from database in $result.
	*/
	$query="SELECT * FROM protocol,patient,disp_drugs WHERE patient.patient_ID=protocol.patient_ID and disp_drugs.protocol_ID=protocol.protocol_ID AND onlylab=0 and completed like 0 AND VisitDate>(DATE_SUB('$today',INTERVAL 14 DAY)) $searchpara GROUP BY protocol.protocol_ID";
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
					Prescriptions
				</th>
			</tr>  
			";
		
		$previous='';
		
		## This loop will be run once for each of the output patients from the database query.
		while($row = mysqli_fetch_object($result)){
			
			## Initialise objects of patient and protocol by their IDs.
			$patient = new Patient($row->patient_ID);
			$protocol=new Protocol($row->protocol_ID);
			
			## Print the patient's data in the table and in the last column a link for entering their test results.
			echo"
					<tr>
						<td style=border-left:none>
							";
							$visitdate=date("d/m/y",strtotime($protocol->getVisitDate()));
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
							<a href=\"patient_drugs.php?patient_ID=$row->patient_ID&protocol_ID=$row->protocol_ID\">dispense</a>
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


	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");
?>