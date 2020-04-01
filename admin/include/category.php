<?php
if (isset($_GET['act'])) {
	$action = explode('-', $_GET['act']);
	if ($action[0] == 'edit' OR $action[0]=='new') {
		if (isset($_POST['apply'])) {
			if ($_POST['apply']=='edit') {
				$id = $action[1];
				$name = $_POST['name'];
				$query = "UPDATE category SET name='$name' WHERE id=$id";
				mysqli_query($link, $query) OR die(mysqli_error($link));
				$_SESSION['message'] = '<br>Категория успешно обновлена<br>';
				header('Location: http://es-portfolio.ddns.net:71/admin/?set=category');
			} else {
				$name = $_POST['name'];
				$query = "INSERT INTO category SET name='$name'";
				mysqli_query($link, $query)  OR die(mysqli_error($link));
				$_SESSION['message'] = '<br>Категория успешно добавлена<br>';
				header('Location: http://es-portfolio.ddns.net:71/admin/?set=category');
			}				
		} else {
			
			?>
			<form method="POST" action="">
			<?php if (isset($action[1])) {
				?>
				<input type="hidden" name="id" value="<?= $action[1] ?>"
				<?php
				}
				?>
			<label for="name">Название: </label>
			<input id="name" name="name" value="<?php if (isset($action[2])) echo $action[2] ?>">
			<input type="hidden" name="apply" value="<?= $action[0] ?>">
			<input type="submit" value="Сохранить">
			</form>
			<?php
		}
	}
	if ($action[0] == 'del') {
		$id = $action[1];
		$query = "SELECT COUNT(tops.text) as count FROM category RIGHT JOIN tops ON category.id=tops.category_id WHERE category.id=$id";
		$result = mysqli_fetch_assoc(mysqli_query($link, $query));
		if ($result['count'] < 1) {
			$query = "DELETE FROM category WHERE id=$id";
			mysqli_query($link, $query) OR die(mysqli_error($link));
			$_SESSION['message'] = '<br>Категория успешно удалена<br>';
			header('Location: http://es-portfolio.ddns.net:71/admin/?set=category');
		} else {
			$_SESSION['message'] = '<br>В категории есть анекдоты. Удалять можно только пустые категории<br>';
			header('Location: http://es-portfolio.ddns.net:71/admin/?set=category');
		}
	}
	
} else {
	$query = "SELECT * FROM category";
	$data = mysqli_query($link, $query) OR die(mysqli_error($link));
	for ($users = []; $row = mysqli_fetch_assoc($data); $users[] = $row);	
	?>
	<table>
	<thead>
	<th>id</th><th>Название</th>
	</thead>
	<?php
	foreach ($users as $elem) {
		?> 
		<tr>
		<?php
		foreach ($elem as $subElem) {
			?>
			<td><?= $subElem ?></td>
			<?php
		} // ячейки из базы закончил, пошли кнопки управления(метка для себя)
		?>
		<td><a href="/admin/?set=category&act=edit-<?= $elem['id'] ?>-<?= $elem['name'] ?>">edit</a></td>
		<td><a href="/admin/?set=category&act=del-<?= $elem['id'] ?>">del</a></td>
		</tr>
		<?php
	}
	?>	
	</table>
	<a href="/admin/?set=category&act=new">Добавить</a>
	<?php	
}
