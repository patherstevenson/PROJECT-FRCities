<?php

set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');
require_once('lib/fonctions_parms.php');
require_once('lib/initDataLayer.php');
try {
  
   $login = checkString('register_login'); // car conflit avec double id login (celui du login_form)
   $password = checkString('register_password'); // car conflit avec double id password (celui du login_form)
   $nom = checkString('name');// car conflit avec double id nom (celui du form choix)
   $prenom = checkString('prenom');
   
   $create = $data->createUser($login, $password, $nom, $prenom);
   
   produceResult($login);

  }
  catch (PDOException $e){
      produceError('login déjà utilisé');
  }
  catch (ParmsException $e){
    produceError('argument manquant');
}

?>
