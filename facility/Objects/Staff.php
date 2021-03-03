<?php

	class Staff{

		## A staff object contains all general data, a staff has. In the following its attributes are defined.
		private $staff_ID;	## Staff's ID.
		private $name;	## Staff's name.
		private $username;	## Staff's username.
		private $qualification;	## Staff's qualification.
        private $password;	## Staff's password.
        private $department_ID; ## Staff's department. 
		
		/*
		## This function is called, if a new staff object is needed for futher actions.
		## Saves the information of that staff from database (identified by staff ID) in that new stadd object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Staff($staff_ID){
			global $link;
			$query = "SELECT * FROM staff WHERE staff_ID = $staff_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->name = $row->name;
				$this->username = $row->username;
				$this->qualification = $row->qualification;
                $this->password = $row->password;
                $this->department_ID = $row->department_ID;
			}
			$this->staff_ID = $staff_ID;
		}

		/*
		## Constructor of new test.
		## Is called, if a new test is created in database.
		## The data of the new test is saved in database some of its parameters (the others are having default values).
		## Save this data also in a new created test object and return this object for further actions.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public static function new_Staff($name,$username,$qualification,$password,$department_ID){
			global $link;
			$query = "INSERT INTO `staff`(`name`,`username`,`qualification`,`password`,`department_ID`) VALUES ('$name','$username','$qualification','$password','$department_ID')";
			mysqli_query($link,$query);
				
			$staff_ID = mysqli_insert_id($link);
			$instance = new self($staff_ID);
			return $instance;
		}

		/*
		## Getter function.
		## Returns the ID of that staff, on which the function is called.
		*/
		public function getStaff_ID(){
			return $this->staff_ID;
		}

		/*
		## Getter function.
		## Returns the name of that staff, on which the function is called.
		*/	
		public function getName(){
			return $this->name;
		}

		/*
		## Getter function.
		## Returns the username of that staff, on which the function is called.
		*/	
		public function getUsername(){
			return $this->username;
		}

		/*
		## Getter function.
		## Returns the qualification of that staff, on which the function is called.
		*/
		public function getQualification(){
			return $this->qualification;
		}

		/*
		## Getter function.
		## Returns the password of that staff, on which the function is called.
		*/
		public function getPassword(){
			return $this->password;
		}

        /*
		## Getter function.
		## Returns the department of that staff, on which the function is called.
		*/
		public function getDepartment_ID(){
			return $this->department_ID;
        }
        
		/*
		## Setter function.
		## Updates the name of the staff in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/			
		public function setName($var){
			global $link;
			$query = "UPDATE staff SET name='$var' WHERE staff_ID = $this->staff_ID";
			mysqli_query($link,$query);
			return $this->name = $var;
		}

		/*
		## Setter function.
		## Updates the username of the staff in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/			
		public function setUsername($var){
			global $link;
			$query = "UPDATE staff SET username='$var' WHERE staff_ID = $this->staff_ID";
			mysqli_query($link,$query);
			return $this->username = $var;
        }

        /*
		## Setter function.
		## Updates the qualification of the staff in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/			
		public function setQualification($var){
			global $link;
			$query = "UPDATE staff SET qualification='$var' WHERE staff_ID = $this->staff_ID";
			mysqli_query($link,$query);
			return $this->name = $var;
        }
        
        /*
		## Setter function.
		## Updates the password of the staff in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/			
		public function setPassword($var){
			global $link;
			$query = "UPDATE staff SET password='$var' WHERE staff_ID = $this->staff_ID";
			mysqli_query($link,$query);
			return $this->name = $var;
        }

        /*
		## Setter function.
		## Updates the department of the staff in database.
		## Returns the updated information.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/			
		public function setDepartment_ID($var){
			global $link;
			$query = "UPDATE staff SET department_ID='$var' WHERE staff_ID = $this->staff_ID";
			mysqli_query($link,$query);
			return $this->name = $var;
        }

        /*
        ## Getter function.
        ## Inquires whether a staff member with a certain name is listed, if so returns its staff ID.
        */
        public function getStaffByName($name){
            global $link;
            $query = "SELECT * FROM staff WHERE name='$name'";
            $result=mysqli_query($link,$query);
            $object=mysqli_fetch_object($result);
            if(! empty($object)){
                return $object->staff_ID;
            }else{
                return false;
            }
            
		}
		
		/*
        ## Getter function.
        ## Inquires whether a staff member with a certain username is listed, if so returns its staff ID.
        */
        public function getStaffByUsername($username){
            global $link;
			$query = "SELECT * FROM staff WHERE username='$username'";
            $result=mysqli_query($link,$query);
            $object=mysqli_fetch_object($result);
            if(! empty($object)){
                return $object->staff_ID;
            }else{
                return false;
            }
            
        }
	}
?>
