<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title></title>
  </head>
  <body>
    <?php
      if(!isset($_SESSION['name_user'])){
        header('Location: login.php');
        die();
      }

      function verifyClass($class_name, $id_user){
        $there = false;

        try{
        //define PDO - tell about the database file
        $pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

        //write SQL
        $statement = $pdo->query("SELECT id_turma FROM turma WHERE nome_turma='".$class_name."' AND id_usuario='".$id_user."'");

        //run the SQL
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);


        }catch(PDOException $e){
          $rows = 0;
          echo "<pre>";
          echo $e;
          echo "</pre>";
        }
        if (count($rows) == 0){
          $there = true;
        }
        return $there;

      }

      function getClassId($class_name){
        try{
          //define PDO - tell about the database file
          $pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

          //write SQL
          $statement = $pdo->query("SELECT id_turma FROM turma WHERE nome_turma='".$class_name."'");

          //run the SQL
          $rows = $statement->fetchAll(PDO::FETCH_ASSOC);


        }catch(PDOException $e){
          $rows = 0;
          echo "<pre>";
          echo $e;
          echo "</pre>";
        }
        return $rows[0]['id_turma'];
      }

      $class_name = $_POST['class_name'];
      $course_class = $_POST['course_class'];
      if($_POST['student'] != null){
        $students = $_POST['student'];
      }else{
        $students = [];
      }
      $id_user = $_SESSION['id_user'];

      $sql = "INSERT INTO turma (nome_turma, nome_curso, id_usuario) VALUES ('".$class_name."', '".$course_class."', '".$id_user."')";

      try{
        if(verifyClass($class_name, $id_user)){
          //define PDO - tell about the database file
          $pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

          //Execute the query
          $pdo->exec($sql);

          $_GET['message'] = '<font color="green">TURMA CRIADA COM SUCESSO</font><br>';
        }else{
          $_GET['message'] = '<font color="red">JÁ EXISTE UMA TURMA COM ESSE NOME</font><br>';
          header('Location: addClass.php');
          die();
        }


      }catch(PDOException $e){
        $_SESSION['error'] = $e;
        header('Location:errorPage.php');
      }

      $id_turma = getClassId($class_name);

      for($i=0; $i<count($students); $i++){
        $sql = "INSERT INTO aluno_turma (id_aluno, id_turma) VALUES ('".$students[$i]."', '".$id_turma."')";

        try{
          //define PDO - tell about the database file
          $pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

          //Execute the query
          $pdo->exec($sql);

          //$_GET['message'] = '<font color="green">TURMA CRIADA COM SUCESSO</font><br>';


        }catch(PDOException $e){
          $_SESSION['error'] = $e;
          header('Location:errorPage.php?message='.$_GET['message']);
        }
      }
      header('Location: ./?message='.$_GET['message']);

     ?>
  </body>
</html>
