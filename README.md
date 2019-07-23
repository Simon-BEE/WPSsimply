# WPS simply.

## Description :
Création site avec fournisseurs, produits, clients, et entrepôt.  
Pas de logo.  
*Nom du site*: WPS simply.  
*Nom de domaine*: WPSsimply.com

## Schéma :
Un fournisseur peut proposer plusieurs produits.  
Un produit ne peut avoir qu'un fournisseur.  
Un même produit peut être stocké dans plusieurs entrepôts.  
Il n'y a qu'un seul entrepôt par ville.  
Un utilisateur peut etre fournisseur ou gérant d'entrepôt.  
Il ne peut être gérant que d'un entrepôt.  

![Schema UML](/www/public/assets/img/warehouse.png "Schema UML")

## Couleurs :
Sombres : gris/noir principalement, mis en évidence avec du orange pour les liens.

## Pages :
    * Pages fournisseurs : une avec la liste de tous puis une avec chaque fournisseur et leurs produits.
      * URL:  WPSsimply.com/suppliers *&&*  WPSsimply.com/supplier/slug-id
    * Pages warehouses : une avec la liste de tous puis une avec chaque warehouse et leurs produits.
      * URL:  WPSsimply.com/warehouses *&&*  WPSsimply.com/warehouse/slug-id
    * Pages produits : une avec tous les produits puis une avec produit et leur fournisseur et quel(s) warehouse ils sont.
      * URL:  WPSsimply.com/products *&&*  WPSsimply.com/product/slug-id

    * Page de connexion
      * URL : WPSsimply.com/login
    * Page d'inscription
      * URL : WPSsimply.com/register
    * Page de profil affichant son rôle (fournisseur, ou gérant warehouse)
      * URL : WPSsimply.com/profile

    * Ajout de produit et modification
      * URL : WPSsimply.com/product/add *&&* WPSsimply.com/product/slug-id/edit
    * Ajout de fournisseur et modification
      * URL : WPSsimply.com/supplier/add *&&* WPSsimply.com/supplier/slug-id/edit
    * Ajout d'entrepôt et modification
      * URL : WPSsimply.com/warehouse/add *&&* WPSsimply.com/warehouse/slug-id/edit

## Fonctionnalités : 
* Ajouter un produit si on est fournisseur.
* Devenir fournisseur.
* Ajouter un warehouse.
* Sélectionner des produits pour son warehouse.

## Maquette(s) :

![Maquette home](/www/public/assets/img/wps_home.png "Maquette home")