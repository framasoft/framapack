<?php
/*
 Framapack est un installeur de logiciels libres.
 Copyright (C) 2009  Simon Leblanc <contact@leblanc-simon.eu>
 
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as
 published by the Free Software Foundation, either version 3 of the
 License.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Affero General Public License for more details.
 
 You should have received a copy of the GNU Affero General Public License
 along with this program.  If not, see http://www.gnu.org/licenses/.
*/

// La base de données
define('DB_CONN', 'mysql:dbname=framapack;host=localhost');

// Le nom d'utilisateur pour la base de données (null si aucun)
define('DB_USER', 'framapack');

// Le mot de passe pour la base de données (null si aucun)
define('DB_PASS', '');

// Les options pour la base de données (null si aucun, un tableau serialisé sinon)
define('DB_OPTS', serialize(array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")));

// Le répertoire des exécutables de compilation (pas de slash à la fin)
define('COMPILATION_FOLDER', dirname(__FILE__).'/../compilation');

// Le répertoire des templates (pas de slash à la fin)
define('TPL_FOLDER', dirname(__FILE__).'/../tpl');

// Le répertoire du template du fichier make
define('MAKE_TPL', dirname(dirname(__FILE__)).'/compilation');

// Le répertoire du fichier make
define('MAKE_FOLDER', '/home/framapack/framainstall_server');

// Le chemin du fichier apps.h
define('APPS_TPL', '/home/framapack/framainstall_server/src/apps.h');

// Indique si on utilise UPX ou non (doit être installé sur le serveur)
define('USE_UPX', true);

// Chemin du binaire d'UPX (uniquement si on utilise UPX)
define('UPX_BIN', '/usr/bin/upx');

// L'adresse e-mail de l'admin (en cas de bug dans l'application)
define('EMAIL_ADMIN', 'your@email.com');

// Si vous souhaitez mettre des statistiques, placez le code ici
$stats = ''; /*'<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-896440-9");
pageTracker._setDomainName(".framapack.org");
pageTracker._trackPageview();
} catch(err) {}</script>';*/

define('STATS', $stats);

// Le lien du site internet (utilisé pour le flux RSS)
define('WEBSITE_LINK', 'http://www.framapack.org/');

// Le nombre d'élément dans le flux RSS
define('NB_ITEM_RSS', 10);
