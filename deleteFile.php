<?php

  // Getting settings to send email
  $ini_file = parse_ini_file('settings.ini', true);
  // Getting seting to use python in windows or linux
  $python_w = $ini_file['SYSTEM']['python_windows_path'];
  $python_l = $ini_file['SYSTEM']['python_linux_path'];

  $path = $_GET['path'];

  if(strtolower($ini_file['SYSTEM']['OS']) == 'windows'){
    $cmd = $python_w.' deleteFile.py '.$path;
  }else if(strtolower($ini_file['SYSTEM']['OS']) == 'linux'){
    $cmd = $python_l.' deleteFile.py '.$path;
  }

  sleep(8);
  exec($cmd, $out);
  echo 'prova deletada com sucesso . Path:'. $path;

 ?>
