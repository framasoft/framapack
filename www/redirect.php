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

require_once 'inc/functions.php';

if (isset($_GET['app']) && !empty($_GET['app'])) {
  $app = (string)$_GET['app'];
} else {
  // error 404
  die('Error');
}

$application = new Application();
if ($application->getByLabel($app) === false) {
  // error 404
  die('Error app');
}

header('Location: '.$application->getLink());
exit;