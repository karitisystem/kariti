<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="../../../styles/styles.css">
    <script>
			function preencher(){
        // Pega o valor do id da prova
				var id_test = document.getElementById("test_name").value.split(",")[0];
				// Envia por url qual o estado selecionado
				ajaxRequest('../../../services/get_student_test_ajax?sigla=' + id_test, 'retorno');
			}

      function callLoading(txt){
        const loading_html = document.getElementById('loading');
        if(loading_html.style.display === 'none'){
          loading_html.innerHTML = '<img class="loading_gif" src="../../../assets/Icons/download.gif">' + txt;
          loading_html.style.display = 'flex';
        }else{
          loading_html.style.display = 'none';
        }
      }

      function downloadTemplate(){
        var id_student = document.getElementById("student").value;
        var ids = document.getElementById("test_name").value.split(",");
        var id_test = ids[0], id_class = ids[1];
        //Envia por url qual o estado selecionado
        ajaxRequest('../../../services/download_template?id_test=' + id_test + '&id_class=' + id_class + '&id_student=' + id_student, 'retornoDownloadTemplate');
        callLoading('GERANDO CARTÕES-RESPOSTA');
      }

      // Download the tamplates
      function retornoDownloadTemplate(tail){
        path = `../../../../${tail}`
        let element = document.createElement('a');
      	element.setAttribute('href', path);
      	element.setAttribute('download', 'Provas');

      	element.style.display = 'none';
      	document.body.appendChild(element);

      	element.click();

      	document.body.removeChild(element);
        callLoading();

        // Precisamos desse tempo antes do programa apagar o arquivo, pra dar tempo dele ser baixado
        // Tempo : 15s
        ajaxRequest('../../../services/delete_file?path=' + tail, 'retornoDelete');
      }

      function retornoDelete(text){
        let txt = text;
      }

			function retorno(texto){
				//Transforma a str em um vetor baseado no parâmtro ,
				var vetor = texto.split(",");

				//Seleciona o elemnto cidades pelo id
				var students = document.getElementById("student");

				//Remove itens de students
				while (students.length > 0) {
					students.remove(0);
				}

				//Cria os elementos option dentro do select alunos
        var student = document.createElement("option");
        student.text = "Todos";
        student.value = "0";
        students.add(student);
				for (var i = 0; i < vetor.length; i++) {
					var student = document.createElement("option");
					student.text = vetor[i].split(';')[0];
					student.value = vetor[i].split(';')[1];
					students.add(student);
				}
			}

			//Executa um arquivo php especificado
			function ajaxRequest(url, callbackFunction){
				//incompatível com IE6 e IE5
				var xmlhttp;
				if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
					xmlhttp = new XMLHttpRequest();
				}else{// code for IE6, IE5
					xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
					//xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				//entender esta chamada
				xmlhttp.onreadystatechange = function(){
					//Esta funçãoo é invocada sempre que readyState for alterado.
					//A propriedade readyState pode assumir os seguintes valores:
					//0: requisição não iniciada
					//1: conexão estabelecida
					//2: requisiçã enviada
					//3: requisição sendo processada
					//4: requisição processada e resposta pronta
					if (xmlhttp.readyState == 4){
						eval(callbackFunction + "('" + xmlhttp.responseText + "')");
					}
				}
				xmlhttp.open("GET",url);
				xmlhttp.send();
			}

		</script>
    <title>Busca de Prova</title>
  </head>
  <body onload="preencher()">
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
          // Ordena as provas em ordem alfabética
          $sql .= ' ORDER BY nome_prova';
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

      <?php

        if(isset($_GET['message'])){
          if ($_GET['message'] != ''){
            echo '<div class="message_container">';
            echo '<div class="message">'.$_GET['message'].'</div>';
            echo '</div>';
            $_GET['message'] = '';
          }
        }

       ?>

      <div class="container all">
        <form action="view_test/index.php" method="post">
          <div class="inner_container">
              <div class="title">BUSCA DE PROVA</div>
                  <select title="Selecione a prova que deseja verificar" class="input_text" id="test_name" name="id_test" onchange="preencher()">
                    <?php
                    foreach ($test as $k) {
                      echo '<option value="'.$k['id_prova'].','.$k['id_turma'].'">'.$k['nome_prova'].'</option>';
                    }
                     ?>
                  </select>

                  <select title="Selecione a turma" class="input_text" id="student" name="id_student">
                  </select>
          </div>
          <button class="button" type="submit">VERIFICAR PROVA</button>
          <button class="button" type='button'  onclick="downloadTemplate()">GERAR CARTÃO RESPOSTA</button>
        </form>
        <a href='../../menu'><img src="../../../assets/Icons/voltar.svg" class="icon_voltar"></a>
        <a href='desconectar.php' onclick="return confirm('Tem certeza que deseja sair?')"><img src="../../../assets/Icons/fechar.svg" class="icon_fechar"></a>
      </div>
    </section>
  </body>
</html>
