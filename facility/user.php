<?php
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
    include("HTMLParts/HTML_HEAD.php");
    
    ## Initialise new object of staff using the staff ID whichis stored in the session variable.
    $staff=new Staff($_SESSION['staff_ID']);

    ## Call this if-branch in case the user submitted the form.
    if (isset ($_POST['submit'])){

        ## Check if the user entered the correct password.
        if($_POST['old_password']==$_SESSION['staff_password']){
            /*
            ## Check if there already is a staff member by the username the user entered. 
            ## If so, warn the user and stop database change.
            ## Otherwise update the staff member's username in the database and in the session variable. 
            */
            $used_name=Staff::getStaffByUsername($_POST['username']);
            if($used_name!==$staff->getStaff_ID() AND $used_name){
                $message="Unable to change the username. A member of staff with this username is already in the database.";
            }else{
                $staff->setUsername($_POST['username']);
            }

            ## Update the staff member's name in the database and the session variable. 
            $staff->setName($_POST['name']);
            $_SESSION['staff_name']=$_POST['name'];

            ## Update the user's qualification in the database.
            $staff->setQualification($_POST['qualification']);

            ## Check if the user entered a department, update the staff member's department in the database and in the session variable.
            if(! empty($_POST['department'])){
                $department_ID=$staff->setDepartment_ID(Departments::getDepartmentId($_POST['department']));
            }else{
                $department_ID=$staff->setDepartment_ID('');
            }
            $_SESSION['staff_department']=$department_ID;
            
            ## Call this if-branch in case the user wants to change the password.
            if(! empty($_POST['new_password'])){

                /*
                ## Check if the new password and the confirmation password match.
                ## If so, write it to database, otherwise display a warning message.
                */
                if(! empty($_POST['confirm_password']) AND $_POST['new_password']==$_POST['confirm_password']){
                    $staff->setPassword($_POST['new_password']);
                    $_SESSION['staff_password']=$_POST['new_password'];
                }else{
                    $message="The new password could not be verified since it didn't match the confirmation password.";
                }
                
                
            }
        }else{
            $message="Your password is incorrect.";
        }
        if(isset($message)){
            Settings::messagebox($message);
        }
    }
    ## Initialise variables with the staff's name, qualification and department.
    $name=$_SESSION['staff_name'];
    $username=$staff->getUsername();
    $qualification=$staff->getQualification();
    $department=Departments::getDepartmentName($_SESSION['staff_department']);

    ## Print the user profile as an input form which is prefilled with the staff member's data.
    echo '
        <h1>   
            User Profile of '.$name.'
        </h1>
        <br>
        <form method="post" action="user.php" autocomplete="off">
            <h3>
                Name
            </h3>
            <input type="text" value="'.$name.'" name="name" required>
            <h3>
                Username
            </h3>
            <input type="text" value="'.$username.'" name="username" required>
            <h3>
                Qualification
            </h3>
            <input type="text" value="'.$qualification.'" name="qualification" required>
            <h3>
                Department
            </h3>
            <select name="department">
                <option value=""></option>';
                foreach($DEPARTMENTS AS $staff_department){
                    echo '<option value="'.$staff_department.'"';
                    if($department==$staff_department){
                        echo 'selected';
                    }
                    echo'>'.$staff_department.'</option>';
                }
                echo'
            </select>
            <h3>
                Password
            </h3>
            <input type="password" name="old_password" required minlength="5">
            <br>
            <h3>
                Change Password
            </h3>
            <h4>
                New Password
            </h4>
            <br>
            <input type="password" name="new_password" minlength="5">
            <br>
            <h4>
                Confirm Password
            </h4>
            <br>
            <input type="password" name="confirm_password" minlength="5">
            <br>
            <br>
            <button type="submit" id="submitbutton" name="submit"><i class="fas fa-check-circle fa-4x"></i></button>
        </form>
        ';

    ## Contains client-side operations based on javascript.
	include("HTMLParts/HTML_BOTTOM.php");
?>