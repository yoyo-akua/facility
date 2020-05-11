<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
	include("HTMLParts/HTML_HEAD.php");

	/*
	## Increasing maximum execution time of page from 30 seconds to five minutes,
	## because the calculations for the drug record analysis usually require more than 30 seconds.
	*/
	ini_set('max_execution_time', 600); 

	## Variables $from and $to are initialised which are used to set the time frame for the drug record analysis.
	$from=$_GET['from'];
	$to=$_GET['to'];

	## Print headline, input form for the time frame and table head.
	echo'<h1>Drug Report</h1> 
		 <div class="inputform">
			<form action="drug_report.php" method="get" autocomplete="off">
				<div><label>Timeframe:</label><br>
				from<input type=date name="from" value="'.$from.'">
				to<input type=date name="to" value="'.$to.'"><br><br></div>

				<div><input type="submit" value="Submit"></div>			
		</div>
		<table>
			<tr>
			<th style=border-left:none>
				Drug
			</th>
			<th>
				Amount used
			</th>
			<th>
				number of Stock Out Days
			</th>
			<th>
				average consumption per <br>(Not Stock Out) Day
			</th>
			<th>
				Available (today)
			</th>
			<th>
				Stock Out Prognosis
			</th>
			</tr>
		';

	## Initialising $searchpara, on which the search is based (depending on the user's search).
	if(! empty($_GET['search'])){
		$var=$_GET['search'];
		$searchpara=" WHERE Drugname like '%$var%'";
	}else{
		$searchpara='';
	}

	## Initialising variable $days, which describes the number of days, the time frame is capturing.
	$days=ceil(((strtotime($to)+((23*3600)+(59*60)+59))-strtotime($from))/(3600*24));

	/*
	## Get data from database.
	## Get a list of all drugs (that match the search, if active).
	## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
	*/
	$query="SELECT * FROM drugs $searchpara ORDER BY Drugname ASC";
	$result=mysqli_query($link,$query);

	## The following loop will be run once for each of the output drugs from the database query.
	while($row=mysqli_fetch_object($result)){
		
		## Initialising variables with name, ID and unit of drug.
		$drug_name=$row->Drugname;
		$drug_ID=$row->Drug_ID;
		$unit=$row->Unit_of_Issue;
		
		## Print first column with the drug's name and a link to its records.
		echo"
			<tr>
				<td style=border-left:none>
					<a href='disp_drug_protocol.php?Drug_ID=$drug_ID'>$drug_name</a> (".$unit."s)
				</td>
				<td>
					";
					/*
					## Initialise/reset variables.
					## Variable $used is used to calculate the used amount of the drug.
					## $first is needed to avoid counting the physical stock at hand at the beginning of the time frame as "Amount used".
					*/
					$used=0;
					$first=true;
		
					/*
					## Get data from database.
					## Get a list of all protocol entries for the drug within the time frame.
					## Variable $link contains credentials to connect with database and is defined in DB.php which is included by HTML_HEAD.php.
					*/				
					$query2 = "
										SELECT Counts FROM disp_drugs 
											WHERE Drug_ID like '$drug_ID' 
												AND Counts not like ''  
												AND CountDate BETWEEN 
													(SELECT CountDate FROM disp_drugs 
														WHERE Drug_ID like $drug_ID and Counts not like '' 
															AND CountDate<='$from 23:59:59' 
														ORDER BY CountDate DESC LIMIT 1) 
												AND '$to 23:59:59' 
											ORDER BY CountDate ASC
										";
					$result2 = mysqli_query($link,$query2);
					
					## The following loop will be run once for each of the output protocol entries from the database query.
					while($row2 = mysqli_fetch_object($result2)){
						
						## Initialise variable $currentCounts which describes the physical stock in the dispensary for that record entry.
						$currentCounts=$row2->Counts;
						
						/*
						## In the first run of the loop $lastCounts is set equal to $currentCounts, that avoids
						## counting the physical stock at hand at the beginning of the time frame as "Amount used".
						*/
						if($first){
							$lastCounts=$currentCounts;
							$first=false;
						}
						
						## If the physical stock is smaller than the previous one, add the difference to $used.
						if($currentCounts<$lastCounts){
							$used=$used+($lastCounts-$currentCounts);
						}
						
						## Update $lastCounts for the next run of the loop. (It describes the physical stock during the last entry and is needed in the step above.)
						$lastCounts=$currentCounts;
					}
					
					## Print the calculated amount used in the second column.
					echo "$used
				</td>
				<td>
					";
					## Initialise/ reset variable $stockout, which is used to count the number of stock out days.
					$stockout=0;
		
					## This loop will be run once for every day within the time frame.
					for($j=0;$j<$days;$j++){
						
						## $day captures the day which is dealt with in the current run of the loop.
						$day=strtotime("+$j days",strtotime($from));
						$day=date("Y-m-d",$day);
						

						## Get physical stock at hand in the dispensary for that day and save in $disp_stock.
						$disp_stock=Disp_Drugs::getLastCounts($drug_ID,strtotime("$day 23:59:59"));
	
						/*
						## If the physical stock in the dispensary is 0 (or less), get the physical stock in the store for that day.
						## If that is 0 too, increase increase number of stock out days by one.
						*/
						if($disp_stock<=0){
							$store_stock=Store_Drugs::getAmount($drug_ID,strtotime($day));
							$stock=$disp_stock+$store_stock;
							if ($stock==0){
								$stockout++;
							}
						}
					}
					## Print the number of stockout days in the third column.
					echo"
					$stockout
				</td>
				<td>
					";
					## If every day within a time frame has been stockout, the average consumption is 0.
					if($days==$stockout){
						$average=0;
					}
		
					## Otherwise the average consumption is calculated by a division of the amount used by the number of non-stockout days.
					else{
						$average=$used/($days-$stockout);
						$average=round($average,2);
					}
					
					## Print the average consumption in the fourth column.
					echo"$average
				</td>
				<td>
					";
		
						/*
						## If today is not included in the time frame, get the physical stock at hand in the dispensary as of now.
						## Otherwise that has been done before within the calculation of stockout days.
						*/
						if($to!==$today){
							$disp_stock=Disp_Drugs::getLastCounts($drug_ID,time());
						}
						
						## Get the physical stock at hand in the store.
						$store_stock=Store_Drugs::getAmount($drug_ID,time());
						
						## Add the stock in the dispensary and store and print it in the fifth column.
						$stock=$disp_stock+$store_stock;
		
						echo"
						$stock
				</td>
				<td>";
						## If the drug is already out of stock, $prognosis is used to indicate so.
						if($stock==0){
							$prognosis="out of stock";
						}
						
						## If the data are available, calculate the expected day of stock out, using the average consumption.
						else if($average!=0){
							$number=floor($stock/$average);
							$prognosis=strtotime("+$number days",time());
							$prognosis=date("d/m/y",$prognosis);
						}
						
						## If the average consumption is unknown, $prognosis is used to indicate so.
						else{
							$prognosis="no data yet";
						}
						
						## Print the stockout prognosis in the last column.
						echo"$prognosis
			</tr>

				";
	}
	
	## Print a search input field in the upper right corner.
	echo"
			<div class='tableright'>
			<input type='text' name='search' placeholder='search' id='autocomplete' class='autocomplete'>
			<button type='submit' name='submitsearch'><i class='fas fa-search smallsearch'></i></button><br><br>
			</form>
			</div>
			";

	## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");

?>