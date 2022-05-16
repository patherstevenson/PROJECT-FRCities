<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <meta charset="UTF-8"/>
    <title>Créer un compte</title>
    <!-- <link rel="stylesheet" type="text/css" href="style_index.css" /> 
      la page register.php ne sert que d'invocation au form d'inscription la gestion de son affichage et mise en page 
      a été faite et pensée de façon modal sur button "S'inscrire" de la page index.php -->
    <script src="js/register.js"></script>
</head>
<body>
<form id="register" method="POST">
 <fieldset>
 <legend>CRÉER UN COMPTE</legend>
 <hr>
   <label for="nom">Nom :</label>
   <input type="text" name="name" id="name" required="required" autofocus/>
   <label for="prenom">Prénom :</label>
   <input type="text" name="prenom" id="prenom" required="required" autofocus/>
   <label for="login" id ="label_login">Login :</label>
   <input type="text" name="register_login" id="register_login" required="required" autofocus/>
  <label for="password" id="label_pw">Password :</label>
  <input type="password" name="register_password" id="register_password" required="required" />
  <button type="submit" name="register_submit" value="submit_register">S'inscrire</button>
 </fieldset>
</form>
<div id="register_answer">
</div>
</body>
</html>
