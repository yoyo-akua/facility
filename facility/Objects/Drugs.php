<?php
	class Drugs{

		## Define all parameters a drug has.
		private $Drug_ID;		## ID of the drug.
		private $Drugname;		## Name of the drug,
		private $Unit_of_Issue;		## Unit of issue of the drug.
		
		
		/*
		## This function is called, if a new drug object is needed for further actions.
		## Saves the information of that drug from database (identified by drug ID) in that new drug object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Drugs($Drug_ID){
			global $link;
			$query = "SELECT * FROM drugs WHERE Drug_ID = $Drug_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
			    $this->Drugname = $row->Drugname;
			    $this->Unit_of_Issue = $row->Unit_of_Issue;
			}
			$this->Drug_ID = $Drug_ID;
	    	}
		
		/*
		## Constructor of new drug.
		## Is called, if a new drug database entry is created.
		## The data of new drug is saved in database for all its parameters.
		## Save this data also in a new created drug object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Drugs($Drugname,$Unit_of_Issue){
			global $link;
			$query = "INSERT INTO `drugs`(`Drugname`,`Unit_of_Issue`) VALUES ('$Drugname','$Unit_of_Issue')";
			mysqli_query($link,$query);
			
			$Drug_ID = mysqli_insert_id($link);
			$instance = new self($Drug_ID);
			return $instance;
	    	}

		/*
		## Getter function.
		## Returns the ID of drug, on which the function is called.
		*/
		public function getDrug_ID(){
			return $this->Drug_ID;
		}

		/*
		## Getter function.
		## Returns the name of drug, on which the function is called.
		*/
		public function getDrugname(){
			return $this->Drugname;
		}

		/*
		## Getter function.
		## Returns the unit of issue of drug, on which the function is called.
		*/
		public function getUnit_of_Issue(){
			return $this->Unit_of_Issue;
		}
		
		/*
		## Setter function.
		## Updates the name of the drug in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/			
		public function setDrugname($var){
			global $link;
			$query = "UPDATE drugs SET Drugname='$var' WHERE Drug_ID = $this->Drug_ID";
			mysqli_query($link,$query);
			return $this->Drugname = $var;
		}

		/*
		## Setter function.
		## Updates the unit of issue of the drug in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/			
		public function setUnit_of_Issue($var){
			global $link;
			$query = "UPDATE drugs SET Unit_of_Issue='$var' WHERE Drug_ID = $this->Drug_ID";
			mysqli_query($link,$query);
			return $this->Unit_of_Issue = $var;
		}

		/*
		## This function is needed for styling the graphical user interface.
		## It is called on web pages, which show a list of drugs and contain a search function.
		## On this web pages the search result is displayed above all other list entries.
		## This function closes the table of the search result's list and displays a space between the search results and all other outputs.
		## The sent parameter $columns contains the number of columns of the table, that shows the search results,
		## which is needed to close that table.
		*/
		public function drugsearch_space($columns){
			echo"<tr>";
			for($c=1;$c<=$columns;$c++){
				echo"
						<td style=border-left:none>
						</td>
						";
			}
			echo"
					</tr>
					<tr class='emptytable'>
						<td>
						</td>
					</tr>
					<tr class='emptytable'>
						<td>
						</td>
					</tr>
					<tr class='emptytable'>
						<td>
						</td>
					</tr>
					";
		}

		/*
		## This function is needed for styling the graphical user interface.
		## It is called on web pages, which show a list of drugs and contain a search function.
		## This function places the search box in the upper right corner of the page.
		*/
		public function search_submit(){
			echo"
					</table>
					<div class='tableright'>
						<input type='text' name='search'  value='' placeholder='search' id='autocomplete' autocomplete='off' class='autocomplete'>
						<button type='submit' name='submitsearch'><i class='fas fa-search smallsearch'></i></button><br><br>
						<input type='submit' name='submit' value='submit'>
					</form>
					</div>
					";
		}
	}
?>
