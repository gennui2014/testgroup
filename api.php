<?php
$link = mysql_connect('localhost', 'root', '');
if (!$link) {
    die('Ошибка соединения: ' . mysql_error());
}

require_once('config.php');

function checkApiError($apiJsonArray) {
    if(in_array('28', $apiJsonArray['error']['error_code'])) { 
        echo '<h2>Ошибка: Сбой авторизации приложения. Срок действия access_token закончен</h2>';
        exit(); 
    } elseif(in_array('17', $apiJsonArray['error']['error_code'])) {
        echo '<h2>Ошибка: Пройдите валидацию клинув на ссылку: </h2><a hreaf="'.$apiJsonArray['error']['redirect_uri'].'">CLICK HERE</a>';
        exit();
    } elseif(in_array('5', $apiJsonArray['error']['error_code'])) {
        echo '<h2>Ошибка: Вы не авторизованы. Введите access_token в config.php</h2>';
        exit();
    } elseif(in_array('6', $apiJsonArray['error']['error_code'])) {
        echo '<h2>Ошибка: Слишком много запросов в секунду</h2>';
        exit();
    } elseif(in_array('14', $apiJsonArray['error']['error_code'])) {
        echo '<h2>Ошибка: Требуется ввод капчи</h2>';
        exit();
    }
}

function getPOST($url, $post) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); //урл сайта к которому обращаемся 
    curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
    curl_setopt($ch, CURLOPT_HEADER, false); //выводим заголовки
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true); //теперь curl вернет нам ответ, а не выведет
    curl_setopt($ch, CURLOPT_POST, true); //передача данных методом POST
    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post); //тут переменные которые будут переданы методом POST
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}
    
function getApiMethod($method_name, $params) {
    global $access_token;
    global $api_version;

    // Сделаем проверки на токен и версию апи, если их не указали, добавим.
    if (!array_key_exists('access_token', $params) && !is_null($access_token)) {
        $params['access_token'] = $access_token;
    }

    if (!array_key_exists('v', $params) && !is_null($api_version)) {
        $params['v'] = $api_version;
    }
    
    // Сортируем массив по ключам
    ksort($params);
    
    // Отправим запрос
    return(getPOST('https://api.vk.com/method/'.$method_name, $params));
}

?>