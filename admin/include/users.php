<?php
if (isset($_POST['act'])) {
	
	if ($_POST['act'] == 'edit') {
		$name = $_POST['name'];
		$patronymic = $_POST['patronymic'];
		$surname = $_POST['surname'];
		$status = $_POST['status'];
		$id = $_POST['id'];
		$query = "UPDATE users SET name='$name', patronymic='$patronymic', surname='$surname', status_id='$status' WHERE id=$id";
		mysqli_query($link, $query) OR die(mysqli_error($link));
		$_SESSION['message'] = 'Профиль пользователя обновлён';
		header('Location: /admin/?set=users');
	}
	
}
if (isset($_GET['act'])) {
	$action = explode('-', $_GET['act']);
	if ($action[0] == 'edit') {
		if (isset($action[1])) {
			$id = $action[1];
			$query = "SELECT users.id, users.login, users.name, users.patronymic, users.surname, statuses.name AS status, statuses.id AS status_id FROM users LEFT JOIN statuses ON users.status_id=statuses.id WHERE users.id=$id";			
			$user = mysqli_fetch_assoc(mysqli_query($link, $query));			
			$query = "SELECT * FROM statuses";
			$data = mysqli_query($link, $query) OR die(mysqli_error($link));
			for ($statuses = []; $row = mysqli_fetch_assoc($data); $statuses[] = $row);
		}
		?>
		<h2>Редактирование профиля пользователя <?= $user['login'] ?></h2>
		<form action="" method="POST">
		<table>
		<tr><td> Имя: </td><td><input name="name" value="<?= $user['name'] ?>"></td></tr>
		<tr><td> Отчество: </td><td><input name="patronymic" value="<?= $user['patronymic'] ?>"></td></tr>
		<tr><td> Фамилия: </td><td><input name="surname" value="<?= $user['surname'] ?>"></td></tr>
		<tr><td> Статус: </td><td>
		<select name="status">
			<?php foreach ($statuses AS $elem) { ?>
			<option value="<?= $elem['id'] ?>" <?php if ($elem['id']==$user['status_id']) echo 'selected' ?> ><?= $elem['name'] ?></option>
			<?php } ?>
		</select>
			 </td></tr>
		<tr><td><input type="hidden" name="id" value="<?= $user['id'] ?>"><input type="hidden" name="act" value="edit"></td><td><input type="submit" value="Сохранить"></td></tr>
		</table>
		</form>		
		<?php
	}
	if ($action[0] == 'del') {
		if (isset($action[1])) {
			$id = $action[1];
			$query = "DELETE FROM users WHERE id=$id";
			mysqli_query($link,$query);
			$_SESSION['message'] = 'Пользователь удалён';
			header('Location: /admin/?set=users');
		}
	}
	if ($action[0] == 'ban' OR $action[0] == 'unban') {
		if (isset($action[1])) {
			$id = $action[1];
			if ($action[0] == 'ban') $banned = 1; else $banned = 0;
			$query = "UPDATE users SET banned=$banned WHERE id=$id";
			mysqli_query($link,$query);
			$_SESSION['message'] = 'Действие '.$action[0].' успешно';
			header('Location: /admin/?set=users');
		}
	}
} else {
	$query = "SELECT users.id, users.login, users.name, users.patronymic, users.surname, users.reg_date,  statuses.name AS status, users.banned AS ban FROM users LEFT JOIN statuses ON users.status_id=statuses.id";
	$data = mysqli_query($link, $query) OR die(mysqli_error($link));
	for ($users = []; $row = mysqli_fetch_assoc($data); $users[] = $row);
	
	?>
	<table>
	<thead>
	<th>Логин</th><th>Имя</th><th>Отчество</th><th>Фамилия</th><th>Рег дата</th><th>Статус</th>
	</thead>
	<?php
	foreach ($users as $elem) {
		?> 
		<tr>
		<?php
		$i=0;
		foreach ($elem as $subElem) {
			if ($i!=0 AND $i<7) {
			?>
			<td><?= $subElem ?></td>
			<?php
			 }
			 $i++;
		} // ячейки из базы закончил, пошли кнопки управления(метка для себя)
		?>
		<td><a href="/admin/?set=users&act=edit-<?= $elem['id'] ?>">edit</a></td>
		<?php
		if ($elem['ban']) {
			?>
			<td><a href="/admin/?set=users&act=unban-<?= $elem['id'] ?>">unban</a></td>
			<?php
		} else {
			?>
		<td><a href="/admin/?set=users&act=ban-<?= $elem['id'] ?>">ban</a></td>
		<?php } ?>
		<td><a href="/admin/?set=users&act=del-<?= $elem['id'] ?>">del</a></td>
		</tr>
		<?php
	}
	?>	
	</table>
	<?php	
}
?>
