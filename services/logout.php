<?php

set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');
require_once('lib/watchdog.php');

if (!alreadyLogged()) {
  produceError('Non connecté');
}
else{
  produceResult($_SESSION['ident']->login);
  session_destroy();
  header('Location: ../index.php'); // redirection sur index.php après la déconnexion
  exit();
}

?>
