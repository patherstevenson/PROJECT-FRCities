<?php
class DataLayer {
	// private ?PDO $conn = NULL; // le typage des attributs est valide uniquement pour PHP>=7.4

	private  $connexion = NULL; // connexion de type PDO   compat PHP<=7.3
	
	/**
	 * @param $DSNFileName : file containing DSN 
	 */
	function __construct(string $DSNFileName){
		$dsn = "uri:$DSNFileName";
		$this->connexion = new PDO($dsn);
		// paramètres de fonctionnement de PDO :
		$this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // déclenchement d'exception en cas d'erreur
		$this->connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC); // fetch renvoie une table associative
		// réglage d'un schéma par défaut :
		$this->connexion->query('set search_path=communes_mel, authent');
	}
    
	/**
	 * Liste des territoires
	 * @return array tableau de territoires
	 * chaque territoire comporte les clés :
		* id (identifiant, entier positif),
		* nom (chaîne),
		* min_lat (latitude minimale, flottant),
		* min_lon (longitude minimale, flottant),
		* max_lat, max_lon
	 */
	function getTerritoires(): array {
		$sql = "select id, nom , min_lat, min_lon, max_lat, max_lon from territoires join bb_territoires on id=territoire";
		$stmt = $this->connexion->prepare($sql);
		$stmt->execute();
		$res= $stmt->fetchAll();
		return $res;
	}
	
	/**
	 * Liste de communes correspondant à certains critères
	 * @param territoire : territoire des communes cherchées
	 * @return array tableau de communes (info simples)
	 * chaque commune comporte les clés :
		* insee (chaîne),
		* nom (chaîne),
		* lat, lon 
		* min_lat (latitude minimale, flottant),
		* min_lon (longitude minimale, flottant),
		* max_lat, max_lon
	 */
	function getCommunes(?int $territoire=NULL, ?string $nom=NULL, ?int $surface_min=NULL, ?int $pop_min=NULL): array {
		$sql = <<<EOD
		select a.insee, nom, surface, lat, lon, min_lat, min_lon, max_lat, max_lon, pop_min from (select *
			from communes_mel.communes natural join bb_communes) as a
			left join (select insee, pop_totale as pop_min from population where (recensement=2016 or recensement is null)) as b on a.insee=b.insee
EOD;
		$conds =[];  // tableau contenant les code SQL de chaque condition à appliquer
		$binds=[];   // association entre le nom de pseudo-variable et sa valeur
		if ($territoire !== NULL){
			$conds[] = "territoire = :territoire";
			$binds[':territoire'] = $territoire;
		}
		if ($nom !== NULL){
			$conds[] = "nom ILIKE :nom";
			$binds[':nom'] = "%$nom%";
		}
		if ($surface_min !== NULL){
			$surface_min = $surface_min/0.0001;
			$conds[] = "surface >= :surface_min";
			$binds[':surface_min'] = $surface_min;
		}
		if ($pop_min !== NULL){
			$conds[] = "pop_min >= :pop_min";
			$binds[':pop_min'] = $pop_min;
		}
		if (count($conds)>0){ // il ya au moins une condition à appliquer ---> ajout d'ue clause where
			$sql .= " where ". implode(' and ', $conds); // les conditions sont reliées par AND
		}
		$stmt = $this->connexion->prepare($sql);
		$stmt->execute($binds);
		$res= $stmt->fetchAll() ;
		return $res;
	}
	
	
	/**
	 * Information détaillée sur une commune
	 * @param insee : code insee de la commune
	 * @return commune ou NULL si commune inexistante
	 * l'objet commune comporte les clés :
	 *	insee, nom, nom_terr, surface, perimetre, pop2016, lat, lon, geo_shape
	 */
	function getDetails(string $insee): ?array {
		$sql = <<<EOD
			select insee, communes.nom, territoires.nom as nom_terr, surface, perimetre, population.pop_totale as pop2016,
			lat, lon, geo_shape   from communes 
			join communes_mel.territoires on id=territoire
			natural left join communes_mel.population
			where (recensement=2016 or recensement is null) and insee=:insee
EOD;
		$stmt = $this->connexion->prepare($sql);
		$stmt->execute([':insee'=>$insee]);
		$res = $stmt->fetch() ;
		return $res ? $res : NULL;
	}

	/**
	 * fonction de création/ajout d'un utlisateur dans la bdd
	 * @param login : login de l'user
	 * @param password : mot de passe de l'user
	 * @param nom : nom de l'user
	 * @param prenom : prénom de l'user
	 * @return TRUE si l'ajout est effectué sinon FALSE
	 */
 	function createUser(string $login, string $password, string $nom, string $prenom) : ?bool {
		$sql = <<<EOD
		insert into "users" (login, password, nom, prenom)
		values (:login, :password, :nom, :prenom)
EOD;

		$stmt = $this->connexion->prepare($sql);
		$stmt->execute(array(':login'=>$login, ':password'=>password_hash($password, CRYPT_BLOWFISH), ':nom' => $nom, ':prenom' => $prenom));
		$res = $stmt -> fetch();
		return $res ? $res : FALSE;
	 }
	
	 /**
	  * fonction d'authentification 
	  * @param login : login de l'utilisateur
	  * @param password : password de l'utilisateur
	  * @return Identite ou NULL si le password est incorrect ou le login inexistant
	  */
 	function authentification(string $login, string $password) : ?Identite {
		$sql=<<<EOD
		select login, password, nom, prenom from users where login=:login
EOD;

		$stmt = $this->connexion->prepare($sql);
		$stmt->execute(array(':login'=>$login));
		$val = $stmt->fetch();

		if(empty($val) || crypt($password, $val['password']) != $val['password']) return NULL;
		elseif(crypt($password, $val['password']) == $val['password']) 
			return new Identite($val['login'], $val['nom'], $val['prenom']);

	 }

	 /**
	  * obtention des favoris de l'user connecté depuis la table favoris (schema authent)
	  * @param login : login de l'user connecté au site
	  * @return array : tableau contenant les favoris de l'utilisateur
	  */
	 function getFavoris(string $login) : ?array {
		$sql = <<<EOD
		select insee from favoris where login=:login
EOD;
		$stmt = $this->connexion->prepare($sql);
		$stmt->execute(array(':login'=>$login));
		$res= $stmt->fetchAll() ;
		return $res ? $res : array();
	 }

	 /**
	  * fonction qui ajoute un insee en favoris à l'user connecté
	  * @param login : login de l'user
	  * @param insee : numéro insee de la commune
	  * @return string : retourne l'insee ajouté dans la table favoris pour l'user
	  */
	 function addFavori(string $login, string $insee) : array {
		$sql = <<<EOD
		insert into "favoris" (login, insee)
		values (:login, :insee)
EOD;
		$stmt = $this->connexion->prepare($sql);
		$stmt->execute(array(':login'=>$login, ':insee'=>$insee));
		$res = $stmt -> fetch();
		return $res;
	 }

	 /**
	  * fonction qui supprime un insee en favoris de l'user connecté
	  * @param login : login de l'user
	  * @param insee : numéro insee de la commune
	  * @return string : retourne l'insee supprimé dans la table favoris pour l'user sinon NULL
	  */
	 function removeFavori(string $login, string $insee) : array {
		$sql = <<<EOD
		delete from favoris where login=:login and insee=:insee
EOD;
		$stmt = $this->connexion->prepare($sql);
		$stmt->execute(array(':login'=>$login, ':insee'=>$insee));
		$res = $stmt -> fetch();
		return $res; // car si la commune à supprimer n'est pas en favori $res = false
	 }
}
?>