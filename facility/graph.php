<?php
include ("Objects/DB.php");

require_once ('Style/Graphs/src/jpgraph.php');
require_once ('Style/Graphs/src/jpgraph_line.php');
require_once ('Style/Graphs/src/jpgraph_date.php');
require_once ('Style/Graphs/src/jpgraph_bar.php');

$from=$_GET['from'];
$to=$_GET['to'];

$time=$_GET['time'];
if($time!=='day'){
    $type='bar';
}else{
    $type='line';
}

$all=array();
$anc=array();

$date=$from;

$first=true;

$sundaystamp=strtotime("next Sunday",strtotime($from));
$sunday=date("Y-m-d",$sundaystamp);

$lastdaystamp=strtotime(date("Y-m-t",strtotime($from)));
$lastday=date("Y-m-d",$lastdaystamp);

while (strtotime($date) <= strtotime($to)) {
    $queries=array();
    if(!empty($_GET['all'])){
        
        if($time=='day'){
            $queries['all']="SELECT  DATE(VisitDate) Date, COUNT(DISTINCT patient_ID) AS count FROM protocol WHERE VisitDate LIKE '$date%' GROUP BY DAY(VisitDate)";
        }else if($time=='week'){
            if($first){
                $queries['all']="SELECT  DATE(VisitDate) Date, COUNT(DISTINCT patient_ID) AS count FROM protocol WHERE VisitDate BETWEEN '$from' AND '$sunday 23:59:59'  AND VisitDate<='$to 23:59:59' GROUP BY YEARWEEK(VisitDate,1)";
            }else{
                $queries['all']="SELECT  DATE(VisitDate) Date, COUNT(DISTINCT patient_ID) AS count FROM protocol WHERE VisitDate BETWEEN '$date' AND ('$date' + INTERVAL + 7 DAY) AND VisitDate<='$to 23:59:59'  GROUP BY YEARWEEK (VisitDate,1)";
            }
        }else if($time=='month'){
            if($first){
                $queries['all']="SELECT  DATE(VisitDate) Date, COUNT(DISTINCT patient_ID) AS count FROM protocol WHERE VisitDate BETWEEN '$from' AND '$sunday 23:59:59'  AND VisitDate<='$to 23:59:59' GROUP BY MONTH(VisitDate)";
            }else{
                $queries['all']="SELECT  DATE(VisitDate) Date, COUNT(DISTINCT patient_ID) AS count FROM protocol WHERE VisitDate BETWEEN '$date' AND ('$date' + INTERVAL + 7 DAY) AND VisitDate<='$to 23:59:59'  GROUP BY MONTH (VisitDate,1)";
            }
            $queries['all']="SELECT  DATE(VisitDate) Date, COUNT(DISTINCT patient_ID) AS count FROM protocol WHERE VisitDate BETWEEN '$date' AND ('$date' + INTERVAL + 1 MONTH) AND VisitDate<='$to 23:59:59' GROUP BY DAY(VisitDate)";
        }
    }
    if(! empty($_GET['anc'])){
        if($time=='day'){
            $queries['anc']="SELECT  DATE(VisitDate) Date, COUNT(DISTINCT patient_ID) AS count FROM protocol WHERE VisitDate LIKE '$date%' AND ANC_ID!='' GROUP BY DATE(VisitDate)";
        }else if($time=='week'){
            if($first){
                $queries['anc']="SELECT  DATE(VisitDate) Date, COUNT(DISTINCT patient_ID) AS count FROM protocol WHERE ANC_ID!='' AND VisitDate BETWEEN '$from' AND '$sunday 23:59:59'  AND VisitDate<='$to 23:59:59' GROUP BY YEARWEEK(VisitDate,1)";
            }else{
                $queries['anc']="SELECT  DATE(VisitDate) Date, COUNT(DISTINCT patient_ID) AS count FROM protocol WHERE ANC_ID!='' AND VisitDate BETWEEN '$date' AND ('$date' + INTERVAL + 7 DAY) AND VisitDate<='$to 23:59:59'  GROUP BY YEARWEEK (VisitDate,1)";
            }
        }
    }
    foreach($queries AS $data=>$query){
        $result=mysqli_query($link,$query);
        
        if(mysqli_num_rows($result)!==0){
            $row=mysqli_fetch_object($result);
            $$data[]=$row->count;
        }else{
            $$data[]=0;
        }
    }
    if($type=='line'){
        $label[]=strtotime($date);
    }else if($time=='week'){
        if($first){
            $label[]=date('d/m/y',strtotime($from))." -\n".date('d/m/y',$sundaystamp);
        }else{
            $label[]=date('d/m/y',strtotime($date))." -\n".date('d/m/y',strtotime('+6 day',strtotime($date)));
        }
    }else if($time=='month'){
        $label[]=date('M \'y',strtotime($date));
    }
    
    if($time=='day'){
        $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
    }else if($time=='week'){
        if($first){
            $date = date ("Y-m-d", strtotime("next Monday", strtotime($from)));
            $first=false;
        }else{
            $date = date ("Y-m-d", strtotime("+7 day", strtotime($date)));
        }
    }else if($time=='month'){
        $date = date ("Y-m-d", strtotime("+1 month", strtotime($date)));
    }

}





// Setup the graph
$graph = new Graph(1000,500);
if($type=='line'){
    $graph->SetScale("datlin");
}else{
    $graph->SetScale('textint');
}


$theme_class=new UniversalTheme;

$graph->SetTheme($theme_class);
$graph->img->SetAntiAliasing(false);
$graph->SetBox(false);

$graph->SetMargin(60,20,20,80);

$graph->img->SetAntiAliasing();

$graph->yaxis->HideZeroLabel();
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);

$graph->xgrid->Show();
$graph->xgrid->SetLineStyle("solid");

$count=count($label);
if($time=='day'){
    $form='d/m/y';
    $graph->xaxis->scale->SetDateFormat($form);
    if($count>20){
        $number=60*60*24*$count/20;
        $graph->xaxis->scale->ticks->Set($number);
    }
}




if(!empty($_GET["all"])){
    if($type=='line'){
        $all = new LinePlot($all,$label);
        $graph->xaxis->SetLabelAngle(30);
    }else if($type=='bar'){
        $all= new BarPlot($all);
        $all->SetFillColor('#046e8f');
        $all->SetWidth(500/$count);
        $graph->xaxis->SetTickLabels($label);
        #$graph->xaxis->SetTextLabelInterval(4);
    }
    $graph->Add($all);
    $all->SetColor('#046e8f');
}

if(!empty($_GET['anc'])){
    if($type=='line'){
        $anc = new LinePlot($anc,$label);
    }else if($type=='bar'){
        $anc= new BarPlot($anc);
        $anc->SetFillColor('#a8201a');
        $anc->SetWidth(500/$count);
        $graph->xaxis->SetTickLabels($label);

    }
    $graph->Add($anc);
    $anc->SetColor('#a8201a');
}


// Output line
$graph->Stroke();

?>


