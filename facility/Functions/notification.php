<?php 
/*
## Include files.
## Include Push.php which contains the class Push and all functions related to the notifications table in the database.
## Include the DEFAULTS.php to retrieve the Logo for the notification's icon.
## Include variables.php which declares variables used within this function such $own IP which is used to inquire $own_department.
## Include Departments.php which contains a function to determine all departments for which the user's PC is entered.
*/
include("../Objects/Push.php"); 
include("../defaults/DEFAULTS.php");
include("variables.php");
include("../Objects/Departments.php");

SESSION_START();

## Initialise variable $push with a new object of the push class.
$push = new Push(); 

## Initialise variables $array and $rows which will later on be sent along to the javascript creating the notification and calling this page.
$array=array(); 
$rows=array();

/*
## Initialise variable $activity with the time of the last activity on the PC.
## Based on this information, set the value of $repetition_in in a way that, if the user loaded a page for the last time
##      - more than 10 minutes ago: repeat the notification in 5 minutes
##      - more than 5 minutes ago: repeat the notification in 2 minutes
##      - more than 1 minute ago: repeat the notification in 1 minute
*/
$activity=$_SESSION['LAST_ACTIVITY'];

if($activity<(time()-600)){
    $repetition_in=300;
}else if($activity<(time()-300)){
    $repetition_in=120;
}else if($activity<(time()-60)){
    $repetition_in=60;
}

/*
## Initialise variable $nexttime 
##      - either using $repetition_in to assign the proper time value for the next repetition 
##      - or setting it "0" in case the user has been online within the last minute.
##        (if $nexttime is set 0 the notification will be deleted from the database.)
*/
if(isset($repetition_in)){
    $nexttime = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s'))+$repetition_in);
}else{
    $nexttime=0;
}

/*
## Initialise variable $_SESSION['Push'] in case it isn't set yet.
## This variable will be used to remember all notifications which are supposed to be repeated (since the user hasn't been online within the last minute)
## and check with the next calling of this script (20 seconds later), if the user has come online in the meantime. 
## If so (and the user came online directly after the notification was sent - so he should have seen it), delete the particular notification 
## and prevent it from being shown again (cf. end of the script).
*/
if(! isset($_SESSION['Push'])){
    $_SESSION['Push']=array();
}

## Initialise variable $own_department, calling the function getDepartment() to inquire the departments which are assigned to the user's PC.
$own_department=Departments::getDepartment();

## Run this loop once for each department that the user's PC belongs to.
foreach($own_department AS $department){

    ## Get all notifications for that department and save them in $notifList.
    $notifList = $push->listNotificationUser($own_IP); 

    ## Initialise variable $record which is used to count the notifications.
    $record = 0;

    ## Run this loop once for each notification entry saved in $notifList.
    foreach ($notifList as $key) {
       
        ## Retrieve title and text message and assign it to the corresponding variables.
        $data['title'] = $key['title'];
        $data['msg'] = $key['notif_msg'];

        ## Also add the facility's logo which is included by Departments.php at the beginning of the page.
        $data['icon'] = $LOGO;

        ## Add all data of the notification to the $rows array.
        $rows[] = $data;
        
        ## In case the notification isn't to be shown again anyway, add it to $_SESSION['Push'].
        if($nexttime!==0){
            $_SESSION['Push'][]=$key['notification_ID'];
        }
        
        /*
        ## Update the notification's entry in the database with the time of the next repetition.
        ## If $nexttime is set 0 the notification will be deleted from the database.
        */
        $push->updateNotification($key['notification_ID'],$nexttime);

        ## Increase the count of $record by 1. 
        $record++;
    }
}

## Initialise $array with the number of notifications, the notifications itself and a boolean, if there even are any notifications to be displayed.
$array['notif'] = $rows;
$array['count'] = $record;
$array['result'] = true;

## Send $array to the javascript function calling this page (see: /HTMLParts/JavaScript/notifications.js)
echo json_encode($array);

/*
## As described above, in case the user has come online within the last 20 seconds, 
## delete the notifications which were to be displayed again on the last run of the script (20 seconds ago) from the database.
*/
if($activity>(time()-20)){
    foreach($_SESSION['Push'] AS $last){
        Push::updateNotification($last,0);
    }
    unset($_SESSION['Push']);
}

?>