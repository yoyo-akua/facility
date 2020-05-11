<?php
    class Seen{
        private $protocol_ID;
        private $OPD;
        private $Consulting;
        private $Laboratory;
        private $Dispensary;
        private $Maternity;
        private $Nutrition;

		public function Seen($protocol_ID){
			global $link;
			$query = "SELECT * FROM seen WHERE protocol_ID = $protocol_ID";
			$result = mysqli_query($link,$query);
			while($row = mysqli_fetch_object($result)){
				$this->OPD = $row->OPD;
                $this->Consulting = $row->Consulting;
                $this->Laboratory = $row->Laboratory;
                $this->Dispensary = $row->Dispensary;
                $this->Maternity = $row->Maternity;
                $this->Nutrition = $row->Nutrition;
			}
			$this->protocol_ID = $protocol_ID;
		}


		public static function new_Seen($protocol_ID){
			global $link;
			$query = "INSERT INTO `seen`(`protocol_ID`) VALUES ('$protocol_ID')";
			mysqli_query($link,$query);

			$protocol_ID = mysqli_insert_id($link);
			$instance = new self($protocol_ID);
			return $instance;		
		}
    }
?>