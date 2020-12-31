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

        public function getDepartmentId($name){
            global $link;
            $query="SELECT Department_ID FROM departments WHERE Department='$name'";
            $object=mysqli_query($link,$query);
            $ID=mysqli_fetch_object($object);
            return $ID->Department_ID;
        }

        public function getDepartmentName($ID){
            global $link;
            $query="SELECT Department FROM departments WHERE Department_ID='$ID'";
            $object=mysqli_query($link,$query);
            $name=mysqli_fetch_object($object);
            return $name->Department;
        }
    }
?>