<?php
// Формирование переменных с данными для html/css шаблона
// Формирование бокового списка категорий
$query = "SELECT * FROM category";
$result = mysqli_query($link, $query)  OR die(mysqli_error($link));
for ($category_data = []; $row = mysqli_fetch_assoc($result); $category_data[] = $row);
$categoryList = '';
foreach ($category_data AS $elem) {
	$categoryList.='<li><a href="/'.$elem['id'].'/">'.$elem['name'].'</a></li>';
}
//------------------------------------------------------------

// Формируем данные для области контента
$page = $_GET['page'] ?? '0';
$limit = $top_count;
if ($page > 1) {
	$start_page = ($page-1)*$top_count;
	$limit = $start_page.','.$top_count;
}
switch ($uri[0]) {	
	case array_search($uri[0], $dialogs)>0:
		$content_data = $uri[0]; // вместо данных метка для формы ввода
		if ($uri[0] == 'profile') {
			$id = $_SESSION['id'];
			$query = "SELECT login,name,patronymic,surname FROM users WHERE id=$id";
			$user_data = mysqli_fetch_assoc(mysqli_query($link,$query));
			$user_data['id'] = $id;
			$query = "SELECT COUNT(*) as count FROM tops WHERE author_id=$id";
			$data = mysqli_fetch_assoc(mysqli_query($link,$query));
			$topsNumber = $data['count'];
		}
		break;
	case $uri[0] > 0 AND $uri[0] < 200:
		// Формируем набор анекдотов для вывода в блок контент		
		$page = $_GET['page'] ?? '1';
		$where = 'category_id='.$uri[0].' AND accepted=1';
		$subjects_count = query_count($link,'tops',$where);
		$pag = make_pag($page,$subjects_count,$top_count,$topic = 'none');	// Формирование пагинации
		
		$query = "SELECT tops.id, tops.text, tops.date, tops.like_level, category.name AS category, users.login AS author 
			FROM tops LEFT JOIN category ON tops.category_id=category.id LEFT JOIN users ON tops.author_id=users.id
			WHERE tops.accepted=1 AND category.id=$uri[0] ORDER BY tops.id DESC LIMIT $limit";
		$result = mysqli_query($link, $query)  OR die(mysqli_error($link));
			for ($content_data = []; $row = mysqli_fetch_assoc($result); $content_data[] = $row);		
		break;
	default:
		$where = 'category_id>0 AND accepted=1';
		$page = $_GET['page'] ?? '1';
		$subjects_count = query_count($link,'tops',$where);
		$pag = make_pag($page,$subjects_count,$top_count,$topic = 'none');	// Формирование пагинации		
		$query = "SELECT tops.id, tops.text, tops.date, tops.like_level, category.name AS category, users.login AS author 
		FROM tops LEFT JOIN category ON tops.category_id=category.id LEFT JOIN users ON tops.author_id=users.id
		WHERE tops.accepted=1 ORDER BY tops.id DESC   LIMIT $limit";
		$result = mysqli_query($link, $query) OR die(mysqli_error($link));
		for ($content_data = []; $row = mysqli_fetch_assoc($result); $content_data[] = $row);
		break;		
	}	
	
	
//------------------------------------------------------------

// Формирование данных главного меню
$menu_data = [];
if (!isset($_SESSION['auth']) OR $_SESSION['auth'] == false) {
	$menu_data[] = ['/login/','Авторизация'];
	$menu_data[] = ['/reg/','Регистрация'];
} else {
	$menu_data[] = ['/new/','Новый анекдот'];
	$menu_data[] = ['/profile/', 'Профиль'];
	$menu_data[] = ['/out/','Выход'];
}
if (isset($_SESSION['status']) AND $_SESSION['status'] == 'admin')
$menu_data[] = ['/admin/','Админка'];
	
$greeting = 'Привет, гость';
if (!empty($_SESSION['name'])) {
	$greeting = 'Привет, '.$_SESSION['name'];
	if (!empty($_SESSION['patronymic'])) {
		$greeting .= ' '.$_SESSION['patronymic'];
	}
} elseif (isset($_SESSION['login']) AND !empty($_SESSION['login'])) {
	$greeting = 'Привет, '.$_SESSION['login'];
}

