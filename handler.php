<?php
$link = mysqli_connect('f0229431.xsph.ru', 'f0229431_root', 'admin', "f0229431_root");

if (!$link) {
    echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
    echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

if (!isset($_REQUEST)) {
    return;
}

//Строка для подтверждения адреса сервера из настроек Callback API
$confirmationToken = 'c06880a7';

//Ключ доступа сообщества
$token = '7b45b786905029b4ffc0588e8f4ac23c1cfc2a270536ab6e2005198f0d550821354004a88e703a1e57ed2';

// Secret key
$secretKey = 'TestADMIN';

//Получаем и декодируем уведомление
$data = json_decode(file_get_contents('php://input'));

// проверяем secretKey
if($data->secret !== $secretKey && $data->type != 'confirmation')
    return;

//echo $data;

//Проверяем, что находится в поле "type"
switch ($data->type) {
    //Если это уведомление для подтверждения адреса сервера...
    case 'confirmation':
        //...отправляем строку для подтверждения адреса
        echo $confirmationToken;
        break;

    //Если это уведомление о новом сообщении...
    case 'message_new':
		$idCurrUser = $data->object->user_id;
		$bodyText = intval($data->object->body);
		$request_params = "null";
		
		if($idCurrUser == "321321 95265482!"){
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
	
		echo 'ok';
        break;
}
?>