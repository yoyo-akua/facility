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
	## This visit (by this particular patient) is identified by variable $visit_ID.
	## The patient's complaints are added to the database, if they were defined in browser.
	## There is also an entry added to the protocol, which states that complaints have been recorded.
	*/
	if(! empty($_POST['submit'])){
		$visit_ID=$_POST['visit_ID'];
		if(! empty($_POST['coughing'])){
			$coughing=1;
		}else{
			$coughing=0;
		}
		if(! empty($_POST['vomitting'])){
			$vomitting=1;
		}else{
			$vomitting=0;
		}
		if(! empty($_POST['fever'])){
			$fever=1;
		}else{
			$fever=0;
		}
		if(! empty($_POST['diarrhoea'])){
			$diarrhoea=1;
		}else{
			$diarrhoea=0;
		}
		$protocol=protocol::new_Protocol($visit_ID,'complaints recorded');
		$complaints=Complaints::new_Complaints($protocol->getProtocol_ID(),$coughing,$vomitting,$fever,$diarrhoea,$_POST['others']);
	}
	
	echo "<h1>Complaints</h1>";
	/*
	## $searchpara is the variable on which the search is based. 
	## The function Patient::simple_search() prints a simple input form for name and OPD number to search in the list.
	## If the user used the search, the function also adds the parameters to $searchpara.
	*/
	$searchpara=Patient::simple_search('complaints.php');
	
	/*
	## Get data from database.
	## Get all patients, 
	##		- which are visiting today ($today is defined in HTML_HEAD.php),
	##		- whose treatment is not finished (completed like 0),
	##		- which match to current search parameters 'OPD Number' or 'Name' in case these search parameters are used.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	## Save all data from database in $result.
	*/
	$query="SELECT * FROM patient,visit WHERE visit.patient_ID=patient.patient_ID $searchpara AND onlylab=0 AND visit_ID NOT IN (SELECT visit_ID FROM complaints,protocol WHERE protocol.protocol_ID=complaints.protocol_ID) AND checkout_time like '0000-00-00 00:00:00'  GROUP BY visit.visit_ID ORDER BY checkin_time ASC";
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
	## At this, checkboxes for coughing, vomitting, fever and diarrhoea can be selected and a text can be entered for others can be entered.
	*/
	else{
		Patient::currenttablehead();  
		echo"
			<th>
				Coughing
			</th>
			<th>
				Vomitting
			</th>
			<th>
				Fever
			</th>
			<th>
				Diarrhoea
			</th>
			<th>
				Others
			</th>
			<th style=border-left:none>
			</th>
			</tr>  
		";
		
		
		while($row = mysqli_fetch_object($result)){

			$visit_ID=$row->visit_ID;
			$visit=new Visit($visit_ID);

			$patient_ID=$row->patient_ID;
			$patient = new Patient($patient_ID);

			$age=$patient->getAge(strtotime($visit->getCheckin_time()),'calculate');

			$patient->currenttablerow();
			
			echo"
				<form action='complaints.php' method='post'>
					<td>
						<input type='checkbox' name='coughing' value='1'>			
					</td>
					<td>
						<input type='checkbox' name='vomitting' value='1'>
					</td>
					<td>
						<input type='checkbox' name='fever' value='1'>
					</td>
					<td>
						<input type='checkbox' name='diarrhoea' value='1'>
					</td>
					<td>
						<textarea name='others' length='1000' style=width:90px;height:25px></textarea>
					<td>
						<input type='hidden' name='visit_ID' value='".$row->visit_ID."'>
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
