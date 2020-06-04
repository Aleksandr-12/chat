<?php
	session_start();//Подключение должно быть на первой строчке в коде, иначе появится ошибка
	$db = mysqli_connect("localhost","wertual_root","id85P60c");

	mysqli_select_db($db,"wertual_chat");


	function send($db,$message) {

		if(auth($db,$_SESSION['login'],$_SESSION['password'])) {//Если авторизация удачна
		$message = htmlspecialchars($message);//Заменяем символы ‘<’ и ‘>’на ASCII-код

		$message = trim($message); //Удаляем лишние пробелы

		$message = addslashes($message); //Экранируем запрещенные символы

		$result = mysqli_query($db,"INSERT INTO messages (user_id,message) VALUES ('$_SESSION[id]','$message')");//Заносим сообщение в базу данных

		}

		return load($db); //Вызываем функцию загрузки сообщений

	}
	
	function sendTaskMessage($db, $message, $tasks_id,$user_id) {
		if(auth($db,$_SESSION['login'],$_SESSION['password'])) {//Если авторизация удачна
		$message = htmlspecialchars($message);//Заменяем символы ‘<’ и ‘>’на ASCII-код

		$message = trim($message); //Удаляем лишние пробелы
		
		$task_id = trim($task_id); //Удаляем лишние пробелы

		$message = addslashes($message); //Экранируем запрещенные символы
		
		$task_id = addslashes($task_id); //Экранируем запрещенные символы

		$result = mysqli_query($db,"INSERT INTO tasks_messages (tasks_id,user_id,message) VALUES ('$tasks_id','$_SESSION[id]','$message')");
		
		$user_result = mysqli_query($db,"SELECT * FROM userList WHERE id != '$_SESSION[id]'");

			if(mysqli_num_rows($user_result) >= 1) {
				
				while($user = mysqli_fetch_array($user_result)){
					mysqli_query($db,"UPDATE status SET status_name = '1' WHERE tasks_id = '$tasks_id'");
				}
			}

		}
		

		return loadOneTask($db,$tasks_id,$user_id); //Вызываем функцию загрузки сообщений
	}
	
	function doChat($db,$message,$user_id){
		
		if(auth($db,$_SESSION['login'],$_SESSION['password'])) {//Если авторизация удачна
		
		$message = htmlspecialchars($message);//Заменяем символы ‘<’ и ‘>’на ASCII-код
		
		$user_id = htmlspecialchars($user_id);//Заменяем символы ‘<’ и ‘>’на ASCII-код

		$message = trim($message); //Удаляем лишние пробелы
		
		$user_id = trim($user_id); //Удаляем лишние пробелы

		$message = addslashes($message); //Экранируем запрещенные символы
		
		$user_id = addslashes($user_id); //Экранируем запрещенные символы
		
		$repChats = mysqli_query($db,"SELECT * FROM chats WHERE (user_id = '$user_id' and curr_user_id = '$_SESSION[id]') xor (user_id = '$_SESSION[id]' and curr_user_id = '$user_id')");
		//return var_dump($repChats);
		if(mysqli_num_rows($repChats) >= 1){
			mysqli_query($db,"UPDATE chats SET message = '$message' WHERE (user_id = '$user_id' and curr_user_id = '$_SESSION[id]') xor (user_id = '$_SESSION[id]' and curr_user_id = '$user_id')");
			$last_id = mysqli_fetch_array($repChats);
			$chats_id = $last_id['id'];
			//return $chats_id;
			loadUpStatus($db,$user_id, $chats_id);
			
		
		} else{
			$result = mysqli_query($db,"INSERT INTO chats (user_id,curr_user_id,message) VALUES ('$user_id','$_SESSION[id]','$message')");
			$last = mysqli_query($db,"SELECT * FROM chats ORDER BY id DESC LIMIT 1");
			$last_id = mysqli_fetch_array($last);
			$chats_id = $last_id['id'];
			//return $chats_id;
			loadInsrtStatus($db,$user_id, $chats_id);
		}

		
		
		
		
		$result = mysqli_query($db,"INSERT INTO chat_messages (user_id, chats_id, message) VALUES ('$_SESSION[id]','$chats_id','$message')");
		
		}

		return loadOneChat($db,$chats_id,$user_id); //Вызываем функцию загрузки сообщений
	}
	
	function loadUpStatus($db,$user_id, $chats_id){
		$user_result = mysqli_query($db,"SELECT * FROM userList WHERE id='$user_id' or id = '$_SESSION[id]' ");//Получаем данные об авторе сообщения
		if($user_result){
			if(mysqli_num_rows($user_result) >= 1) {
				while($user = mysqli_fetch_array($user_result)){
					mysqli_query($db,"UPDATE status_chat SET status_name = '1' WHERE user_id = '$user[id]' AND chats_id = '$chats_id'");
					}
				}
		}
		
	}
	
	function loadInsrtStatus($db,$user_id, $chats_id){
		
		$user_result = mysqli_query($db,"SELECT * FROM userList WHERE id='$user_id' or id = '$_SESSION[id]' ");//Получаем данные об авторе сообщения
		if($user_result){
			if(mysqli_num_rows($user_result) >= 1) {
				while($user = mysqli_fetch_array($user_result)){
					mysqli_query($db,"INSERT INTO status_chat  (user_id, curr_user_id, status_name, chats_id) VALUES ('$user[id]', '$_SESSION[id]', '1', '$chats_id')");
				}
			
			
			}
		}
		
	}
	
	function loadUser($db, $user_id){
		$data = array();
			if(auth($db,$_SESSION['login'],$_SESSION['password'])) {//Проверяем успешность авторизации
			
			

						$user_result = mysqli_query($db,"SELECT * FROM userList WHERE id='$user_id' ");//Получаем данные об авторе сообщения

							if(mysqli_num_rows($user_result) == 1) {
								
								$user = mysqli_fetch_array($user_result);
								
								$loadUser .= "<img class='circusImgTask'  src='img/photo.png' alt >
								<div class='inner-chat-task'>
									<h1>$user[login]</h1>
									<div data-user_id = '$user[id]' class='fl-div'>
										<h3>Deliver flowers, Moscow </h3>
										
									</div>
									
								
									
								</div>
								<div class='menu'>
									<div class='menu-chat' id='button-menu-chat'>
										<img class='img-grascale' src='img/mini-chat.png' alt=''><span class='chat-img'>Chat</span>
									</div>
									<div class='menu-chat' id='button-menu-task'>
										<img src='img/mini-task.png' alt=''><span class='chat-task'>Task</span>
									</div>
								</div>";
								
							}
						
			}
			$data['user'] = $loadUser;
			
			return json_encode($data);
	}
	
	function sendChat($db,$message,$chats_id,$user_id){
		
		if(auth($db,$_SESSION['login'],$_SESSION['password'])) {//Если авторизация удачна
		
		$message = htmlspecialchars($message);//Заменяем символы ‘<’ и ‘>’на ASCII-код
		
		$chats_id = htmlspecialchars($chats_id);//Заменяем символы ‘<’ и ‘>’на ASCII-код

		$message = trim($message); //Удаляем лишние пробелы
		
		$chats_id = trim($chats_id); //Удаляем лишние пробелы

		$message = addslashes($message); //Экранируем запрещенные символы
		
		$chats_id = addslashes($chats_id); //Экранируем запрещенные символы

		$result = mysqli_query($db,"INSERT INTO chat_messages (user_id,chats_id,message) VALUES ('$_SESSION[id]','$chats_id','$message')");
		
		mysqli_query($db,"UPDATE chats SET message = '$message' WHERE (user_id = '$user_id' and curr_user_id = '$_SESSION[id]') or (curr_user_id = '$user_id' and user_id = '$_SESSION[id]')");
		
		loadUpStatus($db,$user_id, $chats_id);
		
		}

		return loadOneChat($db,$chats_id,$user_id); //Вызываем функцию загрузки сообщений
	}
	
	function doTask($db,$message,$tems){
		
		if(auth($db,$_SESSION['login'],$_SESSION['password'])) {//Если авторизация удачна
		
		$message = htmlspecialchars($message);//Заменяем символы ‘<’ и ‘>’на ASCII-код
		
		$tems = htmlspecialchars($tems);//Заменяем символы ‘<’ и ‘>’на ASCII-код

		$message = trim($message); //Удаляем лишние пробелы
		
		$tems = trim($tems); //Удаляем лишние пробелы

		$message = addslashes($message); //Экранируем запрещенные символы
		
		$tems = addslashes($tems); //Экранируем запрещенные символы

		$result = mysqli_query($db,"INSERT INTO tasks (user_id,message,tems,status) VALUES ('$_SESSION[id]','$message','$tems','1')");
				
		$last= mysqli_query($db,"SELECT * FROM tasks ORDER BY id DESC LIMIT 1");
		$last_id = mysqli_fetch_array($last);
		$result = mysqli_query($db,"INSERT INTO tasks_messages (tasks_id,user_id,message) VALUES ('$last_id[id]','$_SESSION[id]','$message')");	
		
			$user_result = mysqli_query($db,"SELECT * FROM userList");

			if(mysqli_num_rows($user_result) >= 1) {
				
				while($user = mysqli_fetch_array($user_result)){
					mysqli_query($db,"INSERT INTO status  (user_id, status_name, tasks_id,self_user_id) VALUES ('$user[id]','1','$last_id[id]','$_SESSION[id]')");
				}
				

				
			}
		}

		return loadTasks($db); //Вызываем функцию загрузки сообщений
	}

	function auth($db,$login,$pass) {

	//Находим совпадение в базе данных

		$result = mysqli_query($db,"SELECT * FROM userList WHERE login='$login' AND password='$pass'");

		

		if($result) {

			if(mysqli_num_rows($result) == 1) {//Проверяем, одно ли совпадение

			$user = mysqli_fetch_array($result); //Получаем данные пользователя и заносим их в сессию

			$_SESSION['login'] = $login;

			$_SESSION['password'] = $pass;

			$_SESSION['id'] = $user['id'];

			return true; //Возвращаем true, потому что авторизация успешна

			} else {

				unset($_SESSION); //Удаляем все данные из сессии и возвращаем false, если совпадений нет или их больше 1

				return false;

			}

			} else {

				return false; //Возвращаем ложь, если произошла ошибка

		}

	}

	function exitAuth(){
		unset($_SESSION['login']);
		unset($_SESSION['password']);
	}


	function load($db) {

		$echo = "";

		if(auth($db,$_SESSION['login'],$_SESSION['password'])) {//Проверяем успешность авторизации

			$result = mysqli_query($db,"SELECT * FROM messages"); //Запрашиваем сообщения из базы

		if($result) {

			if(mysqli_num_rows($result) >= 1) {

				while($array = mysqli_fetch_array($result)) {//Выводим их с помощью цикла

					$user_result = mysqli_query($db,"SELECT * FROM userList WHERE id='$array[user_id]'");//Получаем данные об авторе сообщения

					if(mysqli_num_rows($user_result) == 1) {

					$user = mysqli_fetch_array($user_result);

						//$echo .= "<div class='chat__message chat__message_$user[nick_color]'><b>$user[login]:</b> $array[message]</div>"; //Добавляем сообщения в переменную $echo
						$echo .= " <div class='chat-message' >
								<h3>$user[login]</h3>
								<p>$array[message]</p>
								<p class='date-time'>$array[date]</p>
								</div>"; //Добавляем сообщения в переменную $echo

						}

				}

			} else {

			$echo = "Нет сообщений!";//В базе ноль записей

			}

		}

		} else {

		$echo = "Проблема авторизации";//Авторизация не удалась

		}

		return $echo;//Возвращаем результат работы функции

}

		//Получаем переменные из супермассива $_POST

		//Тут же их можно проверить на наличие инъекций

		if(isset($_POST['act'])) {$act = $_POST['act'];}
		
		if(isset($_POST['task_id'])) {$task_id = $_POST['task_id'];}
		
		if(isset($_POST['user_id'])) {$user_id = $_POST['user_id'];}
		
		if(isset($_POST['chats_id'])) {$chats_id = $_POST['chats_id'];}

		if(isset($_POST['var1'])) {$var1 = $_POST['var1'];}

		if(isset($_POST['var2'])) {$var2 = $_POST['var2'];}

		switch($_POST['act']) {//В зависимости от значения act вызываем разные функции

		case 'load':

			$echo = load($db); //Загружаем сообщения

		break;

		case 'send':

			if(isset($var1)) {

				$echo = send($db,$var1); //Отправляем сообщение

			}

		break;
		
		case 'loadOneTask':

			if(isset($task_id) && isset($user_id)) {

				$echo = loadOneTask($db,$task_id,$user_id); //Отправляем сообщение

			}

		break;
		
		case 'loadOneChat':

			if(isset($user_id) && isset($chats_id)) {

				$echo = loadOneChat($db,$chats_id, $user_id); //Отправляем сообщение

			}

		break;
		
		case 'sendChat':

			if(isset($var1) && isset($var2) && isset($user_id)) {

				$echo = sendChat($db,$var1,$var2,$user_id); //Отправляем сообщение

			}

		break;
		
		case 'loadUser':

			if(isset($user_id)) {

				$echo = loadUser($db,$user_id); //Отправляем сообщение

			}

		break;
		
		case 'doTask':

			if(isset($var1) && isset($var2)) {

				$echo = doTask($db,$var1,$var2); //Отправляем сообщение

			}

		break;
		
		case 'sendTaskMessage':

			if(isset($var1) && isset($var2) && isset($user_id)) {

				$echo = sendTaskMessage($db,$var1,$var2,$user_id); //Отправляем сообщение

			}

		break;
		
		case 'loadTask':
			$echo = loadTasks($db); //Отправляем сообщение
		break;
		
		case 'doChat':
		if(isset($var1) &&  isset($user_id)) {
			$echo = doChat($db,$var1,$user_id); //Отправляем сообщение
		}
		break;
		
		case 'loadChats':
			$echo = loadChats($db); //Отправляем сообщение loadNotificChat
		break;
		
		case 'loadNotificChat':
			$echo = loadNotificChat($db); //Отправляем сообщение
		break;
		
		case 'loadNotific':
			$echo = loadNotific($db); //Отправляем сообщение
		break;
		
		case 'exit':
			exitAuth();
		break;

		case 'auth':

			if(isset($var1) && isset($var2)) {//Авторизуемся

			if(auth($db,$var1,$var2)) {

			//	$echo = loadTasks($db);

			}

			}

		break;

		}

		echo $echo;//Выводим результат работы кода
		
		
		function loadChats($db){
				$echo = "";
			
				if(auth($db,$_SESSION['login'],$_SESSION['password'])) {

					//$result = mysqli_query($db,"SELECT * FROM chats Where curr_user_id = '$_SESSION[id]'"); 
					$result = mysqli_query($db,"SELECT * FROM chats WHERE curr_user_id = '$_SESSION[id]' or user_id = '$_SESSION[id]'"); 
				
				if($result) {
					
					$data = array();
					
										
					if(mysqli_num_rows($result) >= 1) {
						
						while($array = mysqli_fetch_array($result)) {//Выводим их с помощью цикла
					
						//$user_result = mysqli_query($db,"SELECT * FROM userList WHERE id='$array[user_id]'");//Получаем данные об авторе сообщения
						$user_result = mysqli_query($db,"SELECT * FROM userList WHERE  (id = '$array[curr_user_id]' and id != '$_SESSION[id]') or (id = '$array[user_id]' and id != '$_SESSION[id]')");//Получаем данные об авторе сообщения

							if(mysqli_num_rows($user_result) == 1) {
								
								$user = mysqli_fetch_array($user_result);
								
								$status = mysqli_query($db,"SELECT * FROM status_chat WHERE user_id = '$_SESSION[id]' AND chats_id = '$array[id]'"); 
								
								$res = mysqli_fetch_array($status);
								if($res['status_name'] == 1){
									$notifTask = 'notifTask';
								}else{
									$notifTask = '';
								}
								
								$data['chats'] .= "<img class='circusImg' src='img/photo.png' alt >
								<div  data-user_id = '$user[id]'  data-chats_id = '$array[id]' class='inner-chat-message $notifTask'>
									<div class='fl-div'>
										<span>$user[login]</span>
										<span>$array[date]</span>
									</div>
								<p>$array[message]</p>
								
							</div>"; //Добавляем сообщения в переменную $echo*/
								
							}

				}

					} else {
						//$echo = "Нет сообщений!";//В базе ноль записей
						$data['error'] = 'нет сообщение этого задания';
					}
				}

				} else {
					$data['error'] = "Проблема авторизации";
					//$echo = "Проблема авторизации";//Авторизация не удалась
				}

			//return $echo;//Возвращаем результат работы функции
			
			return json_encode($data);
		}
		
		function loadOneChat($db,$chats_id,$user_id){
			
			
			
			$data = array();
			if(auth($db,$_SESSION['login'],$_SESSION['password'])) {//Проверяем успешность авторизации
			
			
			/*$chats = mysqli_query($db,"SELECT * FROM chats WHERE user_id = '$user_id' or curr_user_id = '$user_id' limit 1"); //Запрашиваем сообщения из базы
			//return var_dump($chats);
			if(mysqli_num_rows($chats) >= 1) {
						
						while($array = mysqli_fetch_array($chats)) {//Выводим их с помощью цикла*/
						mysqli_query($db,"UPDATE status_chat SET status_name = '0'  WHERE  user_id = '$_SESSION[id]' AND chats_id = '$chats_id'");
						
						$user_result = mysqli_query($db,"SELECT * FROM userList WHERE id='$user_id' ");//Получаем данные об авторе сообщения

							if(mysqli_num_rows($user_result) == 1) {
								
								$user = mysqli_fetch_array($user_result);
								
								$chatPerson .= "<img class='circusImgTask'  src='img/photo.png' alt >
								<div class='inner-chat-task'>
									<h1>$user[login]</h1>
									<div  data-user_id = '$user[id]' data-chats_id='$chats_id' class='fl-div'>
										<h3>Deliver flowers, Moscow </h3>
										<span>$user[date]</span>
									</div>
									<p>$user[message]</p>
									
								</div>
								<div class='menu'>
									<div class='menu-chat' id='button-menu-chat'>
										<img class='img-grascale' src='img/mini-chat.png' alt=''><span class='chat-img'>Chat</span>
									</div>
									<div class='menu-chat' id='button-menu-task'>
										<img src='img/mini-task.png' alt=''><span class='chat-task'>Task</span>
									</div>
								</div>";
								
							}
						/*}
							

					}*/
			
			$data['chatPerson'] = $chatPerson;
			
			
			
			$result = mysqli_query($db,"SELECT * FROM chat_messages WHERE chats_id = '$chats_id'"); //Запрашиваем сообщения из базы
			if($result) {

				if(mysqli_num_rows($result) >= 1) {
					
					while($array = mysqli_fetch_array($result)) {//Выводим их с помощью цикла
					
					$user_result = mysqli_query($db,"SELECT * FROM userList WHERE id='$array[user_id]'");//Получаем данные об авторе сообщения

						if(mysqli_num_rows($user_result) == 1) {
							
							$user = mysqli_fetch_array($user_result);
							
							$chatsAll .="<div class='chat-message'  >
								<h3>$user[login]</h3>
								<p>$array[message]</p>
								<p class='date-time'>$array[date]</p>
								</div>";
							
						}
						

			}
			$data['chatsAll'] = $chatsAll;

				} else {
					$data['error'] = 'нет сообщение этого задания';

					//$echo = "Нет сообщений!";//В базе ноль записей

				}

			}

			} else {
				

				//$echo = "Проблема авторизации";//Авторизация не удалась
				$data['error'] = "Проблема авторизации";
			}
						
			return json_encode($data);
		}
		
		
		function loadOneTask($db,$id,$user_id){
			
			
			
			$data = array();
			if(auth($db,$_SESSION['login'],$_SESSION['password'])) {//Проверяем успешность авторизации
			
			
			$task = mysqli_query($db,"SELECT * FROM tasks WHERE user_id = '$user_id' and id = '$id'"); //Запрашиваем сообщения из базы
			
			/*mysqli_query($db,"UPDATE userList SET status = '0'  WHERE  id = '$_SESSION[id]'"); */
			
			
			
			/*$status = mysqli_query($db,"SELECT * FROM status WHERE tasks_id = '$id' AND status_name = '1'");
			
			$status_res = mysqli_fetch_array($status);
			
			if($status_res){
				if($_SESSION['id'] != $status_res['self_user_id']){
					mysqli_query($db,"UPDATE status SET status_name = '0'  WHERE  user_id = '$user_id' AND self_user_id = '$user_id'");
				}
			}*/
			
			mysqli_query($db,"UPDATE status SET status_name = '0'  WHERE  user_id = '$_SESSION[id]' AND tasks_id = '$id'");
			
			if(mysqli_num_rows($task) >= 1) {
						
						while($array = mysqli_fetch_array($task)) {//Выводим их с помощью цикла

						$user_result = mysqli_query($db,"SELECT * FROM userList WHERE id='$array[user_id]' ");

							if(mysqli_num_rows($user_result) == 1) {
								
								$user = mysqli_fetch_array($user_result);
								
								$top = "<img class='circusImgTask'  src='img/photo.png' alt >
								<div class='inner-chat-task'>
									<h1>$array[login]</h1>
									<div data-task_id='$array[id]' data-user_id = '$user[id]' class='fl-div'>
										<h3>Deliver flowers, Moscow </h3>
										<span>$array[date]</span>
									</div>
									<span class='tems-user' >$array[tems]</span>
									<p>$array[message]</p>
									
								</div>
								<div class='menu'>
									<div class='menu-chat' id='button-menu-chat'>
										<img class='img-grascale' src='img/mini-chat.png' alt=''><span class='chat-img'>Chat</span>
									</div>
									<div class='menu-chat' id='button-menu-task'>
										<img src='img/mini-task.png' alt=''><span class='chat-task'>Task</span>
									</div>
								</div>";
								
							}
						}
							

					}
			
			$data['top'] = $top;
			
			
			
			$result = mysqli_query($db,"SELECT * FROM tasks_messages WHERE tasks_id = '$id'"); //Запрашиваем сообщения из базы
			if($result) {

				if(mysqli_num_rows($result) >= 1) {
					
					while($array = mysqli_fetch_array($result)) {//Выводим их с помощью цикла

					$user_result = mysqli_query($db,"SELECT * FROM userList WHERE id='$array[user_id]'");//Получаем данные об авторе сообщения

						if(mysqli_num_rows($user_result) == 1) {
							
							$user = mysqli_fetch_array($user_result);
							
							$echo .="<div class='chat-message'  >
								<h3 class='nick-clck' data-task_id='$array[id]' data-user_id = '$user[id]' >$user[login]</h3>
								<p>$array[message]</p>
								<p class='date-time'>$array[date]</p>
								</div>";
							
						}
						

			}
			$data['bottom'] = $echo;

				} else {
					$data['error'] = 'нет сообщение этого задания';

					//$echo = "Нет сообщений!";//В базе ноль записей

				}

			}

			} else {
				

				//$echo = "Проблема авторизации";//Авторизация не удалась
				$data['error'] = "Проблема авторизации";
			}
						
			return json_encode($data);
		}
		
		function loadNotific($db){
		
			$result = mysqli_query($db,"SELECT COUNT(*) FROM status WHERE status_name = '1' AND user_id = '$_SESSION[id]'"); 
			$result_set = mysqli_fetch_array($result);
			
				if($result_set) {
					$data = array();
						$data['status'] = $result_set[0];
					}else {
							
							$data['error'] = 'no notif';
					}
			return json_encode($data);
		}
		
		function loadNotificChat($db){
		
			$result = mysqli_query($db,"SELECT * FROM status_chat WHERE user_id = '$_SESSION[id]' AND status_name = '1'"); 
			$result_set = mysqli_fetch_array($result);
			
				if($result_set) {
					$data = array();
						$data['stat_chat'] = 1;
					}else {
							
							$data['stat_chat'] = 'no chat';
					}
			return json_encode($data);
		}
		
		function loadTasks($db) {

			$echo = "";
		
			if(auth($db,$_SESSION['login'],$_SESSION['password'])) {

				$result = mysqli_query($db,"SELECT * FROM tasks"); 
				
				

				if($result) {
					
					$data = array();
					
					if(mysqli_num_rows($result) >= 1) {
						
						while($array = mysqli_fetch_array($result)) {//Выводим их с помощью цикла
					
						$user_result = mysqli_query($db,"SELECT * FROM userList WHERE id='$array[user_id]'");//Получаем данные об авторе сообщения

							if(mysqli_num_rows($user_result) == 1) {
								
								$user = mysqli_fetch_array($user_result);
								
								$status = mysqli_query($db,"SELECT * FROM status WHERE user_id = '$_SESSION[id]' AND tasks_id = '$array[id]'"); 
								$res = mysqli_fetch_array($status);
								if($res['status_name'] == 1 /*&& $res['user'] == $_SESSION['id']*/){
									$notifTask = 'notifTask';
								}else{
									$notifTask = '';
								}
								
								$data['task'] .= "<img class='nick-clck circusImg' data-user_id = '$user[id]' src='img/photo.png' alt >
								<div data-task_id='$array[id]' data-user_id = '$user[id]' class='inner-chat $notifTask'>
									<div class='fl-div'>
										<span>$user[login]</span>
										<span>$array[date]</span>
									</div>
								<p>$array[tems]</p>
								
							</div>"; //Добавляем сообщения в переменную $echo*/
								
							}

				}

					} else {
						//$echo = "Нет сообщений!";//В базе ноль записей
						$data['error'] = 'нет сообщение этого задания';
					}
				}

				} else {
					$data['error'] = "Проблема авторизации";
					//$echo = "Проблема авторизации";//Авторизация не удалась
				}

		//return $echo;//Возвращаем результат работы функции
		
		return json_encode($data);

}