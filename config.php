<?php

//Для запросов
$access_token = '579391ef04aa6a5cd0f3815fc19af40ae89f06cd428ccfd992e95c7c94c80e357fa7e09b298f6a86407b2';
$group_id = '122348920';

//Общие настройки
$font = "UniNeue-HeavyItalic.otf";
$font_size = 64;
$font_color = '255,255,255';

//Число комментов
$count_comments_text_pixel_x = 464;
$count_comments_text_pixel_y = 232;

//Число смартфонов
$count_smartphone_text_pixel_x = 1233;
$count_smartphone_text_pixel_y = 164;

//Другое
define('BASEPATH', str_replace('\\', '/', dirname(__FILE__)) . '/');
$output_header = BASEPATH.'header/output.png';
$api_version = "5.63";
session_start();
?>