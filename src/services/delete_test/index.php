<?php

  session_start();

  function deleteTest($id_test){
    $sql_prova = "DELETE FROM prova WHERE id_prova = '".$id_test."'";
    $sql_gabarito = "DELETE FROM gabarito WHERE id_prova = '".$id_test."'";
    $sql_aluno_prova = "DELETE FROM aluno_prova WHERE id_prova = '".$id_test."'";
    try{
      //define PDO - tell about the database file
      $pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      //Execute the query
      $pdo->exec($sql_prova);
      $pdo->exec($sql_gabarito);
      $pdo->exec($sql_aluno_prova);
      $message = '<font color="green">PROVA DELETADA COM SUCESSO</font><br>';

    }catch(PDOException $e){
      $message = '<font color="red">NÃO FOI POSSÍVEL DELETAR A PROVA</font><br>';
    }

    return $message;
  }

  $id_test = $_POST['id_test'];

  $_GET['message'] = deleteTest($id_test);

  header('Location: ../../pages/test/search_test?message='.$_GET['message'])


 ?>
