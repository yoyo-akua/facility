<?php

	class Delivery{
		
		## Define all parameters a delivery entry has. This object represents a certain delivery entry.
		private $Delivery_ID;		## ID of the partiular delivery register entry.
		private $del_category_ID;		## ID of corresponding delivery parameter.
		private $result;		## Result for a partiular delivery parameter.
		private $maternity_ID;		## Client's maternity ID.
		private $protocol_ID;		## The delivery's protocol ID.

		/*
		## This function is called, if a new delivery entry object is needed for further actions.
		## Saves the information of that delivery entry from database (identified by delivery ID) in that new delivery entry object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Delivery($Delivery_ID){
			global $link;
			$query = "SELECT * FROM delivery WHERE Delivery_ID = $Delivery_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->del_category_ID = $row->del_category_ID;		
				$this->result = $row->result;		
				$this->maternity_ID = $row->maternity_ID;
				$this->protocol_ID = $row->protocol_ID;
			}
			$this->Delivery_ID = $Delivery_ID;
		}

		/*
		## Constructor of new delivery entry.
		## Is called, if a new delivery entry database entry is created.
		## The data of new delivery entry is saved in database for all its parameters.
		## Save this data also in a new created delivery entry object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_delivery($del_category_ID,$result,$maternity_ID,$protocol_ID){
			global $link;
			$query = "INSERT INTO `delivery`(`del_category_ID`,`result`,`maternity_ID`,`protocol_ID`) VALUES ('$del_category_ID','$result','$maternity_ID','$protocol_ID')";
			mysqli_query($link,$query);

			$Delivery_ID = mysqli_insert_id($link);
			$instance = new self($Delivery_ID);
			return $instance;
		}	
		
		/*
		## Getter function.
		## Returns the ID of the delivery entry, on which the function is called.
		*/
		public function getDelivery_ID(){
			return $this->Delivery_ID;
		}
		
		/*
		## Getter function.
		## Returns the ID of the corresponding parameter of the delivery entry, on which the function is called.
		*/
		public function getDel_category_ID(){
			return $this->del_category_ID;
		}
		
		/*
		## Getter function.
		## Returns the result for the partiular parameter of the delivery entry, on which the function is called.
		*/
		public function getResult(){
			return $this->result;
		}
		
		/*
		## Getter function.
		## Returns the maternity ID of the client, on which the function is called.
		*/
		public function getMaternity_ID(){
			return $this->maternity_ID;
		}	
		
		/*
		## Setter function.
		## Updates the ID of the corresponding parameter of the delivery entry, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setDel_category_ID($var){
			global $link;
			$query = "UPDATE delivery SET del_category_ID='$var' WHERE Delivery_ID = $this->Delivery_ID";
			mysqli_query($link,$query);
			return $this->Delivery_ID = $var;
		}
		
		/*
		## Setter function.
		## Updates the result for the partiular parameter of delivery entry, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setResult($var){
			global $link;
			$query = "UPDATE delivery SET result='$var' WHERE Delivery_ID = $this->Delivery_ID";
			mysqli_query($link,$query);
			return $this->Delivery_ID = $var;
		}
		
		/*
		## Setter function.
		## Updates the maternity ID of the client, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setMaternity_ID($var){
			global $link;
			$query = "UPDATE delivery SET maternity_ID='$var' WHERE Delivery_ID = $this->Delivery_ID";
			mysqli_query($link,$query);
			return $this->maternity_ID = $var;
		}	
		
		/*
		## This function is responsible for displaying a certain client's delivery and its information.
		## For that, the necessary HTML commands are added to a HTML buffer, to print the information later.
		## The sent parameters have the following meaning:
		##		- $protocol_ID contains the ID of that client's visit, which is corresponding to the current client's delivery. 
		##		  This is necessary to print the client's vital signs. 
		##		- $maternity_ID contains the ID of the client's maternity which is necessary to retrieve the client's general pregnancy data. 
		## This function returns the HTML buffer $html.
		*/	
		public function display_delivery($maternity_ID,$visit_ID){
			
			## Initialise objects of maternity (client's general pregnancy data) and protocol (that particular OPD visit's data).
			$maternity=new Maternity($maternity_ID);

			## Display the client's vital signs.
			$html='<details><summary><h4><u>Vital Signs</u></h4></summary><div style="margin-left:10px">'.Vital_Signs::display_admission_data($visit_ID).'</div></details>';
			
			/*
			## Initialise variable $lastcategory which is needed to determine whenever a new category/parameter of delivery entries begins,
			## and print the corresponding headline.
			*/
			$lastcategory='';
			
			/*
			## Get data from database.
			## Get all delivery register entries for that particular pregnancy of the client.
			## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
			## Save all data from database in $result.
			*/
			global $link;
			$query="SELECT * FROM delivery WHERE maternity_ID=$maternity_ID ORDER BY del_category_ID";
			$result=mysqli_query($link,$query);
			
			## This loop will be run once for each of the output delivery register entries from the database query.
			while($row=mysqli_fetch_object($result)){
				
				/*
				## Initialise object of Delivery (contains the result of a certain delivery parameter) 
				## and Delivery Category (general information on a certain delivery parameter).
				*/
				$Delivery=new Delivery($row->delivery_ID);
				$Cat_ID=$Delivery->getDel_category_ID();
				$Del_Cat=new Delivery_Categories($Cat_ID);

				## Initialise variables with the category's name and its (theoretically) possible outcomes.
				$category=$Del_Cat->getcategory_name();
				$outcomes=$Del_Cat->getoutcomes();

				## Initialise variable with the client's result regarding the particular parameter of delivery entry.
				$results=$Delivery->getResult();
				
				## Initialise empty variable with the parameter's unit.
				$unit="";

				## Depending on the particular type of outcomes defined in $outcomes, inquire the parameter's unit.
				if(strstr($outcomes,"number")){
					$unit=Delivery::extract_unit('number',$outcomes);
				}else if(strstr($outcomes,"text")){
					$unit=Delivery::extract_unit('text',$outcomes);
				
				/*
				## If a parameter repesents a time, this if-branch is called
				## The time information, which is saved in database, is transformed into British format 
				*/
				}else if(strstr('datetime',$outcomes)){
					$results=date("d/m/Y H:i",strtotime($results));
				}
				
				if($Cat_ID==1){
					$html.= '<details><summary><h3 style="display:inline">Stage 1-3</h3></summary>';
				}
				if($Cat_ID==7){
					$html.= '</details><details><summary><h3 style="display:inline">State of the Newborn</h3></summary>';
				}
				if($Cat_ID==18){
					$html.= '</details><details><summary><h3 style="display:inline">Newborn Care</h3></summary>';
				}
				if($Cat_ID==21){
					$html.= '</details><details><summary><h3 style="display:inline">Stage 4</h3></summary>';
				}
				if($Cat_ID==26){
					$html.= '</details><details><summary><h3 style="display:inline">Discharge</h3></summary>';
				}
				/*
				## Buffer the name of the parameter, if not the same as in last run of the loop, in $html.
				## Also buffer the parameter's result and unit.
				*/
				if($category==$lastcategory){
					$html.=", $results $unit";
				}else{
					$html.="<br><h4>$category:</h4> $results $unit";
					
					## Update $lastcategory for the next run of the loop.
					$lastcategory=$category;
				}
			}
			
			$html.="</details>";
			## Return the HTML buffer.
			return $html;
		}
		
		/*
		## The function is responsible for extracting the particular parameter's/category of delivery's unit.
		## The sent parameters have the following meaning:
		##		- $type contains the input type of the parameter, which is necessary for a correct extraction.
		##		- $outcomes contains the string from which the unit is supposed to be extracted.
		## This function returns the unit buffered in $unit.
		*/	
		public function extract_unit($type,$outcomes){
			$unit=str_replace($type,"",$outcomes);
			$unit=str_replace("(","",$unit);
			$unit=str_replace(")","",$unit);

			return $unit;
		}
		
		/*
		## The function is responsible for printing the form for entering delivery's data.
		## The sent parameters have the following meaning:
		##		- $results contains the result of the parameter that was previously entered in case the client is editing the delivery's data,
		##		- $edit indicates whether the client is editing the delivery data or initially saving them,
		##		- $ID contains the ID of the delivery parameter which is dealt with in this call of the function,
		##		- $name is the name of the input field for the parameter,
		##		- $Outcome contains the possible outcomes for the input field (input type and unit).
		## This function returns the HTML buffer $html.
		*/	
		public function print_form($results,$edit,$ID,$name,$Outcome){
			
			/*
			## If the ID of the delivery parameter/category is 9, which indicates,
			## that the input field is supposed to contain the APGAR score of the child, call this if-branch.
			*/
			if($ID==9){
				
				## Retrieve the APGAR-Scores from the previous results, if the user is editing the delivery entry.
				if($edit){
					$APGAR=explode(', ',$results);
					$APGAR_1=str_replace("/10","",$APGAR[0]);
					$APGAR_5=str_replace("/10","",$APGAR[1]);
				}

				## Print the input fields and prefill it, if the user is editing the delivery entry.
				echo"
						after 1 minute:<input type='number' name='APGAR_1-$name'";
						if($edit){
							echo "value='$APGAR_1'";
						}
						echo" min='0' max='10'>/10<br>
						after 5 minutes:<input type='number' name='APGAR_5-$name'";
						if($edit){
							echo "value='$APGAR_5'";
						}
						echo" min='0' max='10'>/10</div>
						";
			}
			
			## If the input field is supposed to contain a figure, call this if-branch.
			else if(strstr($Outcome,"number")){
				
				## Retrieve the unit from the string of possible outcomes.
				$unit=Delivery::extract_unit('number',$Outcome);
				
				/*
				## Print the input field and the unit
				## Prefill input field, if the user is editing the delivery entry.
				*/
				echo"
						<input type='number' step='0.1' name='$name'";
						if($edit){
							echo "value='$results'";
						}
						echo" min='0'> $unit</div>";
			}
			
			## If the input field is supposed to contain a text, call this if-branch.
			else if(strstr($Outcome,"text")){
				
				## Retrieve the unit from the string of possible outcomes.
				$unit=Delivery::extract_unit('text',$Outcome);
				
				/*
				## Print the input field and the unit.
				## Prefill input field, if the user is editing the delivery entry.
				*/
				echo"
						<input type='text' name='$name'";
						if($edit){
							echo "value='$results'";
						}
						echo"> $unit</div>";
			}
			
			## If the input field is supposed to contain a time, call this if-branch.
			else if(strstr($Outcome,"datetime")){
				
				/*
				## Initialise variables with the date and the time of the previous entry, if the user is editing the delivery entry.
				## Otherwise initialise them with the current time.
				*/
				$maxdate=$date=date("Y-m-d",time());
				$maxtime=$time=date("H:i",time());

				if($edit){
					$date=date("Y-m-d",strtotime($results));
					$time=date("H:i",(strtotime($results)-strtotime($date)-3600));
				}else{
					$date=$maxdate;
					$time=$maxtime;
				}
				
				## Print the input fields for date and time and prefill them.	
				echo"
						<input type='date' name='$name' value='$date' max='$maxdate'>
						<input type='time' name='time_$name' value='$time' max='$maxtime'></div>
						";
			}
			
			## If the input field is supposed to contain a drop down menu, call this if-branch.
			else{
				
				## Create an array with the select options.
				$array=explode(",",$Outcome);
				
				/*
				## Print the drop down menu
				## If the user is editing the delivery entry, select the previously chosen select option.
				*/
				echo"<select name='$name'>";
				for($j=0;$j<count($array);$j++){
					echo"<option value='$array[$j]'";
					if($edit AND $results==$array[$j]){
						echo "selected";
					}
					echo">$array[$j]</option>";
				}
				echo"</select></div>";
			}
		}
	}
?>