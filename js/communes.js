
window.addEventListener('load',initForm);

function initForm(){
  fetchFromJson('services/getTerritoires.php')
  .then(processAnswer)
  .then(makeOptions);
  
  document.forms.form_communes.addEventListener("submit", sendForm);
  
  // décommenter pour le recentrage de la carte :
  document.forms.form_communes.territoire.addEventListener("change",function(){
    centerMapElt(this[this.selectedIndex]);
  });
}

function processAnswer(answer){
  if (answer.status == "ok")
    return answer.result;
  else
    throw new Error(answer.message);
}


function makeOptions(tab){
  for (let territoire of tab){  
    let option = document.createElement('option');
    option.textContent = territoire.nom;
    option.value = territoire.id;
    document.forms.form_communes.territoire.appendChild(option);
    for (let k of ['min_lat','min_lon','max_lat','max_lon']){
      option.dataset[k] = territoire[k];
    }
  }
}

function clearSomething(element) {
  var div = document.getElementById(element);
  while(div.firstChild && div.removeChild(div.firstChild));
}

function sendForm(ev){ // form event listener
  ev.preventDefault();
  let args = new FormData(this);
  let queryString = new URLSearchParams(args).toString();
  let url = 'services/getCommunes.php?' + queryString;
  fetchFromJson(url)
  .then(processAnswer)
  .then(makeCommunesItems);
}

function makeCommunesItems(tab){
  var ul = document.getElementById("liste_communes");
  clearSomething("liste_communes");
  for (let commune of tab) {
    let li = document.createElement('li');
    li.textContent = commune.nom;
    li.value = commune.id;
    ul.appendChild(li);
    for (let k of ['insee', 'nom', 'lat', 'lon', 'min_lat', 'min_lon', 'max_lat', 'max_lon']){
      li.dataset[k] = commune[k];
    }
    li.addEventListener("mouseover",function() {
      centerMapElt(li);
    });
    li.addEventListener("mousedown",function() {
      let insee = commune['insee'];
        fetchCommune(insee);
        document.getElementById("details").style.display="block";
    });
  }
  clearSomething("details");
  fetchFavoris();
}

function fetchCommune(insee){
  fetchFromJson('services/getDetails.php?insee=' + insee)
    .then(processAnswer)
    .then(displayCommune);
}

function displayCommune(commune){
  clearSomething("details");
  let details = document.getElementById("details");
  let ul = document.createElement('ul');
  for (let detail of ['insee','nom','nom_terr','surface','perimetre','pop2016','lat','lon']) {
    let li = document.createElement('li');
    li.textContent = detail + " : " + commune[detail];
    ul.appendChild(li);
  }
  details.appendChild(ul);
  createDetailMap(commune);
}

/**
 * Recentre la carte principale autour d'une zone rectangulaire
 * elt doit comporter les attributs dataset.min_lat, dataset.min_lon, dataset.max_lat, dataset.max_lon, 
 */
function centerMapElt(elt){
  let ds = elt.dataset;
  map.fitBounds([[ds.min_lat,ds.min_lon],[ds.max_lat,ds.max_lon]]);
}

function fetchFavoris() {
  fetchFromJson('services/getFavoris.php')
  .then(makeFavoris)
  .then(fetchListFavoris);
}

function makeFavoris(tab) {
  if (tab["status"] == "ok") {
    document.querySelectorAll(".gg-remove").forEach(el => el.remove());
    document.querySelectorAll(".gg-add").forEach(el => el.remove());
    let ul = document.getElementById("liste_communes");
    fav = tab["result"];
    child = 0;
    while (child < ul.children.length) {
      isFavori = false;
      index = 0;
      let icon = document.createElement('i');
      while (!isFavori && index < fav.length) {
        if (fav[index]['insee'] == ul.children[child].dataset['insee']) {
          icon.setAttribute("class","gg-remove");
          icon.setAttribute("id",ul.children[child].dataset['insee']);
          icon.addEventListener("mousedown",fetchRemoveFavori);
          isFavori = true;
        }
        index++;
      }
      if (!isFavori) {
        icon.setAttribute("class","gg-add");
        icon.setAttribute("id",ul.children[child].dataset['insee']);
        icon.addEventListener("mousedown",fetchAddFavori);
      }
      ul.children[child].appendChild(icon);
      child++;
    }
  }
  return tab;
}


function fetchAddFavori(insee) {
  fetchFromJson("services/addFavori.php?insee=" + String(insee.target.id))
  .then(processAnswer)
  .then(fetchFavoris);
}

function fetchRemoveFavori(insee) {
  fetchFromJson("services/removeFavori.php?insee=" + String(insee.target.id))
  .then(processAnswer)
  .then(fetchFavoris);
}


function hideDetails () {
  document.getElementById("details").style.display ='none';
  document.getElementsByClassName("modal")[0].style.display='none';
  document.getElementById("liste_communes").style.display='block';
  
}

function hideDetailsLog () {
  document.getElementById("details").style.display ='none';
  document.getElementById("liste_communes").style.display='none';
}