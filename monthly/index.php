<?php
require_once 'Functions.php';
$fun = new Functions();

$endpoint = $_GET[ "endpoint" ];
$startdate = $_GET[ "strtdate" ];
$enddate = $_GET[ "enddate" ];

if($endpoint==0){
    echo $fun -> dataSumMrchnt($startdate,$enddate);
}
else if($endpoint==1){
    echo $fun -> dataSeperateMrchnt($startdate,$enddate);
}
else "Failure";



?>