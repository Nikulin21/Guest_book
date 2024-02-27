<?php

function debug($data){

    return"<pre>". print_r($data,1) ."</pre>";
}

function registration(): bool{

    global $pdo;

    $login = !empty($_POST['login']) ? trim($_POST['login']): '' ;
   $pass = !empty($_POST['pass']) ? trim($_POST['pass']) : '';


  if(empty($login) || empty($pass) ){

      $_SESSION['errors'] = 'Поля логин/пароль обязателини';
       return false;
  }

  $res = $pdo->prepare("SELECT COUNT(*) FROM users WHERE login = ?  ");

  $res->execute([$login]);
  if($res->fetchColumn()  ){
      $_SESSION['errors'] = 'Данное  имя пользователья уже используеться';
      return false;
  }

  $pass = password_hash($pass , PASSWORD_DEFAULT);
  $res = $pdo->prepare("INSERT INTO users(login , pass) VALUES(? ,?)");
 if($res->execute([$login , $pass])){
    $_SESSION['success'] = "Успешная регистрация";
    return true;
 }else{
     $_SESSION['errors'] = "Ошибка регистраций";
     return false;
 }


}

function login():bool{

    global $pdo;
    $login = !empty($_POST['login']) ? trim($_POST['login']): '';
    $pass = !empty($_POST['pass']) ? trim($_POST['pass']): '';

    if(empty($login) || empty($pass) ){
        $_SESSION['errors'] = 'Поля логин/пароль обязательные';
      return false;
    }

    $res = $pdo->prepare("SELECT * FROM users WHERE login = ?");
    $res ->execute([$login]);
    if(!$user = $res->fetch()) {
        $_SESSION['errors'] = 'Логин/пароль введени неверно';
        return false;
           }


    if(!password_verify($pass , $user['pass'])){
        $_SESSION['errors'] = "Логин/пароль введени неверно";
        return false;
    }else{
        $_SESSION['success'] ="Ви успешно авторизовались";
        $_SESSION['user']['name'] = $user['login'];
        $_SESSION['user']['id'] = $user['id'];
        return true;
    }

}


function save_message() : bool {

    global $pdo;
    $message = !empty($_POST['message'] )? trim($_POST['message']) : '';

    if(!isset($_SESSION['user']['name'])){
        $_SESSION['errors'] = 'Необходимо авторизоваться';
        return false;
    }
    if(empty($message)){
        $_SESSION['errors'] ="Введите текст сообшения";
        return false;
    }
    $res= $pdo->prepare("INSERT INTO  messages(name , message) VALUES (? ,? ) ");
    if($res->execute([$_SESSION['user']['name'] , $message])){
        $_SESSION['success']= "Сообщения добавлено";
        return true;
    }else{
        $_SESSION['errors'] ='Errors';
        return false;
    }
}

function get_message():array{

  global $pdo;
  $res = $pdo -> query("SELECT * FROM messages");
  return $res->fetchAll();

}
