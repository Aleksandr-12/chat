<!DOCTYPE html>
<html lang="ru">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="assets/style.css" />

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<title>chat</title>
</head>
<body>
<button id="btn_modal_window">Сделать задачу</button>
  <div id="my_modal" class="modal">
    <div class="modal_content">
      <span class="close_modal_window">×</span>
    
	<div class="wrap-chat">
		<div class="top-panel">
			
			<h1>Отправить задачу</h1>
			
		</div>
		
		
		<div class="bottom-panel">
			<form method='post' id='doTask'>
				<input type='text' id='message-tems' class='chat-form__inpt' placeholder='Write Tems'>
				<input type='text' id='message-text' class='chat-form__inpt' placeholder='Write Message'>
				<input  type='submit' class='chat-form__subm'  >
			</form>
		</div>
					
	</div>
</div>
    
  </div>
	

<div class="wrapper">
	<div class="wrap-chats">
		<div class="top-panel">
			<div class="top-button">
				<img width="48px" src="../img/gumbur.jpeg" alt="" >
			</div>
			
			<h1 id='head-task-chat'>Tasks</h1>
			<div class="top-notification">
				<div id="notif"></div>
				<img src="../img/notif.png" alt="" >
			</div>
		</div>
		<div class="inwraper-message">
		<div id="topPanel">
				</div>
			<div class="wraper-message" >
				
			
				<div id="messageTask">
				</div>
			</div>	
		</div>
		<div class="line-hor"></div>
		<div id="panel-button-do-chat">
			<div class='bottom-panel'>
				<form method='post' id='do-chat'>
					<input type='text' id='message-do-chat' class='chat-form__inpt' placeholder='Write Message'>
					<input  type='submit' class='chat-form__subm'  >
				</form>
			</div>
		</div>
		<div id="panel-button-chat">
			<div class='bottom-panel'>
				<form method='post' id='send-chat'>
					<input type='text' id='message-chat' class='chat-form__inpt' placeholder='Write Message'>
					<input  type='submit' class='chat-form__subm'  >
				</form>
			</div>
		</div>
		<div id="panel-button-task">
			<div class='bottom-panel'>
				<form method='post' id='sendTaskMessage'>
					<input type='text' id='message-text-task' class='chat-form__inpt' placeholder='Write Message'>
					<input  type='submit' class='chat-form__subm'  >
				</form>
			</div>
		</div>
			<div class="bottom-panel-task">
				<div class="wrap-notif-chat">
					<input  type='submit' class='chat-of-tasks' id="chatAll"  ><div id="notif-chat" ></div>
				</div>
				<!--<input  type='submit' class='chat-tasks'  >-->
				<input  type='submit' class='chat-task-submit' value="Задачи" id="taskAll" >
			</div>
					
	</div>
</div>








<script type="text/javascript" src="assets/jquery-1.11.0.min.js"></script>

<script type="text/javascript" src="assets/script.js"></script>
<script type="text/javascript" src="assets/main.js"></script>
<div id="insertScript">
	<script  type="text/javascript">

</script>
</div>
<div id="insertScriptTask">
	
</div>
</body>
</html>