<?php
session_start(); // On en a besoin pour utiliser la variable globale SESSION

	include_once "libs/maLibUtils.php";
	include_once "libs/maLibSQL.pdo.php";
	include_once "libs/maLibSecurisation.php"; 
	include_once "libs/modele.php"; 

	$addArgs = "";

	if ($action = valider("action"))
	{
		ob_start ();
		echo "Action = '$action' <br />";

		switch($action) {
			case 'Connexion' :
				if ($login = valider("login"))
					if ($passe = valider("passe")) {
						if (verifUser($login,$passe)) {
							if (valider("remember")) {
								setcookie("login",$login , time()+60*60*24*30);
								setcookie("passe",$passe, time()+60*60*24*30);
								setcookie("remember",true, time()+60*60*24*30);
							} else {
								setcookie("login","", time()-3600);
								setcookie("passe","", time()-3600);
								setcookie("remember",false, time()-3600);
							}
							connecterUtilisateur(valider("idUser","SESSION"));
							$addArgs = "?view=listes";
					}
				}
			break;

			case 'Inscription' :
				$SQL1="SELECT * FROM users";
				$user=parcoursRs(SQLSelect($SQL1));
				$valide=true;
				if ($pseudo=valider("pseudo")){
					if ($passe=valider("passe")){
						foreach ($user as $dataUser){
							if ($dataUser['pseudo'] == $pseudo) {
								$valide=false;
						}
					}
					if ($valide=true){
						mkUser($_POST['nom'], $_POST['prenom'], $_POST['pseudo'], $_POST['passe'], $_POST['idQuestion'], $_POST['rep']);
						setcookie("USERNAME","", time()-3600);
						setcookie("PASSWORD","", time()-3600);
						setcookie("REMEMBER",false, time()-3600);
						$addArgs = "?view=listes";
					}
					else{
						$addArgs = "?view=login";
					}
				}
			}
			break;

			case 'Deconnexion' :
				session_destroy();
				deconnecterUtilisateur(valider("idUser","SESSION"));
			break;

			function __construct($foo = null)
			{
				$this->foo = $foo;
			}

			case 'Catalogue' : 
				if (isset ($_POST['nouveauProduit'])) {
					ajouterProduitAuCatalogue($_POST['nouveauProduit'],$_POST['idCategorie']);
				//echo $_POST['idCategorie'];
								}
				$addArgs = "?view=catalogue";
			break;

			case 'Profil' :
				if (isset ($_POST['newPasse'])) {
					$infosUser=listerInfosUtilisateur($login,$passe);
					foreach ($infosUser as $infos){
						$idUser=$infos['id'];
					}
					changerPasse($idUser,$_POST['newPasse']);
				$addArgs = "?view=profil";
			}
			break;

			case 'Activer' :
			if ($idListe = valider("idListe")) {
				reactiverListe($idListe);
				$addArgs = "?view=listes";
			}
			break;

			case 'Archiver' :  
			if ($idListe = valider("idListe")) {
				archiverListe($idListe);
				$addArgs = "?view=listes";
			}
			break; 
			case 'Supprimer' :
			if ($idListe = valider("idListe")) {
				supprimerListe($idListe);
				$addArgs = "?view=listes";
			}
			break;

			case 'Creer' :
				creerListe(valider("nvListe"));
				$addArgs = "?view=listes";
			break;

			case 'Poster' : 
				$User=valider('idUser',"GET");
				echo($User);
				$List=valider('idListe',"GET");
				echo($List);
				$prod=valider('produitsliste',"GET");
				echo($List);
				$SQL="INSERT INTO `produitsliste`(`idListe`, `idAuteur`, `idProduitsCatalogue`) VALUES ($List,$User,\"$produitsliste\")";
				SQLInsert($SQL);
				echo("test");
				$addArgs=("?view=mlist&idListe=".$List);
		}

	}

	$urlBase = dirname($_SERVER["PHP_SELF"]) . "/index.php";

	header("Location:" . $urlBase . $addArgs);

	ob_end_flush();

// Pour plus d'infos sur ob_start...etc aller sur php.net
?>

