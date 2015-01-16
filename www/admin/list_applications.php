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

require_once '../inc/functions.php';

if (isset($_GET['delete']) === true && count($_GET['delete']) > 0) {
  // Supprime les catégories sélectionnées
  $application = new Application();
  foreach ($_GET['delete'] as $id) {
    if ($application->getById($id)) {
      $application->delete();
    }
  }
  
  header('Location: list_applications.php');
  exit;
} elseif (isset($_GET['action']) && ($_GET['action'] == 'monter' || $_GET['action'] == 'descendre')) {
  // On récupère l'identifiant
  $id = (int)$_GET['id'];
  if ($id === 0) {
    header('Location: list_applications.php');
    exit;
  }
  
  if ($_GET['action'] == 'monter') { // On veut mettre en avant l'application
    $application = new Application();
    if ($application->getById($id)) {
      $application->up();
    }
  } elseif ($_GET['action'] == 'descendre') { // On veut mettre l'application plus en retrait
    $application = new Application();
    if ($application->getById($id)) {
      $application->down();
    }
  }
  
  header('Location: list_applications.php');
  exit;
}

$category = new Category();

$category->doSelect('', 'name');

$title = 'Liste des applications';
$style = '../style_admin';

include TPL_FOLDER.'/list_applications.php';