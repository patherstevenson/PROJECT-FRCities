<?php

set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');
require_once('lib/fonctions_parms.php');
require_once('lib/initDataLayer.php');
require_once('lib/watchdog.php');

try {
  
  if(alreadyLogged()) {
    produceError("Vous êtes déjà connecté");
  }
  else {
    $login = checkString('login');
    $password = checkString('password');

    if(tryConnect($my_authent)) {
      produceResult($_SESSION['ident']);
    }
    else {
      produceError('Login ou password incorrect');
     }
   }
  }
  catch (PDOException $e){
      produceError('Login ou password incorrect');
  }
  catch (ParmsException $e){
    produceError('Login ou password manquant');
}
  

?>
