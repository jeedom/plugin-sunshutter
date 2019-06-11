# Description

Ce plugin vous permet de gerer plus facilemement la position de vos volets en fonction de la position du soleil. Ce plugin est completement cloudless

# Configuration du plugins

Rien de spécial ici juste à installer et activer le plugin

## Comment ca marche ?

Le plugin va regler la position de vos volets par rapport à des positions du soleil (Azimuth et Altitude) en fonction de condition.

# Configuration des volets

La configuration se decompose en plusieurs onglet.

## Equipement

Vous retrouvez dans le premier onglet toute la configuration de votre équipement :

- Nom de l’équipement : nom de votre équipement Simulation,
- Objet parent : indique l’objet parent auquel appartient l’équipement,
- Activer : permet de rendre votre équipement actif,
- Visible : rend votre équipement visible sur le dashboard.


## Configuration

### Général

- Cron de verification : très important c'est la fréquence à laquelle jeedom va verifier la position du soleil et les conditions pour positionner le volet. Trop court le volet bougera tout le temps, trop long cela risque de ne pas etre efficace. Un temps de 30min (\*/30 * * * \*) est pas mal.
- Ne pas reprendre la main : interdit au systeme de gestion de volet de modifier la position de celui-ci si il a été bougé manuellement. Exemple le systeme ferme le volet, vous l'ouvrez il n'y touchera plus jusqu'a ce que la commande "Executer les actions" soit déclenchée

### Coordonnées

- Latitude : la latitude de votre volet/maison
- Longitude : la longitude de votre volet/maison
- Altitude : l'altitude de votre volet/maison

### Volet

- Etat volet : commande indiquant la position actuel du volet
- Position volet : commande permettant de positionner le volet

## Condition

- Condition pour action : si cette condition n'est pas vrai le plugin ne modifiera pas la position du volet
- Forcer l'ouverture si : si cette condition est vrai le volet s'ouvrira (la verification se fait sur le cycle du cron)
- Forcer l'ouverture immediatement si : si cette condition est vrai le volet s'ouvrira immediatement
- Forcer la fermeture si : si cette condition est vrai le volet se fermera (la verification se fait sur le cycle du cron)
- Forcer l'ouverture immediatement si : si cette condition est vrai le volet se fermera immediatement

## Positionnement

C'est ici que vous allez pouvoir gerer le positionenement du volet en fonction de la position du soleil.

### Général

La vous indiqué la position ouvert et fermer du volet (c'est ces position qui sont utilisée par les conditions)

### Positionnement

Tableau de positionnement du volet en fonction du soleil. Vous indiquer 2 bornes d'azimuth et d'elevation (en ° pour les 2), dès que les conditions sont remplis le volet se met à la position donnée.

>**ASTUCE**
>
>Petite astuce le site [suncalc.org](https://www.suncalc.org) qui permet, une fois votre adresse rentrée, de voir la position du soleil (et donc les angles Azimuth et d'élévation) en fonction des heures de la journée (il suffit de faire glisser le petit soleil en haut)

## Planning

Vous voyez ici les plannification de positionnement du volet faite dans le planning Agenda

## Commandes

- Azimut soleil : angle Azimuth actuel du soleil
- Elévation soleil : angle d'élevation actuel du soleil
- Executer action : force le calcul de position du volet en fonction de la position du soleil et des conditions et lui applique le resultat. A noter que c'est cette commande qu'il faut lancer pour repasser en gestion automatique si vous avez modifier la position de votre volet manuellement et coché la case "Ne pas reprendre la main"
- Dernière position : derniere position demandé au volet par le plugin
- Etat gestion : état de la gestion (suspendu ou pas)
- Reprendre : force la remise en mode automatique de la gestion
- Suspendre : suspend le positionnement automatique du volet
- Rafraichir : mets à jour les valeurs des commandes "Azimut soleil" et "Elévation soleil"
