<?php
 
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <meta charset="UTF-8"/>
    <title>Communes de la MEL</title>
    <link rel="stylesheet" type="text/css" href="style_index.css" />
    <script src="js/fetchUtils.js"></script>
    <script src="js/communes.js"></script>
    <script src="js/carte.js"></script>
    <script src="js/login.js"></script>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
   integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
   crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
   integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
   crossorigin=""></script>

  </head>
<body>
<header>
  <h1 class="title">
    <div class="logo_mel"><img src="images/logo_mel.png"></div>
    LES COMMUNES DE LA MEL
  </h1> 
  <div id="user"></div>
  <div id="register_form" class="modal">
<span onclick="document.getElementById('register_form').style.display='none'" class="close" title="Fermer">&times;</span>
  <?php require_once("register.php");?>
</div>
</header>
<section id="main">
  <div id="choix">
    <form id="form_communes" action>
      <fieldset>
        <legend>Choix des communes</legend>
          <hr/>
        <label>Territoire :
          <select name="territoire">
              <option value=""
                      data-min_lat="50.499" data-min_lon="2.789"
                      data-max_lat="50.794" data-max_lon="3.272"
              >
                Tous
              </option>
          </select>
        </label>
        <label>Nom de commune :
            <input name="nom" id="nom" type="text"/>
            <br />
        </label>
        <label>Surface minimale (en hectares) :
            <input name="surface" id="surface" type="number" min="0"/>
            <br />
        </label>
        <label>Population minimale :
            <input name="pop_min" id="pop_min" type="number" min="0"/>
            <br />
        </label>
        </fieldset>
      <button type="submit" id="button_communes" onclick="hideDetails()">Afficher la liste </button>
    </form>
  </div>
  <div id='carte'></div>
  <ul id="liste_communes">
</ul>
  <div id="details"></div>
</section>
<footer>
</footer>
</body>
</html>
