# WPS simply.

## Dates :
**Debut** : 23/07/2019  
**Fin** : 07/08/2019
**Présentation** : du 08/08/2019 au 09/08/2019

## Description :
Création site avec fournisseurs, produits, clients, et entrepôt.  
Les gérants d'entrepôt selectionne des produits proposés par des fournisseurs.  
Les fournisseurs proposent des produits.  
Pas de logo.  
Nom du site: **WPS simply**.  
Nom de domaine: **WPSsimply.com**

## Schéma :
Un fournisseur peut proposer plusieurs produits.  
Un produit ne peut avoir qu'un fournisseur.  
Un même produit peut être stocké dans plusieurs entrepôts.  
Il n'y a qu'un seul entrepôt par ville.  
Un utilisateur peut etre fournisseur ou gérant d'entrepôt.  
Il ne peut être gérant que d'un entrepôt.  

![Schema UML](/presentation/models/warehouse.png "Schema UML")

## Couleurs :
Utilisation d'un thème Bootstwatch (modification stylistique de Bootstrap) : **LUX**.
Sombres : **gris/noir** principalement, mis en évidence avec du **orange** pour les liens ou boutons.

## Typographies :
La police sans-serif utilisée par le thème bootswatch : **"Nunito Sans"**.  
Les police monospace utilisée par le thème bootswatch : **"SFMono-Regular"**.

## Pages :

* **Pages générales** : accueil, mentions légales et 404
  * URL : WPSsimply.com/
  * URL : WPSsimply.com/legal-notices
  * URL : WPSsimply.com/404

* **Pages fournisseurs** : une avec la liste de tous puis une avec chaque fournisseur et leurs produits.
  * URL:  WPSsimply.com/suppliers *&&*  WPSsimply.com/supplier/slug-id
* **Pages warehouses** : une avec la liste de tous puis une avec chaque warehouse et leurs produits.
  * URL:  WPSsimply.com/warehouses *&&*  WPSsimply.com/warehouse/slug-id
* **Pages produits** : une avec tous les produits puis une avec produit et leur fournisseur et quel(s) warehouse ils sont.
  * URL:  WPSsimply.com/products *&&*  WPSsimply.com/product/slug-id

* **Page de connexion**
  * URL : WPSsimply.com/login
* **Page d'inscription**
  * URL : WPSsimply.com/register
* **Page de profil** affichant son rôle (fournisseur, ou gérant warehouse) et possibilité de modification de ses données personnelles
  * URL : WPSsimply.com/profile

* **Messagerie interne** :
  * **Pages affichant tous les contact déjà effectués**
    * URL : WPSsimply.com/profile/id/message
  * **Pages affichant tous les messages avec un contact et possibilité de répondre**
    * URL : WPSsimply.com/profile/id/message/show/contact_id
  * **Page pour ecrire un nouveau message**
    * URL : WPSsimply.com/profile/id/message/new

* **Ajout de produit et modification**
  * URL : WPSsimply.com/product/add *&&* WPSsimply.com/product/slug-id/edit
* **Ajout de fournisseur et modification**
  * URL : WPSsimply.com/supplier/add *&&* WPSsimply.com/supplier/slug-id/edit
* **Ajout d'entrepôt et modification**
  * URL : WPSsimply.com/warehouse/add *&&* WPSsimply.com/warehouse/slug-id/edit

* **Section Administration**
    * **Index de l'administration**
      * URL : WPSsimply.com/admin
    * **Ajout de fournisseur et modification**
      * URL : WPSsimply.com/admin/supplier/add *&&* WPSsimply.com/admin/supplier/slug-id
    * **Ajout d'entrepôt et modification** et mettre des produits
      * URL : WPSsimply.com/admin/warehouse/add *&&* WPSsimply.com/admin/warehouse/slug-id *&&* WPSsimply.com/admin/warehouse/slug-id/product
    * **Ajout de produit et modification**
      * URL : WPSsimply.com/admin/product/add *&&* WPSsimply.com/admin/product/slug-id
    * **Ajout et modification utilisateur**
      * URL : WPSsimply.com/admin/user/add *&&* WPSsimply.com/admin/user/id

> ***En gras** les pages effectuées*

## Fonctionnalités : 
* Devenir fournisseur.
* Ajouter un produit si on est fournisseur.
* Devenir gérant d'entrepôt.
* Ajouter un warehouse si on est gérant.
* Sélectionner des produits pour son warehouse.

## TODO :
* ~~Possibilité de modifier ses infos persos~~
* ~~Espace Admin~~
* ~~Fonctions delete en admin~~
* ~~Ajouter des mentions légales~~
* ~~Ajouter une page 404~~
* ~~Implementer la connexion via Google~~ et Facebook
* ~~Refaire première maquette (home mobile)~~
* ~~Maquette administration~~
* ~~Boîte de messagerie interne~~
* ~~Mettre à jour UML~~
* Commenter le code *in progress*

**Pour la présentation :**
* ~~Rechercher les fonts utilisées par bootswatch LUX~~
* ~~Ecrire un texte (2min) en anglais pour présenter le projet~~
* ~~Faire un fichier de présentation (slides/powerpoint/...)~~

## Fichiers présentations :

Slide de présentation : [slide presentation](/presentation/slides-WPS.html)  
Texte en anglais : [here](/presentation/texte-anglais)

## Maquettes :  

### Maquette accueil mobile
![Maquette home](/presentation/models/maq00.png "Maquette home")
--------------------------
### Maquette d'un formulaire mobile
![Maquette form](/presentation/models/maq01.png "Maquette form")
--------------------------
### Maquette d'une page fournisseur/produit/entrepôt mobile
![Maquette listing](/presentation/models/maq02.png "Maquette listing")
--------------------------
### Maquette accueil écran large
![Maquette home large](/presentation/models/maq03.png "Maquette home large")
--------------------------
### Maquette d'une page fournisseur/produit/entrepôt écran large
![Maquette listing large](/presentation/models/maq04.png "Maquette listing large")
--------------------------
### Maquette d'un formulaire écran large
![Maquette form large](/presentation/models/maq05.png "Maquette form large")
--------------------------
### Maquette administration mobile
![Maquette admin mobile](/presentation/models/maq06.png "Maquette admin mobile")
--------------------------
### Maquette administration écran large
![Maquette admin large](/presentation/models/maq07.png "Maquette admin large")
