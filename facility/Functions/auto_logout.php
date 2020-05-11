<?php 
    /*
	## Sessions are used to store variables which are maintained for a certain time period and are usable in the entire application.
	## Reset the session after 30 minutes of inactivity.
    */
    global $_SESSION;
	if(isset($_SESSION['LAST_ACTIVITY']) AND (time()-$_SESSION['LAST_ACTIVITY']>600)){
		session_unset();
		session_destroy();
		
		## Start a new session and set default values.
		ini_set("session.use_trans_sid",true);
		session_start();
        $_SESSION['cookie_support']=0;
    }

    ## Call this if-branch, if no session has been set yet.
    else if(! isset($_SESSION['LAST_ACTIVITY'])){
        ## Start a new session and set default values.
        ini_set("session.use_trans_sid",true);
        session_start();
        $_SESSION['cookie_support']=0;

    }
    $_SESSION['LAST_ACTIVITY']=time();
    
?>