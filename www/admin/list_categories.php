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
  $category = new Category();
  foreach ($_GET['delete'] as $id) {
    if ($category->getById($id)) {
      $category->delete();
    }
  }
  
  header('Location: list_categories.php');
  exit;
}

$category = new Category();

$category->doSelect('', 'name');

$title = 'Liste des catégories';
$style = '../style_admin';

include TPL_FOLDER.'/list_categories.php';