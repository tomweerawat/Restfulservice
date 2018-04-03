<?php
function dbcon(){
  $server='148.72.232.174:3306';
	$user='Notkak199412';
	$pass='0959518332';
	$dbname='findhouse';
	return new mysqli($server,$user,$pass,$dbname);
}
 $db = dbcon();

 $location = $_GET["price"];

 $test = explode(",", $location);
 // $lat=$test[0]+1-1;
 // $lng=$test[1]+1-1;
 $proptype = $test[0];
 $price = $test[1];
 $ptype = $test[2];
 // $price1 = "ราคา 1500001 - 2000000";
if (strpos($price, '1500001') !== false){
  $data = 1;
  echo $data;
}
else if(strpos($price, '500001') !== false){
  $data = 2;
  echo $data;
}
else if(strpos($price, '1000001') !== false){
  $data = 3;

}
else if(strpos($price, '2000001') !== false){
  $data = 4;
  echo $data;
}
else if(strpos($price, '3000001') !== false){
  $data = 5;
  echo $data;
}
else if(strpos($price, '4000001') !== false){
  $data = 6;
  echo $data;
}
else {
  $data = 7;
  echo $data;
}

  // echo "<pre>";print_r($price1);exit();
  switch($data){
    case "1":
      $rate="`price`<500000";

      break;
    case "2":
      $rate="`price`>=500001 AND `price`<=1000000";
      break;
    case "3":
      $rate="`price`>=1000001 AND `price`<=2000000";

      break;
    case "4":
      $rate="`price`>=2000001 AND `price`<=3000000";
      break;
    case "5":
      $rate="`price`>=3000001 AND `price`<=4000000";
      break;
    case "6":
      $rate="`price`>=4000001 AND `price`<=5000000";
      break;
    case "7":
      $rate="`price`>=5000001";

      break;
  }
  if($data==3){
    $sqlstr="SELECT *FROM property
    INNER JOIN address ON address.address_id = property.address_id
     INNER JOIN user ON user.user_id = property.user_id";
     $query=$db->query($sqlstr);
     while ( $row = $query->fetch(PDO::FETCH_ASSOC)) {
     echo $row;
     }



    }
mysqli_set_charset($db,"utf8");



 ?>
