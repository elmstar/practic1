<?php
// функции валидации (не стал изобретать новые, взял из свежей задачи предыдущих уроков)
function val_password($pass, $conf) {
	$val=['err'=>0, 'mess'=>''];
	if ($pass!=$conf)
	$val=['err'=>1, 'mess'=>'Пароль и подтверждение не совпадают<br> '];
	if (empty($pass))
	$val=['err'=>0, 'mess'=>''];
	return $val;
}
function val_chars($str) {
	$i=0;
	$val=['err'=>0, 'mess'=>''];
	while($i<strlen($str)) {
		$test_char=ord($str[$i]);
		if (($test_char<48 OR $test_char>122) OR ($test_char>=58 AND $test_char<65) OR ($test_char>=91 AND $test_char<97)) {
			$val=['err'=>2, 'mess'=>'Строка содержит не допустимые символы<br> '];
			break;
		}
		$i++;
	}
	return $val;
}
function val_unicum($link, $login) {
	$query="SELECT * FROM users WHERE login='$login'";
	$test=mysqli_fetch_assoc(mysqli_query($link, $query));		
	mysqli_query($link, $query) or die(mysqli_error($link));
	if (empty($test))
	$val=['err'=>0, 'mess'=>''];
	else
	$val=['err'=>3,'mess'=>'Указаный логин уже используется. Используйте другой<br>'];
	return $val;
}
function val_str_len($str, $down, $up, $mod) {	
	$val=['err'=>0, 'mess'=>''];
	if (strlen($str)<$down)
	$val=['err'=>4,'mess'=>'Указаный '.$mod.' слишком маленький<br>'];
	if (strlen($str)>$up)
	$val=['err'=>4,'mess'=>'Указаный '.$mod.' слишком большой<br>'];
	if (strlen($str)==0)
	$val=['err'=>5, 'mess'=>'Ваш '.$mod.' пустой'];
	return $val;
}
// Функции по работе с пользователями
function userReg($user_data,$link) {
	$userErr=0;	$mess='';
	$result=val_unicum($link,$user_data['login']);	$userErr+=$result['err']; $mess.=$result['mess'];
	$result=val_chars($user_data['login']);			$userErr+=$result['err']; $mess.=$result['mess'];
	$result=val_str_len($user_data['login'], 5, 30, 'логин');	
	$userErr+=$result['err']; $mess.=$result['mess'];
	$result=val_password($user_data['pass'], $user_data['confirm']);
	$userErr+=$result['err']; $mess.=$result['mess'];
	$result=val_str_len($user_data['pass'], 8, 100, 'пароль');
	$userErr+=$result['err']; $mess.=$result['mess'];
	$login=$user_data['login'];
	$pass=password_hash($user_data['pass'], PASSWORD_DEFAULT);
	$name=$user_data['name'];
	$patronymic=$user_data['patronymic'];
	$surname=$user_data['surname'];
	$val=['err'=>$userErr, 'mess'=>$mess];
	if ($userErr<1) {
		$query="INSERT INTO users SET login='$login', pass='$pass', reg_date=CURRENT_DATE(), status_id=2, name='$name', patronymic='$patronymic', surname='$surname', banned=0";
		mysqli_query($link,$query) or die(mysqli_error($link));
		$id=mysqli_insert_id($link);
		$_SESSION['id']=$id;
		$_SESSION['login']=$login;
		$_SESSION['status']='user';
		$_SESSION['auth']=true;
		$val['err']=0; $val['mess']='Регистрация успешна';
	} else {
		$val=['err'=>$val['err'],'mess'=>$val['mess']];
	}
	return $val;
}
function userLogin($user_data, $link) {
	$login=$user_data['login']; $pass=$user_data['pass'];	
if (!empty($login) and !empty($pass)) {		
		$query="SELECT users.id,users.login,users.pass, users.name, users.patronymic, users.surname, users.banned, statuses.name AS status 
		FROM users 
		LEFT JOIN statuses ON users.status_id=statuses.id 
		WHERE login='$login'";		
		$user=mysqli_fetch_assoc(mysqli_query($link,$query));
		$user_id=$user['id'];
		$salt=$user['pass'];
		$status=$user['status'];
		$banned=$user['banned'];
		if (password_verify($pass, $salt)) {
			if ($banned==0) {		
				$_SESSION['message']='Пользователь прошел авторизацию<br>';
				$_SESSION['auth']='true';
				$_SESSION['login']=$login;
				$_SESSION['id']=$user_id;				
				$_SESSION['name']=$user['name'];
				$_SESSION['patronymic']=$user['patronymic'];
				if (empty($status)) $_SESSION['status']='user';
				else $_SESSION['status']=$status;
				header('Location: /');
			} else {
				$_SESSION['message']='Ваша учётка забанена <br>';
				header('Location: /login/');
			}
		} else {
			$_SESSION['message']='Пользователь неверно ввел логин или пароль, выполним какой-то код<br>';
			header('Location: /login/');
		}		
	} 
}
function addTop($data,$link) {// Не забыть написать обработку символов(функция по замене некоторых символов, чтоб не поломать работу скрипта)
	$text=htmlentities(strip_tags($data['text']), ENT_QUOTES);
	
	$cat_id=$data['cat_id'];
	$id=$_SESSION['id'];
	if (!empty($data['text'])) {
		$query="INSERT INTO tops SET text='$text', category_id=$cat_id, date=CURRENT_DATE(), author_id=$id, accepted=0, like_level=0";
		mysqli_query($link, $query) or die(mysqli_error($link));
		$_SESSION['message']='Анекдот успешно внесён';
		header('Location: /');
	} else {
		$_SESSION['message']='Может всётаки закинем какой-нибудь анекдот? <br>';
		header('Location: /new/');
	}
}
function make_pag($page,$subjects_count,$page_count,$topic='none') {   // утащил из другого проекта, нужно подправлять	
	if ($subjects_count>$page_count) {
		if ($topic=='none') {	// если топик не указан(умолчание), значит это страницы списка тем, иначе страницы с ответами по теме, добавим доп. параметр в URL
			$topic_out='';
		} else {
			$topic_out='&topic='.$topic;
		}
		$pag='<div class="pagination"><table class="pagination"><tr><td';
		if ($page==1) {
		$pag.=' class="pag_current"><span aria-hidden="true">&laquo;</span></td>';
		} else {
		$pag.=' class="pag_default"><a href="?page=1'.$topic_out.'"><span aria-hidden="true">&laquo;</span></a></td>';
		}
		$subjects=0;
		$page_ind=1;
		while ($subjects<$subjects_count) {
			$pag.='<td';
			if ($page==$page_ind) {
				$pag.=' class="pag_active">'.$page_ind.'</td>';
			} else {
				$pag.=' class="pag_default"><a href="?page='.$page_ind.$topic_out.'">'.$page_ind.'</a></td>';
			}
			$page_ind++;
			$subjects+=$page_count;
			}		
			$page_ind--;
		if ($page==$page_ind) {
		$pag.='<td class="pag_current"><span aria-hidden="true">&raquo;</span></td>';
		} else {
		$pag.='<td class="pag_default"><a href="?page='.$page_ind.$topic_out.'"><span aria-hidden="true">&raquo;</span></a></td>';
		}
		$pag.='</tr></table></div><!-- class="pagination" -->';
		return $pag;
	} else {
		return '';
	}
}
function query_count($link,$source,$where) {			// Выясняем кол-во записей в таблице(темы, ответы)		
	$query = "SELECT COUNT(*) as count FROM $source WHERE $where";
	$result = mysqli_query($link, $query) or die( mysqli_error($link) );
	$count_arr = mysqli_fetch_assoc($result);
	$count = $count_arr['count'];
	return $count;
}

function changeLevel($set,$link,$return) {	// Оцениваем анекдот
	$user_id=$_SESSION['id'];
	$top_id=$set[0];
	$where='user_id='.$user_id.' AND top_id='.$top_id;	
	if (query_count($link,'like_rel',$where)<1) {
		$query = "INSERT INTO like_rel SET user_id=$user_id, top_id=$top_id, rated=1";
		mysqli_query($link, $query) or die( mysqli_error($link));
		if ($set[1]=='plus') $event='like_level+1'; else $event='like_level-1';
		$query = "UPDATE tops SET like_level=$event WHERE id=$top_id";
		var_export($query);
		mysqli_query($link, $query) or die( mysqli_error($link));
		$_SESSION['message']='рейтинг анекдота #'.$top_id.' изменён <br>';
		header('Location:'.$return);
	} else {
		$_SESSION['message']='повторная оценка этого анекдота не предусмотрена';
		header('Location:'.$return);
	}
}
