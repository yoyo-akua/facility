<?php
	ini_set("memory_limit","1024M");
	## Contains global variables and functions which are needed within this page.
	include("setup.php");

	## Increasing maximum execution time of page from 30 seconds to five minutes, beause the creation of the report can take a while. 
	ini_set('max_execution_time', 600); 

	/*
	## Variables $from and $to are initialised which are used to set the time frame for the search.
	## They are retrieved from the url with which the page is called.
	*/
	$from=$_GET['from'];
	$to=$_GET['to'];


	

	/*
	## Initialising variables and setting as arrays.
	## Each set of data (patient and the time of its visit) will later be checked for its sex and age, using $sex_array and $age_array.
	## $age_array is used to define the lower limit of each age group, the lower limit of the next age group is used as the upper limiter.
	## The diagnosis "Malaria" has several sub-categories, which are defined in $malaria_array.
	## At the end of the list, there are two rows, not referring to diagnoses. Their categories are captured in $extra_category_array.
	## $diagnosis_query is used throughout the script as a database query for getting the list of all diagnoses.
	## $all will be used as a multi-dimensional, overall array in which the data are counted.
	*/
	$sex_array=array('male','female');
	$age_array=array(0,(29/365.25),1,5,10,15,18,20,35,50,60,70,110);
	$malaria_array=array('suspected','suspected, tested','tested positive','not tested, but treated as Malaria','in pregnancy, suspected','in pregnancy, suspected, tested','in pregnancy, tested, positive','in pregnancy, not tested, but treated as Malaria');
	$extra_category_array=array('Reattendance','Referral');
	$diagnosis_query="SELECT * FROM diagnoses ORDER BY DiagnosisClass,Diagnosis_ID";
	$all=array();
	
	## The following nested loops are used to initialise multi-dimensional array variables for each field of the table and setting them as 0.
	foreach($sex_array AS $sex){
		$all[$sex]=array();
		for($age=1;$age<count($age_array);$age++){
			$all[$sex][$age_array[$age]]=array();
			$diagnosis_result=mysqli_query($link,$diagnosis_query);

			foreach($extra_category_array AS $extra_category){
				$all[$sex][$age_array[$age]][$extra_category]=0;
				$all['total']['total'][$extra_category]=0;
				$all[$sex]['total'][$extra_category]=0;
			}

			while($diagnosis_row=mysqli_fetch_object($diagnosis_result)){
				$diagnosis=$diagnosis_row->DiagnosisName;
				$all[$sex][$age_array[$age]][$diagnosis]=0;
				$all['total']['total'][$diagnosis]=0;
				$all[$sex]['total'][$diagnosis]=0;
				if($diagnosis=='Uncomplicated Malaria'){
					$all[$sex][$age_array[$age]][$diagnosis]=array();
					$all['total']['total'][$diagnosis]=array();
					$all[$sex]['total'][$diagnosis]=array();
					foreach($malaria_array AS $malaria){
						$all[$sex][$age_array[$age]][$diagnosis][$malaria]=0;
						$all['total']['total'][$diagnosis][$malaria]=0;
						$all[$sex]['total'][$diagnosis][$malaria]=0;
					}
				}
			}
		}
	}

	$previous_visit_ID='';

	/*
	## Get data from database. 
	## Get all patients' and their visits' data within the timeframe defined by $from and $to.
	## First get all patients who have not been diagnosed,
	## then get all that have a diagnosis.
	## This differentiation is necessary to count malaria cases that were suspected, but not confirmed as positive.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
	*/
	$query_array=array();
	$query_array['undiagnosed']="SELECT * FROM patient,protocol,visit WHERE visit.visit_ID=protocol.visit_ID AND patient.patient_ID=visit.patient_ID AND protocol.visit_ID=visit.visit_ID AND visit.visit_ID NOT IN (SELECT visit_ID FROM diagnosis_ids,protocol WHERE protocol.protocol_ID=diagnosis_ids.protocol_ID) AND visit.checkin_time BETWEEN '$from' AND '$to 23:59:59' AND onlylab=0";
	$query_array['diagnosed']="SELECT * FROM patient,protocol,diagnosis_ids,visit WHERE visit.visit_ID=protocol.visit_ID AND diagnosis_ids.protocol_ID=protocol.protocol_ID AND patient.patient_ID=visit.patient_ID AND visit.checkin_time BETWEEN '$from' AND '$to 23:59:59' AND onlylab=0";
	foreach($query_array AS $condition=>$query){
		
		$result=mysqli_query($link,$query);

		/*
		## The following loop will be run once for each of the output patient visits from the database query.
		## This loop and the nested loops within it are used to categorise each patient by sex and age group.
		## Afterwards the amount of patients with the same diagnosis is counted for each category.
		*/
		while($row=mysqli_fetch_object($result)){

			## Initialise new objects of patient by the patient-ID.
			$patient=new Patient($row->patient_ID);

			## Initialising object of visit by visit ID.		
			$visit_ID=$row->visit_ID;
			$visit=new Visit($visit_ID);

			

			

			/*
			## This loop is used to categorise patients by sex by running through defined sexes (male and female) and
			## checking to which the patient belongs.
			*/
			foreach($sex_array AS $sex){

				## Checking to which sex the patient belongs.
				if($patient->getSex()==$sex){

					/*
					## This loop is used to categorise patients by age group by running through all defined age groups and
					## checking to which the patient belongs.
					*/
					for($age=1;$age<count($age_array);$age++){

						/*
						## $agefrom and $ageto are used to convert the ageframes that are retrieved from $age_array into American date format, 
						## which can be read by the programme.
						*/
						$agefrom=date("Y-m-d",(strtotime($visit->getCheckin_time())-(365.25*24*3600*$age_array[($age-1)])));
						$ageto=date("Y-m-d",(strtotime($visit->getCheckin_time())-(365.25*24*3600*$age_array[$age])));

						## Checking to which age group the patient belongs.
						if($patient->getBirthdate()<$agefrom AND $patient->getBirthdate()>=$ageto){

							## If the patient was referred, this if-branch is called and the value of the correspondent variables increased by one.
							if(Referral::checkReferral($visit_ID)){
								$all[$sex][$age_array[$age]]['Referral']++;
								$all['total']['total']['Referral']++;
								$all[$sex]['total']['Referral']++;
							}

							## If the patient is reattending, this if-branch is called and the value of the correspondent variables increased by one.
							if($condition=='diagnosed' AND (new Diagnosis_IDs($row->diagnosis_entry_ID))->getReattendance()==1){
								$all[$sex][$age_array[$age]]['Reattendance']++;
								$all['total']['total']['Reattendance']++;
								$all[$sex]['total']['Reattendance']++;
							}

							/*
							## Get data from database.
							## Get list of all diseases, using $diagnosis_query.
							## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
							*/
							$diagnosis_result=mysqli_query($link,$diagnosis_query);
						
							## The following loop will be run once for each of the output diagnoses from the database query.
							while($diagnosis_row=mysqli_fetch_object($diagnosis_result)){

								## Initialising variables for name and ID of the Diagnosis
								$diagnosis=$diagnosis_row->DiagnosisName;
								$diagnosis_ID=$diagnosis_row->Diagnosis_ID;

								/*
								## This if-branch is called, if the diagnosis is Malaria, to deal with some of the special cases, 
								## that are described in $malaria_array.
								## For that it is necessary to know, that the amount of malaria diagnosed patients is composed by two cases:
								## 		- patient was tested for malaria
								##		- malaria was diagnosed for patient without performing a test before.
								## The following if-branch checks, whether the patient has been tested for malaria independent from the actual diagnosis.
								*/



								if($diagnosis=='Uncomplicated Malaria'){

									/*
									## Get data from database.
									## Check in the database if patient has been tested for Malaria.
									## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
									## Make sure to only do this, in case you haven't done it before with the same patient.
									*/
									if($visit_ID!==$previous_visit_ID){

										/*
										## Set/Reset $malaria_tested as false. It is used to check, if a patient has been counted as "Uncomplicated Malaria, suspected, tested" before.
										## This is necessary, because there are two places in the source code where this could happen.
										*/
										$malaria_tested=false;

										$malaria_query="SELECT * FROM tests,parameters,lab,protocol WHERE protocol.protocol_ID=lab.protocol_ID_ordered AND parameters.test_ID=tests.test_ID AND lab.parameter_ID=parameters.parameter_ID AND test_name='Malaria' AND protocol.visit_ID='$visit_ID' GROUP BY protocol.visit_ID";
										$malaria_result=mysqli_query($link,$malaria_query);

										## This if-branch is called, if patient has been tested for Malaria.
										if(! empty(mysqli_fetch_object($malaria_result))){
											## $malaria_tested is set true, to prevent the same variables from being increased by one again for the same patient and to indicate, that the person has been tested for Malaria.
											$malaria_tested=true;
											
											## Depending on the patient being pregnant or not, the correspondent variables are increased by one.
											if($visit->getPregnant()==1){
												$all[$sex][$age_array[$age]][$diagnosis]['in pregnancy, suspected']++;
												$all[$sex]['total'][$diagnosis]['in pregnancy, suspected']++;
												$all['total']['total'][$diagnosis]['in pregnancy, suspected']++;
												$all['total']['total'][$diagnosis]['in pregnancy, suspected, tested']++;
												$all[$sex]['total'][$diagnosis]['in pregnancy, suspected, tested']++;
												$all[$sex][$age_array[$age]][$diagnosis]['in pregnancy, suspected, tested']++;
											}else{
												##1st option suspected
												$all[$sex][$age_array[$age]][$diagnosis]['suspected']++;
												$all[$sex]['total'][$diagnosis]['suspected']++;
												$all['total']['total'][$diagnosis]['suspected']++;
											
												$all[$sex][$age_array[$age]][$diagnosis]['suspected, tested']++;
												$all[$sex]['total'][$diagnosis]['suspected, tested']++;
												$all['total']['total'][$diagnosis]['suspected, tested']++;
											}
											
										}
									}
								}
								## This if branch is called, if the patient has actually been diagnosed with a certain diagnosis.
								if($condition=='diagnosed'){

									## Initialise a variable $importance containing information whether the diagnosis was primary, secondary or provisional.
									$importance=$row->importance;

									if($row->diagnosis_ID==$diagnosis_ID){
										/*
										## This if-branch is called, if the diagnosis is Malaria, to deal with some of the special cases, 
										## that are described in $malaria_array.
										## For that it is necessary to know, that the amount of malaria diagnosed patients is composed by two cases:
										## 		- patient was tested for Malaria
										##		- malaria was diagnosed for patient without performing a test before.
										## The following if-branch checks, Malaria was diagnosed for patient,
										## without performing a test before (by if(! $malaria_tested).
										*/
										if($diagnosis=='Uncomplicated Malaria'){
											/*
											## If the correspondent variables haven't been increased by one before (indicated by $malaria_tested), 
											## they will be increased by one here, depending if the patient is pregnant or not.
											## The variable for not tested, but treated as Malaria, is also increased by one in that case.
											*/
										
											if(!$malaria_tested){
												if($visit->getPregnant()==1){
													$all[$sex][$age_array[$age]][$diagnosis]['in pregnancy, suspected']++;
													$all[$sex]['total'][$diagnosis]['in pregnancy, suspected']++;
													$all['total']['total'][$diagnosis]['in pregnancy, suspected']++;

													$all[$sex][$age_array[$age]][$diagnosis]['in pregnancy, not tested, but treated as Malaria']++;
													$all[$sex]['total'][$diagnosis]['in pregnancy, not tested, but treated as Malaria']++;
													$all['total']['total'][$diagnosis]['in pregnancy, not tested, but treated as Malaria']++;
												}else{
													##2nd option suspected
													$all[$sex][$age_array[$age]][$diagnosis]['suspected']++;
													$all[$sex]['total'][$diagnosis]['suspected']++;
													$all['total']['total'][$diagnosis]['suspected']++;

													$all[$sex][$age_array[$age]][$diagnosis]['not tested, but treated as Malaria']++;
													$all[$sex]['total'][$diagnosis]['not tested, but treated as Malaria']++;
													$all['total']['total'][$diagnosis]['not tested, but treated as Malaria']++;
												}
											}

											## If patient has been tested for Malaria, the correspondent variables are increased by one, depending if the patient is pregnant or not.
											else{
												if($visit->getPregnant()==1){
													$all[$sex][$age_array[$age]][$diagnosis]['in pregnancy, tested, positive']++;
													$all[$sex]['total'][$diagnosis]['in pregnancy, tested, positive']++;
													$all['total']['total'][$diagnosis]['in pregnancy, tested, positive']++;
												}else{
													$all[$sex][$age_array[$age]][$diagnosis]['tested positive']++;
													$all[$sex]['total'][$diagnosis]['tested positive']++;
													$all['total']['total'][$diagnosis]['tested positive']++;
												}
											}

										}

										## This if-branch is called for any other diagnosis, that is not Malaria and increases the correspondent variables by one.
										else if($importance!==3 AND $diagnosis!=='Reattendance'){
											$all[$sex]['total'][$diagnosis]++;
											$all['total']['total'][$diagnosis]++;
											$all[$sex][$age_array[$age]][$diagnosis]++; 
											
										}
									}
								}
							}
						}
					}
				}
			}
			## Update $previous_protocol_ID for the next run of the loop.
			$previous_visit_ID=$visit_ID;
		}
	}

	/*
	## $fromdisplay and $todisplay are converting the time frame into British time format.
	## The British time format is used to display a date within the pdf report.
	*/
	$fromdisplay=date("d/m/y",strtotime($from));
	$todisplay=date("d/m/y",strtotime($to));


	/*
	## Initialising variables.
	## Variable $style contains any styling attributes that should apply for certain elements in the pdf file.
	## Variable $html is used to buffer any content that is to be displayed in the pdf file.
	## The function Settings::pdf_header() is used to create the letter head of the pdf file, which includes the facility's data and the logo.
	## Below that comes a headline and the head of the table.	
	*/
	$style='
					td{
						border:0.3px solid grey;
						text-align:center;
						}
					th{
						border:0.3px solid grey;
						text-align:center;
						font-weight:bold;
						}
					h1{
						text-align:center;
						font-size:250%;
						}
					tr.class{
						background-color:lightgrey;
						}
				';
	$html=Settings::pdf_header($style,'').'
				<h1>Morbidity Report from '.$fromdisplay.' to '.$todisplay.'</h1>
				<table style="width:95%">
					<tr>
						<th style="width:10%;border:none"></th>
						<th colspan="'.count($age_array).'">Male</th>
						<th colspan="'.count($age_array).'">Female</th>
						<th rowspan="2">Grand Total</th>
					</tr>
					<tr>
						<th style="width:10%">Age</th>
			';
	
	## Completing the head of the table with the age frames and adding that to $html.
	foreach($sex_array AS $sex){
		for($age=1;$age<count($age_array);$age++){
			if($age_array[$age]==(29/365.25)){
				$html.='<th>0-28 days</th>';
			}else if($age_array[($age-1)]==(29/365.25)){
				$html.='<th>1-11 months</th>';
			}else if($age_array[($age-1)]==70){
				$html.='<th>70+</th>';
			}else{
				$html.= '<th>'.$age_array[($age-1)].'-'.($age_array[$age]-1).'</th>';
			}
		}
		$html.='
				<th>
					Total
				</th>
				';
	}
	
	$html.= '</tr>';

	## Initialising variable $lastclass, which is used to indicate wehenever a new class of diagnoses is starting, to display it in the table.
	$lastclass='';
	/*
	## Get data from database.
	## Get list of all diseases, using $diagnosis_query.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
	*/
	$diagnosis_result=mysqli_query($link,$diagnosis_query);
	
	## The following loop will be run once for each of the output diagnoses from the database query.
	while($diagnosis_row=mysqli_fetch_object($diagnosis_result)){
		
		## Initialising variables with the name and the class of the diagnosis.
		$class=$diagnosis_row->DiagnosisClass;
		$diagnosis=$diagnosis_row->DiagnosisName;
		
		## If new class of diagnoses is beginning, display that in the table.
		if($class!==$lastclass){
			$html.='
					<tr class="class"  style="width:10%">
						<th colspan="'.(count($age_array)*2+2).'">
							'.$class.'
						</th>
					</tr>
					';
		}
		## $lastclass updated to be used in the next run of the loop.
		$lastclass=$class;
		
		## This if-branch is called, if the diagnosis is Malaria, to display the special cases, that are described in $malaria_array.
		if($diagnosis=='Uncomplicated Malaria'){
			foreach($malaria_array AS $malaria){
				$html.= '
						<tr>
							<th  style="width:10%">
								'.$diagnosis.' ('.$malaria.')
							</th>
						';
				foreach($sex_array AS $sex){
					for($age=1;$age<count($age_array);$age++){
						$html.= "<td>".$all[$sex][$age_array[$age]][$diagnosis][$malaria]."</td>";
					}
					$html.='
							<td>
								<b>'.$all[$sex]['total'][$diagnosis][$malaria].'</b>
							</td>
							';
				}
				$html.='
							<td>
								<b>'.$all['total']['total'][$diagnosis][$malaria].'</b>
							</td>
						</tr>';
			}
		}
		
		## This if-branch is called for any other diagnosis, to display the correspondent numbers in the table.
		else if($diagnosis!=='Reattendance'){
			$html.= '
					<tr>
						<th  style="width:10%">
							'.$diagnosis.'
						</th>
					';
			foreach($sex_array AS $sex){
				for($age=1;$age<count($age_array);$age++){
					$html.= "<td>".$all[$sex][$age_array[$age]][$diagnosis]."</td>";
				}
				$html.='
						<td>
							<b>'.$all[$sex]['total'][$diagnosis].'</b>
						</td>
						';
			}
			$html.='
						<td>
							<b>'.$all['total']['total'][$diagnosis].'</b>
						</td>
					</tr>';
		}
	}
	 $html.='
				<tr class="class" style="width:10%">
					<th colspan="'.(count($age_array)*2+2).'">
						Reattendance and Referrals
					</th>
				</tr>
				';
	
	## This loop is used to display the figures for the two special cases described in $extra_category.
	foreach($extra_category_array AS $extra_category){
		$html.= '
					<tr>
							<th  style="width:10%">
								'.$extra_category.'
							</th>
					';
				foreach($sex_array AS $sex){
					for($age=1;$age<count($age_array);$age++){
						$html.= "<td>".$all[$sex][$age_array[$age]][$extra_category]."</td>";
					}
					$html.='
							<td>
								<b>'.$all[$sex]['total'][$extra_category].'</b>
							</td>
							';
				}
				$html.='
							<td>
								<b>'.$all['total']['total'][$extra_category].'</b>
							</td>
						</tr>';
	}
	$html.='</table>';

	## Initialise variables for the name of the pdf and it's page format.
	$pdfName='consulting_report('.$fromdisplay.'-'.$todisplay.').pdf';
	$size='A1';

	
	## This function is creating the pdf file, using the data stored in $html as content.
	Settings::pdf_execute($pdfName,$size,$html);
?>
