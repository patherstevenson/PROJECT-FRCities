window.addEventListener('load', checkLogin);

function checkLogin() {
    fetchFromJson('services/getSession.php') // voir commentaire dans services/getSession.php pour comprendre l'intérêt recherché ici
    .then(checkAnswer);
}

function checkAnswer(tab) {
    if (tab['status'] =='ok') {
        userLogOn(tab["result"]);
    }
    else {
        userLogOut();
    }
}

function clearSomething(element) {
    var div = document.getElementById(element);
    while(div.firstChild && div.removeChild(div.firstChild));
}

function forceResetCommunes() {
    // va nous permettre d'obliger l'utilisateur à réutiliser le formulaire 
    // de communes pour générer l'affichage de la gestion des favoris dans le commune.js
    clearSomething("liste_communes");
    document.getElementById("form_communes").reset();
}

function sendLoginForm(ev){
    ev.preventDefault();
    let formData = new FormData(this);
    fetchFromJson('services/login.php', {method: 'post', body : formData})
    .then(LoginAnswer);
}

function LoginAnswer(tab) {
    clearSomething("login_answer");
    let div = document.getElementById("login_answer");
    let message;
    if (tab["status"] == "error") {
        message = document.createTextNode("Erreur à la connexion : " + tab["message"]);
        div.appendChild(message);
    }
    else {
        checkAnswer(tab);
    }
}

function userLogOn(user) {
    // nettoyage du contenu du div class=user
    clearSomething('user');

    // reset de l'affichage des communes et des inputs formulaire
    forceResetCommunes();

    // info de l'user
    let div = document.getElementById("user");
    div.innerHTML = '<span class="user_avatar"><img class="avatar" src="images/avatar_def.png"></span> ' 
    let span = document.createElement("span");
    span.setAttribute("class","user_desc");
    span.textContent = user.prenom + " " + user.nom + " " + " ( " + user.login +" ) ";
    div.appendChild(span);

    // button de déconnexion
    let logout_form = document.createElement("form");
    logout_form.setAttribute("action","services/logout.php")
    logoutButton = document.createElement("button");
    logoutButton.setAttribute("type", "submit");
    logoutButton.setAttribute("id", "button_logout");
    logoutButton.textContent = "Déconnexion";
    logout_form.appendChild(logoutButton);
    div.appendChild(logout_form);

    // création de la ul de la liste des favoris
    let fav = document.createElement("ul");
    fav.setAttribute("id","liste_favoris");
    document.getElementById("main").appendChild(fav);
    fetchListFavoris();
}

function userLogOut() {
    // nettoyage du contenu du div class=user
    clearSomething('user');

    // génération du formulaire pour se connecter
    let login_form = document.createElement("form");
    login_form.setAttribute("id","login_form");
    login_form.setAttribute("method","post");
    form_field = document.createElement("fieldset");
    login_form.appendChild(form_field);

    // login input
    login = document.createElement("input");
    login.setAttribute("type", "text");
    login.setAttribute("name","login");
    login.setAttribute("id","login");
    login.setAttribute("placeholder","Login");
    login.setAttribute("autofocus",""); // autofocus
    form_field.appendChild(login);

    // password input
    password = document.createElement("input");
    password.setAttribute("type", "password");
    password.setAttribute("name","password");
    password.setAttribute("id","password");
    password.setAttribute("placeholder","Password");
    form_field.appendChild(password);

    // ajout du listener d'envoi du form
    login_form.addEventListener("submit",sendLoginForm);

    // button de connexion
    let connexion = document.createElement("button");
    connexion.setAttribute("type","submit");
    connexion.setAttribute("id","connecter");
    connexion.setAttribute("onclick","hideDetailsLog()");
    connexion.textContent = 'Se connecter';
    form_field.appendChild(connexion);

    // ajout d'un redirect vers register.php
    let register = document.createElement("button");
    register.textContent = "S'inscrire";
    register.setAttribute("onclick","printRegister()");
    register.setAttribute("style","width:auto")
    register.setAttribute("id","register_redict");
    register.setAttribute("type","button");

    // zone de texte pour le retour de login
    let login_answer = document.createElement("p");
    login_answer.setAttribute("class","login_answer");
    login_answer.setAttribute("id","login_answer");

    // ajout au div class=user
    let div = document.getElementById("user");
    div.appendChild(login_form);
    div.appendChild(register);
    div.appendChild(login_answer);

    // reset de l'affichage des communes et des inputs formulaire
    forceResetCommunes();

}

function fetchListFavoris() {
    fetchFromJson("services/getFavoris.php")
    .then(processAnswer)
    .then(ListFavoris)
}

function ListFavoris(tab) {
    clearSomething("liste_favoris");
    let ul = document.getElementById("liste_favoris");
    for (let commune of tab) {
        let li = document.createElement("li");
        li.textContent = commune.insee;
        li.addEventListener("mousedown",function() {
        let insee = commune['insee'];
            fetchCommune(insee);
        });
        let icon = document.createElement('i');
        icon.setAttribute("class","gg-remove");
        icon.setAttribute("id",commune.insee);
        icon.addEventListener("mousedown",fetchRemoveListFavori);
        li.appendChild(icon);
        ul.appendChild(li);
    }
    return tab;   
}

function fetchRemoveListFavori(insee) {
    fetchFromJson("services/removeFavori.php?insee=" + String(insee.target.id))
    .then(processAnswer)
    .then(fetchListFavoris);
    let icon = document.getElementById(String(insee.target.id));
    icon.setAttribute("class","gg-add");
    icon.removeEventListener("mousedown",fetchRemoveFavori);
    icon.addEventListener("mousedown",fetchAddFavori);
  }


function processAnswer(answer){
    if (answer.status == "ok")
      return answer.result;
    else
      throw new Error(answer.message);
  }
  
function printRegister() {
    document.getElementById('register_form').style.display='block';
    hideDetailsLog();
}