<?php
if($_GET['debug'] == 1) {ini_set("display_errors",1);} else {ini_set("display_errors",0);}
error_reporting(E_ALL);

require_once('config.php');
require_once('api.php');

header('Content-type: text/html; charset=utf-8');

// Получим текущую дату
$date_today = date('Ymd');

$currrent_post = 8; //Номер поста
$current_countCom = 0;

/*Количество комментов под постом*/
$wall_get = getApiMethod('wall.getComments', array(
    'owner_id' => '-'.$group_id,
	'post_id' => $currrent_post
));

if($wall_get) {
    $wall_get = json_decode($wall_get, true);   
	$current_countCom = $wall_get['response']['count'];
}
/*КОНЕЦ количество комментов под постом*/

/**/
$db_selected = mysql_select_db('f0229431_root', $link);

$result = mysql_query("SELECT count FROM countsmart");
if (!$result) {
    $message  = 'Неверный запрос: ' . mysql_error() . "\n";
    die($message);
}

$count_smartphone = mysql_fetch_assoc($result)['count'];
mysql_free_result($result);
/**/

// -----------------------------------------------------------------------------
// --------------------------------- Подготовка шапки ---------------------------------
// -----------------------------------------------------------------------------
$draw = new ImagickDraw(); 

$bg = new Imagick(BASEPATH.'header/bg.jpg');

$draw->setTextAlignment(Imagick::ALIGN_CENTER);

// Количество комментов под постом
$draw->setFont(BASEPATH."/font/".$font);
$draw->setFontSize($font_size);
$draw->setFillColor("rgb(".$font_color.")");
$draw->setTextAlignment(\Imagick::ALIGN_CENTER);
$bg->annotateImage($draw, $count_comments_text_pixel_x, $count_comments_text_pixel_y, -6, mb_strtoupper($current_countCom, 'UTF-8'));

// Количество смартфонов
$draw->setFont(BASEPATH."/font/".$font);
$draw->setFontSize($font_size);
$draw->setFillColor("rgb(".$font_color.")");
$draw->setTextAlignment(\Imagick::ALIGN_CENTER);
$bg->annotateImage($draw, $count_smartphone_text_pixel_x, $count_smartphone_text_pixel_y, -6, mb_strtoupper($count_smartphone, 'UTF-8'));

$bg->setImageFormat("png");
$bg->writeImage($output_header);



// -----------------------------------------------------------------------------
// --------------------------- ЗАГРУЗКА НА СЕРВЕР ------------------------------
// -----------------------------------------------------------------------------

// Получим адресс сервера
$getUrl = getApiMethod('photos.getOwnerCoverPhotoUploadServer', array(
    'group_id' => $group_id,
    'crop_x2' => '1590'
));

if($getUrl) {
    $getUrl = json_decode($getUrl, true);

    $url = $getUrl['response']['upload_url'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('photo' => new CURLFile($output_header, 'image/jpeg', 'image0')));
    $upload = curl_exec( $ch );
    curl_close( $ch );

    if($upload) {
        $upload = json_decode($upload, true);

        $getUrl = getApiMethod('photos.saveOwnerCoverPhoto', array(
            'hash' => $upload['hash'],
            'photo' => $upload['photo'],
        ));

        if(stripos($getUrl, 'response":{"images":[{')) {
            print_r('<p>*** Успешно загрузили обложку в группу</p></br>');
        } else {
            print_r('Ошибка при загрузке обложки '.$getUrl);
        }
        
    }

    
    if(file_exists('header/top_likes.jpg')) {
        unlink('header/top_likes.jpg');
    }
    if(file_exists('header/top_comments.jpg')) {
        unlink('header/top_comments.jpg');
    }
    if(file_exists('header/last_subscribe.jpg')) {
        unlink('header/last_subscribe.jpg');
    }
   
}

// Функция склонения слов
function correctForm($number, $suffix) {
    $keys = array(2, 0, 1, 1, 1, 2);
    $mod = $number % 100;
    $suffix_key = ($mod > 7 && $mod < 20) ? 2: $keys[min($mod % 10, 5)];
    return $suffix[$suffix_key];
}

?>