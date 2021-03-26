<?php
	/*
	## Contains global variables which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
    include("HTMLParts/HTML_HEAD.php");
    
    /*
	## Variables $from and $to are initialised which are used to set the time frame for the search.
	## They are retrieved from the url with which the page is called.
	*/
	$from=$_GET['from'];
    $to=$_GET['to'];
    
    $query_not_entered="SELECT * FROM patient pa,protocol pr,visit v WHERE pa.patient_ID=v.patient_ID AND pr.visit_ID=v.visit_ID AND pr.protocol_ID NOT IN (SELECT protocol_ID FROM diagnosis_ids) AND checkin_time BETWEEN '$from' AND '$to 23:59:59' AND onlylab=0 GROUP BY v.visit_ID";
	$result_not_entered=mysqli_query($link,$query_not_entered);
	$not_entered=array();
	$number=0;
	while($row=mysqli_fetch_object($result_not_entered)){
		$not_entered[]=$row->Name.' ('.$row->OPD.')\n';
		$number++;
	}
    
    echo "<div class='center'>
            Please be patient. Loading report...<br>
            <font style='color:grey'>This can take a few moments.</font>
        </div>";

	if(! empty($not_entered)){
        echo'<script type="text/JavaScript">;
					if(! window.confirm("The following ('.$number.') clients have not been entered completely:\n'.implode('',$not_entered).'")){
						window.history.back();
					}else{
						window.location.href="consulting_report.php?from='.$from.'&to='.$to.'";
					}
                </script>';
    }else{
        echo '<script>window.location.href=("consulting_report.php?from='.$from.'&to='.$to.'")</script>';
    }
?>