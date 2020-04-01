<?php
$uri = explode('/',trim(preg_replace('#(\?.*)#','',$_SERVER['REQUEST_URI']),'/'));// массив параметров из ЧПУ
if (!isset($uri[0]) OR empty($uri[0]))
$uri[0] = 'all';
session_start(); 																// Работа с сессиями
if ($uri[0] == 'out') {			// Процесс разлогинивания
	$_SESSION['auth'] = false; $_SESSION['status']=null;
	$_SESSION['login'] = null; $_SESSION['id'] = null;$_SESSION['name'] = null;$_SESSION['patronymic'] = null;
	header('Location: /');
}
if (isset($_SESSION['message']) AND !empty($_SESSION['message'])) {
	$mess = $_SESSION['message'];
	unset($_SESSION['message']);
} else {
	$mess = '';
}

include 'includes/config.php';		// Основные переменные(настройки)
include $inc.'functions.php';		// Основные функции

// Поместил сюда направление данных с форм в функции
// Данные с массива POST
if (isset($_POST['act'])) {			
	if (isset($_POST['login']) AND !empty($_POST['login'])) setcookie('login',$_POST['login'], time()+3600 );	// Работа с кукисами после заполнения формы
	if (isset($_POST['name']) AND !empty($_POST['name'])) setcookie('name',$_POST['name'],  time()+3600 );
	if (isset($_POST['patronymic']) AND !empty($_POST['patronymic'])) setcookie('patronymic',$_POST['patronymic'],  time()+3600 );
	if (isset($_POST['surname']) AND !empty($_POST['surname'])) setcookie('surname',$_POST['surname'],  time()+3600 );
	$action = $_POST;
	if ($_POST['act'] == 'reg') {		// Регистрация
		$regResult = userReg($action,$link);
		$_SESSION['message'] = $regResult['mess'];
		if ($regResult['err']<1) {
			header('Location: /');
		} else {
			header('Location: /reg/');
		}
	}
	if ($_POST['act']=='new') {
		addTop($action,$link);
	}
	if ($_POST['act']=='login') {	// Аутентификация
		userLogin($action,$link);
	}
	
	if ($_POST['act'] == 'profile_edit') {
		$name = $_POST['name'];
		$patronymic = $_POST['patronymic'];
		$surname = $_POST['surname'];		
		$id = $_POST['id'];
		$query = "UPDATE users SET name='$name', patronymic='$patronymic', surname='$surname' WHERE id=$id";
		mysqli_query($link, $query) OR die(mysqli_error($link));
		$_SESSION['message'] = 'Профиль пользователя обновлён';
		header('Location: /profile/');
	}
	if ($_POST['act'] == 'change_pass') {
		$old_pass = $_POST['old_pass'];
		$new_pass = $_POST['new_pass'];
		$pass_confirm = $_POST['confirm_pass'];
		$user_id = $_SESSION['id'];
		if (!empty($old_pass) AND !empty($new_pass)) {
			$err = 0; $mess = '';
			$query = "SELECT pass FROM users WHERE id=$user_id";
			$data = mysqli_query($link, $query) OR die(mysqli_error($link));
			$result = mysqli_fetch_assoc($data);
			$salt = $result['pass'];			
			if (password_verify($old_pass, $salt)) {
				$res = val_password($new_pass, $pass_confirm);
				$err += $res['err']; $mess .= $res['mess'];
				$res = val_str_len($new_pass, 8, 64, 'новый пароль');
				$err += $res['err']; $mess .= $res['mess'];
				if ($err<1) {
					$pass = password_hash($new_pass, PASSWORD_DEFAULT);
					$query = "UPDATE users SET pass='$pass' WHERE id=$user_id";
					mysqli_query($link, $query) OR die(mysqli_error($link));
					$_SESSION['message'] = 'Пароль успешно изменён';
					header('Location: /profile/');
				} else {
					$_SESSION['message'] = $mess;
					header('Location: /profile/');
				}
				
			} else {
				$_SESSION['message'] = 'Старый пароль указан не верно';
				header('Location: /profile/');
			}
		} else {
			$_SESSION['message'] = 'Все поля должны быть заполнены';
				header('Location: /profile/');
		}
	}
}
// Данные с массива GET

if (isset($_GET['like'])) { // обработка оценки анекдотов(оценивается 1 раз без возможности отмены)
	$return = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "&like"));
	$like_set = explode('-',$_GET['like']);	
	changeLevel($like_set,$link,$return);
}
$title = 'Банк анекдотов';			// содержимое переменной title
include $inc.'calculation.php';		// код формирующий переменные шаблона(логические конструкции без функций)
include $tmp_mod.'/template.php';		// Подгрузить основной файл шаблона
