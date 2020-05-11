<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");
	
	/*
	## Variable, which represents the search parameters a current search is based on.
	## Initialising this variable.
	*/
	$searchpara = "";

	/*
	## This if-branch is called, if the user is clicking on one OPD patient's submit button.
	## This visit (by this particular patient) is identified by variable $protocol_ID.
	## The patient's vital signs are added to the system, if they were defined in browser.
	*/
	if(! empty($_POST['submit'])){
		$protocol_ID=$_POST['protocol_ID'];
		if(Vital_Signs::already_set($protocol_ID)){
			(new Vital_Signs($protocol_ID))->setVital_signs('post');
		}else{
			$vital_signs=Vital_Signs::new_Vital_Signs($protocol_ID,$_POST['BP'],$_POST['weight'],$_POST['pulse'],$_POST['temperature'],$_POST['MUAC']);
		}
	}

	
	echo "<h1>Vital Signs</h1>";
	/*
	## $searchpara is the variable on which the search is based. 
	## The function Patient::simple_search() prints a simple input form for name and OPD number to search in the list.
	## If the user used the search, the function also adds the parameters to $searchpara.
	*/
	$searchpara=Patient::simple_search('vital_signs.php');
	
	/*
	## Get data from database.
	## Get all patients, 
	##		- which are visiting today ($today is defined in HTML_HEAD.php),
	##		- which treatment is not finished (completed like 0),
	##		- which match to current search parameters 'OPD Number' or 'Name' in case these search parameters are used.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	## Save all data from database in $result.
	*/
	$query="SELECT * FROM protocol,patient WHERE patient.patient_ID=protocol.patient_ID and VisitDate like '%$today%' $searchpara AND onlylab=0 AND protocol_ID NOT IN (SELECT protocol_ID FROM vital_signs) AND completed like '0'  ORDER BY VisitDate ASC";
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
	## At this, columns for blood pressure, pulse, weight, temperature, MUAC can be entered.
	*/
	else{
		Patient::currenttablehead();  
		echo"
			<th>
				Blood Pressure
			</th>
			<th>
				Pulse
			</th>
			<th>
				Weight
			</th>
			<th>
				Temperature
			</th>
			<th>
				MUAC
			</th>
			<th style=border-left:none>
			</th>
			</tr>  
		";
		
		
		while($row = mysqli_fetch_object($result)){
			$protocol_ID=$row->protocol_ID;
			$patient_ID=$row->patient_ID;
			$patient = new Patient($patient_ID);
			$patient->currenttablerow($protocol_ID);
			$BP_last=Vital_Signs::last_BPs($protocol_ID);
			

			echo"
				<form action='vital_signs.php' method='post'>
					<td>";
					## If $BP_last is set, show a tooltip with the clients last 5 BPs.
					if(!empty($BP_last)){
						echo"
						<div class='tooltip' style='line-height:normal'>
							<input type='text' name='BP' class='smalltext'> mmHg
								<span class='tooltiptext' style='text-align:left'>
									$BP_last
								</span>
						</div>";
					}else{
						echo "<input type='text' name='BP' class='smalltext'> mmHg";
					}
					echo"			
					</td>
					<td>
						<input type='number' name='pulse' min='0' max='200'> bpm
					</td>
					<td>
						<input type='number' name='weight' step='0.1' min='0' max='300'> kg
					</td>
					<td>
						<input type='number' name='temperature' min='30' max='45' step='0.1'>&#176C
					</td>
					<td>
						<input type='number' name='MUAC' min='0' step='0.1'>cm
					<td>
						<input type='hidden' name='protocol_ID' value='".$row->protocol_ID."'>
						<input type='submit' name='submit' value='submit'>
					</td>
				</form>
				</tr>
			";
		}
		Patient::tablebottom();
	}

	## contains HTML/CSS structure, which styles the graphical user interface in the browser
	include("HTMLParts/HTML_BOTTOM.php");		
?>
