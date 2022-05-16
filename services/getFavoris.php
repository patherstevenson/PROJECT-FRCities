<?php
set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');
require_once('lib/initDataLayer.php');
require_once('lib/watchdog.php');

try{
  if (alreadyLogged()) {
    $user = $_SESSION['ident']->login;
    $favoris = $data->getFavoris($user);
    
    produceResult($favoris);
  }
  else {
    produceError("Non connecté");
    exit();
  }
  
}
catch (PDOException $e){
    produceError($e->getMessage());
}


?>
