<?php
  
 //session_save_path(__DIR__);
 session_name('ma_session');
 session_start();
 
 /**
  * test si le script s'exécute dans une session où l'utilisateur s'est déjà authentifié
  * se fonde sur le témoin d'authentification : $_SESSION['ident']
  */
 function alreadyLogged() : bool {
   return  isset($_SESSION['ident']); 
 }

 /** 
  * test si le script s'exécute dans une session ou l'authentification à échoué 
  * se fonde sur le témoin d'échec : $_SESSION['echec']
  */
 
 /**
  * tente de réaliser une nouvelle connexion
  * - si des identifiants valides ont été fournis dans $_POST, la connexion est réussie
  *    et l'identité de l'utilisateur est rangée dans $_SESSION['ident']
  * @param $authent_function  fonction d'authentification de profil  function(login, password) : Identite
  * @return false si connexion échouée, true si résussie
  */
 function tryConnect(callable $authent_function) : bool {
   if ( !isset($_POST['login']) || !isset($_POST['password']) ) // pas de login ou pas de password fourni => échec
     return FALSE;
    
   $person = $authent_function($_POST['login'],$_POST['password']);
   if ($person === NULL) {// authentification en échec
     return FALSE;
   }
   // authentification réussie
   $_SESSION['ident'] = $person;
   return TRUE;
 }
 
 $my_authent = [$data,"authentification"];  // méthode d'authentification à 
?>
