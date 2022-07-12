<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../../../../styles/styles.css">
    <title>Editar Usuário</title>
  </head>
  <body>
      <?php
      	if(!isset($_SESSION['name_user'])){
      		$_GET['message'] = '<font color="red">VOCÊ PRECISA ESTAR LOGADO PARA ACESSAR</font><br>';
      		header('Location: login.php');
      		die();
      	}
       	if($_SESSION['id_user'] != '1'){
          $_GET['message'] = '<font color="red">VOCÊ NÃO TEM PERMISSÃO DE ACESSO A ESSA PÁGINA</font><br>';
      		header('Location: login.php');
      		die();
        	}

        $id_user_edit = $_GET['id_user'];
        function getUser($id_user_edit){
      		try{
      			//define PDO - tell about the database file
      			$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      			//write SQL
      			$statement = $pdo->query("SELECT * FROM usuario WHERE id_usuario='".$id_user_edit."'");

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

        $rows = getUser($id_user_edit);

      ?>
      <section class="window">
        <div class="container all">
          <form action="settings.php?id_user=<?php echo $id_user_edit; ?>" method="post">
            <div class="title">EDITAR USUÁRIO</div>
            <div class="inner_container">
              <input type="text" class="input_text" placeholder="Digite o seu nome de usuário" value="<?php echo $rows[0]['nickname']; ?>" name="nick_user" required>
              <input type="text" class="input_text" placeholder="Digite o seu nome completo" value="<?php echo $rows[0]['nome']; ?>" name="name_user" required>
              <input type="password" class="input_text" placeholder="Digite a sua senha"  name="psw" required>
            </div>
            <button class="button" type="submit">ATUALIZAR</button>
          </form>
          <a href='javascript:history.back(-1)'><img src="../../../../assets/Icons/voltar.svg" class="icon_voltar"></a>
          <a href='desconectar.php' onclick="return confirm('Tem certeza que deseja sair?')"><img src="../../../../assets/Icons/fechar.svg" class="icon_fechar"></a>
        </div>
    </section>
  </body>
</html>
