<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../../../../styles/styles.css">
    <title>Edição de Turma</title>
    <script>

        function changeLegendFile(){
          let legend = document.getElementById('file_legend');
          let input = document.getElementById('csv_file');
          if (input.value != ''){
            legend.innerHTML = 'Arquivo selecionado.';
          }else{
            legend.innerHTML = 'Clique aqui para selecionar o arquivo CSV contendo os alunos.';
          }
        }

        function changeStyle(id){
          let cb_input = document.getElementById('cb'+id);
          let cb_img = document.getElementById('icon'+id);

          if(cb_input.checked){
            document.getElementById('cb_lb'+id).style.color = '#13D4E2';
            cb_img.src = '../../../../assets/Icons/menos.svg'
          }else{
            document.getElementById('cb_lb'+id).style.color = '#BAE6E9';
            cb_img.src = '../../../../assets/Icons/mais.svg'
          }

        }

       function limparStudent(variavel){
         for (var i = 0; i < variavel.length; i++){
           document.getElementById(variavel[i]).checked = false;
           changeStyle(variavel[i].replace('cb', ''));
         }
       }

       function selectByCSV(variavel){
         for (var i = 0; i < variavel.length; i++){
           var element = document.getElementById('cb' + variavel[i]);
           if(element !== null){
             element.checked = true;
             changeStyle(variavel[i]);
           }
         }
       }

       function cadastraCSV(){
        //Seleciona o arquivo no input file
        const input = document.getElementById("csv_file");
        let legend = document.getElementById('file_legend').textContent;
        if(legend == 'Arquivo selecionado.'){
          const reader = new FileReader();
          reader.onload = function(){
            //console.log(reader.result);
            csv_keys = reader.result;
            //Separa as linhas tendo como parâmetro \n
            keys = csv_keys.split('\n');
            keys_str = '';
            //Cria a string com as keys separada por schlussel
            for (let i = 0; i < keys.length-1; i++){
              if (i != 0){
                keys_str += 'schlussel' + keys[i].trim();
              }else{
                keys_str += keys[i].trim();
              }
              ajaxRequest('../../add_class/add_class_CSV.php?keys=' + keys_str, 'retorno');
            }
          }
          reader.readAsText(input.files[0]);
        }else{
          alert('Nenhum arquivo selecionado!')
        }
      }

 			function retorno(texto){
        //Separa os ids por schlussel
        var ids = texto.split("schlussel");
        //Ativa a função que seleciona os alunos correspondentes aos ids
        selectByCSV(ids);

 			}

      //Não sei o que faz
			function ajaxRequest(url, callbackFunction){
				//incompat�vel com IE6 e IE5
				var xmlhttp;
				if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
					xmlhttp = new XMLHttpRequest();
				}else{// code for IE6, IE5
					xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
					//xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				//entender esta chamada
				xmlhttp.onreadystatechange=function(){
					//Esta fun��o � invocada sempre que readyState for alterado.
					//A propriedade readyState pode assumir os seguintes valores:
					//0: requisi��o n�o iniciada
					//1: conex�o estabelecida
					//2: requisi��o enviada
					//3: requisi��o sendo processada
					//4: requisi��o processada e resposta pronta
					if (xmlhttp.readyState == 4){
						eval(callbackFunction + "('" + xmlhttp.responseText + "')");
					}
				}
				xmlhttp.open("GET",url);
				xmlhttp.send();
			}

    </script>
  </head>
  <body>
    <?php
      if(!isset($_SESSION['name_user'])){
        $_GET['message'] = '<font color="red">VOCÊ PRECISA ESTAR LOGADO PARA ACESSAR</font><br>';
        header('Location: login.php');
        die();
      }

      function getClass($id_class_edit, $id_user){
      		try{
      			//define PDO - tell about the database file
      			$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      			//write SQL
      			$statement = $pdo->query("SELECT nome_turma, nome_curso FROM turma WHERE id_usuario='".$id_user."' AND id_turma='".$id_class_edit."'");
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

        function getStudentAtClass($id_class_edit){
      		try{
      			//define PDO - tell about the database file
      			$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      			//write SQL
      			$statement = $pdo->query("SELECT id_aluno FROM aluno_turma WHERE id_turma='".$id_class_edit."'");
      			//run the SQL
      			$rows = $statement->fetchAll(PDO::FETCH_ASSOC);

      		}catch(PDOException $e){
      			$rows = 0;
      			echo "<pre>";
      			echo $e;
      			echo "</pre>";
      		}

          $students = [];
          for($i = 0; $i < count($rows); $i++ ){
            $students[$i] = $rows[$i]['id_aluno'];
          }

      		return $students;
      	}

        function setChecked($id_student, $array){
          if(in_array($id_student, $array)){
            echo 'checked';
          }
        }

        $id_class_edit = $_GET['id_class'];
        $id_user = $_SESSION['id_user'];

        $class = getClass($id_class_edit, $id_user);
        $students = getStudentAtClass($id_class_edit);

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
       <div class="container all container_extended">
         <div class="title">ADICIONAR ALUNOS POR CSV</div>
         <div class="inner_container contour">
           <div class="div_file">
               <!-- MAX_FILE_SIZE deve preceder o campo input -->
               <input type="hidden" name="MAX_FILE_SIZE"  value="20000000" />
               <!-- O Nome do elemento input determina o nome da array $_FILES -->

               <input class="button" onchange="changeLegendFile()" id="csv_file" name="userfile" type="file"/>
               <label for="csv_file"><span class="button button_file">Procurar Arquivo</span></label>
               <span class="file_legend" id="file_legend">Clique aqui para selecionar o arquivo CSV contendo os alunos.</span>
           </div>
           <a href="../../../../assets/docs/students_class.csv" download="Modelo Turma CSV(não modifica a formatação).csv"><input class="button button_file" type="button" value="Baixar Modelo CSV" /></a>
           <a class="button" href="#divList" onclick="cadastraCSV()">CADASTRAR CSV</a>
          </div>

          <form action="settings.php?id_class=<?php echo $id_class_edit; ?>" method="post">
            <div class="title">EDIÇÃO DE TURMA</div>
            <div class="inner_container">
              <input type="text" class="input_text" placeholder="Digite o nome da turma" value="<?php echo $class[0]['nome_turma']; ?>" name="class_name" required>
              <input type="text"class="input_text" placeholder="Digite o curso da turma" value="<?php echo $class[0]['nome_curso']; ?>" name="course_class" required>
            <!--PHP-->
            <?php

              if(isset($_GET['message'])){
                if ($_GET['message'] != ''){
                  echo '<div class="message message_inner">'.$_GET['message'].'</div>';
                  $_GET['message'] = '';
                }
              }

              function getStudent($id_user){
              	try{
              		//define PDO - tell about the database file
              		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

              		//write SQL
              		$statement = $pdo->query("SELECT * FROM aluno WHERE id_usuario='".$id_user."' ORDER BY nome_aluno");

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

              $rows = getStudent($_SESSION['id_user']);

              //Colocando os id do alunos em um único array
              $id_student_js = '';
              for ($i = 0; $i < count($rows); $i++) {
                $id_student_js .= '"cb'.$rows[$i]['id_aluno'].'"';
                if ($i != count($rows)-1){
                  $id_student_js .= ',';
                }
              }

              ?>
              <!--JAVASCRIPT-->
              <script>
                var id_students = [<?php echo $id_student_js; ?>];
              </script>
              <?php

              echo '<br>';
              echo '<input type="button" class="button" onclick="limparStudent(id_students)" value="DESMARCAR TODOS OS ALUNOS"/>';
              echo '<div class="divList" id="divList">';
              echo '<table class="view_table_add_student">';
              echo '<tbody>';
              for ($i = 0; $i < count($rows); $i++) {
                $id_aluno = $rows[$i]['id_aluno'];
                echo '<tr id="cb_tr'.$id_aluno.'">';
                echo '<td>';
                echo '<input style = "display: none;" onchange="changeStyle('.$id_aluno.')" type = "checkbox" id = "cb'.$id_aluno.'" name = "student[]" value = "'.$id_aluno.'"';
                setChecked($id_aluno, $students);
                echo '>';
                echo '<label for = "cb'.$rows[$i]['id_aluno'].'"><img id="icon'.$id_aluno.'" class="cb_icon" src="../../../../assets/Icons/mais.svg"></ label>';
                echo '</td>';

                echo '<td>';
                echo '<label id="cb_lb'.$id_aluno.'" for = "cb'.$rows[$i]['id_aluno'].'">'.$rows[$i]['nome_aluno'].'</ label>';
                echo '</td>';
                echo '</tr>';
                // É feita uma primeira verificação nos estilo
                echo '
                  <script>
                    changeStyle('.$id_aluno.');
                  </script>
                ';
              }
              echo '</tbody>';
              echo '</table>';
              echo '</div>';

            ?>
            </div>
            <button class="button" onclick="return confirm('Deseja salvar as alterações?');" type="submit">ATUALIZAR TURMA</button>
        </form>
        <a href='../'><img src="../../../../assets/Icons/voltar.svg" class="icon_voltar"></a>
        <a href='desconectar.php' onclick="return confirm('Tem certeza que deseja sair?')"><img src="../../../../assets/Icons/fechar.svg" class="icon_fechar"></a>
      </div>
    </section>
  </body>
</html>
