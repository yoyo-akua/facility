<?php 
	/*
	## Contains global variables and functions which are needed within this page.
	## Contains also HTML/CSS structure, which styles the graphical user interface in the browser.
	*/
    include("HTMLParts/HTML_HEAD.php");

    $from=$_GET['from'];
    $to=$_GET['to'];


    if(!empty($_GET['all'])){
        $all='on';
    }else{
        $all='';
    }

    if(!empty($_GET['anc'])){
        $anc='on';
    }else{
        $anc='';
    }

   

   echo'
        <form action="graphs.php" method="get">
            <b>Timeframe:</b><br>
            from<input type=date name="from" value="'.$from.'"  max="'.$today.'">
            to<input type=date name="to" value="'.$to.'" max="'.$today.'"><br>

            
            <input type="checkbox" name="all"';
            if(! empty($_GET['all'])){
                echo'checked="checked"';
            }
            echo'>all patients<br>

            <input type="checkbox" name="anc"';
            if(! empty($_GET['anc'])){
                echo'checked="checked"';
            }
            echo'>anc clients<br>

            <input type="submit" value="submit"><br>
        </form>';



    echo'<img src="graph.php?from='.$from.'&to='.$to.'&all='.$all.'&anc='.$anc.'">'; 
    

    

?>