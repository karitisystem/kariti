<?php
    session_start();

    // Ini configs
    $ini_file = parse_ini_file('../../settings.ini', true);
    if(strtolower($ini_file['SYSTEM']['OS']) == 'windows'){
      $_SESSION['absolute_path_base'] = $ini_file['SYSTEM']['cut_path_w'];
    }else if(strtolower($ini_file['SYSTEM']['OS']) == 'linux'){
      $_SESSION['absolute_path_base'] = $ini_file['SYSTEM']['cut_path_l'];
    }

    function getUser($nick_user){
      try{
        //define PDO - tell about the database file
        $pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');
        //write SQL
        $statement = $pdo->query("SELECT * FROM usuario WHERE nickname='".$nick_user."'");
        //run the SQL
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

      }catch(PDOException $e){
        $rows = 0;
        echo "<pre>";
        echo $e;
        echo "</pre>";
      }
      return $rows;
    }

    $nick_usuario = $_POST['nick_user'];
    $psw = md5($_POST['psw']);

    $rows = getUser($nick_usuario);
    $_SESSION['id_user'] = $rows[0]['id_usuario'];
    $_SESSION['name_user'] = $rows[0]['nome'];

    if ($psw != $rows[0]['senha']){
      $_GET['message'] = '<font color="red">ID OU SENHA INCORRETOS</font><br>';
      header('Location:./?message='.$_GET['message']);
      die();
    }
    
    header('Location: ../menu');
?>
