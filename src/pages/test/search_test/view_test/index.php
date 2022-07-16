<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../../../../styles/styles.css">
    <title>Análise de Prova</title>
    <script>

    function markAll(ids){
      let my_check = document.getElementById('my_check').checked;
      if(my_check == true){
        for(let i = 0; i < ids.length; i++){
          document.getElementById('cb' + ids[i]).checked = true;
        }
      }else{
        for(let i = 0; i < ids.length; i++){
          document.getElementById('cb' + ids[i]).checked = false;
        }
      }
    }

    function callLoading(txt, gif_name){
      const loading_html = document.getElementById('loading');
      if(loading_html.style.display === 'none'){
        loading_html.innerHTML = '<img class="loading_gif" src="../../../../assets/Icons/'+gif_name+'.gif">' + txt;
        loading_html.style.display = 'flex';
      }else{
        loading_html.style.display = 'none';
      }
    }

    function sendEmail(id_test, id_student){
      var online = navigator.onLine;
      // Verify if there is conection to the internet
      if(online){
        let vetor = [];
        for(let i = 0; i < id_student.length; i++){
          let element = document.getElementById('cb' + id_student[i]);
          if(element.checked == true){
            let id = id_student[i];
            let hit = document.getElementById('hit' + id).value;
            let grade = document.getElementById('grade' + id).value;
            let str = id + 'schlussel' + hit + 'schlussel' + grade;
            vetor.push(str);
          }
        }
        let student_data = vetor.join('ergebnis');
        if(vetor.length > 0){
          ajaxRequest('../../../../services/send_email?id_test=' + id_test + '&student_data=' + student_data, 'retornoSendEmail');
        }
        callLoading('ENVIANDO E-MAILS', 'send_email');
      }else{
        alert('Você não está conectado à internet!');
      }
    }

    function retornoSendEmail(text){
      let txt = text;
      callLoading()

      const message_container = document.getElementById('message_container');
      const message_html = document.getElementById('message');
      if(message_container.style.display === 'none'){
        message_html.innerHTML =  txt;
        message_container.style.display = 'flex';
      }else{
        message_html.innerHTML =  txt;
      }
    }

      function downloadTest(id_test, dados){
        let vetor = [];
        for(let i = 0; i < dados.length; i++){
          let element = document.getElementById('cb' + dados[i]);
          if(element.checked == true){
            let id = dados[i];
            vetor.push(id);
          }
        }
        if(vetor.length > 0){
          let ids = vetor.join('schlussel');
          ajaxRequest('../../../../services/download_test?id_test=' + id_test + '&id_students=' + ids, 'retornoTests');
          callLoading('GERANDO PROVAS', 'download');
        }else {
          alert('Você não selecionou nenhum aluno!')
        }
      }

      function retornoTests(tail){
        let path = `../../../../../${tail}`
        let element = document.createElement('a');
      	element.setAttribute('href', path);
      	element.setAttribute('download', 'Provas');

      	element.style.display = 'none';
      	document.body.appendChild(element);

      	element.click();

      	document.body.removeChild(element);

        ajaxRequest('../../../../services/delete_file?path=' + tail, 'retornoDelete');
        callLoading();
      }

      function retornoDelete(text){
        let txt = text;
      }

      function downloadCSV(dados){
        let datas = '';
        let j = 0;
        let test_name = document.getElementById('testNameJS').value;
        //Verifica em todos os checkboxs
        for(let i = 0; i < dados.length; i++){
          let element = document.getElementById('cb' + dados[i]);
          if(element.checked == true){
            let id = document.getElementById('id' + dados[i]).value;
            let name = document.getElementById('name' + dados[i]).value;
            let grade = document.getElementById('grade' + dados[i]).value;
            //Separa os elementos por schlussel
            let str = id + 'schlussel' + name + 'schlussel' + grade;
            // ergebnis
            if(j != 0){
              datas += 'ergebnis' + str;
            }else{
              datas += str;
            }
            j += 1;
          }
        }
        if (j != 0){
          ajaxRequest('../../../../services/download_grades?datas=' + datas + '&test_name=' + test_name, 'retornodownloadCSV');
          callLoading('GERANDO CSV', 'download');
        }else {
          alert('Você não selecionou nenhum aluno!');
        }
      }

      function retornodownloadCSV(tail){
        let path = `../../../../services/download_grades/${tail}`
        let element = document.createElement('a');
        element.setAttribute('href', path);
        element.setAttribute('download', tail);
        
        element.style.display = 'none';
        document.body.appendChild(element);
        
        element.click();
        
        document.body.removeChild(element);
        // Precisamos desse tempo antes do programa apagar o arquivo, pra dar tempo dele ser baixado
        // Tempo : 15s
        path = `src/services/download_grades/${tail}`
        ajaxRequest('../../../../services/delete_file?path=' + path, 'retornoDelete');
        callLoading();
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
         console.log(xmlhttp.responseText);
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
    <div style="display: none;" class="loading" id="loading">
    </div>
    <?php
      if(!isset($_SESSION['name_user'])){
        $_GET['message'] = '<font color="red">VOCÊ PRECISA ESTAR LOGADO PARA ACESSAR</font><br>';
        header('Location: login.php');
        die();
      }

      function getTest($id_test){
      	try{
      		//define PDO - tell about the database file
      		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      		//write SQL
      		$statement = $pdo->query("SELECT * FROM prova WHERE id_prova='".$id_test."'");

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

      function getTemplate($id_test){
      	try{
      		//define PDO - tell about the database file
      		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      		//write SQL
      		$statement = $pdo->query("SELECT n_questao, opcao, peso FROM gabarito WHERE id_prova='".$id_test."'");

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

      function getTestStudent($id_student, $id_test){
      	try{
      		//define PDO - tell about the database file
      		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      		//write SQL
      		$statement = $pdo->query("SELECT n_questao, opcao FROM aluno_prova WHERE id_aluno='".$id_student."' AND id_prova='".$id_test."'");

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

      function getStudents($id_student, $id_class){
      	try{

          //Quer dizer que toda a turma foi selecionada
          if ($id_student == '0'){
            //Pegando primeiro os ids dos alunos relacionados as turma
            $sql = "SELECT id_aluno FROM aluno_turma WHERE id_turma='".$id_class."'";

            $pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

        		//write SQL
        		$statement = $pdo->query($sql);

        		//run the SQL
        		$id_student = $statement->fetchAll(PDO::FETCH_ASSOC);

            //Redundando o dado de is pra ficar junto com os nomes certos
            $sql = "SELECT id_aluno, nome_aluno FROM aluno WHERE";
            for($i = 0; $i < count($id_student); $i++ ){
              if($i < count($id_student)-1){
                $sql .= ' id_aluno = '.$id_student[$i]["id_aluno"].' OR ';
              }else{
                $sql .= ' id_aluno = '.$id_student[$i]["id_aluno"];
              }
            }
            $sql .= ' ORDER BY nome_aluno';

          //Quer dizer que apenas um aluno foi selecionado
          }else{
            $sql = "SELECT id_aluno, nome_aluno FROM aluno WHERE id_aluno='".$id_student."'";
          }


      		//define PDO - tell about the database file
      		$pdo = new PDO('sqlite:'.$_SESSION['absolute_path_base'].'src/database/database.db');

      		//write SQL
      		$statement = $pdo->query($sql);

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

      function getTestResult($template, $testStudent){
        $is_test_done = count($testStudent) <= 0 ? false : true;
        $hit = 0;
        $grade = 0;
        for($i = 0; $i < count($template); $i++){
          for($j = 0; $j < count($testStudent); $j++){
            if($template[$i]['n_questao'] == $testStudent[$j]['n_questao']){
              if($template[$i]['opcao'] == $testStudent[$j]['opcao']){
                $hit++;
                $grade += (float)$template[$i]['peso'];
              }
            }
          }
        }

        return [$hit, $grade, $is_test_done];
      }

      $vector = explode("," , $_POST['id_test']);
      $id_test = $vector[0];
      $id_class = $vector[1];
      $id_student = $_POST['id_student'];

      $test = getTest($id_test);
      $template = getTemplate($id_test);
      $students = getStudents($id_student, $id_class);

     ?>
     <section class="window">

       <div style="display: none;" class="message_container" id="message_container">
         <div class="message" id="message"></div>
       </div>

       <div class="container all container_extended">
         <form enctype="multipart/form-data" action="../../../../services/delete_test/index.php" method="POST">
           <div class="title">ANÁLISE DE PROVA</div>
            <div class="inner_container">

             <?php
               echo '<div class="caption">';
               echo 'Prova: '.$test[0]["nome_prova"].'<br>';
               echo 'Total de questões: '.$test[0]["n_questoes"].'<br>';
               echo '</div>';

               //Table starts here
               echo '<div class="divList">';
               echo '<table class="test_table_v">';
               echo '<thead>';
               echo '<tr>';
               echo '<th></th><th class="align_left">Aluno:</th><th class="align_right">Total de Acertos:</th class="align_right"><th class="align_right">Notal Final:</th>';
               echo '</tr>';
               echo '</thead>';
               echo '<tbody>';
               for($i = 0; $i < count($students); $i++){
                 echo '<tr>';
                 echo '<td>';
                 echo '<input type = "checkbox" id = "cb'.$students[$i]["id_aluno"].'" name = "student[]" value = "'.$students[$i]["id_aluno"].'">';
                 echo '</td>';
                 echo '<td class="align_left">';
                 echo '<label for = "cb'.$students[$i]["id_aluno"].'">'.$students[$i]["nome_aluno"].'</label>';
                 echo '</td>';

                 $testStudent = getTestStudent($students[$i]['id_aluno'], $id_test);
                 $vector = getTestResult($template, $testStudent);
                 $is_test_done = $vector[2];
                 $hit = $is_test_done ? $vector[0] : '-' ;
                 $grade = $is_test_done ? $vector[1] : '-';
                 echo '<td class="align_right">'.$hit.'</td>';
                 echo '<td class="align_right">'.$grade.'</td>';
                 echo '</tr>';
                 //Hidden inputs
                 //Id
                 echo '<input type="hidden" id="id'.$students[$i]["id_aluno"].'" value="'.$students[$i]["id_aluno"].'">';
                 //Nome
                 echo '<input type="hidden" id="name'.$students[$i]["id_aluno"].'" value="'.$students[$i]["nome_aluno"].'">';
                 //Nº Acertos
                 echo '<input type="hidden" id="hit'.$students[$i]["id_aluno"].'" value="'.$hit.'">';
                 //Nota
                 echo '<input type="hidden" id="grade'.$students[$i]["id_aluno"].'" value="'.$grade.'">';
                 $all_students_ids[] = $students[$i]["id_aluno"];
               }
               echo '</tbody>';
               echo '</table>';
               echo '</div>';
               //Prova
               echo '<input type="hidden" id="testNameJS" value="'.$test[0]["nome_prova"].'">';
               // Id test
               echo '<input type="hidden" name="id_test" value="'.$id_test.'">';


              ?>
              <div class="caption">
              <input onchange="markAll([<?php echo implode(',', $all_students_ids);?>])" type = "checkbox" name="my_check" id = "my_check">
              <label for="my_check">Selecionar Todos</label>
              </div>
       </div>
       <!--Transforma os itens de $all_students_ids em uma string separada por , (vírgula)-->
       <button type="button" class="button" onclick="downloadTest(<?php echo $id_test.',['.implode(',', $all_students_ids  );?>])">BAIXAR PROVAS</button>

       <button type="button" class="button" onclick="downloadCSV([<?php echo implode(',', $all_students_ids  );?>])">BAIXAR CSV</button>

       <button type="button" class="button" onclick="sendEmail(<?php echo $id_test.',['.implode(',', $all_students_ids  );?>])">ENVIAR POR EMAIL</button>

       <button type="submit" class="red-button" onclick="return confirm('Deseja realmente apagar esta prova?')">DELETAR PROVA</button>
     </form>
     <a href='javascript:history.go(-1)'><img src="../../../../assets/Icons/voltar.svg" class="icon_voltar"></a>
       <a href='desconectar.php' onclick="return confirm('Tem certeza que deseja sair?')"><img src="../../../../assets/Icons/fechar.svg" class="icon_fechar"></a>
     </div>
   </section>
  </body>
</html>
