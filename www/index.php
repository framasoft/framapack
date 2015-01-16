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

if (isset($_GET['share']) === true && empty($_GET['share']) === false) {
  $_POST['applications'] = explode('-', $_GET['share']);
  $_POST['submit'] = 'share';
}

if (isset($_POST['submit']) === true && empty($_POST['submit']) === false && isset($_POST['applications']) === true) {
  $md5 = getMd5installateur($_POST);
  $installateur = MAKE_FOLDER.'/bin/Release/FramaInstall_'.$md5.'.exe';
  Compilation::increase($md5);
  if (file_exists($installateur)) {
    downloadFile($installateur);
  } else {
    genMake($_POST['applications'], $md5);
    compileInstall($md5);
    downloadFile($installateur);
  }
}

try {
  $category = new Category();
  
  $category->doSelect('', 'name ASC');
  
  $title = 'Framapack - L\'installeur de logiciels libres';
  
  $js = true;
  
  $style = 'style';
  include TPL_FOLDER.'/index.php';
} catch (Exception $e) {
  mail(EMAIL_ADMIN, 'Erreur Framapack', $e->getMessage());
  die('Erreur au niveau de l\'application');
}
