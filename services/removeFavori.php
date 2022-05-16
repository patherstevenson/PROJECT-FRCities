<?php
set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');
require_once('lib/fonctions_parms.php');
require_once('lib/initDataLayer.php');
require_once('lib/watchdog.php');

try{
  if (alreadyLogged()) {
    $user = $_SESSION['ident']->login;
    $insee = checkString("insee");

    $removed = $data->removeFavori($user,$insee);

    produceResult($insee);
  }
  else {
    produceError("Non connecté");
    exit();
  }
  
}
catch (PDOException $e){
    produceError("Insee non présent dans les favoris");
}


?>
