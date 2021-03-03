<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
    include("HTMLParts/HTML_HEAD.php");
    
    ## Call this if-branch when the user has submitted the login <form action="" class=""></form>
    if(isset($_POST['submit'])){

        ## Initialise variables with the name and the password entered by the user. 
        $name=$_POST['username'];
        $password=$_POST['password'];

        ## Use the name to get the ID of the staff member. If it is not in the system display a message.
        if(Staff::getStaffByUsername($name)){
            $ID=Staff::getStaffByUsername($name);
            
            ## Initialise new object of staff.
            $staff=new Staff($ID);

            /*
            ## Check if the password is correct.
            ## If so, save password, name, ID and department of the staff member and forward the user to the home page.
            ## Otherwise display a warning message. 
            */
            if($password==$staff->getPassword()){
                $_SESSION['staff_password']=$password;
                $_SESSION['staff_name']=$staff->getName();
                $_SESSION['staff_ID']=$ID;
                $_SESSION['staff_department']=$staff->getDepartment_ID();
                echo '<script>history.go(-2)</script>';
            }else{
                $message="This username password combination is wrong. Please try again!";
            }
        }else{
            $message="Unknown user";
        }
        if(isset($message)){
            Settings::messagebox($message);
        }
    }

    ## Display a login form with an input field for the name and the password of the user.
    echo '
        <h1>   
            Login
        </h1>
        <br>
        <form method="post" action="login.php">
            <h3>
                Username
            </h3>
            <input type="text" id="autocomplete" name="username" class="autocomplete" autocomplete="off" required>
            <h3>
                Password
            </h3>
            <input type="password" name="password" required minlength="5">
            <br>
            <br>
            <button type="submit" id="submitbutton" name="submit"><i class="fas fa-check-circle fa-4x"></i></button>
        </form>
        ';

    ## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");
?>