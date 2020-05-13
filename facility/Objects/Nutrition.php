<?php
    class Nutrition{
        private $protocol_ID;
        private $nutrition;

        public function Nutrition($protocol_ID){
			global $link;
			$query = "SELECT * FROM nutrition WHERE protocol_ID = $protocol_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->nutrition = $row->nutrition;
			}
			$this->protocol_ID = $protocol_ID;
        }
        
        public static function new_Nutrition($protocol_ID){
			global $link;
			$query = "INSERT INTO `nutrition`(`protocol_ID`) VALUES ('$protocol_ID')";
			mysqli_query($link,$query);
			
			$protocol_ID = mysqli_insert_id($link);
			$instance = new self($protocol_ID);
			return $instance;	
        }
        
        public function getNutrition(){
			return $this->nutrition;
        }
        
        public function setNutrition($var){
			global $link;
			$query = "UPDATE nutrition SET nutrition='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->MUAC = $var;
		}
    }
?>