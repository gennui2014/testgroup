<?php

if (!isset($_REQUEST)) {
    return;
}

echo "sdfghg";

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
		$request_params = array(
		'user_id' => $data->object->user_id,
		'message' => 'Test',
		'access_token' => $token,
		'v' => '5.8'		
		);
		
        file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));
		
		return 'ok';
        /*//...получаем id его автора
        $userId = $data->object->user_id;
        //затем с помощью users.get получаем данные об авторе
        $userInfo = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$userId}&v=5.80"));

        //и извлекаем из ответа его имя
        $user_name = $userInfo->response[0]->first_name;

        //С помощью messages.send и токена сообщества отправляем ответное сообщение
        $request_params = array(
            'message' => "{$user_name}, ваше сообщение зарегистрировано!<br>".
                            "Мы постараемся ответить в ближайшее время.",
            'user_id' => $userId,
            'access_token' => $token,
            'v' => '5.80'
        );

        $get_params = http_build_query($request_params);

        file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);

        //Возвращаем "ok" серверу Callback API
        echo('ok');
*/
        break;

    // Если это уведомление о вступлении в группу
    case 'group_join':
	/*
        //...получаем id нового участника
        $userId = $data->object->user_id;

        //затем с помощью users.get получаем данные об авторе
        $userInfo = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$userId}&v=5.8"));

        //и извлекаем из ответа его имя
        $user_name = $userInfo->response[0]->first_name;

        //С помощью messages.send и токена сообщества отправляем ответное сообщение
        $request_params = array(
            'message' => "Добро пожаловать в наше сообщество МГТУ им. Баумана ИУ5 2016, {$user_name}!<br>" .
                            "Если у Вас возникнут вопросы, то вы всегда можете обратиться к администраторам сообщества.<br>" .
                            "Их контакты можно найти в соответсвующем разделе группы.<br>" .
                            "Успехов в учёбе!",
            'user_id' => $userId,
            'access_token' => $token,
            'v' => '5.8'
        );

        $get_params = http_build_query($request_params);

        file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);

        //Возвращаем "ok" серверу Callback API
        echo('ok');
	*/
        break;
}
?>