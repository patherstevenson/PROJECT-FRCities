<?php
set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');
require_once('lib/fonctions_parms.php');
require_once('lib/initDataLayer.php');

try{
  $territoire = checkUnsignedInt('territoire',NULL,FALSE);
  $nom = checkString('nom',NULL,FALSE);
  $surface_min = checkUnsignedInt('surface',NULL,FALSE);
  $pop_min = checkUnsignedInt('pop_min',NULL,FALSE);
  $communes = $data->getCommunes($territoire,$nom,$surface_min,$pop_min);
  
  produceResult($communes);
  
}
catch (PDOException $e){
    produceError($e->getMessage());
}


?>
