<?php
	## Contains global variables and functions which are needed within this page.
	include("setup.php");
	
	/*
	## Increasing maximum execution time of page from 30 seconds to five minutes,
	## because the calculations for the OPD report usually require more than 30 seconds.
	*/
	ini_set('max_execution_time', 600); 

	/*
	## Variables $from and $to are initialised which are used to set the time frame for the OPD report.
	## They are retrieved from the url with which the page is called.
	*/
	$from=$_GET['from'];
	$to=$_GET['to'];

	/*
	## Initialising variables and setting as arrays.
	## Each set of data (patient and the time of its visit) will later be checked for its insurance status, sex and age using $insurance_array, $sex_array and $age_array.
	## $new_old_array is used to categorise the patient into new (if they have not been in the facility this year) or old.
	## $age_array is used to define the lower limit of each age group, the lower limit of the next age group is used as the upper limiter.
	## $all will be used as a multi-dimensional, overall array in which the data are counted.
	*/
	$insurance_array=array('insured','non insured');
	$new_old_array=array('new','old');
	$sex_array=array('male','female');
	$age_array=array(0,(29/365.25),1,5,10,15,18,20,35,50,60,70,110);
	$all=array();

	## The following nested loops are used to initialise multi-dimensional array variables for each field of the table and setting them as 0.
	foreach($insurance_array AS $insurance){
		$all[$insurance]=array();
		foreach($new_old_array AS $new_old){
			$all[$insurance][$new_old]=array();
			foreach($sex_array AS $sex){
				$all[$insurance][$new_old][$sex]=array();
				for($age=1;$age<count($age_array);$age++){
					$all[$insurance][$new_old][$sex][$age_array[$age]]=0;
					$all[$insurance][$new_old][$sex]['total']=0;
					$all['total']['total'][$sex][$age_array[$age]]=0;
					$all['total']['total'][$sex]['total']=0;
				}
			}
		}
	}

	/*
	## Get data from database. 
	## Get all patients' and their visits' data within the timeframe defined by $from and $to.
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
	*/
	$query="SELECT * FROM patient,protocol WHERE patient.patient_ID=protocol.patient_ID AND protocol.VisitDate BETWEEN '$from' AND '$to 23:59:59' AND onlylab=0 ";
	$result=mysqli_query($link,$query);
	/*
	## The following loop will be run once for each of the output patient visits from the database query.
	## This loop and the nested loops within it are used to categorise each patient by sex and age group, insurance status and new or old.
	## Afterwards the amount of patients is counted for each category.
	*/
	while($row=mysqli_fetch_object($result)){
		
		## Initialise new objects of protocol and patient by their protocol-ID and patient-ID.
		$patient=new Patient($row->patient_ID);
		$protocol=new Protocol($row->protocol_ID);

		## These if-branches are used to set a variable $insurance with the insurance status.
		if(! empty($patient->getNHIS()) AND $protocol->getExpired()==0){
			$insurance='insured';
		}else{
			$insurance='non insured';
		}

		## These if-branches are used to set a variable $new_old with the "new/old status".
		if($protocol->getnew_p()==1){
			$new_old='new';
		}else{
			$new_old='old';
		}

		/*
		## These nested loops are used to determine to which age group and sex the patient belongs.
		## The inner loop increases the amount of the patients in the correspondent categories by one.
		*/
		foreach($sex_array AS $sex){
			if($patient->getSex()==$sex){
				for($age=1;$age<count($age_array);$age++){
					$agefrom=date("Y-m-d",(strtotime($protocol->getVisitDate())-(365.25*24*3600*$age_array[($age-1)])));
					$ageto=date("Y-m-d",(strtotime($protocol->getVisitDate())-(365.25*24*3600*$age_array[$age])));

					if($patient->getBirthdate()<$agefrom AND $patient->getBirthdate()>=$ageto){
						$all[$insurance][$new_old][$sex][$age_array[$age]]++;
						$all[$insurance][$new_old][$sex]['total']++;
						$all['total']['total'][$sex][$age_array[$age]]++;
						$all['total']['total'][$sex]['total']++;
					}
				}
			}
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
						}
				';
	$html=Settings::pdf_header($style,'').'
			<h1>OPD Report from '.$fromdisplay.' to '.$todisplay.'</h1>
			<table>
				<tr>
					<th style="border:none"></th>
					<th colspan="4">insured</th>
					<th colspan="4">non insured</th>
					<th rowspan="2" colspan="2">total</th>
				</tr>
				<tr>
					<th style="border:none"></th>
			';
	for($i=1;$i<=2;$i++){
		$html.='<th colspan="2">new</th>
				<th colspan="2">old</th>';
	}
	$html.='
				</tr>
				<tr>
					<th>age</th>
				';
	for($i=1;$i<=5;$i++){
		$html.='<th>male</th>
				<th>female</th>';
	}
	$html.='</tr>';

	/*
	## Add the data in table.
	## Each table row begins with the specific age group
	## The table is stored in $html.
	##
	*/
	for($age=1;$age<count($age_array);$age++){
		$html.='<tr>';
		if($age_array[$age]==(29/365.25)){
			$html.='<th>0-28 days</th>';
		}else if($age_array[($age-1)]==(29/365.25)){
			$html.='<th>1-11 months</th>';
		}else if($age_array[($age-1)]==70){
			$html.='<th>70+</th>';
		}else{
			$html.= '<th>'.$age_array[($age-1)].'-'.($age_array[$age]-1).'</th>';
		}
		foreach($insurance_array AS $insurance){
			foreach($new_old_array AS $new_old){
				foreach($sex_array AS $sex){
					$html.='<td>'.$all[$insurance][$new_old][$sex][$age_array[$age]].'</td>';
				}
			}
		}
		foreach($sex_array AS $sex){
			$html.='<td>'.$all['total']['total'][$sex][$age_array[$age]].'</td>';
		}
		$html.='</tr>';
	}
	$html.='<tr><th>total</th>';
	foreach($insurance_array AS $insurance){
		foreach($new_old_array AS $new_old){
			foreach($sex_array AS $sex){
				$html.='<td>'.$all[$insurance][$new_old][$sex]['total'].'</td>';
			}
		}
	}
	foreach($sex_array AS $sex){
		$html.='<td>'.$all['total']['total'][$sex]['total'].'</td>';
	}
	$html.= '
					</tr>
				</table>
			</body>
			';

	## Initialise variables for the name of the pdf and it's page format.
	$pdfName='OPD_report('.$fromdisplay.'-'.$todisplay.').pdf';
	$size='A4';

	## This function is creating the pdf file, using the data stored in $html as content.
	Settings::pdf_execute($pdfName,$size,$html);

?>