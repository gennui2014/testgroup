<?php
header("HTTP/1.1 200 OK");

$link = mysqli_connect('f0229431.xsph.ru', 'f0229431_root', 'admin', "f0229431_root");

if (!$link) {
    exit;
}

if (!isset($_REQUEST)) {
    return;
}

$confirmationToken = 'c06880a7';
$token = '7b45b786905029b4ffc0588e8f4ac23c1cfc2a270536ab6e2005198f0d550821354004a88e703a1e57ed2';
$secretKey = 'TestADMIN';

$data = json_decode(file_get_contents('php://input'));

if($data->secret !== $secretKey && $data->type != 'confirmation')
    return;

switch ($data->type) {
    case 'confirmation':
        echo $confirmationToken;
        break;

    case 'message_new':
		echo 'ok';
		$idCurrUser = $data->object->user_id;
		$bodyText = intval($data->object->body);
		$request_params = "null";
		
		if($idCurrUser == "95265482" || $idCurrUser == "89481221"){
			$sql = "UPDATE countsmart SET count='".$bodyText."'";

			
			if (mysqli_query($link, $sql)) {
				$request_params = array(
				'user_id' => $data->object->user_id,
				'message' => 'Количество изменено на: '.$bodyText,
				'access_token' => $token,
				'v' => '5.69'		
				);
			} else {
				$request_params = array(
				'user_id' => $data->object->user_id,
				'message' => 'Ошибка: '.mysqli_error($link),
				'access_token' => $token,
				'v' => '5.69'		
				);
			}
			
			file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));		
		} else
		{
			$request_params = array(
				'user_id' => $data->object->user_id,
				'message' => "Вам запрещен доступ",
				'access_token' => $token,
				'v' => '5.69'		
				);
				
			file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));
		}	
        break;
}
?>