<?php
	class Lab{
		/*
		## Each lab object represents a parameter's result of a certain test and is saved in Laboratory register entry. 
		## In the following all its parameters are described.
		*/
		private $lab_ID;			## ID of Laboratory register entry.
		private $protocol_ID_ordered;		## ID of protocol entry describing the ordering of the test.
		private $protocol_ID_results;		## ID of protocol entry describing the submission of results of the test.
		private $parameter_ID;		## ID of the result's corresponding parameter. 
		private $test_results;		## Contains the parameter's result.
		private $other_facility;	## Status, whether the parameter/the corresponding test has to be tested in another facility's Laboratory. 
		private $lab_list_ID;		## ID of the lab list entry containing also the lab number.


		/*
		## This function is called, if a new Laboratory register entry object is needed for futher actions.
		## Saves the information of that store register entry from database (identified by Laboratory register entry ID) in
		## that new Laboratory register entry object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Lab($lab_ID){
			global $link;
			$query = "SELECT * FROM lab WHERE lab_ID = $lab_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
			   		$this->protocol_ID_ordered = $row->protocol_ID_ordered;
					$this->parameter_ID = $row->parameter_ID;
					$this->test_results = $row->test_results;
					$this->other_facility = $row->other_facility;
					$this->lab_list_ID = $row->lab_list_ID;
					$this->protocol_ID_results = $row->protocol_ID_results;
			}
			$this->lab_ID = $lab_ID;
		}

		/*
		## Constructor of new Laboratory register entry.
		## Is called, if a new Laboratory database entry is created.
		## The data of new Laboratory register entry is saved in database for some of its parameters (the others are having default values).
		## Save this data also in a new created Laboratory register entry object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Lab($protocol_ID_ordered,$parameter_ID,$lab_list_ID){
			global $link;
			$query = "INSERT INTO `lab`(`protocol_ID_ordered`,`parameter_ID`,`lab_list_ID`) VALUES ('$protocol_ID_ordered','$parameter_ID','$lab_list_ID')";
			mysqli_query($link,$query);
			
			$lab_ID = mysqli_insert_id($link);
			$instance = new self($lab_ID);
			return $instance;
		}

		/*
		## Getter function.
		## Returns the results of that Laboratory register entry, on which the function is called.
		*/
		public function getTest_results(){
			return $this->test_results;
		}

		/*
		## Getter function.
		## Returns the ID of protocol entry describing the ordering of the test of that Laboratory register entry, on which the function is called.
		*/
		public function getProtocol_ID_ordered(){
			return $this->protocol_ID_ordered;
		}

		/*
		## Getter function.
		## Returns the ID of protocol entry describing the submission of the test of that Laboratory register entry, on which the function is called.
		*/
		public function getProtocol_ID_results(){
			return $this->protocol_ID_results;
		}

		/*
		## Getter function.
		## Returns the status, whether a parameter/corresponding test of that Laboratory register entry, on which the function is called,
		## has to be performed in another facility's Laboratory.
		*/
		public function getOther_facility(){
			return $this->other_facility;
		}

		/*
		## Setter function.
		## Updates the results of that Laboratory register entry, on which the function is called, in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setTest_results($var){
			global $link;
			$query = "UPDATE lab SET test_results='$var' WHERE lab_ID = $this->lab_ID";
			mysqli_query($link,$query);
			return $this->test_results = $var;
		}

		/*
		## Setter function.
		## Updates the status in database, whether a parameter/corresponding test of that Laboratory register entry, 
		## on which the function is called, has to be performed in another facility's Laboratory.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setOther_facility($var){
			global $link;
			$query = "UPDATE lab SET other_facility='$var' WHERE lab_ID = $this->lab_ID";
			mysqli_query($link,$query);
			return $this->other_facility = $var;
		}

		/*
		## Setter function.
		## Updates the ID of protocol entry describing the submission of the test in database of that Laboratory register entry, on which the function is called.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function setProtocol_ID_results($var){
			global $link;
			$query = "UPDATE lab SET protocol_ID_results='$var' WHERE lab_ID = $this->lab_ID";
			mysqli_query($link,$query);
			return $this->protocol_ID_results = $var;
		}


		/*
		## This function is responsible for displaying the results of all test parameters corresponding to a certain patient's visit in a list.
		## For that, all necessary HTML commands are buffered in the variable $html to show these information later. 
		## The sent parameter $protocol_ID identifies, to which patient's visit the results are going to be printed.
		## The sent parameter $tooltips contains the status, whether tooltips are enabled or not.
		## If they are enabled, a notice appears for the user, if the user hovers over a certain information in the browser.
		## This tooltip displays the value (range) a parameter would have, if the corresponding patient is healthy.
		## This function returns the HTML buffer $html.
		*/
		public function display_results($lab_number,$tooltips){

			/*
			## Get data from database.
			## Get all ordered tests and their parameter, which were performed on a certain patient's visit.
			*/
			global $link;
			$query="SELECT * FROM lab,lab_list WHERE lab_list.lab_list_ID=lab.lab_list_ID AND lab_number='$lab_number'";
			$result=mysqli_query($link,$query);
			
			/*
			## Initialise variables, which are needed within this function.
			## The variable $last_test is used to group all patient's visit's parameters by their corresponding test. 
			*/
			$last_test='';
			$html='';
			
			## This loop is run once for each test's parameter from database corresponding this certain patient's visit.
			while($row=mysqli_fetch_object($result)){

				/*
				## Initialise some more needed variables for each parameter.
				## Create a new parameter object and get its result's unit ($unit) as well as
				## the a value a parameter would have, if the corresponding patient is healthy ($ref).
				*/
				$parameter_ID=$row->parameter_ID;
				$test_results=$row->test_results;
				$other_facility=$row->other_facility;
				
				$parameter= new Parameters($parameter_ID);
				$test_ID=$parameter->getTest_ID();
				$parameter_name=$parameter->getParameter_name();
				$unit=$parameter->getUnits();
				$ref=$parameter->getReference_range();

				## Create a new test object to get the name to the parameter's corresponding test.
				$test=new Tests($test_ID);
				$test_name=$test->getTest_name();
				
				## This if-branch is called, if a result exists for the parameter.
				if(! empty($test_results)){

					/*					
					## This if-branch is used to group the parameters by their corresponding tests.
					## Add a headline with corresponding test's name above each parameter's group to the HTML buffer, to print it later.
					## If the corresponding test has to be performed in another facility's Laboratory, colour it grey and
					## add a notice to the HTML buffer. 
					*/
					if($test_name!==$last_test){
						$html.="<h4>$test_name</h4><br>";
						if($other_facility==1){
							$html.="&nbsp&nbsp&nbsp&nbsp&nbsp<text style='color:grey'>This test was performed in a different Lab.</text><br>";
						}
						$last_test=$test_name;
					}

					/*
					## Add the parameter's name and its results and their units to the HTML buffer, to print them later.
					## If tooltips are enabled, add further the value a parameter has, when the patient is healthy, 
					## as tooltip to the HTML buffer.  
					*/					
					$html.="<h5>$parameter_name ";
					if(! empty($parameter_name)){
						$html.=":";
					}
					$html.= "</h5> ";
									if($tooltips=='tooltips on'){
										$html.="<div class='tooltip'>";
									}
									$html.= $test_results;
										if($tooltips=='tooltips on'){
											$html.="
														<span class='tooltiptext'>
															$ref
														</span>
														";
										}
									if($tooltips=='tooltips on'){
										$html.="</div>";
									}
								 $html.=" ".$unit."<br>";
				}
				
			}
			return $html;
		}
		
		
		/*
		## This function is responsible for displaying a head of table, which shows results for performed tests.
		## It has the columns test's name, its result, the result's unit and a value, the parameter would have, if the patient is healthy.
		## The necessary HTML commands are buffered in the variable $html for later printing.
		## This function returns the HTML buffer. 
		*/
		public function result_tablehead(){
			$html="
					
					<table>
						<tr>
							<th>
								Test
							</th>
							<th>
								Result
							</th>
							<th>
								Unit
							</th>
							<th>
								Reference Range
							</th>
						</tr>
						<tr>
							<td>
							</td>
						</tr>
					";
			return $html;
		}
		
		/*
		## This function is responsible displaying a row of table, which shows results for performed tests.
		## It has the columns test's name, its result, the result's unit and a value, the parameter would have, if the patient is healthy.
		## The necessary HTML commands are buffered in the variable $html for later printing.
		## The sent parameter $last_test is used to group all patient's visit's parameters by their corresponding test. 
		## It contains the name of the last known parameter group.
		## This function returns the HTML buffer. 
		*/
		public function result_table($last_test){

			/*
			## Initialise variables, which are needed within this function.
			## The variable $unit contains the result's unit.
			## The variable $ref contains the a value a parameter would have, if the corresponding patient is healthy.
			*/			
			$parameter_ID=$this->parameter_ID;
			$test_results=$this->test_results;

			$parameter= new Parameters($parameter_ID);
			$test_ID=$parameter->getTest_ID();
			$parameter_name=$parameter->getParameter_name();
			$unit=$parameter->getUnits();
			$ref=$parameter->getReference_range();
			
			$test=new Tests($test_ID);
			$test_name=$test->getTest_name();

			$html='';
			
			## Add the parameter and its information as table row to the HTML buffer.
			if(! empty($test_results)){
				
				/*
				## Add the name of the parameter's corresponding test to the HTML buffer, 
				## if the current parameter does not match to the last known parameter group (=test). 
				*/
				if($test_name!==$last_test){
					$html.='
								<tr>
									<td>
									</td>
								</tr>
								<tr>
									<th>
										<u>'.$test_name.'</u>
									</th>
								</tr>
								 ';
				}
				$html.="
							<tr>
								<th>
									$parameter_name
								</th>
								<td>
									$test_results
								</td>
								<td>
									$unit
								</td>
								<td>
									$ref
								</td>
							</tr>
							";
				return $html;
			}
		}

		/*
		## This function inquires the number of decimal places.
		## This information is needed as attribute of a form's input field of type 'number'. 
		## The sent parameter $min contains the minimum of the number's value range, which is analysed to inquire the number of decimal places.
		*/
		public function getStep($min){
			if(strstr($min,'.')){
				$step_arr=explode('.',$min);
				$step_count=strlen($step_arr[1]);
				$step='0.';
				if($step_count>1){
					for($j=1;$j<$step_count;$j++){
						$step.='0';
					}
				}
				$step.='1';
			}else{
				$step=1;
			}
			return $step;
		}
	}
?>
