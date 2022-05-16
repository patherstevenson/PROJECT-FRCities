<?php

/**
 * service qui retourne l'objet Identite de l'utilisateur s'il est connecté
 * ce qui permet lors d'un refresh de la page du site de conserver la session active
 * et éviter le fait d'avoir l'interface non connecté lors d'un refresh de page 
 * et de ne plus pouvoir se connecter étant donner que l'utilisateur n'aura pas cliquer
 * sur le bouton de déconnexion donc pas de logout donc il ne pourra pas se connecter avec son login
 * car il sera indiqué qu'il est déjà connecté
 */

set_include_path('..'.PATH_SEPARATOR);
require_once('lib/common_service.php');
require_once('lib/watchdog.php');

if (alreadyLogged()) {
  produceResult($_SESSION['ident']);
}
else {
  produceError("Non connecté");
  exit();
}

?>
