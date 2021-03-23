<?php

	class Disp_Drugs{

		/*		
		## A Dispensary drug object represents a new entry in Dispensary register. With each entry only one certain drug is handled. 
		## In the following all parameters of such an entry are defined.
		*/
		private $Disp_Drugs_ID;		## ID of the register entry in Dispensary.
		private $Drug_ID;		## ID of that drug.
		private $Store_Drugs_ID;	## ID of the corresponding store register entry.
		private $Prescribed;		## Amount of prescribed drug. 
		private $CountDate;		## Date of the Dispensary register entry. 
		private $Counts;		## The amount of a certain drug in Dispensary corresponding to a certain Dispensary register entry.
		private $dosage_recommendation; ## Some particular dosage recommendations of a certain Dispensary register entry.
		private $prescription_protocol_ID;		## ID of the protocol entry linked to the prescription.
		private $given_protocol_ID;				## ID of the protocol entry linked to the handing out of the drug.
		
		
		/*
		## This function is called, if a new drug object is needed for futher actions.
		## Saves the information of that drug from database (identified by drug ID) in that new drug object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Disp_Drugs($Disp_Drugs_ID){
			global $link;
			$query = "SELECT * FROM disp_drugs WHERE Disp_Drugs_ID = $Disp_Drugs_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->Drug_ID = $row->Drug_ID;		
				$this->Store_Drugs_ID = $row->Store_Drugs_ID;
				$this->Prescribed = $row->Prescribed;
				$this->CountDate = $row->CountDate;
				$this->Counts = $row->Counts;	
				$this->dosage_recommendation =$row->dosage_recommendation;
				$this->prescription_protocol_ID = $row->prescription_protocol_ID;	
				$this->given_protocol_ID = $row->given_protocol_ID;
			}
			$this->Disp_Drugs_ID = $Disp_Drugs_ID;
		}
		

		/*
		## Constructor of new drug.
		## Is called, if a new drug database entry is created.
		## The data of new drug is saved in database for all its parameters.
		## Save this data also in a new created drug object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Disp_Drugs($Drug_ID,$Store_Drugs_ID,$Prescribed,$Counts,$dosage_recommendation,$prescription_protocol_ID){
			global $link;
			$query = "INSERT INTO `disp_drugs`(`Drug_ID`,`Store_Drugs_ID`,`Prescribed`,`Counts`,`dosage_recommendation`,`prescription_protocol_ID`) VALUES ('$Drug_ID','$Store_Drugs_ID','$Prescribed','$Counts','$dosage_recommendation','$prescription_protocol_ID')";
			mysqli_query($link,$query);
			
			$Disp_Drugs_ID = mysqli_insert_id($link);
			$instance = new self($Disp_Drugs_ID);
			return $instance;
		}

		/*
		## Getter function.
		## Returns the ID of that Dispensary register entry, on which the function is called.
		*/
		public function getDisp_Drugs_ID(){
			return $this->Disp_Drugs_ID;
		}

		/*
		## Getter function.
		## Returns the ID of that drug, which is corresponding to that Dispensary register entry, on which the function is called.
		*/
		public function getDrug_ID(){
			return $this->Drug_ID;
		}

		/*
		## Getter function.
		## Returns the ID of that store register entry, which is corresponding on that Dispensary register entry, on which the function is called.
		*/
		public function getStore_Drugs_ID(){
			return $this->Store_Drugs_ID;
		}

		/*
		## Getter function.
		## Returns the amount of a patient's prescribed drug of that Dispensary register entry, on which the function is called.
		*/
		public function getPrescribed(){
			return $this->Prescribed;
		}

		/*
		## Getter function.
		## Returns the date of that Dispensary register entry, on which the function is called.
		*/
		public function getCountDate(){
			return $this->CountDate;
		}

		/*
		## Getter function.
		## Returns the amount of a drug in Dispensary corresponding to that Dispensary register entry, on which the function is called.
		*/		
		public function getCounts(){
			return $this->Counts;
		}

		/*
		## Getter function.
		## Returns dosage recommendation of that Dispensary register entry, on which the function is called.
		*/
		public function getdosage_recommendation(){
			return $this->dosage_recommendation;
		}
		
		/*
		## Getter function.
		## Returns protocol ID of the patient's visit which corresponds to the drug register entry, on which the function is called.
		*/
		public function getprescription_protocol_ID(){
			return $this->prescription_protocol_ID;
		}
		
		/*
		## Getter function.
		## Returns protocol ID of the patient's visit which corresponds to the drug register entry, on which the function is called.
		*/
		public function getgiven_protocol_ID(){
			return $this->given_protocol_ID;
		}
		
		/*
		## Setter function.
		## Updates the ID of that Dispensary register entry, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/	
		public function setStore_Drugs_ID($var){
			global $link;
			$query = "UPDATE disp_drugs SET Store_Drugs_ID='$var' WHERE Disp_Drugs_ID = $this->Disp_Drugs_ID";
			mysqli_query($link,$query);
			return $this->Store_Drugs_ID = $var;
		}

		/*
		## Setter function.
		## Updates the amount of patient's prescribed drug of that Dispensary register entry, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setPrescribed($var){
			global $link;
			$query = "UPDATE disp_drugs SET Prescribed='$var' WHERE Disp_Drugs_ID = $this->Disp_Drugs_ID";
			mysqli_query($link,$query);
			return $this->Prescribed = $var;
		}

		/*
		## Setter function.
		## Updates the date of that Dispensary register entry, on which the function is called, in database.
		## Returns the updated information. 
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setCountDate($var){
			global $link;
			$query = "UPDATE disp_drugs SET CountDate='$var' WHERE Disp_Drugs_ID = $this->Disp_Drugs_ID";
			mysqli_query($link,$query);
			return $this->CountDate = $var;
		}

		/*
		## Setter function.
		## Updates the the amount of a drug in Dispensary corresponding to that Dispensary register entry, on which the function is called, in database.
		## Returns the updated information. 
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setCounts($var){
			global $link;
			$query = "UPDATE disp_drugs SET Counts='$var' WHERE Disp_Drugs_ID = $this->Disp_Drugs_ID";
			mysqli_query($link,$query);
			return $this->Counts = $var;
		}

		/*
		## Setter function.
		## Updates dosage recommendation of that Dispensary register entry, on which the function is called, in database.
		## Returns the updated information. 
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setdosage_recommendation($var){
			global $link;
			$query = "UPDATE disp_drugs SET dosage_recommendation='$var' WHERE Disp_Drugs_ID = $this->Disp_Drugs_ID";
			mysqli_query($link,$query);
			return $this->dosage_recommendation = $var;
		}

		/*
		## Setter function.
		## Updates dosage recommendation of that Dispensary register entry, on which the function is called, in database.
		## Returns the updated information. 
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setgiven_protocol_ID($var){
			global $link;
			$query = "UPDATE disp_drugs SET given_protocol_ID='$var' WHERE Disp_Drugs_ID = $this->Disp_Drugs_ID";
			mysqli_query($link,$query);
			return $this->given_protocol_ID = $var;
		}

		/*
		## Display the Dispensary drugs in a table with columns: drugname, the amount of dispensary stock, stock taking and store stock.
		## The variable $result, which is sent when calling this function, contains a list of all drugs and their parameters, which are to be shown.
		*/
		public function drugs_in_disp($result){

			## This loop is run once for each drug, which is sent in $result when calling the function 'drugs_in_disp'.
			while($row=mysqli_fetch_object($result)){

				/*
				## Initialising some variables, which are needed within this function.
				## Variable $amount contains the amount of the current drug in store stock.
				## Variable $lastCounts contains the amount of the current drug in dispensary stock.				
				*/
				$Drugname=$row->Drugname;	
				$Drug_ID=$row->Drug_ID;
				$lastCounts=Disp_Drugs::getLastCounts($row->Drug_ID,time());
				$amount=Store_Drugs::getAmount($row->Drug_ID,time());

				/*
				## The table rows are printed.
				## The store stock is shown as 'Nill', if no more of that drug is available.
				## The dispensary stock is handled the same way. Furthermore a notice 'get from store' is printed,
				## if the amount is smaller than 0 (which means that the drug has been prescribed to a patient even though it was nill in dispensary (but not store)).				
				*/
				echo"
					<tr>
						<td style=border-left:none>
							<a href='disp_drug_protocol.php?Drug_ID=$Drug_ID'>$Drugname</a>
						</td>
						<td>
							";
							if ($lastCounts<0){
								echo "get from store";

							}else if ($lastCounts==0){
								echo "Nill";
							}else{
								echo $lastCounts;
							}
							echo"
						</td>
						<td>
								<input type='number' name='count_$Drug_ID'";
								if(! empty($_GET["count_$Drug_ID"]) AND ! empty($_GET['search'])){
									echo "value=".$_GET["count_$Drug_ID"];
								}
								echo" min='0'>
						</td>
						<td>
							";
							if($amount==0){
								echo "Nill";
							}else{
								echo $amount;
							}
							echo"
						</td>
					</tr>
						";
			}
		}

		/*
		## This function displays a table with all drugs to prescribe certain drugs to a certain patient.
		## This table contains the following columns: a checkbox to prescribe a drug, the drug's amount, which is to be prescribed,
		## the drug name, dosage recommendations and the available drug's amount as sum of both departments - Dispensary and store. 
		## The variable $result, which is sent when calling this function, contains a list of all drugs and their parameters.
		*/
		public function patient_drugs($result){

			## This loop is run once for each drug, which is sent in $result when calling the function 'patient_drugs'.
			while($row=mysqli_fetch_object($result)){

				/*
				## Initialising some variables, which are needed within this function.
				## Variable $unit contains the unit of issue of the current drug.
				## Variable $amount contains the amount of the current drug in store stock.
				## Variable $lastCounts contains the amount of the current drug in dispensary stock.	
				## Variable $available contains the over all available amount of each drug in the whole facility.			
				*/
				$drug=new drugs($row->Drug_ID);
				$drugname=$drug->getDrugname();
				$drugID=$row->Drug_ID;
				$unit=$drug->getUnit_of_Issue();
				$lastCounts=Disp_Drugs::getLastCounts($row->Drug_ID,time());
				$amount=Store_Drugs::getAmount($row->Drug_ID,time());
				$available=$lastCounts+$amount;

				/*
				## The table rows are printed.
				## The checkbox is checked, if a drug was already selected for patient's prescription.
				## The dosage recommendation is handled the same way. 
				## The store stock is shown as 'Nill', if no more of that drug is available.
				## The dispensary stock is handled the same way.				
				*/
				echo "
					<tr>
						<td style=border-left:none>
							<input type='checkbox' name='filter_$drugID'";
								if (! empty($_GET["filter_$drugID"])){
									echo"checked='checked'";
								}
							echo">
						</td>
							<td style=text-align:left>
							<select name='pattern_$drugID' oninput='PrefillAmounts($drugID, \"$unit\")'>
								<option value=''></option>
								<option value='od'";
									if(! empty($_GET["pattern_$drugID"])){
										if($_GET["pattern_$drugID"]=='od'){
											echo"selected='selected'";
										}
									}
								echo"							
								>od</option>
								<option value='bd'";
									if(! empty($_GET["pattern_$drugID"])){
										if($_GET["pattern_$drugID"]=='bd'){
											echo"selected='selected'";
										}
									}
								echo"							
								>bd</option>
								<option value='tds'";
									if(! empty($_GET["pattern_$drugID"])){
										if($_GET["pattern_$drugID"]=='tds'){
											echo"selected='selected'";
										}
									}
								echo"							
								>tds</option>
								<option value='qid'";
									if(! empty($_GET["pattern_$drugID"])){
										if($_GET["pattern_$drugID"]=='qid'){
											echo"selected='selected'";
										}
									}
								echo"							
								>qid</option>
								<option value='prn'";
									if(! empty($_GET["pattern_$drugID"])){
										if($_GET["pattern_$drugID"]=='prn'){
											echo"selected='selected'";
										}
									}
								echo"							
								>prn</option>
								<option value='stat'";
									if(! empty($_GET["pattern_$drugID"])){
										if($_GET["pattern_$drugID"]=='stat'){
											echo"selected='selected'";
										}
									}
								echo"							
								>stat</option>
								<option value='noct'";
									if(! empty($_GET["pattern_$drugID"])){
										if($_GET["pattern_$drugID"]=='noct'){
											echo"selected='selected'";
										}
									}
								echo"							
								>noct</option>
							</select>
							x
							<input type='number' name='days_$drugID'";
								if(! empty($_GET["days_$drugID"])){
									echo"value='".$_GET["days_$drugID"]."'";
								}
							echo'
							min="0" max="150" step="0.5" oninput="PrefillAmounts('.$drugID.', \''.$unit.'\')"> 
							';
							if($unit=='bottle'){
								echo " x <input type='number' name='mls_$drugID'  ";
								if (! empty($_GET["mls_$drugID"])){
									echo "value='".$_GET["mls_$drugID"]."'";
								}
							echo " step='0.5' min='0.5' max='100'>mls";
							}
							echo"
						</td>
						<td>
							$drugname
						</td>
						<td style=text-align:left>
							<input type='number' name='amount_$drugID' ";
								if (! empty($_GET["amount_$drugID"])){
									echo"value='".$_GET["amount_$drugID"]."'";
								}
							echo " min='0.5' max='150' step='0.5'> $unit(s)
						</td>
						<td>
							$available
						</td>
					</tr>
					";
			}
		}

		/*
		## This function buffers HTML commands to display a list of all prescribed drugs of a certain patient.
		## The drug's name, the amount as well as the unit of issue and the dosage recommendation is shown.
		## The variables, which are sent when calling this function, have the following meaning:
		##		- $protocol_ID links to the entire protocol entry of the patient,
		##		- $function is responsible to display a delete icon behind each prescribed drug, if it is not set in read only modus.
		## The HTML buffer $html is returned.
		*/
		public function display_prescribed_drugs($visit_ID,$function,$circumference){

			/*
			## Initialising some variables, which are needed within this function.
			## Variable $thispage contains the URL of the web page, which shows the summary of prescribed drugs of a certain patient,
			## and is used to forward the user to this site back, after it has deleted an already prescribed drug.
			## Variable $uniqueID is used to prevent performing the same action by refreshing the web page.
			## Both are defined in "variables.php" which is included by "setup.php".
			## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.	
			*/
			global $thispage;
			global $uniqueID;
			global $link;
			$thispage=preg_replace('/&token=(.*)&/',"&token=$uniqueID&",$thispage);
			$html='';

			
			/*
			## Get data from database.
			## Get all the dispensary's drug register entries for the patient.
			*/
			
			if($circumference=='both'){
				$query="SELECT * FROM disp_drugs d, protocol p WHERE p.protocol_ID=d.prescription_protocol_ID AND p.visit_ID=$visit_ID";
			}else if($circumference=='issued'){
				$query="SELECT * FROM disp_drugs d, protocol p WHERE p.protocol_ID=d.given_protocol_ID AND p.visit_ID=$visit_ID";
			}else if($circumference=='prescribed'){
				$query="SELECT * FROM disp_drugs d, protocol p WHERE p.protocol_ID=d.prescription_protocol_ID AND d.given_protocol_ID=0 AND p.visit_ID=$visit_ID";
			}

			$drug_result=mysqli_query($link,$query);
			## This loop is run once for each prescribed drug of the patient.
			while($row=mysqli_fetch_object($drug_result)){

				/*
				## Initialising some more variables, which are needed within this loop.
				## Variable $unit contains the unit of issue of the current drug.
				## Variable $amount contains the amount the current drug.
				## Variable $dosage_recommendation contains dosage recommendations of the current drug.
				## Variable $Counts contains the amount of a drug in Dispensary corresponding to the current Dispensary register entry.		
				*/
				$Disp_Drug=new Disp_Drugs($row->Disp_Drugs_ID);
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
				
				## Call this if-branch, if the user is not calling the funtion from protocol and only wants drugs to be displayed that were issued by the dispensary (not written to buy).
				if((! empty($_POST['facilitydrugs']) AND $Counts!=='') OR empty($_POST['facilitydrugs'])){
					/*
					## Information about each patient's prescribed drug is buffered in $html, to print it later.
					## Depending on the drug's availability in Dispensary corresponding to the current Dispensary register entry,
					## the prescribed amount is buffered in a different way:
					##		- if the drug's amount in Dispensary is empty, buffer a notice to get from store,
					##		- if the drug is not available at all in whole facility, buffer a notice, needs to buy the drug by itself.
					##		- if the drug is available in Dispensary, buffer the prescribed amount.
					*/
					if($Counts<0 OR ($function=='delete' AND $amount>$disp_stock AND $amount<$available)){
						$html.= '<div class="tooltip">
									<b>'.$Drugname.': </b>
										<span class="tooltiptext">
											get from store
										</span>
								</div>'.$amount.' '.$unit.'s ';
									if(! empty($dosage_recommendation)){
										$html.='('.$dosage_recommendation.')';
									}
					}else if((empty($Counts) AND $function!=='delete') OR ($function=='delete' AND $amount>$available)){
						$html.= '<div class="tooltip">
									<text style="color:grey"><b>'.$Drugname.': </b></text>
										<span class="tooltiptext">
											write for patient to buy
										</span>
								</div><text style="color:grey">'.$amount.' '.$unit.'s ';
									if(! empty($dosage_recommendation)){
										$html.='('.$dosage_recommendation.')';
									}
								$html.='</text>';
					}else{
						$html.= '<b>'.$Drugname.': </b> '.$amount.' '.$unit.'s ';
									if(! empty($dosage_recommendation)){
										$html.='('.$dosage_recommendation.')';
									}
					}

					/*
					## This if-branch is called, if a patient's prescribed drug is deletable.
					## Add the command to print a delete icon into the HTML buffer.  
					*/
					if($function=='delete'){
						$html.= "<a href='$thispage&delete=$row->Disp_Drugs_ID'><i class='fas fa-times-circle'></i></a>";
					}
					$html.='<br>';
				}
			}
			return $html;
		}

		/*
		## Calculation of the amount of a certain drug in Dispensary at the time of a certain date depending on all Dispensary register entries until that date.
		## Both parameters are identified by the information, which is sent when calling this function ($Drug_ID and $timestamp).
		## Calculated amount is returned.
		*/
		public function getLastCounts($Drug_ID,$timestamp){
			$time=date("Y-m-d H:i:s",$timestamp);
			global $link;
			$query = "SELECT * FROM disp_drugs WHERE Drug_ID like '$Drug_ID' AND Counts not like ''  AND CountDate<='$time' ORDER BY CountDate DESC LIMIT 0,1";
			$result = mysqli_query($link,$query);
			$object = mysqli_fetch_object($result);
			if(! empty($object)){
				$lastCounts= $object->Counts;
			}else{
				$lastCounts=0;
			}
			
			return $lastCounts;
		}
		
		/*
		## This function returns whether a certain patient has received any drugs on a specific visit.
		## The sent parameter $protocol_ID links to the patient's visit's entry.
		## The function retrieves from the database, whether there is any drug protocol entry with this protocol ID
		## and returns "true" if there are any, otherwise "false".
		*/
		public function drugs_prescribed($visit_ID){
			
			global $link;
			
			$query="SELECT * FROM disp_drugs d, protocol p WHERE d.prescription_protocol_ID=p.protocol_ID AND p.visit_ID='$visit_ID'";
			$result=mysqli_query($link,$query);

			if(mysqli_num_rows($result)!==0){
				$prescribed=true;
			}else{
				$prescribed=false;
			}
			
			return $prescribed;
		}

		public function drugs_issued($visit_ID){
			
			global $link;
			
			$query="SELECT * FROM disp_drugs d, protocol p WHERE d.given_protocol_ID=p.protocol_ID AND p.visit_ID='$visit_ID'";
			$result=mysqli_query($link,$query);

			if(mysqli_num_rows($result)!==0){
				$issued=true;
			}else{
				$issued=false;
			}
			
			return $issued;
		}

		/*
		## This function is used to delete a specific dispensary register entry, when adapting the patient's diagnosis.
		## The sent parameter $Disp_Drugs_ID is used to link to the particular entry.
		*/
		public function delete_disp_drugs($Disp_Drugs_ID){
			global $link;
			$query="DELETE FROM disp_drugs WHERE Disp_Drugs_ID=$Disp_Drugs_ID";
			mysqli_query($link,$query);
		}

	}
?>
