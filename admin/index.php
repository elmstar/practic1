<?php
session_start();
if (isset($_SESSION['message']) AND !empty($_SESSION['message'])) {
	$mess = $_SESSION['message']; unset($_SESSION['message']);
}
if (isset($_SESSION['status']) AND $_SESSION['status'] == 'admin') {	
	include '../includes/config.php';	
	$set = $_GET['set']??'users';		
	// HTML-шаблон админки
	?>
	
	<!DOCTYPE html>
<html>
<head>
<title>Панель управления: <?= $set ?></title>
<style>
.adm_cat_list {background-color:#B4D87E; border: 1px solid #6F903F; width: 200px; min-height: 100px; float:left;}
.adm_cat_list li a { text-decoration:none;}
.adm_set_panel {min-width: 900px; margin-left: 10px; padding: 5px; min-height: 90px; float:left;border: 1px solid #6F903F; max-width:900px;}
.adm_set_panel table, .adm_set_panel td, .adm_set_panel table th {border: 1px solid #7F3F90; border-collapse:collapse; padding: 3px;}
.adm_set_panel a {text-decoration:none; color: #A0097C; background-color:#FBE9F7; }
.adm_set_panel table { min-width:500px; max-width:800px;}
.adm_set_panel pre { width:765px; word-wrap: normal;}
.selected, .selected a {background-color:#A0097C; color:#FEBFEF; width: 150px;}
.message {clear: left;}
</style>
</head>
<body>
	
	<div class="adm_cat_list">
		<h2>Админка</h2>
	<ul>
	<li <?php if ($set == 'users') echo 'class="selected"' ?>><a href="?set=users"> Пользователи</a></li>
	<li <?php if ($set == 'category') echo 'class="selected"' ?>><a href="?set=category">Категории</a></li>
	<li <?php if ($set == 'accepting') echo 'class="selected"' ?>><a href="?set=accepting">Одобрение</a></li>
	<hr>
	<li><a href="/">На сайт</a></li> 
	</ul>
	</div><!-- class="adm_cat_list" -->
	<div class="adm_set_panel">
		<?php
		include 'include/'.$set.'.php';
		?>
	</div><!-- class="adm_set_panel" -->
	<div class="message">
	
	
	<?php
	if (isset($mess))
	echo $mess;
	?>
	</div><!-- class="message" -->
	</body>
</html>
	<?php	
} else {
	$_SESSION['message'] = 'Доступа нет';
	header('Location: /');
}
