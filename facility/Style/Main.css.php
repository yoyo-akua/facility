<?php

header('Content-type: text/css');
include("./../Objects/DB.php");
include("./../Objects/Settings.php");

$colours=Settings::getColours();

## Initialise variables with the colours.
$main="rgb($colours->main)";
$h1="rgb($colours->h1)";
$light="rgb($colours->light)";
$input="rgb($colours->input)";
$menu="rgb($colours->menu)";
$tile="rgb($colours->tile)";
$hover="rgb($colours->hover)";
?>

/*the container must be positioned relative:*/
.autocomplete {
  position: relative;
  display: inline-block;
}

.autocomplete-items {
	border: 1px solid #d4d4d4;
	width: 100%;
	max-width: 220px;
  border-bottom: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}

.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff; 
  border-bottom: 1px solid #d4d4d4; 
}

/*when hovering an item:*/
.autocomplete-items div:hover {
  background-color: #e9e9e9; 
}

/*when navigating through the items using the arrow keys:*/
.autocomplete-active {
  background-color: <?php echo $main; ?> !important;
  color:white;
}

ul {
	list-style: none;
	padding: 0px;
	margin: 0px;
	z-index:1;
}
ul.right>li>ul{
	right:0px;
	margin-right:30%;
}
ul.right>li{
	right:0;
	float:right;
}

ul li {
	 display: block;
	 position: relative;
	 float: left;
	 border:none;
	 z-index:1;
}
li ul {
	display: none;
	z-index:1;
}
ul li a {
	display: block;
	background: <?php echo $main; ?>;
	padding-left: 10%;
	padding-top:5%;
	padding-bottom:5%;
	padding-right:0%;
	text-decoration: none;
	white-space: nowrap;
	color: black;
	width:100%;
	z-index:1;
}

ul li a:hover {background: <?php echo $main; ?>;}
  li:hover>ul {display: block; position: absolute;}
  li:hover li {float: none;}
  li:hover a {background: <?php echo $menu; ?>;border:0.5px solid white;box-shadow: 3px 3px 6px gray;}
  
li:hover li a:hover {
	background: <?php echo $hover; ?>;
	color: white;
	box-shadow: inset 2px 2px 3px darkgray;
	text-shadow: 1px 1px 1px darkgray;
}
  
  #drop-nav li ul li{border-top: 0px;}
	ul ul ul {left:110%;top:0}

.menu-left{
	padding-right:10px;
	color:<?php echo $tile;?>;
	text-shadow:1px 1px 1px white;
	position:absolute;
}
.menu-text{
	margin-right:25px;
	font-family:Helvetica;
	font-weight:lighter;
	margin-left:30px;
}
.menu-right{
	color:white;
	position:absolute;
	left:100%;
	top:25%;
	padding-right:-20%;
}
.tooltip {
  position: relative;
  line-height:0px;
  text-decoration:underline;
  text-decoration-color:lightgrey;
  display: inline-block;
  word-wrap:break-word;
  white-space:nowrap;
}

.tooltip .tooltiptext{
	visibility: hidden;
	background-color: black;
	color: #fff;
	white-space:nowrap;
	text-align: center;
	padding: 10px;
	border-radius: 6px;
	left:100%;
	position: absolute;
	z-index: 1;
}
.tooltip:hover .tooltiptext {
  visibility: visible;
}
 .tooltip .tooltiptext::after {
  content: " ";
  position: absolute;
  top: 50%;
  right: 100%;
  margin-top: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: transparent black transparent transparent;
}

.tooltip .tooltiptext {
  opacity: 0;
  transition: opacity 1s;
}

.tooltip:hover .tooltiptext {
  opacity: 1;
}
div.fullscreen{
	margin:3%;
	float:left;
	width:100%;
}
div.columnright{
	float:right; 
	width:44%;
	margin-left:3%;
	margin-right:3%;
}
div.columnleft{
	float:left; 
	width:44%;
	margin-left:3%;
	margin-right:3%;	
}
.popupbackground>div{
	position:fixed;
	top:45%;
	right:40%;
	width:20%;
	padding: 15px;
	text-align:center;
	background-color:Gainsboro;
	border:3px double <?php echo $main; ?>;
	text-align:center;
	z-index:4;
}
.popupbackground{
	background-color:grey;
	position:fixed;
	z-index:3;
	width:100%;
	height:100%;
}
#submitconsult{
	position:fixed;
	color: <?php echo $main; ?>;
	bottom: 5%;
	left: 25%;
	z-index:2;
	text-shadow: 2px 2px rgb(240,240,240);
}
#submitconsult:hover{
	color: <?php echo $h1; ?>;
}

#submitbutton{
	color: <?php echo $main; ?>;
	text-shadow: 2px 2px rgb(240,240,240);
}
#submitbutton:hover{
	color: <?php echo $hover; ?>;
} 
#ban_diagnosis:hover{
	color:grey;
}
.columnleft>div{
	margin-top:10px;
}
.columnleft>div>label{
	font-weight:bold;
}
.columnleft>details>div>label{
	font-weight:bold;
}
.columnright>div{
	margin-top:10px;
}
.columnright>div>label{
	font-weight:bold;
}
.columnright>details>div>label{
	font-weight:bold;
}
div.normal{
	float:left; 
	width:100%;
}
summary>h2{
	display:inline;
}
summary>h1{
	display:inline;
}
details{
	margin-left:20px;
}
details>input{
	margin-left:35px;
}
details>div>select{
	margin:35px;
}
summary>h2>div{
	display:inline;
	margin:0px;
}
a.button{
	background-color: <?php echo $main; ?>;
	color: White;
	border:1px solid black;
	margin-left:10px;
	margin-right:10px;
	padding:3px;
	height:30px;
	border-radius:5px;
}
a.button:hover{
	box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.32);
	text-shadow: 2px 2px 1px darkgrey;
}
input.smalltext{
	width:80px;
}
td.columns{
	width:200px;
	border:none;
	text-align:left;
	vertical-align:top;
}

.header {
	top:0px;
    left:0px;
    width:100%;
    height:40px;
    background-color:<?php echo $main; ?>;
	margin:0px;
	z-index:1;
	box-shadow:1px 1px 6px #a0a0a0;	
}
/* Page content */
.content {
  padding: 16px;
}
.sticky {
  position: fixed;
  top: 0px;
  left:0px
  width: 100%;
}
.sticky + .content {
  padding-top: 102px;
}
#home{
	padding:5px;
	color:white;
	font-size:26px;
}

#home.shadow:hover,.shadow:hover{
	color:<?php echo "rgba($colours->light,0.8)"; ?>;
	text-shadow: 1px 1px 1px #def, 0 0 0 #000, 1px 1px 1px #def;
}
#protocol_search{
	font-size:50px;
	color:rgb(70,70,70);
	margin:5px 20px 5px 10px;
}
#protocol_search:hover{
	color:rgba(105,105,105,0.8);
	text-shadow: 1px 1px 1px #def, 0 0 0 #000, 1px 1px 1px #def;
}
.smallsearch{
	font-size:20px;
	padding:2px;
	color:black;
}
#department_search{
	font-size:40px;
	margin:10px;
}
.smallsearch:hover,#department_search:hover{
	color:<?php echo "rgba($colours->main,0.8)"; ?>;
	text-shadow: 1px 1px 1px #def, 0 0 0 #000, 1px 1px 1px #def;
}
table{
	margin-left:20px;
	border-spacing:0px;
}
tr.receivedtable{
	background-color:<?php echo $menu; ?>;
}
.receivedtable > td{
	border-bottom:1.5px solid <?php echo $main; ?>;
	border-top:1.5px solid <?php echo $main; ?>;
}
.fa-arrow-up{
	position:fixed; 
	bottom:0px;
	right:0px;
	color:<?php echo $main; ?>;
	margin:10px;
}
.fa-arrow-up:hover{
	color:<?php echo $h1; ?>;
}
tr.adjustmenttable{
	background-color:<?php echo $input; ?>;
}
.beam{
	white-space:nowrap;
	background-color:Gainsboro;
	margin:0px;
	border:1px solid DarkGrey;
	width:500%;
}
.beam>table{
	margin:0px;
}
tr.beam >td{
	border:0px;
	text-align:left;
	vertical-align:top;
	background-color:Gainsboro;
	margin:0px;
}
tr.beam >td>details>summary{
	color:Gainsboro;
	text-align:center;
}
tr.beam >td>details>summary>div{
	display:inline;
}
tr.beam >td>details>summary>div>div{
	margin:0px;
	display:inline-block;
	vertical-align:middle;
	color:White;
	font-size:12px;
	margin:0px;
}
.smalltile{
    background-color:DarkGray;
	box-shadow:1px 1px 6px #a0a0a0;	
    width: 50px;
	display:inline-block;
	height: 50px;
    margin: 0px;
    text-align: center;
    position: relative;
    padding: 5px;
    font-size: 15px;
	border-radius:12%;
	border-right: solid 1.5px grey;
	border-bottom: solid 1.5px grey;
}
.smalltile:hover{
	background-color:silver;
	box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.3);
	text-shadow: 2px 2px 1px grey;
}
tr.emptytable >td{
	border:0px;
}
.emptytable >td >input{
	margin:0px;
}
tr.lasttable{
	background-color:DarkGray;
	color:White;
}
.lasttable >td >input{
	background-color:Silver;
	border:0.5px solid DimGray;
	color:white;
	font-style:italic;
}
.lasttable >td >input[type="submit"]{
	background-color: <?php echo $main; ?>;
	color: White;
	border:3px double White;
	margin:0px;
	padding:3px;
	font-style:normal;
}
body{
	margin:0px;
	font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;;
	font-size:15px;
	line-height:1.5;
	z-index:0;
	margin-bottom:30px;
}
td{
	padding: 5px;
	text-align:center;
	border:none;
	border-left:1.5px solid #c7c7c7;
	border-top:1.5px solid #c7c7c7;
	font-size:15px;
}
td > input{
	text-align:center;
}
th{
	border:none;
	border-bottom:1.5px solid #c7c7c7;
	border-left:1.5px solid #c7c7c7;
	padding: 5px;
	vertical-align:bottom;
}
a{
	color:<?php echo $main; ?>;
	text-decoration:none;
}
a:hover{
	color:<?php echo $h1; ?>;
}
td.labtable{
	border:none;
	padding: 10px;
	text-align: left;
	vertical-align: top;
	line-height:1.3;
	margin-left:0px;
}
.labtable >h2{
	margin-left:0px;
}

h1{
    color:<?php echo $h1; ?>;
    text-align: center;
	font-family: Helvetica;
	font-weight: lighter;
	line-height: 1.1;
	text-shadow: 2px 2px rgb(240,240,240);
	font-size: 35px;
}
h2{
    color:<?php echo $main; ?>;
	margin:25px 0px 7px 20px ;
	font-family:Times;
}
h3{
	font-weight:bold;
	font-size:20px;
	color:<?php echo $h1; ?>;
}
h4{
	color:<?php echo $main; ?>;
	margin:0px;
	font-weight:bold;
	padding:0px;
	line-height:1.5;
	display:inline;
	font-size:17px;	
}
h5{
	color:<?php echo $main; ?>;
	margin:0px;
	margin-left:20px;
	font-weight:normal;
	padding:0px;
	line-height:1.5;
	display:inline;
	font-size:15px;
}
h3{
	margin-top:15px;
	margin-bottom:3px;
}
button{
	background-color: transparent;
	padding: 0;
	border: none;
}
input{
	background-color:<?php echo $input; ?>;
	border:1px solid #ccc;
	font-size:15px;
	font-family:courier;
	max-width:220px;
	border-radius:3px;
	height:20px;
}
input:hover{
	box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.10);
}
textarea:hover{
	box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.10);
	background-color:white;
}
textarea{
	border-radius: 5px;
	border:1px solid #ccc;
	font-family:courier;
}
input:focus{
	border-color: <?php echo $hover; ?>;
	outline: 0;
	box-shadow: inset 0 3px 5px rgba(0,0,0,0.1),0 0 5px <?php echo $hover; ?>;
}
input[type="submit"]{
	background-color: <?php echo $main; ?>;
	color: White;
	border:1px solid black;
	margin:10px;
	padding:3px;
	height:30px;
	border-radius:5px;
}
input[type="submit"]:hover{
	box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.32);
	text-shadow: 2px 2px 1px darkgrey;
}
input[type="image"]{
	background-color:transparent;
	border:none;
	padding:15px;
	height:100%;
}
input[type="image"]:hover{
	box-shadow:none;
	filter: invert(40%);
}
input[type="number"]{
	width:80px;
}
.inlinedetails >details{
	display:inline;
}
.inlinedetails >details[open]{
	display:block;
}
.tableright{
	margin:10px;
	position:fixed;
	top:45px;
	right:10px;	
	padding: 15px;
	text-align:center;
	background-color: <?php echo "rgba($colours->light,0.4)"; ?>;
	border:3px double <?php echo $main; ?>;
	color: <?php echo $main; ?>;
	text-align:center;
	z-index:2;
	border-radius:8px;
    box-shadow: 3px 3px 6px gray;
}
.tableright:hover{
    background-color: <?php echo "rgba($colours->light,0.7)"; ?>;
}
.tableright>h3{
	margin-top:7px;
	margin-bottom:7px;
}
.tableright>h2{
	color:firebrick;
	font-weight:bold;
	display:inline;
	font-size:30px;
	margin:20px 0px 20px 0px;
}
.tableright>a{
	background-color: <?php echo $main; ?>;
	color: White;
	border:1px solid black;
	margin-left:10px;
	margin-right:10px;
	padding:3px;
	height:30px;
	border-radius:5px;
}
.tableright>a:hover{
	box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.32);
	text-shadow: 2px 2px 1px darkgrey;
}
.tableright >input{
	background-color:white;
	border:0.5px solid <?php echo $main; ?>;
	text-align:center;
}
.tableright >input[type="submit"]{
	background-color: <?php echo $main; ?>;
	color: White;
	border:1px solid black;
	margin:10px;
	padding:3px;
	font-style:normal;
}
.tableright >input[type="image"]{
	background-color:transparent;
	border:0px;
	padding:0px;
}
#linkbutton{
	background-color:transparent;
	border:0px;
	padding:0px;
	color:<?php echo $main; ?>;
}
.inputform{
    margin: 20px;
    position: relative;
    left: 10px;
}
.inputform details>form>div{
	margin-top:10px;
}
.inputform >form>div>label{
	font-weight:bold;
}
.inputform >form>details>div>label{
	font-weight:bold;
}
label{
	font-weight:bold;
}

.box{
    background-color:<?php echo $light; ?>;
    left:20%;
    width: 60%;
    margin-top: 20px;
    text-align: center;
    position: relative;
    padding: 5px;
    font-size: 20px;
    color: <?php echo $main; ?>;
	border-radius:5px;
	border:1px solid lightgray;
}
.box:hover {
box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.32);
background-color: <?php echo $input; ?>;
text-shadow: 2px 2px 1px lightgray;
}
.middle{
	position:relative;
	text-align:center;
}
.tile{
	background-color:<?php echo $tile; ?>;
	box-shadow:2px 2px 12px #a0a0a0;
	width: 100px;
	display:inline-block;
	height: 100px;
	margin: 10px;
	position: relative;
	padding: 5px;
	font-size: 20px;
	color: white;
	border-radius:12%;
	vertical-align:middle;
	border-right: solid 2px black;
	border-bottom: solid 2px black;
}
.tile:hover{
	background-color:<?php echo $hover; ?>;
	box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.5);
	text-shadow: 4px 4px 2px grey;
}
.middletile{
    background-color:<?php echo $light; ?>;
	box-shadow:1px 1px 6px #a0a0a0;	
    width: 70px;
	display:inline-block;
	height: 70px;
    margin: 0px;
    text-align: center;
    position: relative;
    padding: 5px;
    font-size: 15px;
	border-radius:12%;
	border-right: solid 1.5px <?php echo $hover;?>;
	border-bottom: solid 1.5px <?php echo $hover;?>;
}
.middletile:hover{
	background-color:<?php echo $input; ?>;
	box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.3);
	text-shadow: 2px 2px 1px lightgrey;
}
.middletile>a{
	display:inline-block;
	vertical-align:middle;
	color:<?php echo $main; ?>;
	text-shadow: 0.25px 0.25px 1.5px CadetBlue;
}
.onlytop tr td{
	border-left:none;
}
.onlytop tr th{
	border-left:none;
}
div.center{
	left:50%;
	top:50%;
	position:absolute;
	width:300px;
	height:50px;
	margin-left:-150px;
	margin-top:-25px;
	text-align:center;

}
.tile>a{
	display:inline-block;
	vertical-align:middle;
	color:white;
	text-shadow: 0.5px 0.5px 3px black;
}
.box>a{
	font-style:normal;
	color:black;
}
:active{
	color:#000000;
	text-decoration:none;
}
.badge{
	align-items: center;
	background: #eef0f3;
	border-radius: 5rem;
	font-size: 80%;
	height: 1.2rem;
	line-height: .8rem;
	margin: .1rem;
	overflow: hidden;
	padding: .2rem .4rem;
	text-decoration: none;
	text-overflow: ellipsis;
	vertical-align: middle;
	white-space: nowrap;
	color:dimgrey;
}
.line>input{
	margin-bottom:10px;
	margin-left:0px;
}
[type="file"] {
  height: 0;
  overflow: hidden;
  width: 0;
}
