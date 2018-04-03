<?php
require_once 'DBOperations.php';
class Functions{
private $db;
public function __construct() {
      $this -> db = new DBOperations();
}


public function dataSumMrchnt($startdate, $enddate){
  $db = $this -> db;
  $result =  $db -> dataSumMrchnt($startdate, $enddate);
    header('Content-Type: application/json;charset=utf-8');
   return json_encode($result,true);
}

    public function dataSeperateMrchnt($startdate, $enddate){
        $db = $this -> db;
        $result =  $db -> getDataSeperateMrchnt($startdate, $enddate);
        header('Content-Type: application/json;charset=utf-8');
        return json_encode($result,true);
    }


}
