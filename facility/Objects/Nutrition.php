<?php
    class Nutrition{
        private $protocol_ID;
		private $nutrition_remarks;
		private $management;
		private $BMI_classification;

        public function Nutrition($protocol_ID){
			global $link;
			$query = "SELECT * FROM nutrition WHERE protocol_ID = $protocol_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->nutrition_remarks = $row->nutrition_remarks;
				$this->management = $row->management;
				$this->BMI_classification = $row->BMI_classification;
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
        
        public function getNutrition_remarks(){
			return $this->nutrition_remarks;
		}
		
		public function getManagement(){
			return $this->management;
		}
		
		public function getBMI_classification(){
			return $this->BMI_classification;
        }
        
        public function setNutrition_remarks($var){
			global $link;
			$query = "UPDATE nutrition SET nutrition_remarks='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->remarks = $var;
		}

		public function setManagement($var){
			global $link;
			$query = "UPDATE nutrition SET management='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->management = $var;
		}

		public function setBMI_classification($var){
			global $link;
			$query = "UPDATE nutrition SET BMI_classification='$var' WHERE protocol_ID = $this->protocol_ID";
			mysqli_query($link,$query);
			return $this->BMI_classification = $var;
		}

		public static function nutritionBoolean($visit_ID){
			global $link;
			$query="SELECT * FROM nutrition n,protocol p WHERE n.protocol_ID=p.protocol_ID AND p.visit_ID=$visit_ID";
			$result=mysqli_query($link,$query);
			
			if(mysqli_num_rows($result)!==0){
				$nutrition=mysqli_fetch_object($result)->protocol_ID;
			}else{
				$nutrition=false;
			}

			return $nutrition;
		}


		public static function classify_BMI($protocol_ID,$BMI){
			
			global $link;

			$protocol=new Protocol($protocol_ID);

			$visit_ID=$protocol->getVisit_ID();
			$visit=new Visit($visit_ID);

			$patient_ID=$visit->getPatient_ID();
			$patient=new Patient($patient_ID);

			$sex=$patient->getSex();

			$timestamp=strtotime($visit->getCheckin_time());
			$birthdate=strtotime($patient->getBirthdate());
			$age=floor(($timestamp-$birthdate)/(3600*24*7));
			if($age<=13){
				$query="SELECT * FROM BMI WHERE sex like '$sex' AND `in`='weeks' AND age='$age'";
				$result=mysqli_query($link,$query);
				$SDs=mysqli_fetch_object($result);
			}else{
				$age=floor(($timestamp-$birthdate)/(3600*24*30.4375));
				if($age<=228){
					$query="SELECT * FROM BMI WHERE sex like '$sex' AND `in`='months' AND age='$age'";
					$result=mysqli_query($link,$query);
					$SDs=mysqli_fetch_object($result);
				}else{
					$query="SELECT * FROM BMI WHERE sex like ''";
					$result=mysqli_query($link,$query);
					$SDs=mysqli_fetch_object($result);
				}
			}
			$last=0;
			$lastclass='below';
			foreach($SDs AS $key=>$SD){
				if((strstr($key,'SD') OR strstr($key,'Median')) AND $SD!=0){
					if($BMI>=$last AND $BMI<$SD){
						$class=$key;
						break;
					}
					$last=$SD;
					$lastclass=$key.' to';
				}
			}
			if(! isset($class)){
				$class="";
			}
			if($class=='-3SD'){
				$output='severe underweight';
			}else if($class=='-2SD'){
				$output='underweight';
			}else if($class=='-1SD' OR $class=='Median' OR $class=='+1SD'){
				$output='normal weight';
			}else if($class=='+2SD'){
				$output='overweight';
			}else if($class=='+3SD'){
				$output='obesity';
			}else{
				$output='severe obesity';
				$lastclass='above +3SD';
			}
			if($age<=228){
				$output.=" ($lastclass $class)";
			}
			return $output;
		}
    }
?>