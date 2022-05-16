<?php
set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');
require_once('lib/fonctions_parms.php');
require_once('lib/initDataLayer.php');

try{
  $insee = checkString('insee');
  $commune = $data->getDetails($insee);
  
  produceResult($commune);
  
}
catch (PDOException $e){
    produceError($e->getMessage());
}


?>
