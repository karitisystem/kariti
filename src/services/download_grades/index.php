<?php

  $test_name = $_GET['test_name'];
  $datas = $_GET['datas'];

  $headers = ['Nome da Prova', 'Id do Aluno', 'Nome', 'Nota'];

  $students = [];
  $data[] = explode('ergebnis', $datas);
  foreach ($data[0] as $k) {
    $var[] = explode('schlussel', $k);
    $students[] = [
                    'test_name' => $test_name,
                    'id_student' => explode('schlussel', $k)[0],
                    'student_name' => explode('schlussel', $k)[1],
                    'grade' => explode('schlussel', $k)[2],
                  ];
  }
  $test_name = str_replace('/', '_', $test_name);
  $file_name = 'Notas_Alunos_'.str_replace(' ', '_', $test_name).'.csv';
  $arquivo = fopen($file_name, 'w');
  $delimiter = ',';
  fputcsv($arquivo , $headers, $delimiter);


  foreach ($students as $linha) {
      fputcsv($arquivo, $linha, $delimiter);
  }
  fclose($arquivo);

  echo $file_name;

 ?>
