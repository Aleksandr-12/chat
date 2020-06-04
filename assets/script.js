	var modal = document.getElementById("my_modal");
	var btn = document.getElementById("btn_modal_window");
	var span = document.getElementsByClassName("close_modal_window")[0];
	var task = document.getElementsByClassName("fl-div")[0];
	var clickUser = document.getElementsByClassName("nick-clck")[0];
	
	
	//var messages__container = document.getElementById('messages');
	var message = document.getElementById('message');
	var message_chat = document.getElementById('message_chat');
	
	var block = document.getElementById('block');

	//Контейнер сообщений — скрипт будет добавлять в него сообщения

	var interval = null; //Переменная с интервалом подгрузки сообщений
	let setOut = null; //Переменная с интервалом подгрузки сообщений

	var doTask = document.getElementById('doTask'); //Форма отправки
	
	var sendTaskMessage = document.getElementById('sendTaskMessage'); //Форма отправки
	
	var messageTextTask = document.getElementById('message-text-task'); //Форма отправки
	
	
	var sendChat = document.getElementById('send-chat'); //Форма отправки
	
	var messageChat = document.getElementById('message-chat'); //Форма отправки
	
	var chatAll = document.getElementById('chatAll'); //Форма отправки
	
	var taskAll = document.getElementById('taskAll'); //Форма отправки
	
	var bottomPanelTask = document.getElementsByClassName('bottom-panel-task')[0]; //Форма отправки
	
	var PanelButtonChat = document.getElementById('panel-button-chat'); //Форма отправки
	
	var PanelButtonDoChat = document.getElementById('panel-button-do-chat'); //Форма отправки
	
	var messageDoChat = document.getElementById('message-do-chat'); //Форма отправки
	
	var doChat = document.getElementById('do-chat'); //Форма отправки
	
	var PanelButtonTask = document.getElementById('panel-button-task'); //Форма отправки
	
	var sendForm = document.getElementById('chat-form'); //Форма отправки
	
	var messageTask = document.getElementById('messageTask'); //Форма отправки
	
	var topPanel = document.getElementById('topPanel'); //Форма отправки
	
	var sendFormPanel = document.getElementById('chat-form-panel'); //Форма отправки

	var messageInputTems = document.getElementById('message-tems'); 
	
	var messageInput = document.getElementById('message-text'); 
	
	var notif = document.getElementById('notif'); 
	
	var notifChat = document.getElementById('notif-chat'); 
	
	//var task_id = $('.inner-chat').attr('data-task_id');

	PanelButtonTask.style.display = "none";
	PanelButtonChat.style.display = "none";
	PanelButtonDoChat.style.display = "none";
	


	function send_request(act, login = null, password = null) {//Основная функция
		
		//Переменные, которые будут отправляться

		var var1 = null;

		var var2 = null;

	if(act == 'auth') {

		//Если нужно авторизоваться, получаем логин и пароль, которые были переданы в функцию

		var1 = login;

		var2 = password;
		
	} else if(act == 'send') {
			var1 = messageInput.value;
	}
	else if(act == 'sendChat') {
			var1 = messageChat.value;
			var chats_id = $('.fl-div').data('chats_id');
			var user_id = $('.fl-div').data('user_id');
			var2 = chats_id;
						
	}
	else if(act == 'sendTaskMessage' || act=='loadOneTask') {
		
			var task_id = $('.fl-div').data('task_id');
			var user_id = $('.fl-div').data('user_id');
			
			var1 = messageTextTask.value;
			var2 = task_id;
			
			
	}
	else if(act=='doChat'){
		var user_id = $('.fl-div').data('user_id');
		var1 = messageDoChat.value;
	}
	else if(act=='loadOneTask') {
		
			var task_id = $('.fl-div').data('task_id');
			var user_id = $('.fl-div').data('user_id');
		
	}
	else if(act=='loadOneChat') {
		
		var user_id = $('.fl-div').data('user_id');
		
	}
	 else if(act == 'doTask') {
			var1 = messageInput.value;
			var2 = messageInputTems.value;
	}


	$.post('chat.php',{ //Отправляем переменные

	act: act,

	var1: var1,

	var2: var2,
	
	chats_id:chats_id,
	task_id:task_id,
	
	user_id: user_id

	}).done(function (data) {

		data = $.parseJSON(data);
		if(data.error){
			alert(data.error);
		}
		if(data.chatPerson){
			
			showOneChat(data.chatPerson,data.chatsAll);
		
		}
		if(data.chats){
			showAllChats(data.chats);
		}
		if(data.task){
			if($('#topPanel')){
				$('#topPanel').empty();
			}
			if($('#panel-button-chat')){
				PanelButtonChat.style.display = "none";
			}
			if($('#panel-button-Task')){
				PanelButtonTask.style.display = "none";
			}
			if($('#panel-button-do-chat')){
				PanelButtonDoChat.style.display = "none";
			}
			messageTask.innerHTML = data.task;
			
		}else {
			showOneTsk(data.top,data.bottom);
			
		}
		
		

		if(data.error){
			
			if(data.error == 'Проблема авторизации') {

				clearInterval(interval); //Если проблема авторизации, отключаем автообновление

				if(login == null && password == null) {

					login = prompt('Введите логин: ');//Запрашиваем логин

				if(login != null) {

					password = prompt('Введите пароль: ');//Запрашиваем пароль

					send_request('auth',login,password); //Отправляем еще один запрос
					}
				}
			}
		}
			

		if(act == 'auth') {
			interval = setInterval(update,500); //Заново запускаем автообновление
		}
		if(act == 'send' || act=="doTask" || act=='sendTaskMessage' || act=='sendChat') {
			messageInput.value = '';
			messageInputTems.value = '';
			messageTextTask.value = '';
			sendChat.value = '';
		}
		
		if(act == 'auth' || act == 'send' || act=='doTask' || act == 'loadTask'  || act=='sendTaskMessage') {
			scrolling();
					
		}
		});

	}


	function update() {
		send_request('loadTask');
	}
	
	
	function updateTask() {
		send_request('loadOneTask');
	}
	function updateOneChat() {
		send_chat_request();
	}
	function updateNotif() {
		send_chat_request('loadNotific');
	}
	
	function updateNotifChat() {
		send_chat_request('loadNotificChat');
	}
	
	
	
	//intervalTask = setInterval(updateTask,1000);
	//	interval = setInterval(update,1000);
	//intervalChats = setInterval(updateChats,1000)
	
	intervalNotif = setInterval(updateNotif,800)
	updateNotifChat = setInterval(updateNotifChat,800)

	taskAll.onclick = function (e) {
		e.preventDefault();
		send_request('loadTask');			
		scrolling();
	};
	
	
	$('body').on('click', '#button-menu-task', function(e){
		e.preventDefault();
		send_request('loadTask');			
		scrolling();
	});
	
	$('body').on('click', '#button-menu-chat', function(e){
		e.preventDefault();
		var act = 'loadChats';
		$.ajax({
				url: 'chat.php',
				type: 'POST',
				data: {act:act},
				success: function(data){
					
				data = $.parseJSON(data);
						
				if(data.chats){
					showAllChats(data.chats);
				}
					
					
				},
				error:function(){
					alert('Error!');
				}
			});
		
		scrolling();
	});
	

	doTask.onsubmit = function () {

		send_request('doTask');
		modal.style.display = "none";
		return false; 

	};
	
	
	doChat.onsubmit = function () {
		send_chat_request('doChat');
		//send_request('doChat');
		return false; //Возвращаем ложь, чтобы остановить классическую отправку формы
	};
	
	sendChat.onsubmit = function () {
		send_chat_request('sendChat');
		
		return false; //Возвращаем ложь, чтобы остановить классическую отправку формы
	};
	
	sendTaskMessage.onsubmit = function () {
		send_request('sendTaskMessage');
		return false; //Возвращаем ложь, чтобы остановить классическую отправку формы
	};
		
	chatAll.onclick = function (e) {
		e.preventDefault();
					
		var act = 'loadChats';
		$.ajax({
				url: 'chat.php',
				type: 'POST',
				data: {act:act},
				success: function(data){
					
				data = $.parseJSON(data);
						
				if(data.chats){
					showAllChats(data.chats);
				}
					
					
				},
				error:function(){
					alert('Error!');
				}
			});
		
		scrolling();
	};
	function send_chat_request(act){
			if(act == 'sendChat'){
				var1 = messageChat.value;
				
				var chats_id = $('.fl-div').data('chats_id');
				var user_id = $('.fl-div').data('user_id');
				
				var2 = chats_id;
				messageChat.value = '';
				dataAll = {var1:var1, chats_id:chats_id, var2:var2, user_id:user_id, act:act};
				
			}
			if(act == 'doChat'){
				var user_id = $('.fl-div').data('user_id');
				var1 = messageDoChat.value;
				dataAll = {act:act, var1:var1, user_id:user_id};
				
			}
			if(act == 'loadNotific'){
				dataAll = {act:act};
			}
			if(act == 'loadNotificChat'){
				dataAll = {act:act};
			}
		
		$.ajax({
				url: 'chat.php',
				
				data: dataAll,
				type: 'POST',
				
				success: function(data){
				data = $.parseJSON(data);
				
				if(data.status){
					showStatusTask(data.status);
				}
				
				if(data.stat_chat){
					showStatusChat(data.stat_chat);
				}
				
				if(data.chatPerson){
					
					showOneChat(data.chatPerson,data.chatsAll);
				}
				if(data.chats){
					showAllChats(data.chats);
				}
					
					
				},
				error:function(){
					alert('Error!');
				}
			});
			
		scrolling();
	}
	
		
	$('body').on('click', '.nick-clck', function(e){
		e.preventDefault();
		var user_id = $(this).data('user_id');
		var act = 'loadUser';
		$.ajax({
			url: 'chat.php',
			data: {user_id:user_id, act:act},
			type: 'POST',
			
			success: function(data){
				
				data = $.parseJSON(data);
				
				showUser(data.user);
				scrolling();
			},
			error:function(){
				alert('Error!');
			}
		});
		
		
	
	});
	
	
	
	$('body').on('click', '.inner-chat-message', function(e){
		e.preventDefault();
		var user_id = $(this).data('user_id');
		var chats_id = $(this).data('chats_id');
		var act = 'loadOneChat';
		
		$.ajax({
				url: 'chat.php',
				data: {user_id:user_id, act:act,chats_id:chats_id},
				type: 'POST',
				
				success: function(data){
							
					data = $.parseJSON(data);
						
					showOneChat(data.chatPerson,data.chatsAll);
					scrolling();
				},
				error:function(){
					alert('Error!');
				}
			});
		
		
	});
	

	$('body').on('click', '.inner-chat', function(e){
		e.preventDefault();
		var task_id = $(this).data('task_id');
		var user_id = $(this).data('user_id');
		
	var act = 'loadOneTask';
	$.ajax({
			url: 'chat.php',
			data: {task_id:task_id,user_id:user_id, act:act},
			type: 'POST',
			
			success: function(data){
								
				data = $.parseJSON(data);
					
				showOneTask(data.top,data.bottom);
				scrolling();
			},
			error:function(){
				alert('Error!');
			}
		});
		
		
	});
	function showUser(res){
		if($('#topPanel')){
				$('#topPanel').empty();
		}
		PanelButtonChat.style.display = "none";
		PanelButtonTask.style.display = "none";
		PanelButtonDoChat.style.display = "block";
		$('#messageTask').empty();
		$('#topPanel').empty();
		$('#topPanel').append(res);
	}
	
	
	function showAllChats(res){
		if($('#topPanel')){
				$('#topPanel').empty();
		}
		if($('#panel-button-do-chat')){
				$('#panel-button-do-chat').empty();
		}
		PanelButtonChat.style.display = "none";
		PanelButtonTask.style.display = "none";
		$('#messageTask').empty();
		$('#messageTask').append(res);
		
	}
	

	function showOneCht(res,top){
			PanelButtonChat.style.display = "block";
			PanelButtonTask.style.display = "none";
			PanelButtonChat.style.display = "none";
			$('#topPanel').empty();
			$('#messageTask').empty();
			$('#topPanel').append(res);
			$('#messageTask').append(top);
		}

	function showOneChat(top,bottom){
	//	$('#insertScriptTask').append(clearInterval(intervalTask));
		//$('#insertScriptTask').append('<script  type="text/javascript">intervalOneChat = setInterval(updateOneChat,1000)</script>');
		PanelButtonTask.style.display = "none";
		PanelButtonChat.style.display = "block";
		PanelButtonDoChat.style.display = "none";
		$('#topPanel').empty();
		$('#messageTask').empty();
		$('#topPanel').append(top);
		$('#messageTask').append(bottom);
		
		if(!$("#button-menu-chat").hasClass('menu-active')){
				$('#button-menu-chat').removeClass('menu-chat');
				$('#button-menu-chat').addClass('menu-active');
				
		}
		if($("#button-menu-task").hasClass('menu-active')){
				$('#button-menu-task').removeClass('menu-active');
				$('#button-menu-task').addClass('menu-chat');
				
				
		}
		
	}

	function showOneTask(top,bottom){
		//$('#insertScript').replaceWith('<div></div>');
		//$('#insertScriptTask').append('<script  type="text/javascript">intervalTask = setInterval(updateTask,1000)</script>');
		PanelButtonTask.style.display = "block";
		if($('#panel-button-do-chat')){
			PanelButtonDoChat.style.display = "none";
		}
		
		$('#topPanel').empty();
		$('#messageTask').empty();
		$('#topPanel').append(top);
		$('#messageTask').append(bottom);
		
		if(!$("#button-menu-task").hasClass('menu-active')){
				$('#button-menu-task').removeClass('menu-chat');
				$('#button-menu-task').addClass('menu-active');
				
		}
		if($("#button-menu-chat").hasClass('menu-active')){
				$('#button-menu-chat').removeClass('menu-active');
				$('#button-menu-chat').addClass('menu-chat');
				
				
		}
	}
	
	function showOneTsk(top,bottom){
		//$('#insertScript').replaceWith('<div></div>');
		PanelButtonTask.style.display = "block";
		$('#topPanel').empty();
		$('#messageTask').empty();
		$('#topPanel').append(top);
		$('#messageTask').append(bottom);
		
	}
	
	function showStatusTask(stat){
		if(stat >= 1){
			var html = '<div class="inner-notif">'+stat+'</div>';
			$('#notif').empty();
			$('#notif').append(html);
		}else{
			$('#notif').empty();
		}
		
		
	}
	
	function showStatusChat(stat){
		if(stat == 1){
			if(!$("#notif-chat").hasClass('notifChat')){
				$('#notif-chat').addClass('notifChat');
				
			}
		}if(stat == 'no chat'){
			 if($("#notif-chat").hasClass('notifChat')){
				$('#notif-chat').removeClass('notifChat');
				
			}
		}
		
	}
	
	function scrolling(){
		messageTask.scrollTop = messageTask.scrollHeight;
	}
			
	function exitAuth(){
		send_request('exit');
		return false;
	}
	setTimeout(function(){
		scrolling();
	},1000);
	
	setTimeout(function(){
		send_request('loadTask');
	},2000);
	
	btn.onclick = function () {
		modal.style.display = "block";
	}
	
	
	 window.onclick = function (event) {
		if (event.target == modal) {
			modal.style.display = "none";
		}
	}

