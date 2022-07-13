<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../../../styles/styles.css">
    <script>

      function sumGrades(ids){
        // alert(ids);
        let sum = 0;
        for(let i=0; i < ids.length; i++){
          let element = document.getElementById(ids[i]);
          // If element is a number
          if (!isNaN(element.value)){
            sum += parseFloat(element.value);
          }
        }
        let element2 = document.getElementById("grade_tot");
        element2.value = sum;
      }
    </script>
    <title>Cadastro de Gabarito</title>
  </head>
  <body>
    <?php
      if(!isset($_SESSION['name_user'])){
        $_GET['message'] = '<font color="red">VOCÊ PRECISA ESTAR LOGADO PARA ACESSAR</font><br>';
        header('Location: login.php');
        die();
      }
      
      $test_name = $_POST['test_name'];
      $class_name = explode(';', $_POST['class_name'])[1];
      $id_class = trim(explode(';', $_POST['class_name'])[0]);
      $a_ques = $_POST['a_ques'];
      $a_alt = $_POST['a_alt'];

     ?>
     <section class="window">
       <div class="container all container_extended">
         <div class="inner_container">
           <form enctype="multipart/form-data" action="settings.php" method="POST">
               <div class="title">CADASTRO DE GABARITO</div>

               <input type="hidden" name="test_name" value="<?php echo $test_name ?>">
               <input type="hidden" name="id_class" value="<?php echo $id_class ?>">
               <input type="hidden" name="a_ques" value="<?php echo $a_ques ?>">
               <input type="hidden" name="a_alt" value="<?php echo $a_alt ?>">

               <?php
                 echo'<div class="caption">';
                 echo 'Prova: '.$test_name.'<br>';
                 echo 'Turma: '.$class_name.'<br>';
                 echo 'Data: <input type="date" onclick="this.select();" name="date_test" required>';
                 echo'</div>';

                 $alternatives = ['A','B','C','D','E','F','G'];

                 echo '<table class="gabarito">';
                 //Impressão das alternativa (A, B, C...)
                 echo '<tr>';
                 echo '<td></td>';
                 for($i = 0; $i < $a_alt; $i++){
                   echo '<td>'.$alternatives[$i].'</td>';
                 }
                 echo '</tr>';
                 //Imprimindo Questões e Radios
                 for($i = 1; $i <= $a_ques; $i++){
                   echo '<tr>';
                   echo '<td>';
                   echo $i;

                   for($j = 1; $j <= $a_alt; $j++){
                     echo '<td>';
                     echo '<input type="radio" name="q'.$i.'" value="'.$j.'" required>';
                     echo '</td>';
                   }
                   echo '<td>';
                   echo '<input min="0" max="100" onclick="this.select();" class="input_number_gabarito" type="number" step="0.01" value="0" id="w'.$i.'" name="w'.$i.'">';
                   echo '</td>';
                   echo '</tr>';
                   $all_grades_ids[] = '"'.'w'.$i.'"';
                 }
                 echo '</table>';
                 $ids =  implode(",", $all_grades_ids);

                 echo '<div class="caption">';
                 echo 'Nota total da prova: <input type="number" class="input_number_gabarito" value="0" id="grade_tot" min="0" max="100" readonly> pontos<br>';
                 echo '</div>';
                 echo'
                 <script>
                  setInterval(function(){ sumGrades(['.$ids.']); }, 1000);
                 </script>
                 ';
                ?>
               <input type="submit" value="CADASTRAR PROVA" class="button"/>
           </form>
         </div>
         <a href='javascript:history.go(-1)'><img src="../../../assets/Icons/voltar.svg" class="icon_voltar"></a>
         <a href='desconectar.php' onclick="return confirm('Tem certeza que deseja sair?')"><img src="../../../assets/Icons/fechar.svg" class="icon_fechar"></a>
       </div>
     </section>
  </body>
</html>
