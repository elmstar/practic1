<?php
// Думаю, при развитии проекта сюда можно заинклюдить файл со специфическими функциями
// В моём случае это пока не надо
?>
<!DOCTYPE html><!-- Заголовочная часть шаблона -->
<html>
<head>
<title><?= $title ?></title>
<style><!-- Весь CSS код для сайта находится здесь(админ) -->
.adm_set_panel {min-width: 900px; margin-left: 10px; padding: 5px; min-height: 90px; float:left;border: 1px solid #6F903F;}
.adm_set_panel table, .adm_set_panel td, .adm_set_panel table th {border: 1px solid #7F3F90; border-collapse:collapse; padding: 3px;}
.adm_set_panel a {text-decoration:none; color: #A0097C; background-color:#FBE9F7; }
.selected, .selected a {background-color:#A0097C; color:#FEBFEF; width: 150px;}

.header {min-width: 900px; max-width: 1200px; min-height:175px;background-color:#A2C96F;border: 1px solid #6F903F;}
.logo {float: left;}
.main_menu {right: 50px;width: 200px;float: left;min-height:100px;}
.greeting {width: 390px;float: left;min-height:100px;}
.sys_message{width: 390px;float: left;}

.category {background-color:#B4D87E; border: 1px solid #6F903F; width: 200px; min-height: 100px; float:left;margin-top: 5px;}
.category li a { text-decoration:none;}
.category h2 {color:#9E6415; font-family:"Helvetica Neue",Helvetica,Arial,sans-serif; font-size:24px;}
.content {background-color:#FFFFFF; min-width:993px; min-height: 600px;max-width:900px; border: 1px solid #6F903F; float: left; margin-top: 5px; margin-left: 5px; margin-bottom:5px;padding: 5px;}
.content textarea {min-width: 800px; max-weight: 1200px; min-height:200px;}

.anecdot {background-color:#E3E8DA; border: 1px solid #6F903F; margin-top: 5px;}
.anecdot_text {background-color:#FFF; border: 1px solid #6F903F; margin:10px; padding: 5px;}
.anecdot_title {text-align: left; width:500px; float:left; margin-left:10px;}
.anecdot_date {text-align: right;}
.anecdot p {width: 70%; float: left; margin: 2px;}
.anecdot table, .anecdot td {border: 1px solid #7F3F90; border-collapse:collapse;}
.anecdot td {min-width:10px; min-height:15px;}
.anecdot a {text-decoration:none; display:block;}

.pagination, .pag_current, .pag_default {border: 1px solid #7F3F90; border-collapse:collapse; min-width:20px; min-height:20px; text-align:center;}
.pag_active {background-color: #95FFC3; min-width:20px; min-height:20px;}
.pag_current {background-color: #BCFFD9;}
.pagination a {display:block;}
.pagination {clear: left;}

.foot {min-width: 900px; max-width: 1200px; min-height:150px;background-color:#A2C96F;border: 1px solid #6F903F; clear: left;}
</style>
<link rel="stylesheet" href="<?= $host.$tmp_mod ?>/bootstrap.css">
</head>
<!-- ----------------------------------Заголовочная чать в теле документа--------------------------------- -->
<body>
<div class="header">
<div class="logo"><a href="<?= $host ?>">
<img class="logo" src="<?= $gl_img ?>logo.png"></a></div><!-- class="logo" -->
	<div class="main_menu">
	<?php if (isset($menu_data)) {?>
		<ul>
		<?php foreach ($menu_data as $elem) {?>
			<li><a href="<?= $elem[0] ?>"><?= $elem[1] ?></a></li>
		<?php } ?>
		</ul>
	<?php } ?>
	</div><!-- class="main_menu" -->
	<div class="greeting">
	<?= $greeting ?>
	</div><!-- class="greeting" -->
	<div class="sys_message"><!-- Здесь скромно расположились сообщения общего плана(flash message) -->
	<?= $mess ?>
	</div><!-- class="sys_message" -->
</div><!-- class="header" -->

<!-- -----------------------------"Туловище документа" начинаем со списка категорий----------------- -->
<div class="category">
	<h2>Анекдоты</h2>
	<ul>
	<li><a href="/">Все анекдоты</a></li>
	<?php if (isset($categoryList)) echo $categoryList ?>
	</ul>
</div><!-- class="category" -->

<div class="content">	
	<?php if (array_search($uri[0],$dialogs)) { ?>
		<?php if ($uri[0]=='reg') { ?>
			<form action="/" method="POST"><!-- ------------Регистрация нового пользователя---------------------------- -->
			<table>
			<tr><td>Логин: </td><td><input name="login" value="<?php if (isset($_COOKIE['login'])) echo $_COOKIE['login'] ?>"> </td></tr>
			<tr><td>Пароль: </td><td><input type="password" name="pass"> </td></tr>
			<tr><td>Пароль подтверждение: </td><td><input type="password" name="confirm"> </td></tr>
			<tr><td>Имя: </td><td><input name="name" value="<?php if (isset($_COOKIE['name'])) echo $_COOKIE['name'] ?>"> </td></tr>
			<tr><td>Отчество: </td><td><input name="patronymic" value="<?php if (isset($_COOKIE['patronymic'])) echo $_COOKIE['patronymic'] ?>"> </td></tr>
			<tr><td>Фамилия: </td><td><input name="surname" value="<?php if (isset($_COOKIE['surname'])) echo $_COOKIE['surname'] ?>"> </td></tr>
			<tr><td><input type="hidden" name="act" value="reg"></td><td><input type="submit" value="Регистрировать"</td></tr>
			</table>
			</form>
		<?php } ?>
		<?php if ($content_data=='login') { ?>
			<form action="/" method="POST"> <!-- ------------------------Форма для аутентификации------------------------ -->
				<table>
				<tr><td>Логин: </td><td><input name="login" value="<?php if (isset($_COOKIE['login'])) echo $_COOKIE['login'] ?>"> </td></tr>
				<tr><td>Пароль: </td><td><input type="password" name="pass"> </td></tr>
				<tr><td><input type="hidden" name="act" value="login"></td><td><input type="submit" value="Войти"</td></tr>
				</table>
			</form>			
		<?php } ?>
		<!-- ------------------------------------------------------------Форма ввода нового анекдота--------------------- -->
		<?php if ($content_data=='new') { ?>
			<form action="/" method="POST">
				<table>
				
				<tr><td>Категория: </td><td>
					<select name="cat_id">
						<?php foreach ($category_data AS $elem) { ?> 
							<option <?php if (isset($_COOKIE['category_select']) AND ($_COOKIE['category_select']==$elem['id'])) echo 'selected' ?> value="<?= $elem['id'] ?>"><?= $elem['name'] ?></option>
						<?php } ?>
					</select>					
				</td></tr>
				<tr><td>Текст: </td><td><textarea name="text"></textarea></td></tr>
				<tr><td><input type="hidden" name="act" value="new"></td><td><input type="submit" value="Добавить"</td></tr>
				</table>
			</form>			
		<?php } ?>
		<!-- ------------Страница редактирования профиля пользователя(каждый пользователь может поправить свои данные)---- -->
		<?php if ($content_data=='profile') { ?>
		<p>Ваших анекдотов в базе: <?= $topsNumber ?></p>
		<h2>Редактирование профиля пользователя <?= $user_data['login'] ?></h2>
		<form action="" method="POST">
		<table>
		<tr><td> Имя: </td><td><input name="name" value="<?= $user_data['name'] ?>"></td></tr>
		<tr><td> Отчество: </td><td><input name="patronymic" value="<?= $user_data['patronymic'] ?>"></td></tr>
		<tr><td> Фамилия: </td><td><input name="surname" value="<?= $user_data['surname'] ?>"></td></tr>		
			 </td></tr>
		<tr><td><input type="hidden" name="id" value="<?= $user_data['id'] ?>"><input type="hidden" name="act" value="profile_edit"></td><td><input type="submit" value="Сохранить"></td></tr>
		</table>
		</form>		
		<h2>Смена пароля</h2>
		<form action="" method="POST">
		<table>
		<tr><td>Старый пароль:</td><td><input type="password" name="old_pass"> </td></tr>
		<tr><td>Новый пароль:</td><td><input type="password" name="new_pass"> </td></tr>
		<tr><td>Подтверждение:</td><td><input type="password" name="confirm_pass"> </td></tr>
		<tr><td><input type="hidden" name="act" value="change_pass"></td><td><input type="submit" value="Сменить"</td></tr>
		</table>		
		</form>		
		<?php } ?>
		<!-- ----------------------------------------------Форма вывода доступных анекдотов-------------------------------- -->
	<?php } else { 
		if (isset($pag)) echo $pag;
		?>
	
	<?php foreach ($content_data as $elem) {	?>
			<div class="anecdot">				
					<div class="anecdot_title"> #<?=$elem['id'] ?> | Анекдоты <?=$elem['category']?></div><!-- class="anecdot_title" --> <div class="anecdot_date"><?=$elem['date']?></div></p>
					<div class="anecdot_text"><?php $text=nl2br($elem['text']); echo $text ?></div><!-- class="anecdot_text" -->
					<p><?=$elem['author']?>
					<table><tr><td><!-- Рейтинг анекдота и кнопки для оценки -->
					<a href="?page=<?php echo $_GET['page']??'' ?>&like=<?=$elem['id'] ?>-minus">-</a></td><td>
					<?php					
					echo $elem['like_level'];					 
					 ?>
					 </td><td><a href="?page=<?php echo $_GET['page']??'' ?>&like=<?=$elem['id'] ?>-plus">+</a>
					 </tr></table>
					 </p>
			</div><!-- class="anecdot" -->
		<?php }} ?>
<?php 
if (isset($pag)) echo $pag;
var_export($_POST);
?>

</div><!-- class="content" -->
<!-- -------------------------------------------------------------Подвальная часть----------------------------------------- -->


<div class="foot">
<p><a href="https://anekdot-z.ru">Anekdot-z.ru</a> 2012 - 2019 - самые смешные анекдоты</p>
<p>Все анекдоты на сайте были собраны с открытых источников и являються народным творчеством. Все анекдоты вымышлены. Совпадения с реальными людьми или событиями случайны.</p>
<p>Материал с данного сайта честно стырил в учебных целях, безо всякой мысли о наживе и для открытой публиции(на профессиональном хостинге) не предназначен</p>
<p>Данный адрес <a href="http://es-portfolio.ddns.net:71/">http://es-portfolio.ddns.net:71/</a> с нестандартным портом 71 - учебный хостинг и предназначен для отработки навыков программирования</p>
</div><!-- class="foot" -->
</body>
</html>


