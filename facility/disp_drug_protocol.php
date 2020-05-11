<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");
	
	## Initialise object of Drugs, using the Drug_ID, by which the page is called.
	$Drug_ID=$_GET['Drug_ID'];
	$Drug= new Drugs($Drug_ID);

	## Initialise variable with drug's name.
	$Drugname=$Drug->getDrugname();

	## This if-branch is called, if the user added an entry to the table.
	if (! empty($_GET['submit'])){
		
		## $Counts and $Dispdate describe the particulars of the new entry (Stock at Hand and Date)
		$Counts=$_GET['counts'];
		$Dispdate=$_GET['Dispdate'];
		
		/*
		## This if-branch is called, if the Stock was set 0 and the entered date is today.
		## ($today is defined in HTML_HEAD, which is included at the beginning of the page)
		## It checks, if the drug is available in the store.
		## If it is, the function Push::new_Drug_Notification() is used to store a notification in the database, 
		## which will inform the Dispensary and Store to issue the drug.
		*/
		if($Counts=='0' AND $Dispdate==$today){
			$amount=Store_Drugs::getAmount($Drug_ID,time());
			if ($amount>0){
				Push::new_Drug_Notification($Drug_ID);
			}
		}
		
		## Add the new entry to the database. (The time of the entry is automatically set to "now")
		$Disp_Drugs=Disp_Drugs::new_Disp_Drugs($Drug_ID,'0','0',$Counts,'','0');
				
		## If the entered date is not today, set the date in the database.
		if($Dispdate!==$today){
			$Dispdate=strtotime($Dispdate);
			$Dispdate=date("Y-m-d\TH:i:s",$Dispdate);
			$Disp_Drugs->setCountDate($Dispdate);
		}
		
		## Inform the user about a successful completion of the entry.
		$message="A current amount of $Counts $Drugname(s) has been entered";
		Settings::messagebox($message);
	}

	## Display headline and table head.
	echo "<h1>$Drugname</h2>
		<table>
			<th style=border-left:none>
				Date
			</th>
			<th>
				Particulars
			</th>
			<th>
				Received
			</th>
			<th>
				Issued
			</th>
			<th>
				Amount on Hand
			</th>
			";

	/*
	## Initialise variables:
	## $previous, which will be used to determine, when a table row has a different date than the previous one.
	## $count is needed to determine decreases and increases in the available amount of drugs displayed in the table.
	*/
	$previous='';
	$counts=0;

	/*
	## Get data from database.
	## Get a list of all entries in the record for this Drug. 
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/
	$query="SELECT * FROM disp_drugs WHERE Drug_ID like $Drug_ID AND Counts not like '' ORDER BY CountDate ASC";
	$result=mysqli_query($link,$query);

	## The following loop will be run once for each of the output drug record entries from the database query.
	while($row=mysqli_fetch_object($result)){
		
		## Initialise variable with the date of the entry.
		$Drugdate=date("d/m/y",strtotime($row->CountDate));
		
		## Colour the table: In dark turquoise if the entry is dealing with a receipt from store and in white if the drug is nill in dispensary, otherwise in light turquoise.
		if (($row->Store_Drugs_ID)!=='0'){
			echo "<tr class='receivedtable'>";
		}else if (($row->Counts)>0){
			echo "<tr class='adjustmenttable'>";
		}else{
			echo "<tr>";
		}
		
		## Display the table row.
		echo"
				<td style=border-left:none>
					";
					/*
					## If the date differs from the previous one, display it in the first column.
					## Update $previous for the next run of the loop.
					*/
					if ($Drugdate!==$previous){
						echo $Drugdate;
						$previous=$Drugdate;
					}
					
					echo"
				</td>
				<td>
					";
					## If the table row shows an entry, that was made directly in this table or the dispensary overview (as stock taking), indicate that in the second column.
					if(($row->Prescribed)=='0' AND ($row->Store_Drugs_ID)=='0'){
						echo 'Stock taking - Adjustments';
					}
					
					## If the table row shows a receipt from store, indicate that in the second column
					else if(($row->Store_Drugs_ID)!=='0'){
						echo 'Received from Store';
					}
		
					/*
					## If the table row shows a prescription to a patient, get the patient's name from the database and
					## display the name and a link to his results in the second column.
					*/
					else{
						$query2="SELECT * FROM protocol,patient,disp_drugs WHERE protocol.patient_ID=patient.patient_ID AND protocol.protocol_ID=disp_drugs.protocol_ID AND Disp_Drugs_ID='".$row->Disp_Drugs_ID."'";
						$result2=mysqli_query($link,$query2);
						$object2=mysqli_fetch_object($result2);
						$patient_name=$object2->Name;
						$protocol_ID=$object2->protocol_ID;
						$patient_ID=$object2->patient_ID;
	
						echo "<a href='patient_visit.php?show=on&protocol_ID=$protocol_ID&patient_ID=$patient_ID'>$patient_name</a>";
					}
					echo"
				</td>
				<td>
					";
					/*
					## If the table row shows a receipt from the store, get the received amount from the database.
					## Display that amount in the third column and leave the fourth empty.
					*/
					if(($row->Store_Drugs_ID)!=='0'){
						$query2="SELECT Issued FROM store_drugs WHERE Store_Drugs_ID like $row->Store_Drugs_ID";
						$result2=mysqli_query($link,$query2);
						while($row2=mysqli_fetch_object($result2)){
							$Received=$row2->Issued;
						}
						$counts=$counts+$Received;
						echo "$Received
							</td>
							<td>
							";
					}
		
					/*
					## If the table row shows a prescription to a patient, get the issued amount from the database.
					## Display that amount in the fourth column and leave the third empty.
					*/
					else if (($row->Prescribed)!=='0'){
						echo"
							</td>
							<td>
								$row->Prescribed
							";
					}
					## This if-branch is called, if the table row shows a stock taking and the amount changes compared to the previous one.
					else if (($row->Counts)!==$counts){
						/*
						## If the table row shows a stock taking and the available amount increased, calculate that amount.
						## Display that amount in the third column and leave the fourth empty.
						*/
						if (($row->Counts)>$counts){
							$adjustments=($row->Counts)-$counts;
							echo"
									$adjustments
								</td>
								<td>
								";
						}
						/*
						## If the table row shows a stock taking and the available amount decreased, calculate that amount.
						## Display that amount in the fourth column and leave the third empty.
						*/						
						else{
							$adjustments=$counts-($row->Counts);
							echo"
								</td>
								<td>
									$adjustments
								";
						}
					}
					## If the table row shows a stock taking, but there is no difference to the previous entry, print the third and fourth column as empty.
					else{
						echo"
							</td>
							<td>
							";
					}
					echo"
				</td>
				<td>
					";
					## Update $counts to the available amount in that table row.
					$counts=$row->Counts;
					
					## Depending on $counts being negative, 0 or positive, display "get from store", "Nill" or the available amount.
					if ($counts<0){
						echo "get from store";
					}else if ($counts==0){
						echo "Nill";
					}else{
						echo $counts;
					}
					echo"
				</td>
			</tr>
			";
	}
	## Display a form for adding an entry at the end of the table.
	echo"
			<form action='disp_drug_protocol.php' method='get'>
				<tr class='lasttable'>
					<td>
						<input type='date' name='Dispdate' value='$today'>
					</td>
					<td>
					</td>
					<td>
						Stock
					</td>
					<td>
						taking:
					</td>
					<td>
						<input type='number' name='counts' min='0'>
						<input type='hidden' name='Drug_ID' value='$Drug_ID'>
					</td>
				</tr>
				<tr class='emptytable'>
					<td>
					</td>
					<td>
					</td>
					<td>
					</td>
					<td>
					</td>
					<td>
						<input type='hidden' name='token' value='$uniqueID'>
						<input type='submit' name='submit' value='save'>
					</td>
			</form>
		</table>";

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>