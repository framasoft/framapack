
# Framapack
L'installateur de logiciels libres par Framasoft.

Documentation rédigée par Alexandre Bintz <<alexandre.bintz@gmail.com>> novembre 2014


## A qui s'adresse cette documentation ?

A toutes les personnes qui doivent intervenir techniquement sur le Framapack
(maintenance, évolution, etc.).


## Le Framapack, c'est quoi ?

Le Framapack permet d'installer en une seule fois et de manière totalement automatisée une sélection de logiciels libres.
Le Framapack s'adresse aux utilisateurs de Windows uniquement.


## Le Framapack, ça marche comment ?

1. je me rends sur le site http://www.framapack.org ;
1. je sélectionne les logiciels que je souhaite installer parmi un catalogue ;
2. je télécharge un programme d'installation qui téléchargera et installera les logiciels sélectionnés sans que j'ai besoin d'intervenir.


## Le Framapack, sous le capot - vue d'ensemble

Le projet Framapack est constitué de deux modules :
- le site web qui héberge le catalogue des logiciels, permet de sélectionner les logiciels à installer et télécharger le programme d'installation ;
- le programme d'installation à proprement parler.

Le programme d'installation est un programme en C++.
Pour que le programme prenne en charge les logiciels du catalogue et prennent en compte la sélection faite par l'utilisateur, le programme est recompilé sur mesure à chaque fois que cela est nécessaire.
La décision de compilation et la configuration de la compilation sont assurées par le site web.
Le programme d'installation est un programme pour Windows, nous sommes donc dans un cas de compilation croisée (serveur sous GNU/Linux).

Scénario typique :

1. L'utilisateur fait sa sélection de logiciels ;
2. Le site génère les fichiers nécessaires en fonction de la sélection faite ;
4. Le site compile le programme d'installation ;
5. Le programme d'installation est proposé au téléchargement.

Plus de détails sur le programme d'installation, le site web et la compilation dans les sections suivantes.


## Framapack : le programme d'installation

Le programme d'installation est le programme que l'utilisateur télécharge après avoir fait sa sélection de logiciels libres sur le site web.


### Vue d'ensemble

Pour chaque logiciel sélectionné, le programme télécharge l'installateur du logiciel puis le lance en arrière plan.
Le programme affiche une barre de progression indiquant l'avancement de l'installation des logiciels.

Le programme d'installation est un executable Windows, codé en C++.
Il utilise l'API Windows pour afficher une fenêtre et des widgets,
et la bibliothèque cURL pour télécharger les logiciels.


### Les fichiers

Tous les fichiers relatifs au programme d'installation sont dans le dossier `framapack/framainstall/trunk/framapack`.

Le dossier `lib` contient les bibliothèques nécessaires à la compilation.
Les fichiers sources se trouvent dans le dossier `src`.

Le programme d'installation est constitué des fichiers source suivants :
- `apps.h`
- `main.cpp`
- `options.h`
- `progress.h`
- `proxy.h`
- `curl/*`

Autres fichiers :
- `resource.h`
- `resource.rc`
- `framapack.ico`
- `framapack.ini`
- `framainstall.exe.Manifest`

Les fichiers suivants sont modifiés en fonction du catalogue de logiciels ou de la sélection faite par un utilisateur :
- `apps.h`

#### Le fichier `apps.h`

##### Vue d'ensemble

Le fichier `apps.h` définit les logiciels gérés par le programme d'installation.
Le fichier est édité automatiquement à chaque mise à jour du catalogue de logiciels libres de sorte qu'il contient toujours la liste complète de tous les logiciels disponibles dans le catalogue.
A la compilation, cette liste est restreinte pour se limiter à la sélection faite par l'utilisateur.

##### En détails

Le fichier `apps.h` définit une classe qui agit comme un conteneur pour les logiciels libres gérés par le programme.
Elle contient plusieurs tableaux qui stockent :
- le nom des logiciels ;
- les URLs où télécharger les installateurs ;
- le chemin où enregistrer le fichier téléchargé ;
- la commande pour lancer l'installation une fois le fichier téléchargé.

Le constructeur de la classe préremplit les tableaux avec les logiciels du catalogue via des appels de la fonction `Apps::add()`.
Chaque appel de `Apps:add()` est entourée de directives `#ifdef` `#endif`, de sorte que la liste des logiciels peut être adaptée grâce à des constantes de préprocesseur (directives `#define`).

Le fichier `apps.h` est édité par le site à chaque mise à jour du catalogue.
Les éléments modifiés sont les suivants :
- la taille des tableaux est définie au nombre de logiciels du catalogues ;
- le constructeur est modifié pour remplir les tableaux avec les données de tous les logiciels du catalogue (écriture des lignes `Apps::add(...)`).

De plus, à la compilation, des constantes de préprocesseurs sont définies en fonction de la sélection de logiciels faite par l'utilisateur, de sorte à n'inclure dans le binaire final que les appels à `Apps::add()` pour les programmes sélectionnés.

La fonction `Apps::nextApp()` prend en paramètre un tableau de 4 string qu'elle remplit avec les données pour un logiciel.
Chaque appel de la fonction renvoie les données du logiciel suivant dans la liste.
Les données renvoyées dans le tableau sont (dans l'ordre) :
- l'URL où télécharger l'installateur ;
- le chemin vers le fichier téléchargé ;
- la commande pour lancer l'installation une fois le fichier téléchargé ;
- le nom du logiciel.

La fonction renvoie `false` si le dernier logiciel de la liste a été lu, `true` sinon.

#### Le fichier `main.cpp`

Le fichier `main.cpp` est le fichier principal de l'application.
Il définit l'interface utilisateur et contient la logique générale du programme.

La fonction `getData()` télécharge un installateur à l'URL indiquée et stocke le fichier téléchargé sous le nom spécifié.
Elle renvoie un booléen qui vaut vrai si le téléchargement s'est bien passé.
La fonction utilise les fonctions de la bibliothèque cURL pour télécharger le fichier.

La fonction `installApp()` installe un logiciel en utilisant la commande passée en paramètre.

La fonction `downloadAndInstall()` télécharge puis lance l'installateur d'un logiciel.

La fonction `installAll()` télécharge et installe tous les logiciels.

#### Le fichier `options.h`

Le fichier `options.h` permet la récupération de certaines options définies dans le fichier `.ini`.
Il déclare et implémente une classe.
Voir section dédiée sur le fichier .ini.

#### Le fichier `progress.h`

Le fichier `progress.h` définit une classe permettant la manipulation d'une barre de progression Windows.
Il déclare et implémente une classe.

#### Le fichier `proxy.h`

Le fichier `proxy.h` définit une classe qui permet l'utilisation d'un serveur proxy pour le téléchargement des installateurs.
Il déclare et implémente une classe.
Les paramètres du proxy sont lus dans le fichier `.ini`.

#### Les fichiers `curl/*`

L'application utilise la bibliothèque cURL ([site officiel](http://curl.haxx.se)) pour télécharger les installateurs des logiciels.
Il est nécessaire d'inclure les headers (fichier `.h`)  situés dans le dossier `curl/`.

#### Le fichier `resource.h`

Le fichier `resource.h` définit des constantes, notamment le chemin vers le fichier `.ini` utilisé par l'application, et des constantes utilisées dans le fichier `resources.rc`.

#### Le fichier `resource.rc`

Le fichier `resource.rc` définit des paramètres pour l'exécutable à produire.
Il définit notamment le fichier icône de l'application, et l'utilisation du Manifest (`framainstall.exe.Manifest`).

#### Le fichier `framapack.ico`

L'icône de l'exécutable.

#### Le fichier `framapack.ini`

Fichier de configuration permettant de définir certaines options, notamment les paramètres du proxy.

#### Le fichier `framainstall.exe.Manifest`

Le fichier `framainstall.exe.Manifest` contient des informations relatives au programme généré destinées à Windows.


### Compilation du programme d'installation

#### Vue d'ensemble

La compilation est une compilation croisée (cross-compilation) sur un hôte GNU/Linux et pour une cible Windows.
Elle est réalisée grâce à MinGW.

Afin de produire un exécutable autonome, les bibliothèques tierces doivent être liées de manière statique.

La compilation utilise un fichier Makefile qui contient toutes les commandes à exécuter permettant ainsi une compilation en une seule commande : `make`.


#### Compilation croisée

Le compilateur utilisé pour la compilation croisée est MinGW.
C'est un port de GCC pour une cible Windows.
L'installation sous Ubuntu est simple: `sudo apt-get install mingw32` (pour 32 ou 64bits, le 32 dans le nom n'a pas de signification particulière).
Les commandes sont préfixées de `i586-mingw32msvc-`.
Exemple: `i586-mingw32msvc-gcc`, `i586-mingw32msvc-g++`, `i586-mingw32msvc-as`, etc


#### Bibliothèques tierces et linkage

Toutes les bibliothèques utilisées sont liées de manière statique lors de la compilation, car on souhaite produire un exécutable autonome (un seul fichier: le `.exe`, pas besoin d'avoir certaines dll installées).

Les bibliothèques utilisées sont les suivantes:
- API Windows pour afficher une fenêtre simple ;
- cURL pour télécharger les logiciels ;
- zlib (dépendance de cURL).

En conséquence les options suivantes doivent être passées lors de la compilation :
- `-L./lib/windows -lcomctl32 -lws2_32 -lwinmm` : bibliothèques Windows ;
- `-L./lib/curl_static -lcurl` : bibliothèque cURL ;
- `-L./lib/zlib -static -lz` : bibliothèque zlib

NB: l'option `-Lpath` indique un dossier dans lequel chercher les bibliothèques ;
l'option `-lname` indique l'utilisation du fichier nommé `libname.a`.

Le linkage statique de la bibliothèque zlib se fait en ajoutant l'option `-static` devant l'option `-lz`.
Le linkage statique de la bibliothèque cURL se fait en utilisant la bibliothèque du dossier `lib/curl_static` et en définissant la constante de préprocesseur `CURL_STATICLIB` (option `-DCURL_STATICLIB`).
Il ne semble pas nécessaire de faire quoi que ce soit pour les bibliothèques Windows.


#### Etapes de compilation et Makefile

La compilation utilise un fichier Makefile qui regroupe toutes les commandes nécessaires à une compilation complète et permet ainsi une compilation en une seule commande : `make`.

Les étapes principales de la compilation sont:
1. compilation des ressources: `resources.rc` donne `resources.res` ;
2. compilation des fichiers sources: `main.cpp`, `resource.h`, `progress.h` et `apps.h` donnent `main.o` ;
3. génération du binaire final à partir de `resources.res`, `main.o` et des bibliothèques tierces.

Des constantes de préprocesseur sont définies lors de la phase de compilation des fichiers sources (étape 2.) pour que le binaire final prenne en compte la sélection de logiciels faite par l'utilisateur.
Voir plus haut le fichier `apps.h`.

Le fichier `Makefile` présent dans `framapack/framainstall/trunk/framapack` est utilisé pour les développements.
Il n'est pas utilisé en production.

Le fichier final utilisé en production est généré automatiquement par le site web pour compiler le programme d'installation en fonction de la sélection de logiciels faite par l'utilisateur.
Voir section dédiée.


## Framapack : le site web

### Vue d'ensemble

Le site web est la vitrine du Framapack. C'est le point d'entrée vers le service.

Le site web permet de parcourir le catalogue des logiciels libres et de faire sa sélection.
Une page F.A.Q. donne des informations détaillées.
Le site propose un flux RSS.

Un espace administrateur protégé permet de gérer le catalogue des logiciels.

Le site s'occupe de configurer et compiler le programme d'installation en fonction du catalogue de logiciels et de la sélection faite par un utilisateur.


### Technologies, grands principes et structuration des fichiers

Le site est constitué d'un ensemble de pages PHP qui communiquent avec une base de données MySQL.

Les pages du site web sont construites à partir de fichiers templates.
Il existe un template pour le contenu principal de chaque page,
ainsi que pour le header et le footer des pages, et le menu de navigation.

Tous les fichiers relatifs au programme d'installation sont dans le dossier `framapack/framainstall/trunk/www`.
Les fichiers sont organisés de la manière suivante :

- dossier `admin` : pages administrateur ;
- dossier `class` : contient des classes PHP correspondant aux tables SQL ;
- dossier `compilation` : fichiers utiles pour la compilation du framapack (voir plus bas section Gestion de la compilation) ;
- dossier `db` : fichiers SQL permettant de générer les tables ;
- dossier `images` : images utilisées sur le site ;
- dossier `inc` : fichiers PHP "include" :
  - `config.php` : définit des constantes ;
  - `fonctions.php` : définit les fonctions principales de l'application web : compilation et téléchargement du binaire, etc.
- dossier `js` : modules javascript ;
- dossier `logo` : logos des logiciels du catalogue ;
- dossier `tpl` : templates pour le contenu des pages et les parties communes.

A la racine on trouve les pages :

- `index.php` : page principale du site ;
- `faq.php` : page F.A.Q. ;
- `redirect.php` : ??

Ainsi que d'autres fichiers :

- `style_admin.css` : feuille de style pour la partie admin ;
- `style.css` : feuille de style générale ;
- `favicon.ico` : icône du site ;
- `functions.js` : fonctions Javascript propres à l'application ;
- `rss.php` : le fichier du flux RSS
- `LICENSE` : license GNU AGPL3
- `RELEASE` : historique de versions du Framapack (site web + application)


### Le système de templates

Pour chaque page, il y a un fichier PHP qui représente la page.
C'est ce fichier qui est chargé par le visiteur.
Il contient généralement la récupération des données et la logique de traitement des actions et finit généralement par inclure le template qui contient le contenu de la page (dossier `/tpl`) ou rediriger vers une autre page.

Les fichiers principaux pour les pages publiques se trouvent à la racine du site.
Les fichiers principaux pour les pages de l'espace administrateur se trouvent dans le dossier `/admin`.

Les fichiers pour le contenu des pages se trouvent dans le dossier `/tpl` (pages publiques et pages admin).
Les templates de contenu incluent eux-même le header et footer.

Le template `/tpl/header.php` attend plusieurs variables :
- `$js` : si définie est vaut vrai, les bibliothèques Javascript (externes et locales) sont inclues ;
- `$style` : définit le nom du fichier CSS à utiliser, sans l'extension ;
- `$title` : titre de la page.


### La base de données

Les logiciels libres disponibles sont enregistrés dans une base de données.

#### Structure de la base de données

Le dossier `/db` contient les fichiers permettant de générer les tables nécessaires à l'application.

Liste des tables et champs :
- `application` : liste des logiciels
  - `id`
  - `category_id`
  - `name` : nom du logiciel
  - `link` : lien vers l'installateur
  - `options`
  - `logo`
  - `description`
  - `version`
  - `updated_at`
  - `label`
  - `is_msi`
  - `position` : permet de gérer l'ordre d'affichage des logiciels
  - `position_install`
  - `notice`
- `category` : liste des catégories de logiciel
  - `id`
  - `name`
- `compilation` : nombre de téléchargements pour les différentes sélections. Une sélection de logiciels est identifiée par la somme MD5 de la concaténation des noms des logiciels.
  - `id`
  - `md5`
  - `nb_download`
- `assoc_application_compilation` : table de liaison entre un logiciel et une compilation
  - `id_application`
  - `id_compilation`

#### Mapping PHP

Le dossier `/class` contient des classes PHP correspondant aux tables SQL.
Ces classes fournissent une interface haut niveau pour la manipulation des données dans les tables.
Une classe permet la manipulation des données dans la table associée.

Liste des classes:
- `Application` (`/class/application.class.php`): table `application`
- `Category` (`/class/category.class.php`): table `category`
- `Compilation` (`/class/compilation.class.php`): table `compilation`
- `AssocApplicationCompilation` (`/class/assoc_application_compilation.class.php`): table `assoc_application_compilation`
- `Database` (`/class/database.class.php`): classe mère de toutes les autres

Une instance de la classe ne correspond pas à un objet représenté par la table :
par exemple une instance de la classe `Catégorie` ne représente pas une catégorie de logiciel
mais permet de manipuler les catégories (ajout, suppression, sélection, etc).
Cependant, chaque instance de la classe peut contenir les données relatives à un enregistrement de la table.

- La fonction `doSelect()` charge les données correspondant à la requête.
- La fonction `next()` permet de définir l'enregistrement actif à l'enregistrement suivant parmi ceux sélectionnés.
- La fonction `delete()` supprime l'enregistrement actif.
- La fonction `getById()` charge l'enregistrement correspondant à l'id indiqué.
- La fonction `populate()` charge l'enregistrement actif avec les données du tableau passé.
- La fonction `save()` crée une nouvelle ligne en BDD ou met à jour la ligne correspondant à l'enregistrement actif.

Les classes offrent d'autres fonctions utilitaires, par exemple: `application::uploadFile()`.


### La page principale

Le fichier `/index.php` correspond à la page principale du site.
Il contient l'essentiel de la logique de haut niveau du site :

- gestion de l'affichage du catalogue et de la sélection des logiciels ;
- gestion de la production et du téléchargement du programme d'installation ;
- gestion des liens de partage (http://www.framapack.org/?share=) ;

Par défaut, le fichier affiche la page principale du site à partir du template `/tpl/index.php` après avoir récupéré la liste des logiciels regroupés par catégories.

La page principale fait usage de Javascript pour gérer la navigation parmi les catégories et pour gérer la sélection des logiciels. L'intégralité de la logique client est contenue dans le fichier `functions.js`.

Le code HTML liste toutes les catégories et tous les logiciels.
Du code Javascript permet de n'afficher qu'une seule catégorie à la fois et de naviguer parmi les différentes catégories (voir fichier `functions.js`, fonction `show()`).

L'utilisateur sélectionne un logiciel en cochant la case à cocher correspondante.
A chaque sélection ou déselection, le retour visuel de la liste des applications sélectionnées et le lien de partage sont mis à jour. Voir fichier `functions.js`, fonctions `addCart()`, `removeCart()`, `updateNbApps()`, `clickCheckbox()`, `buildLink()`.

Lorsque l'utilisateur demande à télécharger le programme d'installation, il valide un formulaire HTML.
Le formulaire est traité par le fichier `/index.php`.
Les cases à cocher pour la sélection des logiciels sont mappées sur un tableau PHP.
Après la validation du formulaire le tableau `$_POST['applications']` contient les valeurs (attibuts `value`) des checkbox cochées.
Ces valeurs correspondent aux id BDD des logiciels.
Le fichier calcule la somme MD5 correspondant à la liste reçue.
Cette somme permet de déterminer si une version compilée du Framapack existe déjà pour la sélection demandée.
Si c'est le cas, le fichier est proposé au téléchargement.
Dans le cas contraire, les fichiers nécessaires sont édités en fonction de la sélection puis le programme est compilé.
Une fois prêt le fichier est proposé au téléchargement.
Plus de détails sur la compilation et la mise en cache dans la section Gestion de la compilation ci-dessous.

#### Lien de partage

Il est possible de partager la sélection de logiciels faite via un lien.
Ce lien est de la forme http://www.framapack.org/?share=42-12
La valeur du paramètre share est constitué par la liste des id BDD des logiciels sélectionnés, séparés par des tirets.
Le lien de partage est construit automatiquement lorsqu'un utilisateur effectue une sélection de logiciels. Il est mis à jour automatiquement à mesure que l'utilisateur sélectionne ou déselectionne des logiciels dans le catalogue.

Lorsqu'un visiteur suit un lien de partage, c'est le fichier `index.php` qui est appelé.
La réception d'un lien de partage a le même effet que la demande de téléchargement du programme d'installation après avoir effectué une sélection : compilation si nécessaire puis téléchargement du programme d'installation.


### L'espace administrateur

L'espace administrateur permet de gérer le catalogue des logiciels libres :
- lister les catégories ;
- lister les logiciels ;
- ajouter une catégorie ;
- ajouter un logiciel ;
- modifier une catégorie ;
- modifier un logiciel ;
- ajout manuel d'une entrée dans le flux RSS (voir plus bas section Le flux RSS).

La page `/tpl/list_categories.php` affiche la liste des catégories dans un tableau.
Pour chaque catégorie les éléments suivants sont présents :
- une case à cocher permettant de marquer la catégorie pour suppression; la case à cocher est mappée sur un tableau PHP ;
- un lien pour supprimer directement la catégorie ;
- un lien pour modifier la catégorie.

Un bouton en fin de page permet la suppression des catégories sélectionnées.

Le traitement de la suppression est effectué par la page `/admin/list_categories.php`
La modification d'une catégorie est assurée par la page `/admin/edit_category.php`

La page `/tpl/list_applications.php` affiche la liste des logiciels dans un tableau.
Pour chaque catégorie les éléments suivants sont présents :
- une case à cocher permettant de marquer le logiciel pour suppression; la case à cocher est mappée sur un tableau PHP ;
- un lien pour supprimer directement le logiciel ;
- un lien pour modifier le logiciel ;
- un lien pour monter le logiciel ;
- un lien pour descendre le logiciel.

Un bouton en fin de page permet la suppression des logiciels sélectionnées.

Le traitement de la suppression des logiciels ainsi que la montée et la descente sont effectuées par la page `/admin/list_applications.php`
La modification d'un logiciel est assurée par la page `/admin/edit_application.php`.

NB: le fichier `apps.h` n'est pas mis à jour lors de la suppression d'un logiciel.

La page `/tpl/edit_category.php` permet de modifier une catégorie ou ajouter une nouvelle catégorie.
Le traitement de la mise à jour est assuré par la page `/admin/edit_category.php`.

La page `/tpl/edit_application.php` permet de modifier les données relatives à un logiciel ou ajouter un nouveau logiciel.
La page permet l'upload d'un fichier pour l'icône du logiciel.
Le traitement de la mise à jour est assuré par la page `/admin/edit_application.php`.
Lors de la modification ou l'ajout d'un logiciel le fichier `apps.h` est regénéré pour prendre en compte les changements.

La page `/tpl/edit_rss.php` permet de créer une nouvelle entrée dans le flux RSS.
Le traitement de la mise à jour est assuré par la page `/admin/edit_rss.php`.


### Le flux RSS

Le site propose un flux RSS.

Il est possible d'ajouter manuellement une entrée dans le flux via l'espace administrateur :
- titre
- catégorie
- description

Voir plus haut section L'espace administrateur.

Le contenu du flux RSS est définit par le fichier `/inc/rss.php`, qui déclare un tableau PHP contenant les données du flux.
L'ajout d'une nouvelle entrée édite le fichier `/inc/rss.php`.

L'accès au flux RSS se fait via le fichier `/rss.php` qui inclue `/inc/rss.php` et construit la vue XML pour le flux RSS.


### Gestion de la compilation

Le programme d'installation proposé au téléchargement doit être adapté à deux choses :
- au catalogue de logiciels : il doit pouvoir télécharger et installer un logiciel du catalogue ;
- à la sélection de logiciels faite par l'utilisateur : il doit télécharger et installer les logiciels sélectionnés par l'utilisateur et uniquement ceux-ci.

Cette adaptation est effectuée par l'édition de certains fichiers et la recompilation du programme d'installation.

#### Adaptation au catalogue : le fichier `apps.h`

L'adaptation du programme d'installation au catalogue est réalisé par l'édition automatique du fichier `apps.h`.
Ce fichier définit la liste des logiciels supportés par le programme d'installation et les informations nécessaires au téléchargement et à l'installation. Voir plus haut la section Le fichier `apps.h`.

Le fichier est mis à jour à chaque modification du catalogue via les pages admin.
Voir plus haut section L'espace administrateur.

La génération du fichier se fait en partant d'un fichier modèle : `www/compilation/apps.h.php`.
Ce fichier modèle contient des placeholders qui sont remplacés par le contenu adéquat.
Le fichier résultant remplace le fichier existant (`framapack/src/apps.h`).

#### Adaptation à la sélection : le Makefile

L'adaptation du programme à la sélection de logiciels faite par l'utilisateur est réalisée par l'édition automatique du fichier Makefile.

Le fichier Makefile définit toutes les commandes permettant la compilation du programme d'installation.
En particulier, il définit la commande de compilation des fichiers sources `.cpp` en `.o`.
Cette commande définit les constantes de préprocesseurs (`#define`) qui vont entrainer l'inclusion ou non des lignes du fichier `apps.h` en fonction de la sélection de logiciels faite par l'utilisateur.
Voir plus haut la section Le fichier `apps.h`.

Le fichier est mis à jour avant chaque compilation, si nécessaire (voir section suivante).

La génération du fichier se fait en partant d'un fichier modèle : `www/compilation/make_tpl.php`.
Ce fichier modèle contient des placeholders qui sont remplacés par le contenu adéquat.
Le fichier résultant est placé dans le dossier `framapack/` et est suffixé par le code MD5 correspondant à la sélection de logiciels.

#### Décision de compilation et système de mise en cache

Les fichiers dépendants de la sélection de logiciels (i.e. l'exécutable final et le Makefile) sont nommés avec un code identifiant de manière unique la sélection : la somme MD5 de la concaténation des noms des logiciels.
Avant chaque téléchargement, le site vérifie si une version compilée pour cette sélection n'existe pas déjà.
Si un exécutable correspondant à la sélection existe déjà, il n'est pas recompilé.

NB: Le système de mise en cache présente une faille :
Une modification d'un logiciel du catalogue (ex: mise à jour du lien de téléchargement)
entraine la regénération du fichier `apps.h`.
Le nouveau fichier prend donc en compte les données modifiées.
Or si un utilisateur demande une sélection qui comprend le logiciel modifié,
et si une version compilée de l'installateur pour cette sélection existe,
alors c'est cette version qui est proposée au téléchargement (le programme n'est pas recompilé).
Par conséquent si cette version date d'avant la modification du logiciel,
elle ne prend pas en compte les changements apportés.


## Crédits

Développeur initial : Simon Leblanc <<contact@leblanc-simon.eu>>

Revue et documentation : Alexandre Bintz <<alexandre.bintz@gmail.com>>
