<?php
error_reporting(0);
header('Content-type: application/json');
$phone = $_GET['phone'];
$token = $_GET['token'];


$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://m.mservice.io/hydra/v2/user/noti',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>
  json_encode([
    "userId" => $phone,
    "fromTime" => (time()-86400)*1000,
    "toTime" => time()*1000,
    "limit" => 100,
    "cursor" => ""
  ]),
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '.$token,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
curl_close($curl);
$response = json_decode($response);
$arr =[];
foreach($response->message->data->notifications as $key => $data )
{
  if($data->refId == 'receive_money_p2p'){
    $extra = json_decode($data->extra);
    $arr[] = [
      "tranId" =>  $extra->tranId,
      "partnerId" => $extra->partnerId,
      "partnerName" => $extra->partnerName,
      "amount" => $extra->amount,
      "comment" => $extra->comment,
      "time" => $data->time

    ];
    
  }
}

echo json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
