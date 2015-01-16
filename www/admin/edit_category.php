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

$category = new Category();

if (isset($_GET['id']) === true && empty($_GET['id']) === false) {
  $id = (int)$_GET['id'];
} else {
  $id = 0;
}

if (isset($_POST['submit']) === true && empty($_POST['submit']) === false) {
  $category->getById($id);
  $_POST['id'] = $category->getId();
  $category->populate($_POST);
  $id = $category->save();
  header('Location: list_categories.php');
  exit;
}

if ($id !== 0) {
  if ($category->getById($id)) {
    $title = 'Modification de la catégorie : "'.$category.'"';
  } else {
    $title = 'Nouvelle catégorie';
  }
} else {
  $title = 'Nouvelle catégorie';
}

$style = '../style_admin';

include TPL_FOLDER.'/edit_category.php';