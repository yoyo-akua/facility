<?php
	class Departments{
        public function getDepartment(){
            global $own_IP;
            global $link;
            $query="SELECT * FROM departments WHERE IP like '%$own_IP%'";
            $result=mysqli_query($link,$query);
            $own_department=array();
            while($row=mysqli_fetch_object($result)){
                $own_department[]=$row->Department;
            }
            if($own_IP=='::1' OR $own_IP=='127.0.0.1' OR $own_IP==$_SERVER['SERVER_ADDR']){
                $own_department[]='Server';
            }
            return $own_department;
        }
    }
?>