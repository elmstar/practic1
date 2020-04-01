<?php
if (isset($_GET['act'])) {
	$action = explode('-', $_GET['act']);
	$id = $action[1];
	if ($action[0] == 'accept') {
		$query = "UPDATE tops SET accepted=1 WHERE id=$id";
		mysqli_query($link, $query)  OR die(mysqli_error($link));
		$_SESSION['message'] = '<br>Анекдот успешно одобрен <br>';
		header('Location: http://es-portfolio.ddns.net:71/admin/?set=accepting');
	} elseif($action[0] == 'del') {
		$query = "DELETE FROM tops WHERE id=$id";
		mysqli_query($link, $query)  OR die(mysqli_error($link));
		$_SESSION['message'] = '<br>Анекдот успешно удалён <br>';
		header('Location: http://es-portfolio.ddns.net:71/admin/?set=accepting');
	}
} else {
	$query = "SELECT id, text FROM tops WHERE accepted=0";
	$result = mysqli_query($link, $query) OR die(mysqli_error($link));
	for ($tops = []; $row = mysqli_fetch_assoc($result); $tops[] = $row);
	//var_export($tops);
	?>
	<table>
	<thead>
	<th>Текст</th><th colspan="2">Действия</th>
	</thead>
	<?php
	
	foreach ($tops as $elem) {		
			?> 
			<tr>
			<?php			
			foreach ($elem as $subElem) {
				if ($subElem != $elem['id']) {
					$text = nl2br($subElem);
				?>
				<td><?= $text ?></td>
				<?php
			}
		} // ячейки из базы закончил, пошли кнопки управления(метка для себя)
		?>
		<td><a href="/admin/?set=accepting&act=accept-<?= $elem['id'] ?>">Принять</a></td>
		<td><a href="/admin/?set=accepting&act=del-<?= $elem['id'] ?>">Удалить</a></td>
		</tr>
		<?php
	}
		
       }
