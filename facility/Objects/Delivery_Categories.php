<?php

	class Delivery_Categories{
	
		## A delivery category object represents a certain category of delivery. It has the following attributes.
		private $del_category_ID;		## ID of delivery category.
		private $category_name;			## Name of delivery category.
		private $outcomes;				## Contains further detailed information as a value type, value range or a unit.
	

		/*
		## This function is called, if a new delivery categories object is needed for futher actions.
		## Saves the information of that delivery category from database (identified by delivery category ID) in 
		## that new delivery category object.
		## Variable $link contains credentials to connect with database and is defined in DB.php which is included by setup.php.
		*/
		public function Delivery_Categories($del_category_ID){
			global $link;
			$query = "SELECT * FROM delivery_categories WHERE del_category_ID = $del_category_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->category_name = $row->category_name;
				$this->outcomes = $row->outcomes;
			}
			$this->del_category_ID = $del_category_ID;
		}
		
		/*
		## Getter function.
		## Returns the name of that delivery category, on which the function is called.
		*/
		public function getcategory_name(){
			return $this->category_name;
		}
		
		/*
		## Getter function.
		## Returns further detailed information of that delivery category, on which the function is called.
		*/
		public function getoutcomes(){
			return $this->outcomes;
		}	
	}
?>