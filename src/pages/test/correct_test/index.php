<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../../../styles/styles.css">
    <title>Correção de Prova</title>
    <script>
      function changeLegendFile(){
        let legend = document.getElementById('file_legend');
        let input = document.getElementById('files');
        if (input.value != ''){
          legend.innerHTML = 'Arquivo(s) selecionado(s).';
        }else{
          legend.innerHTML = 'Clique aqui para selecionar as provas que você escaneou.';
        }
      }

      function callLoading(txt){
        let legend = document.getElementById('file_legend').textContent;
        const loading_html = document.getElementById('loading');
        if(legend == 'Arquivo(s) selecionado(s).'){
          if(loading_html.style.display === 'none'){
            loading_html.innerHTML = '<img class="loading_gif" src="../../../assets/Icons/test.gif">' + txt;
            loading_html.style.display = 'flex';
          }else{
            loading_html.style.display = 'none';
          }
        }
      }
    </script>
  </head>
  <body>
    <div style="display: none;" class="loading" id="loading">
    </div>
    <?php
      if(!isset($_SESSION['name_user'])){
        $_GET['message'] = '<font color="red">VOCÊ PRECISA ESTAR LOGADO PARA ACESSAR</font><br>';
        header('Location: login.php?message='.$_GET['message']);
        die();
      }

      function getIdClass($id_user){
      	try{
      		//define PDO - tell about the database file
      		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      		//write SQL
      		$statement = $pdo->query("SELECT id_turma FROM turma WHERE id_usuario='".$id_user."'/* ORDER BY nome_aluno*/");

      		//run the SQL
      		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);

          if(count($rows) == 0){
            $_GET['message'] = '<font color="red">VOCÊ PRECISA TER CRIADO PELO MENOS UMA TURMA</font><br>';
            header('Location: menu.php?message='.$_GET['message']);
            die();
          }

      	}catch(PDOException $e){
      		$rows = 0;
      		echo "<pre>";
      		echo $e;
      		echo "</pre>";
      	}

      	return $rows;
      }

      function getTest($id_class){
      	try{
          $sql = "SELECT id_prova, nome_prova, id_turma FROM prova WHERE";
          for($i = 0; $i < count($id_class); $i++ ){
            if($i < count($id_class)-1){
              $sql .= ' id_turma = '.$id_class[$i]["id_turma"].' OR ';
            }else{
              $sql .= ' id_turma = '.$id_class[$i]["id_turma"];
            }
          }
      		//define PDO - tell about the database file
      		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      		//write SQL
      		$statement = $pdo->query($sql);

      		//run the SQL
      		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
          if(count($rows) == 0){
            $_GET['message'] = '<font color="red">VOCÊ PRECISA TER CRIADO PELO MENOS UMA PROVA</font><br>';
            header('Location: menu.php?message='.$_GET['message']);
            die();
          }

      	}catch(PDOException $e){
      		$rows = 0;
      		echo "<pre>";
      		echo $e;
      		echo "</pre>";
      	}
      	return $rows;
      }

      $id_class = getIdClass($_SESSION['id_user']);
      $test = getTest($id_class);

     ?>
     <section class="window">
       <div class="container all">
         <div class="inner_container">
          <form enctype="multipart/form-data" action="settings.php" method="POST">
              <div class="title">CORREÇÃO DE PROVA</div>
              <div class="div_file">
                <!-- MAX_FILE_SIZE deve preceder o campo input -->
                <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
                <!-- O Nome do elemento input determina o nome da array $_FILES -->
                <input onchange="changeLegendFile()" class="button" id="files" name="userfile[]" type="file" multiple required/>
                <label for="files"><span type="button" class="button button_file">Procurar Arquivo</span></label>
                <span class="file_legend" id="file_legend">Clique aqui para selecionar as provas que você escaneou.</span>
              </div>
              <br>
              <input class="button" onclick="callLoading('CORRIGINDO PROVAS')" type="submit" value="Enviar arquivo" />
          </form>
        </div>
        <a href='javascript:history.go(-1)'><img src="../../../assets/Icons/voltar.svg" class="icon_voltar"></a>
        <a href='desconectar.php' onclick="return confirm('Tem certeza que deseja sair?')"><img src="../../../assets/Icons/fechar.svg" class="icon_fechar"></a>
      </div>
    <section>
  </body>
</html>
