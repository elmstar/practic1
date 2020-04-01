<?php
// Параметры доступа к базе данных
define("HOST", "127.0.0.1");
define("DB_USER","root");
define("DB_PASS","lf;tytlevfq");
define("DB_NAME","test_56_1");
$link=mysqli_connect(HOST, DB_USER, DB_PASS,DB_NAME) or die(mysqli_error($link));

// Параметры: адреса и пути
$host='http://es-portfolio.ddns.net:71/'; // адрес сайта
$inc='includes/';			// путь до основных инклюдов
$tmp_mod='tmp/'.'default';	// составная переменная: настройка CMS + настройка пользователя(путь к шаблону). Здесь предполагается настройка интерфейса(для каждого пользователя отдельно или для всего сайта)
$gl_img=$host.'img/';		// путь к глобальному набору картинок
$dialogs=['0','reg', 'login', 'new', 'profile']; // перечисление диалоговых страниц(форм), отличных от вывод анекдотов


$top_count=10; 				// количество анекдотов на страницу()
