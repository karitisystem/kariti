<?php

	session_start();

	// Apaga todas as variáveis da sessão
	$_SESSION = array();

	// Se é preciso matar a sessão, então os cookies de sessão também devem ser 	apagados.
	// Nota: Isto destruirá a sessão, e não apenas os dados!
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}

	// Por último, destrói a sessão
	session_destroy();

	//Zera a super global message
	$_GET['message'] = '<font color="green">VOCÊ SE DESCONECTOU</font><br>';;
	header('Location: ../../pages/login?message='.$_GET['message'])

?>
