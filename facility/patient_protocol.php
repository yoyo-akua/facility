<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	/*
	## Initialising variables for the search in the database.
	## $searchpara represents all search parameters a current search is based on.
	## $tables describes in which tables of the database the search takes place.
	## $IDs is needed for an unambiguous association of data sets between tables.
	## $grouping is used to unite several data sets that equal each other in a certain parameter.
	## $having is used to apply search parameters for a complete group.
	*/
	$searchpara='';
	$tables='';
	$IDs='';
	$grouping='';
	$having='';
	
	/*
	## Initialising variable $parameter.
	## It is used to display all search parameters for the user.
	*/
	$parameter='';

	/*
	## Variables $from and $to are initialised which are used to set the time frame for the search.
	## Depending on, if the page was called by an external link or the submission of the search form, 
	## these dates are retrieved either from the URL or the global variable $_POST.
	*/
	if(! empty($_GET['from'])){
		$from=$_GET['from'];
	}else{
		$from=$_POST['from'];
	}

	if(! empty($_GET['to'])){
		$to=$_GET['to'];
	}else{
		$to=$_POST['to'];
	}
	
	## This if-branch is called if $from or $to are set.
	if(! empty($from OR $to)){

		## Using the variables $from and $to the time frame of the search is added to search parameters the current search is based on.
		$searchpara.=" AND checkin_time BETWEEN '$from' AND '$to 23:59:59'";	
		
		## Variables are initialised to transfer dates into british time format.
		$fromdisplay=date("d/m/y",strtotime($from));
		$todisplay=date("d/m/y",strtotime($to));
		
		## Depending on the relationship between $from and $to, the variable $timeframe is set to display the time frame for the user later on.
		if($from==$to){
			if($from==$today){
				$timeframe='today';
			}else{
				$timeframe="on $fromdisplay";
			}
		}else if(empty($from)){
			$timeframe="up to $todisplay";
		}else if(empty($to)){
			$timeframe="since $fromdisplay";
		}else{
			$timeframe="in the time from $fromdisplay to $todisplay";
		}
	}else{
		$timeframe="since the beginning of these records";
	}

	/*
	## This if-branch is called, if the user is searching a patient by its Name (and checking the checkbox).
	## The patient's Name is added to search parameters the current search is based on and buffered in $parameter
	## to display the user all chosen search parameters later.
	*/
	if(! empty($_POST['Namefilter'])){
		if(!empty($_POST['Name'])){
			$var=$_POST['Name'];
			$searchpara .= " AND Name like '%$var%'";
			$parameter.="- are called \"$var\"<br>";
		}	
	}

	/*
	## If the user is searching a patient by its OPD number (and checking the checkbox),
	## the patient's OPD number is added to search parameters the current search is based on and buffered in $parameter
	## to display the user all chosen search parameters later.
	*/
	if(! empty($_POST['OPDfilter'])){
		if(!empty($_POST['OPD'])){
			$var=$_POST['OPD'];
			$searchpara .= " AND OPD like '$var'";
			$parameter.="- have the OPD number \"$var\"<br>";
		}	
	}

	/*
	## If the user is searching a patient by its NHIS number (and checking the checkbox),
	## the patient's NHIS number is added to search parameters the current search is based on and buffered in $parameter
	## to display the user all chosen search parameters later.
	*/
	if(! empty($_POST['NHISfilter'])){
		if(!empty($_POST['NHIS'])){
			$var=$_POST['NHIS'];
			$searchpara .= " AND NHIS like '$var'";
			$parameter.="- have the NHIS number \"$var\"<br>";
		}	
	}

	/*
	## If the user is searching only patients that are entered (or not entered) in "NHIS Claim It" (and checking the checkbox),
	## depending on the selection "yes" or "no" this parameter is added to search parameters the current search is based on and 
	## buffered in $parameter to display the user all chosen search parameters later.
	*/
	/*
	TODO: Integrieren in neue Datenbank
	if(! empty($_POST['enteredfilter'])){
		$var = $_POST['enteredradio'];
		$searchpara .= " AND entered=$var";
		If($var== "1"){
			$parameter.="- are entered in NHIS Claim It<br>";
		}else{
			$parameter.="- are not entered in NHIS Calim It<br>";
		}
	}
	*/
	/*
	## If the user is searching only patients that have (not) been in the facility this year (and checking the checkbox),
	## depending on the selection "old" or "new" this parameter is added to search parameters the current search is based on and 
	## buffered in $parameter to display the user all chosen search parameters later.
	*/
	if(! empty($_POST['newfilter'])){
		$var = $_POST['new'];
		$searchpara .= " AND new_p=$var";
		if($var==0){
			$parameter.="- have been in the facility before <br>";
		}else{
			$parameter.="- have not been in the facility before<br>";
		}
	}

	/*
	## If the user is searching patients by their age (and checking the checkbox),
	## the variables $varfrom and $varto are initialised for adding the age frame to search parameters the current search is based on and 
	## buffering it in $parameter to display the user all chosen search parameters later.
	*/
	if(! empty($_POST['Agefilter'])){
		$varfrom = $_POST['agefrom'];
		$varto = $_POST['ageto'];
		$searchpara .= " AND patient.Birthdate BETWEEN (ADDDATE(visit.checkin_time,INTERVAL - $varto YEAR)) AND (ADDDATE(visit.checkin_time,INTERVAL - $varfrom YEAR))";
		$parameter.="- are between $varfrom and $varto years old<br>";
	}

	/*
	## If the user is searching only patients of a certain sex (and checking the checkbox),
	## depending on the selection "male" or "female" this parameter is added to search parameters the current search is based on and 
	## buffered in $parameter to display the user all chosen search parameters later.
	*/
	if(! empty($_POST['Sexfilter']) AND ! empty($_POST['Sex'])){
		$var = $_POST['Sex'];
		$searchpara .= " AND Sex like '$var'";
		$parameter.="- are $var<br>";
	}

	/*
	## If the user is searching only patients that are insured (or non insured) (and checking the checkbox),
	## depending on the selection "yes" or "no" this parameter is added to search parameters the current search is based on and 
	## buffered in $parameter to display the user all chosen search parameters later.
	*/
	if(! empty($_POST['Insurancefilter'])){
		$Insurance = $_POST['Insurance'];
		$tables.=',insurance';
		$IDs.=' AND visit.visit_ID=insurance.visit_ID';
		If($Insurance== "yes"){
			$searchpara .= " AND NHIS IS NOT NULL and expired=0";
			$parameter.="- are insured<br>";
		}else{
			$searchpara .= " AND (NHIS IS NULL OR expired=1)";
			$parameter.="- are not insured<br>";
		}
	}

	/*
	## If the user is searching only patients that are coming from a certain locality (and checking the checkbox),
	## the search parameter is added the search parameters the current search is based on and 
	## buffered in $parameter to display the user all chosen search parameters later.
	*/
	if(! empty($_POST['Localityfilter'])){
		$var = $_POST['Locality'];
		$searchpara .= " AND Locality = '$var'";
		$parameter.="- stay in $var<br>";
	}

	/*
	## If the user is searching only patients whose diagnoses were entered/ that were consulted (and checking the checkbox),
	## the search parameter is added to the search parameters the current search is based on and 
	## buffered in $parameter to display the user all chosen search parameters later.
	*/
	if(! empty($_POST['allconsulting'])){
		$subquery="SELECT protocol.protocol_ID FROM protocol,diagnosis_ids WHERE protocol.protocol_ID=diagnosis_ids.protocol_ID GROUP BY protocol.protocol_ID";
		$searchpara.=" AND (protocol.protocol_ID IN ($subquery) OR referral not like '')";
		$parameter.="- were consulted<br>";
	}

	/*
	## If the user is searching only patients whose diagnoses were not entered/ that were not consulted (and checking the checkbox),
	## the search parameter is added to the search parameters the current search is based on and 
	## buffered in $parameter to display the user all chosen search parameters later.
	*/
	if(! empty($_POST['noconsulting'])){
		$subquery="SELECT protocol.protocol_ID FROM protocol,diagnosis_ids WHERE protocol.protocol_ID=diagnosis_ids.protocol_ID GROUP BY protocol.protocol_ID";
		$searchpara.=" AND (protocol.protocol_ID NOT IN ($subquery))";
		$parameter.="- were not consulted<br>";
	}

	/*
	## If the user is searching only patients that were pregnant (and checking the checkbox),
	## the search parameter is added to the search parameters the current search is based on and 
	## buffered in $parameter to display the user all chosen search parameters later.
	*/
	if(! empty($_POST['pregnant'])){
		$searchpara.=" AND pregnant not like 0";
		$parameter.="- are pregnant<br>";
	}

	/*
	## If the user is searching only patients that were referred (and checking the checkbox),
	## the search parameter is added to the search parameters the current search is based on and 
	## buffered in $parameter to display the user all chosen search parameters later.
	*/
	if(! empty($_POST['referral'])){
		$searchpara.=" AND referral not like ''";
		$parameter.="- were referred<br>";
	}

	/*
	## If the user is searching only patients that were reattendants (and checking the checkbox),
	## the search parameter is added to the search parameters the current search is based on and 
	## buffered in $parameter to display the user all chosen search parameters later.
	*/
	if(! empty($_POST['reattendance'])){
		$subquery="SELECT protocol.protocol_ID FROM protocol,diagnosis_ids WHERE protocol.protocol_ID=diagnosis_ids.protocol_ID AND reattendance='1'";
		$searchpara.=" AND protocol.protocol_ID IN ($subquery)";
		$parameter.="- came for review<br>";
	}

	/*
	## Get data from database.
	## Get a list of all diseases.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/
	$query="SELECT * FROM diagnoses";
	$result=mysqli_query($link,$query);
	## This loop is run once for every disease from database.
	while($row=mysqli_fetch_object($result)){
	
		## Initialising variables with the name and ID of the disease.
		$Diagnosis_ID=$row->Diagnosis_ID;
		$Diagnosisname=$row->DiagnosisName;
		
		## This if-branch is called, if the user is searching for that particular disease.
		if(!empty($_POST["disease_$Diagnosis_ID"])){
			
			/*
			## This if-branch is called, if the user is only searching for that particular disease, 
			## that is either defined as primary, secondary or as provisional diagnosis.
			*/			
			if (! empty($_POST["radio_$Diagnosis_ID"])){
				
				/*
				## Variable $radio is used to indicate, whether the user is searching for primary, provisional or secondary diagnoses.
				## This is used to add the condition to  $searchpara.
				## The search parameter is added to the search parameters the current search is based on and 
				## buffered in $parameter to display the user all chosen search parameters later, 
				## depending on the user's selection of primary, provisional or secondary diagnosis.
				*/
				$radio=$_POST["radio_$Diagnosis_ID"];
				$subquery="SELECT protocol.protocol_ID FROM protocol,diagnosis_ids WHERE protocol.protocol_ID=diagnosis_ids.protocol_ID AND diagnosis_id='$Diagnosis_ID' AND importance='$radio'";
				$searchpara.=" AND protocol_ID IN ($subquery)";
				$parameter.="- were diagnosed with $Diagnosisname";
				
				if($radio==1){
					$parameter.="(primary)";
				}else if($radio==2){
					$parameter.="(secondary)";
				}else{
					$parameter.="(provisional)";
				}
				
				$parameter.="<br>";
			}
			
			/*
			## If the user did not select any limitation like provisional, primary or secondary for the search,
			## just the disease is added to search parameters the current search is based on and 
			## buffered in $parameter to display the user all chosen search parameters later.
			*/
			else{
				$subquery="SELECT protocol.protocol_ID FROM protocol,diagnosis_ids WHERE protocol.protocol_ID=diagnosis_ids.protocol_ID AND diagnosis_id='$Diagnosis_ID'";
				$searchpara.=" AND protocol_ID IN ($subquery)";
				$parameter.="- were diagnosed with $Diagnosisname<br>";
			}
		}
	}

	/*
	## If the user is searching for patients, that haven't been referred to lab,
	## the search parameter is added to the search parameters the current search is based on and 
	## buffered in $parameter to display the user all chosen search parameters later.
	*/	
	if(! empty($_POST['nolab'])){
		$searchpara.=" AND protocol.protocol_ID NOT IN (SELECT protocol_ID FROM lab_list)";
		$parameter.="- were not tested in lab<br>";
	}

	/*
	## If the user is searching only patients that have (not) paid for their tests or a Minor OP (and checking the checkbox),
	## depending on the selection "yes" or "no" this parameter is added to search parameters the current search is based on.
	## The search parameter is buffered in $parameter to display the user all chosen search parameters later, if "no" is selected. 			## Otherwise the condition will be added to $parameter later on.
	*/
	if(! empty($_POST['payingfilter'])){
		if(! empty($_POST['paying'])){
			$paying = $_POST['paying'];
			if($paying== "yes"){
				$searchpara .= " AND charge not like '0.00'";
			}else{
				$searchpara .= " AND charge like '0.00'";
				$parameter.="- were not charged<br>";
			}
		}
	}

	/*
	## If the user is searching a patient by its lab number (and checking the checkbox),
	## the patient's lab number is added to search parameters the current search is based on and 
	## buffered in $parameter to display the user all chosen search parameters later.
	*/		
	if(! empty($_POST['Lab_IDfilter']) AND !empty($_POST['Lab_ID'])){
		$var=$_POST['Lab_ID'];
		$tables.=",lab_list";
		$IDs.=" AND lab_list.visit_ID=protocol.visit_ID";
		$searchpara .= " AND lab_list.lab_number like '$var'";
		$parameter.="- have Lab Number $var<br>";	
	}

	/*
	## This if-branch is called, if the user is searching for
	##		- all patient's that came to lab (including self-paying ones),
	##		- all patients, that did a specific test
	##		- or all patients, that were tested in a lab (including outsourced tests)
	*/
	if(! empty($_POST['alllab']) OR strstr(http_build_query($_POST),'testfilter') OR ! empty($_POST['other_facility'])){
		
		## Completing variables to add the lab database table to the search.
		$IDs.=" and lab.protocol_ID_ordered=protocol.protocol_ID";
		$tables.=",lab";
		$grouping.=", lab.protocol_ID_ordered";
		
		/*
		## If the user explicitly searchs for all lab patients (including the self-paying ones),
		## this search parameter is buffered in $parameter to display the user all chosen search parameters later.
		## Otherwise, self-paying lab patients are excluded from the search by an addition to search parameters the current search is based on.
		*/
		if(! empty($_POST['alllab'])){
			$parameter.="- were tested in lab (including self-paying)<br>";
		}else{
			$searchpara.=" AND onlylab=0";
		}

		/*
		## If the user explicitly searchs for all tests (including the outsourced ones),
		## this search parameter is buffered in $parameter to display the user all chosen search parameters later.
		## Otherwise, outsourced tests are excluded from the search by an addition to search parameters the current search is based on.
		*/		
		if(! empty($_POST['other_facility'])){
			$parameter.="- were tested in lab (including other labs)<br>";
		}else{
			$searchpara.=" AND other_facility=0";
		}
		
		## This if-branch is called, if the user refined the search to any specific test.
		if(strstr(http_build_query($_POST),'testfilter')){
			
			/*
			## Initialising variables.
			## $first1 is used to detect the first selected test.
			## $parameter_number counts the test's parameters, that are involved in the search.
			## Both is necessary for a correct database query.
			*/
			$first1=true;
			$parameter_number=0;
			
			/*
			## Get data from database. 
			## Get list of all tests.
			## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
			*/
			$query="SELECT * FROM tests ORDER BY frequency ASC";
			$result=mysqli_query($link,$query);
						
			## This loop is run once for each test from database.
			while($row=mysqli_fetch_object($result)){
			
				## Initialising variables with the name and ID of the test.
				$test_ID=$row->test_ID;
				$test_name=$row->test_name;
				
				## This if-branch is called, if the user searched by the current test.
				if(! empty($_POST["testfilter_$test_ID"])){
					
					/*
					## $para is used as buffer to complete $parameter for the display of the parameter for the user with 
					## the refinements, that the user entered.
					*/
					$para=array();
					
					/*
					## Get data from database. 
					## Get list of all parameters of the test.
					## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
					*/
					$query2="SELECT * FROM parameters WHERE test_ID=$test_ID";
					$result2=mysqli_query($link,$query2);
					
					## This loop is run once for each test's parameter.
					while($row2=mysqli_fetch_object($result2)){
					
						## With each run of the loop the number of test's parameters is increased by 1.
						$parameter_number++;
						
						/*
						## Initialising variables.
						## Variable $type stores the input_type of the test, depending on which certain search parameters are offered to the user.
						## The ID and the name of the test's parameter are stored in further variables.
						## The variable $test_outcomes contains the value range for the particular parameter. 
						*/
						$type=$row2->input_type;
						$parameter_ID=$row2->parameter_ID;
						$parameter_name=$row2->parameter_name;
						$test_outcomes=$row2->test_outcomes;
						
						## Add the test parameter to search parameters the current search is based on.
						if($first1){
							$searchpara.=" AND ((parameter_ID=$parameter_ID";
							$first1=false;
						}else{
							$searchpara.=" OR (parameter_ID=$parameter_ID";
						}
						
						## If the parameter's value is saved as a figure and the user entered a range within which this should be, add those parameters to search parameters the current search is based on and buffer it in $para to complete the display of all chosen search parameters for the user.
						if($type=='number' AND ! empty($_POST["from_$parameter_ID"]) AND ! empty ($_POST["to_$parameter_ID"])){
							$from_para=$_POST["from_$parameter_ID"];
							$to_para=$_POST["to_$parameter_ID"];
							$searchpara.=" AND test_results BETWEEN '$from_para' AND '$to_para'";
							$para[$parameter_name]="$from_para-$to_para";
						}
						
						## If the parameter includes concrete values that can be selected, this if-branch is called.
						else if($type=='checkbox' OR $type=='radio' OR $type=='select'){
							
							/*
							## Initialise variables.
							## $j is used to count the number of possible outcomes for the test's parameter.
							## $first2 is used to determine the first one of the possible outcomes that is selected.
							## This is necessary for a correct database query and 
							## grammatical syntax of displaying all chosen search parameters for the user.
							## $any determines if any of the refinements has been selected and also serves the purpose of a correct database query.
							## $checkbox_arr is transferring the enumeration of possible outcomes in $test_outcomes to an array
							## which can be used to create the following loop.
							*/
							$j=0;
							$first2=true;
							$any=false;
							$checkbox_arr=explode(',',$test_outcomes);
							
							## The following loop will be run once for each of the possible outcomes of the parameter.
							foreach($checkbox_arr AS $checkbox){
								## Increase the number of possible outcomes for the test's parameter by one.
								$j++;
								
								/*
								## If the possible outcome is indeed selected, 
								## add it to the search parameters the current search is based on,
								## buffer it in $para for further actions and 
								## set that any of the refinements has been selected to serve the purpose of a correct database query.
								## The first if-branch is called, if the possible outcome is the first, that is selected.
								## Otherwise the second branch is called.
								*/
								if(! empty($_POST["checkbox_$parameter_ID($j)"]) AND $first2){
									$var=$_POST["checkbox_$parameter_ID($j)"];
									$first2=false;
									$any=true;
									$searchpara.=" AND test_results like ('%$var%'";
									$para[$parameter_name]=$var;
									
								}else if(! empty($_POST["checkbox_$parameter_ID($j)"])){
									$var=$_POST["checkbox_$parameter_ID($j)"];
									$searchpara.=" OR '%$var%'";
									$para[$parameter_name].=" & $var";
								}
							}
							
							## If any test was selected, close a bracket in $searchpara (which is necessary for a correct database query).
							if ($any){
								$searchpara.=")";
							}
						}
						
						/*
						## If the test parameter's value is in the form of a text and the user entered a key word to search for,
						## call this if-branch.
						## Add the test parameter to search parameters the current search is based on and 
						## buffer the test parameter in $para for futher actions.
						*/
						else if(($type=='textarea' OR $type=='text') AND ! empty($_POST["text_$parameter_ID"])){
							$var=$_POST["text_$parameter_ID"];
							$searchpara.=" AND test_results like '%$var%'";
							$para[$parameter_name]="includes $var";
						}
						
						## Close a bracket in $searchpara (which is necessary for a correct database query).
						$searchpara.=')';
					}
					
					## Buffer the test in $parameter to display the user all chosen search parameters later.
					$parameter.="- were tested for $test_name ";
					
					/*
					## If there were any refinements for any test parameters in the search,
					## convert the refinements collected in the buffer $para to a sensible text and add it to $parameter,
					## to display the user all chosen search parameters later.
					*/
					if(! empty($para)){
						$para_array=array();
						foreach ($para AS $key=>$value){
							if(! empty($key)){
								$para_array[]="$key:$value";
							}else{
								$para_array[]="$value";
							}
						}
						$para_string=implode(',',$para_array);
						$parameter.="($para_string)";
					}
					
					$parameter.="<br>";
				}
			}
			
			## Close a bracket in $searchpara (which is necessary for a correct database query).			
			$searchpara.=")";
			
			/*
			## Use the amount of test parameters involved in the search for creating a content for $having.
			## This is necessary for a correct database query.
			*/
			$having=" HAVING (COUNT(parameter_ID)=$parameter_number)";
		}
	}
	
	/*
	## If no search parameter of lab is selected, add a condition to thze search parameters the current search is based on,
	## that prevents self-paying lab clients from being shown in the search results.
	*/
	else{
		$searchpara.=" AND onlylab=0";
	}


	/*
	## If the user is searching for patients, that did not attend PNC,
	## the search parameter is added to the search parameters the current search is based on,
	## and is buffered in $parameter to display the user all chosen search parameters later.
	*/	
	if(! empty($_POST['noPNC'])){
		$searchpara.=" and PNC like 0";
		$parameter.="- did not attend PNC<br>";
	}

	/*
	## TODO: anpassen, wenn PNC fertig
	## If the user is searching for patients, that did attend PNC,
	## the search parameter is added to the search parameters the current search is based on,
	## and is buffered in $parameter to display the user all chosen search parameters later.
	*/	
	if(! empty($_POST['allPNC'])){
		$searchpara.=" and PNC like 1";
		$parameter.="- did attend PNC<br>";
	}

	/*
	## If the user is searching for patients, that did not attend ANC,
	## the search parameter is added to the search parameters the current search is based on,
	## and is buffered in $parameter to display the user all chosen search parameters later.
	*/	
	if(! empty($_POST['noANC'])){
		$searchpara.=" and ANC_ID like ''";
		$parameter.="- did not attend ANC<br>";
	}


	/*
	## This if-branch is called, if the user is searching for ANC clients, that
	##		- attended ANC at all,
	##		- are new ANC registrants,
	##		- have their estimated delivery date within a certain time frame,
	##		- are within a certain range of parity,
	##		- came for a certain (number of) visit (e.g. 1st, 2nd, 3rd),
	##		- had a fundal height within a certain range,
	##		- took Malaria prophylaxis,
	##		- got Tetanus/Diphteria vaccine,
	##		- came within a certain week of pregnancy,
	##		- came within a certain trimester of pregnancy,
	##		- or have a body height within a certain range.
	## In sum that means the user is limiting the search to client's visits in the facility, which include an ANC visit.
	*/
	if(! empty($_POST['allANC']) 
		OR ! empty($_POST['newANC'])
		OR ! (empty($_POST['EDDfilter']) AND empty($_POST['EDDfrom']) AND empty($_POST['EDDto']))
		OR ! (empty($_POST['parity_filter']) AND empty($_POST['parity_from']) AND empty($_POST['parity_to'])) 
		OR ! (empty($_POST['visit_number_filter']) AND empty($_POST['visitnumber'])) 
		OR ! (empty($_POST['fundal_height_filter']) AND empty($_POST['fundal_heightfrom']) AND empty($_POST['fundal_heightto'])) 
		OR ! empty($_POST['SP_filter']) 
		OR ! empty($_POST['TT_filter'])
		OR ! (empty($_POST['week_filter']) AND empty($_POST['week']))
		OR ! (empty($_POST['trimester_filter']) AND empty($_POST['trimester']))){
		
			## Completing variables to add the anc database table to the search.
			$tables.=",anc";
			$IDs.=" AND protocol.protocol_ID=anc.protocol_ID";

			/*
			## If the user is searching for patients, that came for a certain (number of) visit (e.g. 1st, 2nd, 3rd),
			## the search parameter is added to the search parameters the current search is based on 
			## and is buffered in $parameter to display the user all chosen search parameters later.
			*/
			if(! (empty($_POST['visit_number_filter']) AND empty($_POST['visitnumber']))){
				$var=$_POST['visitnumber'];
				$searchpara.=" AND visitnumber like'$var'";
				$parameter.="- came for $var. ANC visit<br>";
			}

			/*
			## If the user is searching for patients, that had a fundal height within a certain range,
			## the search parameter is added to the search parameters the current search is based on 
			## and is buffered in $parameter to display the user all chosen search parameters later.
			*/		
			if(! (empty($_POST['fundal_height_filter']) AND empty($_POST['fundal_heightfrom']) AND empty($_POST['fundal_heightto']))){
				$varfrom=$_POST['fundal_heightfrom'];
				$varto=$_POST['fundal_heightto'];
				$searchpara.=" AND FHt>='$varfrom' AND FHt<='$varto'";
				$parameter.="- had a Fundal Height $varfrom-$varto cm<br>";
			}

			/*
			## If the user is searching for patients, that took Malaria prophylaxis,
			## the search parameter is added to the search parameters the current search is based on 
			## and is buffered in $parameter to display the user all chosen search parameters later.
			*/				
			if(! empty($_POST['SP_filter'])){
				$searchpara.=" AND SP not like 0";
				$parameter.="- took Sulfadoxine Pyrimethamine";
				
				/*
				## If a certain number of Malaria prophylaxis is searched, add that to the search parameters,
				## the current search is based on and buffer it in $parameter to display the user all chosen search parameters later.
				*/
				if(! empty($_POST['SP_number'])){
					$var=$_POST['SP_number'];
					$searchpara.=" AND SP like '$var'";
					$parameter.=" ($var. time)";
				}

				$parameter.="<br>";
			}

			/*
			## If the user is searching for patients, that got Tetanus/Diphteria vaccine,
			## the search parameter is added to the search parameters the current search is based on 
			## and is buffered in $parameter to display the user all chosen search parameters later.
			*/				
			if(! empty($_POST['TT_filter'])){
				$searchpara.=" AND TT not like 0";
				$parameter.="- got Tetanus Diphteria vaccine";
				
				/*
				## If a certain number of Tetanus/Diphteria vaccine is searched, add that to the search parameters,
				## the current search is based on and buffer it in $parameter to display the user all chosen search parameters later.
				*/
				if(! empty($_POST['TT_number'])){
					$var=$_POST['TT_number'];
					$searchpara.=" AND TT like '$var'";
					$parameter.=" ($var. dose)";
				}
				
				$parameter.="<br>";
			}

	}

	/*
	## This if-branch is called, if the user is searching for ANC clients, that
	##		- are new ANC registrants,
	##		- have their estimated delivery date within a certain time frame,
	##		- are within a certain range of parity,
	##		- came within a certain week of pregnancy,
	##		- came within a certain trimester of pregnancy,
	##		- have a body height within a certain range,
	##		- delivered,
	##		- delivered babies with a weight within a certain range,
	##		- or delivered singles (or twins).
	*/
	if(! (empty($_POST['week_filter']) AND empty($_POST['week']))
	OR ! empty($_POST['newANC'])
	OR ! (empty($_POST['parity_filter']) AND empty($_POST['parity_from']) AND empty($_POST['parity_to'])) 
	OR ! (empty($_POST['trimester_filter']) AND empty($_POST['trimester']))
	OR ! (empty($_POST['EDDfilter']) AND empty($_POST['EDDfrom']) AND empty($_POST['EDDto']))){
		
		## Completing variable to add the maternity database table to the search.
		$tables.=",maternity";

		/*
		## This if-branch is called, if the user is searching for ANC clients, that
		##		- are new ANC registrants,
		##		- have their estimated delivery date within a certain time frame,
		##		- are within a certain range of parity,
		##		- came within a certain week of pregnancy,
		##		- came within a certain trimester of pregnancy,
		##		- or have a body height within a certain range.
		*/		
		if(! (empty($_POST['week_filter']) AND empty($_POST['week']))
			OR ! empty($_POST['newANC'])
			OR ! (empty($_POST['parity_filter']) AND empty($_POST['parity_from']) AND empty($_POST['parity_to'])) 
			OR ! (empty($_POST['trimester_filter']) AND empty($_POST['trimester']))
			OR ! (empty($_POST['EDDfilter']) AND empty($_POST['EDDfrom']) AND empty($_POST['EDDto']))){
				
				## Completing variable to add the maternity database table to the search.
				$IDs.=" AND maternity.maternity_ID=anc.maternity_ID";


				/*
				## If the user is searching only patients that have (not) attended ANC (with this pregnancy) before (and checking the checkbox),
				## depending on the selection "old" or "new" this search parameter is added to the search parameters,
				## the current search is based on and buffered in $parameter to display the user all chosen search parameters later.
				*/
				if(! empty($_POST['newANCfilter']) AND ! empty($_POST['newANC'])){
					if($_POST['newANC']=='new'){
						$searchpara.=" AND anc.visitnumber like '1'";
						$parameter.="- were new ANC registrants<br>";
					}else{
						$searchpara.=" AND anc.visitnumber not like '1'";
						$parameter.="- were old ANC registrants<br>";
					}
				}

				/*
				## If the user is searching for clients, that came within a certain week of pregnancy,
				## the search parameter is added to the search parameters,
				## the current search is based on and buffered in $parameter to display the user all chosen search parameters later.
				*/						
				if(! (empty($_POST['week_filter']) AND empty($_POST['week']))){
					$week=$_POST['week'];
					$nextweek=$week+1;
					$searchpara.=" AND visit.checkin_time<(ADDDATE(maternity.conception_date,INTERVAL + $nextweek WEEK)) AND visit.checkin_time>=(ADDDATE(maternity.conception_date,INTERVAL + $week WEEK))";
					$parameter.="- were in $week. week pregnant<br>";
				}

				/*
				## If the user is searching for clients, that came within a certain trimester of pregnancy,
				## the search parameter is added to the search parameters,
				## the current search is based on and buffered in $parameter to display the user all chosen search parameters later.
				*/			
				if(! (empty($_POST['trimester_filter']) AND empty($_POST['trimester']))){
					$trimester=$_POST['trimester'];
					$parameter.="- were in $trimester. trimester of pregnancy<br>";

					if($trimester=='1'){
						$searchpara.=" AND visit.checkin_time<(ADDDATE(maternity.conception_date,INTERVAL + 14 WEEK)) AND visit.checkin_time>=maternity.conception_date";
					}else if($trimester=='2'){
						$searchpara.=" AND visit.checkin_time<(ADDDATE(maternity.conception_date,INTERVAL + 28 WEEK)) AND visit.checkin_time>=(ADDDATE(maternity.conception_date,INTERVAL + 14 WEEK))";
					}else if($trimester=='3'){
						$searchpara.=" AND visit.checkin_time<(ADDDATE(maternity.conception_date,INTERVAL + 45 WEEK)) AND visit.checkin_time>=(ADDDATE(maternity.conception_date,INTERVAL + 28 WEEK))";
					}
				}

				/*
				## If the user is searching for clients, that are within a certain range of parity,
				## extend the search parameters, the current search is based on by 
				## calling a loop that is run once for each parity within the range.
				## Furthermore the search parameter is buffered in $parameter to display the user all chosen search parameters later.
				*/			
				if(! (empty($_POST['parity_filter']) AND empty($_POST['parity_from']) AND empty($_POST['parity_to'])) ){
					$varfrom=$_POST['parity_from'];
					$varto=$_POST['parity_to'];
					$first=true;
					for($var=$varfrom;$var<=$varto;$var++){
						if($first){
							$searchpara.=" AND (parity like '%P$var%'";
							$first=false;
						}else{
							$searchpara.=" OR parity like '%P$var%'";
						}
					}
					$searchpara.=")";
					$parameter.="- have parity between $varfrom and $varto<br>";
				}

				/*
				## If the user is searching for clients, that are expected to deliver within a certain time frame,
				## the search parameter is added to the search parameters,
				## the current search is based on by calculating it from the conception date in the database
				## and after transferring the dates to British format add the search parameter to $parameter,
				## to display the user all chosen search parameters later.
				*/			
				if(! (empty($_POST['EDDfilter']) AND empty($_POST['EDDfrom']) AND empty($_POST['EDDto']))){
					$varfrom=$_POST['EDDfrom'];
					$varto=$_POST['EDDto'];
					$searchpara.=" AND (ADDDATE(maternity.conception_date,INTERVAL + 40 WEEK)) BETWEEN '$varfrom' AND '$varto'";

					$varfrom=date('d/m/Y',strtotime($varfrom));
					$varto=date('d/m/Y',strtotime($varto));
					$parameter.="- are expected to deliver between $varfrom and $varto<br>";
				}
			}
		}

		/*
		## This if-branch is called, if the user is searching for ANC clients, that
		##		- delivered,
		##		- delivered babies with a weight within a certain range,
		##		- or delivered singles (or twins).
		*/
		if(! empty($_POST['alldeliveries']) 
			OR ! (empty($_POST['babyweight_filter']) AND empty($_POST['babyweight_from']) AND empty($_POST['babyweight_to']))
			OR (! empty($_POST['single_filter']))){
			
			## Complete variable to add the maternity database table to the search and exclude all non delivery cases.
			$tables.=",delivery";
			$IDs.=" AND protocol.protocol_ID=delivery.protocol_ID";
			$parameter.="- delivered<br>";

			/*
			## If the user is searching for single (or twin) deliveries,
			## a subordinate query is installed to retrieve these data from the delivery database table
			## it uses the keyword "Baby 2" to find twin deliveries.
			## Use this information to extend the search parameters, the current search is based on.
			## The search parameter is added to $parameter to display the user all chosen search parameters later.
			*/
			if(! empty($_POST['single_filter']) AND ! empty($_POST['single'])){
				$subquery="SELECT * FROM delivery WHERE maternity_ID=maternity.maternity_ID AND result LIKE '%Baby 2:%'";
				if($_POST['single']=='single'){
					$searchpara.=" AND NOT EXISTS($subquery)";
					$parameter.="- delivered a single child<br>";
				}else{
					$searchpara.=" AND EXISTS($subquery)";
					$parameter.="- delivered twins<br>";
				}
			}


			/*
			## If the user is searching for clients, that delivered babies within a certain weight range,
			## a subordinate query is installed to retrieve these data from the delivery database table.
			## The search parameter is added to the search parameters,
			## the current search is based on and buffered in $parameter to display the user all chosen search parameters later.
			*/					
			if(! empty($_POST['babyweight_filter']) AND ! empty($_POST['babyweight_from']) AND ! empty($_POST['babyweight_to'])){
				$babyweight_from=$_POST['babyweight_from'];
				$babyweight_to=$_POST['babyweight_to'];
				$subquery="
									SELECT * FROM delivery,delivery_categories 
									WHERE delivery.del_category_ID=delivery_categories.del_category_ID 
										AND delivery.maternity_ID=maternity.maternity_ID 
										AND category_name like 'Weight' 
										AND result BETWEEN '$babyweight_from' AND '$babyweight_to'
								";
				$searchpara.=" AND EXISTS ($subquery)";
				
				$parameter.="- delivered babies between $babyweight_from and $babyweight_to kg<br>";
 
			}
	}

	if((! empty($_POST["BMI_classification_filter"]) AND ! empty($_POST["BMI_classification"]))
		 OR (! empty($_POST["management_filter"]) AND ! empty($_POST["management"]))
		 OR (! empty($_POST["all_nutrition"]))){
		$tables.=",nutrition";
		$IDs.=' AND nutrition.protocol_ID=protocol.protocol_ID';
		$parameter.="- received nutritional care";
		
		if(! empty($_POST['management_filter']) AND ! empty($_POST['management'])){
			$management=$_POST['management'];
			$searchpara.=" AND nutrition.management like '$management'";
			$parameter.=" of the type $management";
		}
		$parameter.='<br>';
		if(! empty($_POST['BMI_classification_filter']) AND ! empty($_POST['BMI_classification'])){
			$classification=$_POST['BMI_classification'];
			$searchpara.=" AND nutrition.BMI_classification like '$classification%'";
			$parameter.="- have $classification";
		}
	}

	if((! empty($_POST["BMI_filter"]) AND ! empty($_POST["BMI_from"]) AND ! empty($_POST["BMI_to"]))
		 OR (! empty($_POST["MUAC_filter"]) AND ! empty($_POST["MUAC_from"]) AND ! empty($_POST["MUAC_to"]))
		 OR (! empty($_POST['height_filter']) AND ! empty($_POST['heightfrom']) AND ! empty($_POST['heightto']))){
		$tables.=",vital_signs";
		$IDs.=" AND vital_signs.protocol_ID=protocol.protocol_ID";
		
		if(! empty($_POST['height_filter']) AND ! empty($_POST['heightfrom']) AND ! empty($_POST['heightto'])){
			$varfrom=$_POST['heightfrom'];
			$varto=$_POST['heightto'];
			$searchpara.=" AND vital_signs.height>='$varfrom' AND vital_signs.height<='$varto'";
			$parameter.="- are $varfrom-$varto cm tall<br>";
		}
		
		if(! empty($_POST["MUAC_filter"]) AND ! empty($_POST["MUAC_from"]) AND ! empty($_POST["MUAC_to"])){
			$varfrom=$_POST['MUAC_from'];
			$varto=$_POST['MUAC_to'];
			$searchpara.=" AND vital_signs.MUAC>='$varfrom' AND vital_signs.MUAC<='$varto'";
			$parameter.="- have $varfrom-$varto cm MUAC<br>";
		}

		if(! empty($_POST["BMI_filter"]) AND ! empty($_POST["BMI_from"]) AND ! empty($_POST["BMI_to"])){
			$varfrom=$_POST['BMI_from'];
			$varto=$_POST['BMI_to'];
			$searchpara.=" AND (vital_signs.weight/((vital_signs.height/100)*(vital_signs.height/100))) BETWEEN '$varfrom' AND '$varto'";
			$parameter.="- have a BMI of $varfrom-$varto  kg/m&sup2<br>";
		}
	}

	/*
	## If the user is searching for patients, that were prescribed drugs to,
	## the search parameter is added to the search parameters,
	## the current search is based on and buffered in $parameter to display the user all chosen search parameters later.
	*/		
	if(! empty($_POST['alldrugs'])){
		$subquery="SELECT protocol.protocol_ID FROM protocol,disp_drugs WHERE protocol.protocol_ID=disp_drugs.prescription_protocol_ID";
		$searchpara.=" and protocol.protocol_ID IN($subquery)";
		$parameter.="- were prescribed drugs to<br>";
	}

	/*
	## If the user is searching for patients, that were not prescribed drugs to,
	## the search parameter is added to the search parameters,
	## the current search is based on and buffered in $parameter to display the user all chosen search parameters later.
	*/	
	if(! empty($_POST['nodrugs'])){
		$subquery="SELECT protocol.protocol_ID FROM protocol,disp_drugs WHERE protocol.protocol_ID=disp_drugs.prescription_protocol_ID";
		$searchpara.=" and protocol.protocol_ID NOT IN($subquery)";
		$parameter.="- were not prescribed drugs to<br>";
	}
	
	/*
	## If the user is searching for patients, that received drugs in the dispensary,
	## a subordinate query is installed to retrieve these data from the disp_drugs database table.
	## The search parameter is added to the search parameters,
	## the current search is based on and buffered in $parameter to display the user all chosen search parameters later.
	*/				
	if(! empty($_POST['facilitydrugs'])){
		$subquery="SELECT protocol.protocol_ID FROM disp_drugs,protocol WHERE protocol.protocol_ID=disp_drugs.prescription_protocol_ID AND Counts not like ''";
		$searchpara.=" AND protocol.protocol_ID IN ($subquery)";
		$parameter.="- received drugs in the dispensary<br>";
	}


	## This if-branch is called, if the user is searching for patients, that were prescribed a specific drug to.
	if(strstr(http_build_query($_POST),'drug_')){
		
		/*
		## Get data from database. 
		## Get list of all drugs in the database.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
		*/
		$query="SELECT * FROM drugs ORDER BY Drugname ASC";
		$result=mysqli_query($link,$query);

		## This loop is run once for each of the output drugs from the database query.
		while($row=mysqli_fetch_object($result)){
			
			## Initialise variables with drug's ID and name.
			$Drug_ID=$row->Drug_ID;
			$Drugname=$row->Drugname;

			/*
			## If the user is searching for patients, that got this drug,
			## a subordinate query is installed to retrieve these data from the disp_drugs database table.
			## The search parameter is added to the search parameters,
			## the current search is based on and buffered in $parameter to display the user all chosen search parameters later.
			*/							
			if(! empty($_POST["drug_$Drug_ID"])){
				$subquery="SELECT protocol.protocol_ID FROM disp_drugs,protocol WHERE protocol.protocol_ID=disp_drugs.prescription_protocol_ID AND Drug_ID='$Drug_ID'";
				## If the user only wants patients who really received the drug in the dispensary, add this parameter to the subordinate query.
				if(! empty($_POST['facilitydrugs'])){
					$subquery.=" AND Counts not like ''";
				}
				$searchpara.=" AND protocol.protocol_ID IN ($subquery)";
				$parameter.="- were prescribed $Drugname to<br>";
			}
		}
	}


	/*
	## If the user is searching for patients, that did any surgery,
	## the search parameter is added to the search parameters,
	## the current search is based on and buffered in $parameter to display the user all chosen search parameters later.
	*/	
	if(! empty($_POST['allsurgeries'])){
		$searchpara.=" AND surgery not like ''";
		$parameter.="- did surgery/procedure<br>";
	}

	/*
	## If the user is searching only patients that have (not) done circumcision (and checking the checkbox),
	## depending on the selection "circumcision" or "other" this parameter is added to the search parameters,
	## the current search is based on and buffered in $parameter to display the user all chosen search parameters later.
	*/
	if(! empty($_POST['circumcision_filter']) AND ! empty($_POST['circumcisions'])){
		if($_POST['circumcisions']=='circumcision'){
			$searchpara.=" AND surgery like '%circumcision%'";
			$parameter.="- were performed circumcision on<br>";
		}else{
			$searchpara.=" AND surgery not like '%circumcision%'";
			$parameter.="- were performed procedure (other than circumcision) on<br>";
		}
	}


	/*
	## Print a headline and a form, 
	## which contains all the search parameters,
	## the input fields for the time frame in which the search is supposed to take place,
	## and general search options by which the user can search a specific patient in the protocol (Name, OPD, NHIS).
	*/
	echo'
			<h1>Protocol</h1>
				<div style="border-top:1px solid DarkGray; border-bottom:1px solid DarkGray;padding:30px;width:30%;margin-bottom:20px">
				<form action="patient_protocol.php" method="post">

					<div><label>Timeframe:</label><br>
					from<input type=date name="from" value="'.$from.'"  max="'.$today.'">
					to<input type=date name="to" value="'.$to.'" max="'.$today.'"><br></div><br>

					<div><input type="checkbox" name="Namefilter" ';
						if(! empty($_POST['Namefilter'])){
							echo "checked='checked'";
						}
						echo'>
					<label>Name:</label>   
					<input type="text" name="Name" ';
						if(! empty($_POST['Name'])){
							echo "value=".$_POST['Name'];
						}
						echo'  class="autocomplete" autocomplete="off" id="autocomplete">  
					<br></div>

					<div><input type="checkbox" name="OPDfilter" ';
						if(! empty($_POST['OPDfilter'])){
							echo "checked='checked'";
						}
						echo'>
					<label>OPD:</label>
					<input type="text" name="OPD" ';
						if(! empty($_POST['OPD'])){
							echo "value=".$_POST['OPD'];
						}
						echo'>
					<br></div>
					
					<div><input type="checkbox" name="NHISfilter" ';
						if(! empty($_POST['NHISfilter'])){
							echo "checked='checked'";
						}
						echo'>
					<label>NHIS:</label>
					<input type="text" name="NHIS" ';
						if(! empty($_POST['NHIS'])){
							echo "value=".$_POST['NHIS'];
						}
						echo'>
					<br></div>

				</div>
				';

	## Print styling elements (text field and table) which are the base for the beam in which all the search parameters can be found.
	echo'
			<div class="beam">
			<table><tr class="beam">
			';

	/*
	## If the department OPD is activated in "defaults/DEFAULTS.php", 
	## an icon, a headline and a drop down list with OPD's search parameters are displayed.
	## These are:
			- a button to create the OPD report,
	##		- the entered status (of NHIS Claim It),
	##		- the old/new status of the patient(s) (which describes, if the patient has been in the facility within this year or not),
	##		- an age range within which the patient(s) should be,
	##		- the sex, locality and insurane status of the patient(s).
	## If the user has searched once before, the OPD's form is prefilled with the search parameters of the previous search. 
	*/
	if(in_array('OPD',$DEPARTMENTS)){
		echo'
				<td><button type="submit" name="submit"><i class="fas fa-search" id="protocol_search"></i></button><td>

				<td><details>
				<summary><div><div class="smalltile">OPD<br><i class="fas fa-id-card-alt fa-2x"></i></div></div></summary>
				<br><a class="button" href="OPD_report.php?from='.$from.'&to='.$to.'">create report</a><br><br>';
				/*
				TODO: Integrieren in neue Datenbank
				<div><label>Entered in NHIS Claim It:</label><br>
				<input type="checkbox" name="enteredfilter" ';
					if(! empty($_POST['enteredfilter'])){
						echo "checked='checked'";
					}
					echo'>
				<input type="radio" name="enteredradio" value="1" ';
					if(! empty($_POST['enteredradio']) AND $_POST['enteredradio']==1){
						echo "checked='checked'";
					}
					echo'>yes
				<input type="radio" name="enteredradio" value="0"';
					if(! empty($_POST['enteredradio']) AND $_POST['enteredradio']==0){
						echo "checked='checked'";
					}
					echo'>no
				</div>		  
				*/
				echo'
				<div><label>Old/New:</label><br>
				<input type="checkbox" name="newfilter" ';
					if(! empty($_POST['newfilter'])){
						echo "checked='checked'";
					}
					echo'>
				<input type="radio" name="new" value="0" ';
					if(! empty($_POST['new']) AND $_POST['new']==0){
						echo "checked='checked'";
					}
					echo'>old
				<input type="radio" name="new" value="1" ';
					if(! empty($_POST['new']) AND $_POST['new']==1){
						echo "checked='checked'";
					}
					echo'>new 
				</div>		         

				<div><label>Age:</label><br>
				<input type="checkbox" name="Agefilter" ';
					if(! empty($_POST['Agefilter'])){
						echo "checked='checked'";
					}
					echo'>
				from <input type=number name="agefrom" ';
					if(! empty($_POST['agefrom'])){
						echo "value=".$_POST['agefrom'];
					}
					echo'>
				to <input type=number name="ageto" ';
					if(! empty($_POST['ageto'])){
						echo "value=".$_POST['ageto'];
					}
					echo'> years
				</div>			

				<div><label>Sex:</label><br>
				<input type="checkbox" name="Sexfilter" ';
					if(! empty($_POST['Sexfilter'])){
						echo "checked='checked'";
					}
					echo'>            
				<input type="radio" name="Sex" value="Male" ';
					if(! empty($_POST['Sex']) AND $_POST['Sex']=='Male'){
						echo "checked='checked'";
					}
					echo'>male
				<input type="radio" name="Sex" value="Female" ';
					if(! empty($_POST['Sex']) AND $_POST['Sex']=='Female'){
						echo "checked='checked'";
					}
					echo'>female       
				<br></div>	
				<label>Height:</label> <br>
				<div><input type="checkbox" name="height_filter" ';
					if(! empty($_POST['height_filter'])){
						echo "checked='checked'";
					}
					echo'>
				from<input type="number" name="heightfrom" step="0.1" min="100" max="200" ';
					if(! empty($_POST['heightfrom'])){
						echo "value='".$_POST['heightfrom']."'";
					}
					echo'>
				to<input type="number" name="heightto" step="0.1" min="100" max="200" ';
					if(! empty($_POST['heightto'])){
						echo "value='".$_POST['heightto']."'";
					}
					echo'> cm<br></div>


				<div><label>Locality:</label><br>
				<input type="checkbox" name="Localityfilter" ';
					if(! empty($_POST['Localityfilter'])){
						echo "checked='checked'";
					}
					echo'>            
				<input type="text" name="Locality" ';
					if(! empty($_POST['Locality'])){
						echo "value=".$_POST['Locality'];
					}
					echo'><br></div>

				<div><label>Insurance:</label><br>
				<input type="checkbox" name="Insurancefilter" ';
					if(! empty($_POST['Insurancefilter'])){
						echo "checked='checked'";
					}
					echo'>            
				<input type="radio" name="Insurance" value="yes" ';
					if(! empty($_POST['Insurance']) AND $_POST['Insurance']=='yes'){
						echo "checked='checked'";
					}
					echo'>yes
				<input type="radio" name="Insurance" value="no" ';
					if(! empty($_POST['Insurance']) AND $_POST['Insurance']=='no'){
						echo "checked='checked'";
					}
					echo'>no   
				<br><br></div>
				</details></td>
				';
	}

	/*
	## If the department Laboratory is activated in "defaults/DEFAULTS.php", 
	## an icon, a headline and a drop down list with Lab's search parameters are displayed.
	## These are:
	##		- all lab patients with the inclusion of self-paying ones,
	##		- all tests with the inclusion of outsourced ones,
	##		- non lab patients,
	##		- the patient's lab number,
	##		- the payment for the test,
	##		- after that follows a list of all tests and their specific search parameters.
	## If the user has searched once before, the Laboratory's form is prefilled with the search parameters of the previous search. 
	*/
	if(in_array('Laboratory',$DEPARTMENTS)){
		echo'
				<td><details>
				<summary><div><div class="smalltile">Lab<br><i class="fas fa-microscope fa-2x"></i></div></div></summary>

				<div><input type="checkbox" name="alllab" ';
					if(! empty($_POST['alllab'])){
						echo "checked='checked'";
					}
					echo'>
				<label>all lab patients</label> (includes self-paying ones)</div>
				
				<div><input type="checkbox" name="other_facility" ';
					if(! empty($_POST['other_facility'])){
						echo "checked='checked'";
					}
					echo'>
				<label>all tests</label> (includes tests performed by other labs)</div>
				
				<div><input type="checkbox" name="nolab" ';
					if(! empty($_POST['nolab'])){
						echo "checked='checked'";
					}
					echo'>
				<label>non lab patients</label><br><br></div>

				<div><input type="checkbox" name="Lab_IDfilter" ';
					if(! empty($_POST['Lab_IDfilter'])){
						echo "checked='checked'";
					}
					echo'>
				<label>Lab Number:</label>     
				<input type="text" name="Lab_ID" ';
					if(! empty($_POST['Lab_ID'])){
						echo "value=".$_POST['Lab_ID'];
					}
					echo'>  
				</div>

				<div><input type="checkbox" name="payingfilter" ';
					if(! empty($_POST['payingfilter'])){
						echo "checked='checked'";
					}
					echo'>
				<label>paying?:</label>
				<input type="radio" name="paying" value="yes" ';
					if(! empty($_POST['paying']) AND $_POST['paying']=='yes'){
						echo "checked='checked'";
					}
					echo'>yes
				<input type="radio" name="paying" value="no" ';
					if(! empty($_POST['paying']) AND $_POST['paying']=='no'){
						echo "checked='checked'";
					}
					echo'>no   
				<br></div>
				';

		echo"<div class='inlinedetails'>";
		
		/*
		## Initialise variables.
		## $first is used to prevent an untimely closing of the drop down list for all lab parameters.
		## $test_ID is needed to save the test's ID and to determine in the following loop, when a new test starts in the list of parameters.
		*/
		$first=true;
		$test_ID='';
		
		/*
		## Get data from database. 
		## Get a list of all tests and their parameters.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		$query="SELECT * FROM tests,parameters WHERE parameters.test_ID=tests.test_ID ORDER BY parameters.test_ID ASC,frequency ASC,test_name ASC";
		$result=mysqli_query($link,$query);
		
		## This loop will be run once for each output parameter of every test from the database query.
		while($row=mysqli_fetch_object($result)){
			
			## If a new tests starts in the list of parameters, call this if-branch.
			if($row->test_ID!==$test_ID){
				
				## Initialise/update variables with test's ID and name.
				$test_ID=$row->test_ID;
				$test_name=$row->test_name;
				
				## Close the drop down list with the previous test's specific search parameters.
				if($first){
					$first=false;
				}else{
					echo"</details>";
				}

				/*
				## Print a checkbox to activate the search for all clients on which this test was performed, the test's name and 
				## start a drop down list in which the specific parameters for the test can be found.
				*/
				echo"
						</font><br><input type='checkbox' name='testfilter_$test_ID' ";
							if(! empty($_POST["testfilter_$test_ID"])){
								echo "checked='checked'";
							}
							echo">
						<details><summary><label>$test_name</label></summary>
						";
			}

			/*
			## Initialise variables with the parameter's general data.
			## $type stores the parameter's input method (single choice, multiple choice, number, short or long text).
			## $parameter_name and $parameter_ID store the parameter's name and ID.
			## $test_outcomes stores the provided options a parameter has.
			## $unit stores the unit of the parameter's result.
			*/
			$type=$row->input_type;
			$parameter_name=$row->parameter_name;
			$parameter_ID=$row->parameter_ID;
			$test_outcomes=$row->test_outcomes;
			$unit=$row->units;

			## Print the parameter's name.
			echo"</text><br>$parameter_name<text style='color:grey'>";
			if(! empty($parameter_name)){
				echo " - ";
			}

			## Call this if-branch, if the input type of the parameter is number.
			if($type=='number'){
			
				/*
				## Analyse the possible test outcomes to get the range and the number of decimal places for the parameter.
				## This information are used to validate the user's search entry for the number range.
				*/
				$range_arr=explode('-',$test_outcomes);
				$min=$range_arr[0];
				$max=$range_arr[1];
				$step=Lab::getStep($min);
				
				## Print input fields for a value range in which the test parameter should be for the search.
				echo"
						from <input type='number' name='from_$parameter_ID' min='$min' max='$max' step='$step' ";
							if(! empty($_POST["from_$parameter_ID"])){
								echo "value=".$_POST["from_$parameter_ID"];
							}
							echo"> 
						to <input type='number' name='to_$parameter_ID' min='$min' max='$max' step='$step' ";
							if(! empty($_POST["to_$parameter_ID"])){
								echo "value=".$_POST["to_$parameter_ID"];
							}
							echo"> $unit
						";
			}
			
			## Call this if-branch, if the parameter has single or multiple choice for its options.
			else if($type=='checkbox' OR $type=='radio' OR $type=='select'){
				
				## Create a list of all possible outcomes from $test_outcomes and print one checkbox for each of them.
				$checkbox_arr=explode(',',$test_outcomes);
				$j=0;
				foreach($checkbox_arr AS $checkbox){
					$j++;
					echo"
						<input type='checkbox' name='checkbox_$parameter_ID($j)' value='$checkbox' ";
							if(! empty($_POST["checkbox_$parameter_ID($j)"])){
								echo "checked='checked'";
							}
							echo"> $checkbox
						";
				}
			}
			
			## Call this if-branch, if the parameter is saved in form of a short or long text.
			else if($type=='textarea' OR $type=='text'){
				
				## Print an input field for a key word search within the result of the test parameter.
				echo"
						find key word: <input type='text' name='text_$parameter_ID' ";
							if(! empty($_POST["text_$parameter_ID"])){
								echo "value=".$_POST["text_$parameter_ID"];
							}
							echo">
						";
			}
		}
		
		## Print necessary HTML commands.
		echo'
							</font>
						</div><br>
					</details>
				</td>
				';
	}

	/*
	## If the Surgery/Procedure are activated in "defaults/DEFAULTS.php", 
	## an icon, a headline and a drop down list with Procedure's search parameters are displayed.
	## These are:
	##		- all surgeries/procedures,
	##		- circumcisions (or other procedures),
	##		- the payment for the surgery.
	## If the user has searched once before, the Procedure's form is prefilled with the search parameters of the previous search.
	*/
	if(in_array('Surgery/Procedure',$DEPARTMENTS)){
		echo'			
				<td>
				<details>
				<summary><div><div class="smalltile">Procedures<br><i class="fas fa-syringe fa-2x"></i></i></div></div></summary>

					<div><input type="checkbox" name="allsurgeries" ';
						if(! empty($_POST['allsurgeries'])){
							echo "checked='checked'";
						}
						echo'>
					<label>all surgeries/procedures</label></div>

					<div><input type="checkbox" name="circumcision_filter" ';
						if(! empty($_POST['circumcision_filter'])){
							echo "checked='checked'";
						}
						echo'>
					<input type="radio" name="circumcisions" value="circumcision" ';
						if(! empty($_POST['circumcisions']) AND $_POST['circumcisions']=='circumcision'){
							echo "checked='checked'";
						}
						echo'><label>circumcisions</label>
					<input type="radio" name="circumcisions" value="othersurgeries" ';
						if(! empty($_POST['circumcisions']) AND $_POST['circumcisions']=='othersurgeries'){
							echo "checked='checked'";
						}
						echo'><label>other surgeries/procedures</label></div>
					<div><input type="checkbox" name="payingfilter" ';
						if(! empty($_POST['payingfilter'])){
							echo "checked='checked'";
						}
						echo'>
					<label>paying?:</label>
					<input type="radio" name="paying" value="yes" ';
						if(! empty($_POST['paying']) AND $_POST['paying']=='yes'){
							echo "checked='checked'";
						}
						echo'>yes
					<input type="radio" name="paying" value="no" ';
						if(! empty($_POST['paying']) AND $_POST['paying']=='no'){
							echo "checked='checked'";
						}
						echo'>no
				</details>
				</td>
				';
	}

	/*
	## Info: It is possible to describe drugs to a patient, even if the Dispensary department is deactivated in 
	## "defaults/DEFAULTS.php". That's why in the following the "Dispensary part" of the protocol is printed, if the 
	## Consulting department is activated in "defaults/DEFAULTS.php".
	## In this case, an icon, a headline and a drop down list with Consulting's search parameters concerning prescriptions are printed.
	## These are:
	##		- all drugs,
	##		- no drugs,
	##		- if the Dispensary department is activated in "defaults/DEFAULTS.php",it is possible limit the search to issued drugs (that excludes drugs that were bought outside),
	##		- specific prescribed drugs.
	## After that follows an icon, a headline and a drop down list with Consulting's search parameters concerning diagnoses.
	## If the user has searched once before, the Consulting's form is prefilled with the search parameters of the previous search.
	*/
	if(in_array('Consulting',$DEPARTMENTS)){
		echo'	
				<td><details>
				<summary><div><div class="smalltile">Dispensary<br><i class="fas fa-pills fa-2x"></i></div></div></summary>
				
				<input type="checkbox" name="alldrugs" ';
					if(! empty($_POST['alldrugs'])){
						echo "checked='checked'";
					}
					echo'> <label>any drugs</label><br>
				<input type="checkbox" name="nodrugs" ';
					if(! empty($_POST['nodrugs'])){
						echo "checked='checked'";
					}
					echo'> <label>no drugs</label><br>';
		if(in_array('Dispensary',$DEPARTMENTS)){
			echo'
						<input type="checkbox" name="facilitydrugs" ';
						if(! empty($_POST['facilitydrugs'])){
							echo "checked='checked'";
						}
						echo'> <label>drugs issued by dispensary</label><br><br>
					';
		}
		
				/*
				## Get data from database. 
				## Get a list of all drugs.
				## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
				*/
				$query="SELECT * FROM drugs ORDER BY Drugname ASC";
				$result=mysqli_query($link,$query);
				
				## This loop is run once for each drug from database.
				while($row=mysqli_fetch_object($result)){
				
					## Initialise/update drug' s ID and Name.
					$Drug_ID=$row->Drug_ID;
					$Drugname=$row->Drugname;
					
					## Print the name of each drug and a checkbox in front of it.
					echo"<input type='checkbox' name='drug_$Drug_ID' ";
						if(! empty($_POST["drug_$Drug_ID"])){
							echo "checked='checked'";
						}
						echo"> <label>$Drugname</label><br>";
				}
		echo'</details></td>';
		
		/*	
		## An icon, a headline and a drop down list with Consulting's search parameters concerning diagnoses are printed.
		## These are:
		##		- a button to create the Consulting report,
		##		- (not) consulted, referred, reattending and pregnant patients,
		##		- followed by a list of all diagnoses, where provisional, primary and secondary can be selected as a limitation.
		## If the user has searched once before, the Consulting's form is prefilled with the search parameters of the previous search.
		*/
		echo'
				<td><details>
				<summary><div><div class="smalltile">Consulting<br><i class="fas fa-stethoscope fa-2x"></i></div></div></summary>
					<br><a class="button" href="consulting_report_remark.php?from='.$from.'&to='.$to.'">create report</a><br><br>
					<div><input type="checkbox" name="allconsulting" ';
						if(! empty($_POST['allconsulting'])){
							echo "checked='checked'";
						}
						echo'><label>all consulted patients</label><br>
					<div><input type="checkbox" name="noconsulting" ';
						if(! empty($_POST['noconsulting'])){
							echo "checked='checked'";
						}
						echo'><label>not consulted patients</label><br><br></div>

					<div><input type="checkbox" name="referral" ';
						if(! empty($_POST['referral'])){
							echo "checked='checked'";
						}
						echo'><label>referred</label></div>
					<div><input type="checkbox" name="reattendance" ';
						if(! empty($_POST['reattendance'])){
							echo "checked='checked'";
						}
						echo'><label>reattending</label></div>
					<div><input type="checkbox" name="pregnant" ';
						if(! empty($_POST['pregnant'])){
							echo "checked='checked'";
						}
						echo'><label>pregnant</label></div>
				';

				/*
				## Get data from database. 
				## Get a list of all diseases/diagnoses.
				## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
				*/
				$query="SELECT * FROM diagnoses WHERE Diagnosis_ID!='0' ORDER BY DiagnosisClass,Diagnosis_ID";
				$result=mysqli_query($link,$query);
				
				/*
				## Initialise variables.
				## Variable $first is used to display the style the printing of the first disease/diagnosis group correctly.
				## Variable $lastClass is used to identify, which disease/diagnoses belongs to the next disease/diagnosis group.
				*/
				$lastClass=0;
				$first=true;
				
				## This loop is run once for each disease/diagnosis from database.
				while($row=mysqli_fetch_object($result)){
				
					## Print the headline of a new disease/diagnosis group, if necessary.
					if(($row->DiagnosisClass)!==$lastClass){
						if($first==false){
							echo"</table></details>";
						}else{
							$first=false;
						}
						echo "<details><summary><h4>$row->DiagnosisClass</h4></summary><table>";
						$lastClass=$row->DiagnosisClass;
					}
					
					/*
					## Print for each disease/diagnos a checkbox and its name as well as 
					## a radio button to limit it as provisional, primary or secondary diagnosis.
					*/
					echo"
							<tr class='emptytable'>
								<td style='padding:0px; text-align:left; vertical-align:top'>
									<input type='checkbox' name='disease_$row->Diagnosis_ID' ";
										if(! empty($_POST["disease_$row->Diagnosis_ID"])){
											echo "checked='checked'";
										}
										echo"> 
								</td>
								<td style='padding:0px; text-align:left; vertical-align:top'>
									<b>$row->DiagnosisName</b><br>
									<font color='grey'>(</font>
										<input type='radio' name='radio_$row->Diagnosis_ID' value='1' ";
											if(! empty($_POST["radio_$row->Diagnosis_ID"]) AND $_POST["radio_$row->Diagnosis_ID"]==1){
												echo "checked='checked'";
											}
											echo"><font color='grey'>primary </font>
										<input type='radio' name='radio_$row->Diagnosis_ID' value='2' ";
											if(! empty($_POST["radio_$row->Diagnosis_ID"]) AND $_POST["radio_$row->Diagnosis_ID"]==2){
												echo "checked='checked'";
											}
											echo"><font color='grey'>secondary</font>
										<input type='radio' name='radio_$row->Diagnosis_ID' value='3' ";
											if(! empty($_POST["radio_$row->Diagnosis_ID"]) AND $_POST["radio_$row->Diagnosis_ID"]==3){
												echo "checked='checked'";
											}
											echo"><font color='grey'>provisional)
									</font>
								</td>
							</tr>
							";
				}
		
		## Print some necessary HTML commands.
		echo'
				</table>
				</details>
				<br>
				</details>
				</td>
				';
	}


	/*
	## If the department Maternity is activated in "defaults/DEFAULTS.php", 
	## an icon, a headline and a drop down list with Maternity's search parameter's are displayed.
	## These are ordered in two classes:
	## 		ANC, with the search parameters 
	##		- (non) ANC attendants, 
	##		- new/old registrants, 
	##		- (non) PNC attendants,
	##		- client's parity, number of visit, body height, week/trimester of pregnancy, 
	##			fundal height, estimated delivery date and Tetanus/Malaria prevention medication.
	##		Delivery, with the search parameters
	##		- referral of labour cases,
	##		- twin/single deliveries,
	##		- birth weight of the baby.
	*/
	if(in_array('Maternity',$DEPARTMENTS)){
		echo'	
				<td><details>
				<summary><div><div class="smalltile">Maternity<br><i class="fas fa-venus fa-2x"></i></div></div></summary>

					<details open><summary><h4>ANC</h4></summary>
					<div><input type="checkbox" name="allANC" ';
						if(! empty($_POST['allANC'])){
							echo "checked='checked'";
						}
						echo'>
					<label>all ANC clients</label><br>
					<input type="checkbox" name="noANC" ';
						if(! empty($_POST['noANC'])){
							echo "checked='checked'";
						}
						echo'>
					<label>non ANC clients</label><br></div>

					<div><input type="checkbox" name="allPNC" ';
						if(! empty($_POST['allPNC'])){
							echo "checked='checked'";
						}
						echo'>
					<label>all PNC clients</label><br>
					<input type="checkbox" name="noPNC" ';
						if(! empty($_POST['noPNC'])){
							echo "checked='checked'";
						}
						echo'>
					<label>non PNC clients</label><br></div><br>

					<div><input type="checkbox" name="newANCfilter" ';
						if(! empty($_POST['newANCfilter'])){
							echo "checked='checked'";
						}
						echo'>
					<input type="radio" name="newANC" value="new" ';
						if(! empty($_POST['newANC']) AND $_POST['newANC']=='new'){
							echo "checked='checked'";
						}
						echo'><label>new registrants</label>
					<input type="radio" name="newANC" value="old" ';
						if(! empty($_POST['newANC']) AND $_POST['newANC']=='old'){
							echo "checked='checked'";
						}
						echo'><label>old registrants</label></div>

					<div><input type="checkbox" name="parity_filter" ';
						if(! empty($_POST['parity_filter'])){
							echo "checked='checked'";
						}
						echo'>
					<label>Parity:</label>
					from <input type="number" name="parity_from" ';
						if(! empty($_POST['parity_from'])){
							echo "value='".$_POST['parity_from']."'";
						}
						echo'> to 
					<input type="number" name="parity_to" ';
						if(! empty($_POST['parity_to'])){
							echo "value='".$_POST['parity_to']."'";
						}
						echo'></div>

					<div><input type="checkbox" name="visit_number_filter" ';
						if(! empty($_POST['visit_number_filter'])){
							echo "checked='checked'";
						}
						echo'>
					<label>Number of Visit:</label>
					<input type="number" name="visitnumber" min="1" max="12" ';
						if(! empty($_POST['visitnumber'])){
							echo "value='".$_POST['visitnumber']."'";
						}
						echo'></div>

					<div><input type="checkbox" name="week_filter" ';
						if(! empty($_POST['week_filter'])){
							echo "checked='checked'";
						}
						echo'>
					<label>Week of Pregnancy:</label> 
					<input type="number" name="week" min="1" max="45" ';
						if(! empty($_POST['week'])){
							echo "value='".$_POST['week']."'";
						}
						echo'></div>

					<div><input type="checkbox" name="trimester_filter" ';
						if(! empty($_POST['trimester_filter'])){
							echo "checked='checked'";
						}
						echo'>
					<label>Trimester:</label> 
					<select name="trimester" style="margin:0px">
						<option value=""></option>
						<option value="1" ';
							if(! empty($_POST['trimester']) AND $_POST['trimester']==1){
								echo "selected";
							}
							echo'>first</option>
						<option value="2" ';
							if(! empty($_POST['trimester']) AND $_POST['trimester']==2){
								echo "selected";
							}
							echo'>second</option>
						<option value="3" ';
							if(! empty($_POST['trimester']) AND $_POST['trimester']==3){
								echo "selected";
							}
							echo'>third</option>
					</select></div>

					<div><input type="checkbox" name="EDDfilter" ';
						if(! empty($_POST['EDDfilter'])){
							echo "checked='checked'";
						}
						echo'>
					<label>Estimated Delivery Date:</label> 
					from<input type="date" name="EDDfrom" ';
						if(! empty($_POST['EDDfrom'])){
							echo "value='".$_POST['EDDfrom']."'";
						}
						echo'>
					to<input type="date" name="EDDto" ';
						if(! empty($_POST['EDDto'])){
							echo "value='".$_POST['EDDto']."'";
						}
						echo'></div>

					<div><input type="checkbox" name="fundal_height_filter" ';
						if(! empty($_POST['fundal_height_filter'])){
							echo "checked='checked'";
						}
						echo'>
					<label>Fundal Height:</label> 
					from<input type="number" name="fundal_heightfrom" step="0.1" min="0" max="45">
					to<input type="number" name="fundal_heightto" step="0.1" min="0" max="45"> cm<br></div>

					<div><input type="checkbox" name="SP_filter" ';
						if(! empty($_POST['SP_filter'])){
							echo "checked='checked'";
						}
						echo'>
					<label>Sulfadoxine Pyrimethamine</label>
					<font color="grey">(number of dosis:</font><input type="number" name="SP_number" min="0" max="5" ';
						if(! empty($_POST['SP_number'])){
							echo "value='".$_POST['SP_number']."'";
						}
						echo'><font color="grey">)</font></div>

					<div><input type="checkbox" name="TT_filter" ';
						if(! empty($_POST['TT_filter'])){
							echo "checked='checked'";
						}
						echo'>
					<label>Tetanus Diphteria vaccine</label>
					<font color="grey">(number of dosis:</font><input type="number" name="TT_number" min="0" max="5" ';
						if(! empty($_POST['TT_number'])){
							echo "value='".$_POST['TT_number']."'";
						}
						echo'><font color="grey">)</font></div>
					</details>

					<details><summary><h4>Deliveries</h4></summary>
					<div><input type="checkbox" name="alldeliveries" ';
						if(! empty($_POST['alldeliveries'])){
							echo "checked='checked'";
						}
						echo'><label>all delivieries</label></div><br>

					<div><input type="checkbox" name="babyweight_filter" ';
						if(! empty($_POST['babyweight_filter'])){
							echo "checked='checked'";
						}
						echo'>
					<label>Birth Weight:</label> 
					from<input type="number" name="babyweight_from" step="0.1" min="0" max="7" ';
						if(! empty($_POST['babyweight_from'])){
							echo "value='".$_POST['babyweight_from']."'";
						}
						echo'>
					to<input type="number" name="babyweight_to" step="0.1" min="0" max="7" ';
						if(! empty($_POST['babyweight_to'])){
							echo "value='".$_POST['babyweight_to']."'";
						}
						echo'> kg<br></div>

					<div><input type="checkbox" name="single_filter" ';
						if(! empty($_POST['single_filter'])){
							echo "checked='checked'";
						}
						echo'>
					<input type="radio" name="single" value="single" ';
						if(! empty($_POST['single']) AND $_POST['single']=='single'){
							echo "checked='checked'";
						}
						echo'><b>Single
					<input type="radio" name="single" value="twins" ';
						if(! empty($_POST['single']) AND $_POST['single']=='twins'){
							echo "checked='checked'";
						}
						echo'>Twins</b>
					</div>
					<br>

				</details></td>';
	}

	if(in_array('Nutrition',$DEPARTMENTS)){
		echo'	
				<td>
					<details>
						<summary><div><div class="smalltile">Nutrition<br><i class="fas fa-weight fa-2x"></i></div></div></summary>
						
						<div><input type="checkbox" name="all_nutrition" ';
								if(! empty($_POST['all_nutrition'])){
									echo "checked='checked'";
								}
								echo'> all nutrition management cases
						</div>
						<br>
						<div><input type="checkbox" name="BMI_filter" ';
								if(! empty($_POST['BMI_filter'])){
									echo "checked='checked'";
								}
								echo'> BMI:
								from<input type="number" name="BMI_from" step="0.01" min="0"';
									if(! empty($_POST['BMI_from'])){
										echo "value='".$_POST['BMI_from']."'";
									}
									echo'>
								to<input type="number" name="BMI_to" step="0.01" min="0"';
									if(! empty($_POST['BMI_to'])){
										echo "value='".$_POST['BMI_to']."'";
									}
									echo'> kg/m&sup2<br>
						</div>
						<div><input type="checkbox" name="BMI_classification_filter" ';
								if(! empty($_POST['BMI_classification_filter'])){
									echo "checked='checked'";
								}
								echo'> BMI classification:
							<select name="BMI_classification" style="margin:0px">
								<option name=""'; 
									if(! empty($_POST['BMI_classification']) AND $_POST['BMI_classification']==''){
										echo 'selected';
									}
									echo'></option>
								<option name="severe underweight"'; 
									if(! empty($_POST['BMI_classification']) AND $_POST['BMI_classification']=='severe underweight'){
										echo 'selected';
									}
									echo'>severe underweight</option>
								<option name="underweight"'; 
									if(! empty($_POST['BMI_classification']) AND $_POST['BMI_classification']=='underweight'){
										echo 'selected';
									}
									echo'>underweight</option>
								<option name="normal weight"'; 
									if(! empty($_POST['BMI_classification']) AND $_POST['BMI_classification']=='normal weight'){
										echo 'selected';
									}
									echo'>normal weight</option>
								<option name="overweight"'; 
									if(! empty($_POST['BMI_classification']) AND $_POST['BMI_classification']=='overweight'){
										echo 'selected';
									}
									echo'>overweight</option>
								<option name="obesity"'; 
									if(! empty($_POST['BMI_classification']) AND $_POST['BMI_classification']=='obesity'){
										echo 'selected';
									}
									echo'>obesity</option>
								<option name="severe obesity"'; 
									if(! empty($_POST['BMI_classification']) AND $_POST['BMI_classification']=='severe obesity'){
										echo 'selected';
									}
									echo'>severe obesity</option>
							</select> <br>
							&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp(this is only saved for patients who have been in nutritional care - you can filter others by BMI range)
						</div>
						<div><input type="checkbox" name="MUAC_filter" ';
								if(! empty($_POST['MUAC_filter'])){
									echo "checked='checked'";
								}
								echo'> MUAC:
								from<input type="number" name="MUAC_from" step="0.1" min="0"';
									if(! empty($_POST['MUAC_from'])){
										echo "value='".$_POST['MUAC_from']."'";
									}
									echo'>
								to<input type="number" name="MUAC_to" step="0.1" min="0" ';
									if(! empty($_POST['MUAC_to'])){
										echo "value='".$_POST['MUAC_to']."'";
									}
									echo'> cm<br>
						</div>
						<div><input type="checkbox" name="management_filter" ';
								if(! empty($_POST['management_filter'])){
									echo "checked='checked'";
								}
								echo"> Type of Management:
							<select name='management' style='margin:0px'>
								<option value=''";if(! empty($_POST['management']) AND $_POST['management']==''){echo 'selected';}echo"></option>
								<option value='Diet Management'";if(! empty($_POST['management']) AND $_POST['management']=='Diet Management'){echo 'selected';}echo">Diet Management</option>
								<option value='Physical Management'";if(! empty($_POST['management']) AND $_POST['management']=='Physical Management'){echo 'selected';}echo">Physical Management</option>
								<option value='Pharmaceutical Management'";if(! empty($_POST['management']) AND $_POST['management']=='Pharmaceutical Management'){echo 'selected';}echo">Pharmaceutical Management</option>
								<option value='Therapeutic Management'";if(! empty($_POST['management']) AND $_POST['management']=='Therapeutic Management'){echo 'selected';}echo">Therapeutic Management</option>
							</select>
	
						</div>
					</details>
				</td>";
	}


	## Initialise a variable which is used to determine which columns are activated for the table of patients in which the search results are displayed.
	$columns=array(
		'entered'=>'off',
		'birthdate'=>'off',
		'newold'=>'off',
		'insured'=>'off',
		'NHIS'=>'off',
		'CCC'=>'off',
		'tests'=>'off',
		'surgery'=>'off',
		'drugs'=>'off',
		'primary'=>'off',
		'secondary'=>'off',
		'provisional'=>'off',
		'ANC'=>'off',
		'nutrition'=>'off');

	## Print a headline, an icon and a drop down list for "Settings" where the columns can be (de)activated.
	echo'
		<td><details>
		<summary><div><div class="smalltile" style="background-color:LightGray;border-right:1px solid DarkGray;border-bottom:1px solid DarkGray"><div>Settings<br><i class="fas fa-cog fa-2x"></i></div></div></summary>
	';

	/*
	## Depending on which departments are activated in defaults/DEFAULTS.php, 
	## print a list of columns which can be (de)activated in the table of patients in which the search results are displayed.
	*/
	if(in_array('OPD',$DEPARTMENTS)){
		echo'	
					<input type="checkbox" name="entered_column"';
					if(! empty($_POST['entered_column'])){
						echo'checked="checked"';
						$columns['entered']='on';
					}
					echo'>
					<label>Entered in NHIS Claim It</label><br>
					
					<input type="checkbox" name="birthdate_column"';
					if(! empty($_POST['birthdate_column'])){
						echo'checked="checked"';
						$columns['birthdate']='on';
					}
					echo'>
					<label>Birthdate</label><br>

					<input type="checkbox" name="newold_column"';
					if(! empty($_GET['newold_column']) OR ! empty ($_POST['newold_column'])){
						echo'checked="checked"';
						$columns['newold']='on';
					}
					echo'>
					<label>old/new</label><br>

					<input type="checkbox" name="insured_column"';
					if(! empty($_GET['insured_column']) OR ! empty($_POST['newold_column'])){
						echo'checked="checked"';
						$columns['insured']='on';
					}
					echo'>
					<label>insured?</label><br>

					<input type="checkbox" name="NHIS_column"';
					if(! empty($_POST['NHIS_column'])){
						echo'checked="checked"';
						$columns['NHIS']='on';
					}
					echo'>
					<label>NHIS Number</label><br>

					<input type="checkbox" name="CCC_column"';
					if(! empty($_POST['CCC_column'])){
						echo'checked="checked"';
						$columns['CCC']='on';
					}
					echo'>
					<label>CC Code</label><br>
					';
	}


	if(in_array('Laboratory',$DEPARTMENTS)){
		echo'	
				<input type="checkbox" name="tests_column"';
				if(! empty($_POST['tests_column'])){
					echo'checked="checked"';
					$columns['tests']='on';
				}
				echo'>
				<label>tests & results</label><br>
				';
	}


	if(in_array('Surgery/Procedure',$DEPARTMENTS)){
		echo'			
				<input type="checkbox" name="surgery_column"';
				if(! empty($_POST['surgery_column'])){
					echo'checked="checked"';
					$columns['surgery']='on';
				}
				echo'>
				<label>surgeries</label><br>';
	}

	if(in_array('Consulting',$DEPARTMENTS)){
		echo'	
				<input type="checkbox" name="drugs_column"';
				if(! empty($_POST['drugs_column'])){
					echo'checked="checked"';
					$columns['drugs']='on';
				}
				echo'>
				<label>prescribed drugs</label><br>

				<input type="checkbox" name="primary_column"';
				if(! empty($_POST['primary_column'])){
					echo'checked="checked"';
					$columns['primary']='on';
				}
				echo'>
				<label>primary diagnoses</label><br>

				<input type="checkbox" name="secondary_column"';
				if(! empty($_POST['secondary_column'])){
					echo'checked="checked"';
					$columns['secondary']='on';
				}
				echo'>
				<label>secondary diagnoses</label><br>

				<input type="checkbox" name="provisional_column"';
				if(! empty($_POST['provisional_column'])){
					echo'checked="checked"';
					$columns['provisional']='on';
				}
				echo'>
				<label>provisional diagnoses</label><br>
				';
	}

	if(in_array('Maternity',$DEPARTMENTS)){
		echo'	
				<input type="checkbox" name="ANC_column"';
				if(! empty($_POST['ANC_column'])){
					echo'checked="checked"';
					$columns['ANC']='on';
				}
				echo'>
				<label>ANC</label><br>
				';
	}
	if(in_array('Nutrition',$DEPARTMENTS)){
		echo'	
				<input type="checkbox" name="nutrition_column"';
				if(! empty($_POST['nutrition_column'])){
					echo'checked="checked"';
					$columns['nutrition']='on';
				}
				echo'>
				<label>Nutrition Management</label><br>
				';
	}

	## Print some necessary HTML commands as well as a button to reset the defined search parameters.
	echo'
				</details></td>
			</form>
			<td>
			</td>
			<td>
				<br>
				<a href="patient_protocol.php?from='.$today.'&to='.$today.'&newold_column=on&insured_column=on" style="color:black">
						&#8592 Reset Search
				</a>
			</table>
			</div>
			<div class="normal"><br><br>
			';

	## Print the table head for the table of patient's in which the search results are displayed.
	Patient::shorttablehead($columns);

	/*
	## Initialise variables.
	## $count is used to count the number of search results within the following loop.
	## $payment is used for a calculation of incomes (if the payment parameter is selected in lab or procedure),
	## $previous saves the date of the previous patient visit entry.
	## This is necessary for only printing the date when a new day in the list "begins".
	*/
	$count=0;
	$payment=0;
	$previous='';

	/*
	## Get data from database. 
	## Get all patients' and their visits' data on which the search parameters apply.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
	*/
	$query="SELECT * FROM protocol,patient,visit$tables WHERE patient.patient_ID=visit.patient_ID AND protocol.visit_ID=visit.visit_ID $IDs $searchpara GROUP BY visit.visit_ID $grouping $having ORDER BY visit.checkin_time ASC ";
	$result = mysqli_query($link,$query);

	## This loop will be run once for each of the output patient visits from the search in the database.
	while($row = mysqli_fetch_object($result)){
		
		## Initialise objects of patient and its visit, by using the correspondent IDs.
		$patient = new Patient($row->patient_ID);
		$protocol = new Protocol($row->protocol_ID);

		$visit_ID=$row->visit_ID;
		$visit = new Visit($visit_ID);
		
		## Print the table row for the patient (visit).
		$patient->shorttablerow($row->visit_ID,$previous,$columns);
		
		## If the user wants to inquire the amount of incomes (either from surgery or lab), add the charge of the patient to $payment.
		if(isset($paying) AND $paying=='yes'){
			$payment=$payment+$row->charge;
		}
		
		## If the patient visited ANC (or delivered), call this if-branch.
		$ANC_ID=ANC::check_ANC($visit_ID);
		$delivery=Delivery::check_delivery($visit_ID);
		if($ANC_ID OR $delivery){
			
			## Inquire the ID of the correspoding pregnancy to client's visit.
			if($delivery){
				$maternity_ID=$delivery;
			}else{
				$maternity_ID=(new ANC($ANC_ID))->getmaternity_ID();
			}
			
			## Print a link to the pregnancy overview of the client at the end of the table row.
			echo"<td style=border:none>
					&#8594;<a href='complete_pregnancy.php?maternity_ID=$maternity_ID'>pregnancy overview</a>
					</td>";
		}

		## If the patient was referred to another facility, print a notice at the end of the table row to which facility.
		if (Referral::checkReferral($row->visit_ID)){
			$referral=new Referral(Referral::checkReferral($row->visit_ID));
			echo"
			<td style=border:none>
			&#8594;referred to ".$referral->getDestination()."
			</td>";
		}

		## If the client came for Postnatal Care, print a notice at the end of the table row.
		if ($protocol->getPNC()==1){
			echo"
			<td style=border:none>
			&#8594;came for PNC
			</td>";
		}
		echo"</tr>";

		## Update $previous for the next run of the loop.
		$previous=date("d/m/y",strtotime($visit->getCheckin_time()));
		
		## Increase counted patients by one.
		$count++;
	}
	echo"</table>";

	/*
	## If the user wants to inquire the sum of income (either from lab or from surgery), 
	## add the sum of the patient's charges to $parameter, to display it to the user later.
	*/
	if(isset($paying) AND $paying=='yes'){
		$parameter.="- were charged ";
		if($payment!=0){
			$parameter.="(Total Sum: $payment GhC)";
		}
		$parameter.="<br>";
	}

	/*
	## Print the number of patients, the time frame and the list of search parameters buffered in $parameter
	##  in the upper right corner of the screen.
	*/
	echo"
	</div>
		<div class='tableright'>";
			echo"<h3>There have been </h3><h2>$count</h2><h3> patients $timeframe</h3>";
			if(! empty($parameter)){
				echo"<h4>who:</h4><br>$parameter";
			}
		echo"</div>";

	## Contains HTML/CSS structure to style the page in the browser.
	include("HTMLParts/HTML_BOTTOM.php");

?>
