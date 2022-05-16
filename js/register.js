
window.addEventListener('load',initForm);

function initForm() {
    document.forms.register.addEventListener("submit", sendFormRegister);
}

function sendFormRegister(ev){
    ev.preventDefault();
    let formData = new FormData(this);
    fetch('./services/createUser.php', {method: 'post', body : formData})
    .then(function(ans){
        return ans.json();
      })
    .then(createMessage);
}

function createMessage(tab) {
    var div = document.getElementById('register_answer');
    while(div.firstChild && div.removeChild(div.firstChild));
    let message;
    if (tab['status'] =='ok') {
        message = document.createTextNode("Création effectuée pour l'utilisateur : " + tab['result']);
    } 
    else if(tab['status'] =='error') { 
        message = document.createTextNode("Erreur à la création du compte : " + tab['message']);
    }
    else {
        message = document.createTextNode("Erreur à la création du compte : Veuillez contacter un administrateur"); // <- bug si affiché 
    }

    div.appendChild(message);
}