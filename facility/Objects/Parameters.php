<?php

	class Parameters{
		
		## A parameter object contains all general data, a test parameter has. In the following its attributes are defined.
		private $parameter_ID;		## Parameter's ID.
		private $test_ID;		## Test's ID, which the parameter is corresponding to.
		private $parameter_name;	## Parameter's name.
		private $input_type;		## Parameter's input method (single choice, multiple choice, number, short or long text).
		private $test_outcomes;		## Provided options a parameter has.
		private $units;			## Option's unit of a parameter.
		private $reference_range;	## Default value, a parameter's option has, if the corresponding patient is healthy.
		
		/*
		## This function is called, if a new parameter object is needed for futher actions.
		## Saves the information of that parameter from database (identified by parameter ID) in that new parameter object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Parameters($parameter_ID){
			global $link;
			$query = "SELECT * FROM parameters WHERE parameter_ID = $parameter_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
					$this->test_ID = $row->test_ID;
			    $this->parameter_name = $row->parameter_name;
					$this->input_type = $row->input_type;
					$this->test_outcomes = $row->test_outcomes;
					$this->units = $row->units;
					$this->reference_range = $row->reference_range;
			}
			$this->parameter_ID = $parameter_ID;
		}

		/*
		## Constructor of new parameter.
		## Is called, if a new parameter is created in database.
		## The data of the new parameter is saved in database for all of its parameters.
		## Save this data also in a new created parameter object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Parameter($test_ID,$parameter_name,$input_type,$test_outcomes,$units,$reference_range){
			global $link;
			$query = "INSERT INTO `parameters`(`test_ID`,`parameter_name`,`input_type`,`test_outcomes`,`units`,`reference_range`) VALUES ('$test_ID','$parameter_name','$input_type','$test_outcomes','$units','$reference_range')";
			mysqli_query($link,$query);
				
			$parameter_ID = mysqli_insert_id($link);
			$instance = new self($test_ID);
			return $instance;
		}	

		/*
		## Getter function.
		## Returns the corresponding test ID of that parameter, on which the function is called.
		*/
		public function getTest_ID(){
			return $this->test_ID;
		}

		/*
		## Getter function.
		## Returns the name of that parameter, on which the function is called.
		*/
		public function getParameter_name(){
			return $this->parameter_name;
		}

		/*
		## Getter function.
		## Returns the input method of that parameter, on which the function is called.
		*/
		public function getInput_type(){
			return $this->input_type;
		}

		/*
		## Getter function.
		## Returns the provided options of that parameter, on which the function is called.
		*/
		public function getTest_outcomes(){
			return $this->test_outcomes;
		}

		/*
		## Getter function.
		## Returns the provided option's units of that parameter, on which the function is called.
		*/
		public function getUnits(){
			return $this->units;
		}

		/*
		## Getter function.
		## Returns the value that parameter's option, on which the function is called, would have, if a patient is healthy.
		*/
		public function getReference_range(){
			return $this->reference_range;
		}	
	}
?>	
