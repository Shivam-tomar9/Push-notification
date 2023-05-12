<?php
require_once "db_connection.php";
header("Content-type: application/json");

$data = json_decode(file_get_contents('php://input'), true);

if(is_array($data) && isset($data['endpoint'])){
  $selectId = $conn->query("SELECT `id` FROM `push_subscribers` WHERE `endpoint` = '{$data['endpoint']}'");
  
  if($selectId->num_rows == 0 && isset($_GET['subscribe'])){
    //subscribe
    $data['expirationTime'] = floor($data['expirationTime'] / 1000); // Miliseconds to seconds
    $query = $conn->query("INSERT INTO `push_subscribers` (`endpoint`, `expiration_time`, `p256dh`, `authKey`) VALUES ('{$data['endpoint']}', '{$data['expiration_time']}', '{$data['keys']['p256dh']}', '{$data['keys']['auth']}')");

    if($query){
      echo json_encode(['status'=>'ok', 'message'=>'Subscribed']);
    }
    else{
      echo json_encode(['status'=>'error', 'message'=>'Try Again']);
    }
  }
  elseif(isset($_GET['unsubscribe'])){
    //unsubscribe
    $conn->query("DELETE FROM `push_subscribers` WHERE `endpoint` = '{$data['endpoint']}'");
    echo json_encode(['status'=>'ok', 'message'=>'Unsubscribed']);
  }
}
