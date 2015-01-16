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

require_once dirname(__FILE__).'/config.php';
require_once dirname(__FILE__).'/../class/category.class.php';
require_once dirname(__FILE__).'/../class/application.class.php';
require_once dirname(__FILE__).'/../class/compilation.class.php';
require_once dirname(__FILE__).'/../class/assoc_application_compilation.class.php';

/**
 * Transforme le choix d'application de l'utilisateur en MD5
 *
 * @param   array   $post   Un tableau contenant les identifiant des applications sélectionnées
 * @return  string          Le MD5 correspondant au choix de l'utilisateur
 */
function getMd5installateur($post)
{
  $where = '';
  foreach ($post['applications'] as $id) {
    if (empty($where) === false) {
      $where .= ', ';
    }
    $where .= (int)$id;
  }
  
  $application = new Application();
  $application->doSelect('id in ('.$where.')', 'name ASC');
  
  $installateur = '';
  while ($application->next()) {
    $installateur .= $application->getName();
  }
  
  return md5($installateur);
}


/**
 * Supprime les fichiers de compilation en cache pour un MD5
 *
 * @param   string    $md5    Le MD5 de la compilation pour laquelle on souhaite supprimer les fichiers en cache
 */
function removeCacheCompilation($md5)
{
  // On supprime le makefile
  if (file_exists(MAKE_FOLDER.'/Makefile_'.$md5) === true) {
    unlink(MAKE_FOLDER.'/Makefile_'.$md5);
  }
  
  // On supprime le dossier obj
  $directory_path = MAKE_FOLDER.'/obj/Release_'.$md5.'/';
  if (file_exists($directory_path) === true && is_dir($directory_path) === true) {
    $directory = opendir($directory_path);
    while ($file = readdir($directory)) {
      if (is_file($directory_path.$file) === false || $file == '.' || $file == '..' ) {
        continue;
      }
      
      unlink($directory_path.$file);
    }
  
    rmdir($directory_path);
  }
  
  // On supprime le binaire
  if (file_exists(MAKE_FOLDER.'/bin/Release/FramaInstall_'.$md5.'.exe') === true) {
    unlink(MAKE_FOLDER.'/bin/Release/FramaInstall_'.$md5.'.exe');
  }
}


/**
 * Lance la compilation pour une compilation identifié par son MD5
 *
 * @param   string    $md5    Le MD5 de la compilation
 * @return  int               La valeur de retour de l'execution de la compilation
 */
function compileInstall($md5)
{
  // On crée les répertoires essentiels
  mkdir(MAKE_FOLDER.'/obj/Release_'.$md5);
  
  exec('cd '.MAKE_FOLDER.' && /usr/bin/make -f Makefile_'.$md5.' Release', $ouput, $return_value);
  
  return $return_value;
}
  

/**
 * Génére le fichier Makefile pour la sélection de l'utilisateur
 *
 * @param   array   $ids    Un tableau contenant les identifiants des applications sélectionnées par l'utilisateur
 * @param   string  $md5    Le md5 correspondant au choix de l'utilisateur
 * @param   int             Le nombre d'octet écrit ou faux en cas d'erreur
 */
function genMake($ids, $md5)
{
  $application = new Application();
  $application->doSelect('id IN ('.implode(',', $ids).')');
  
  $preprocessing = '';
  while ($application->next()) {
    $preprocessing .= ' -D'.$application->getNameClear();
  }
  
  $content = file_get_contents(MAKE_TPL.'/make_tpl.php');
  
  $content = str_replace('%%PREPROCESSING%%', $preprocessing, $content);
  $content = str_replace('%%MD5%%', $md5, $content);
  
  if (USE_UPX === true) {
    $content = str_replace('%%UPX%%', '@'.UPX_BIN.' ./bin/Release/FramaInstall_'.$md5.'.exe', $content);
  } else {
    $content = str_replace('%%UPX%%', '', $content);
  }
  
  return file_put_contents(MAKE_FOLDER.'/Makefile_'.$md5, $content);
}


/**
 * Génére le fichier apps.h en fonction des applications disponible
 * 
 * @param   int   Le nombre d'octet écrit ou faux en cas d'erreur
 */
function genAppsH()
{
  $application = new Application();
  $application->doSelect('', 'position_install ASC, name ASC');
  
  $nb_apps = 0;
  $apps_add = '';
  
  while ($application->next()) {
    $app_temp = 'temp + "\\\\framapack-'.strtolower($application->getNameClear()).$application->getExt().'"';
    $apps_add .= '
#ifdef '.$application->getNameClear().'
    this->add("'.$application->getLink().'", '.$app_temp.', "'.str_replace('%%TEMP%%', '" + '.$app_temp.' + "', $application->getOptions()).'", "'.str_replace('"', '\"', $application->getName()).'");
#endif
    ';
    $nb_apps++;
  }
  
  $content = file_get_contents(MAKE_TPL.'/apps.h.php');
  
  $content = str_replace('%%APPS_ADD%%', $apps_add, $content);
  $content = str_replace('%%NB_APPS%%', $nb_apps, $content);
  
  return file_put_contents(APPS_TPL, $content);
}


/**
 * Envoie un fichier à l'utilisateur
 *
 * @param   string    $file   Le fichier à envoyer à l'utilisateur
 */
function downloadFile($file)
{
  if (file_exists($file) === false) {
    die('Error : no file !');
  }
  
  $filesize = filesize($file);
  header('Content-type: application/force-download');
  header('Content-disposition: attachment; filename=framapack.exe');
  header('Content-Transfer-Encoding: application/octet-stream');
  header('Content-Length: '.$filesize);
  header('Pragma: no-cache');
  header('Cache-Control: must-revalidate, post-check=0, pre-check=0, public');
  header('Expires: 0');
  echo file_get_contents($file);
  exit;
}

/**
 * Ajoute un élément dans le flux RSS
 * @param   array   $datas    Les données à ajouter (title, description, category)
 * @return  bool              Vrai si cela a fonctionné, faux sinon
 */
function addInRss($datas)
{
  if (isset($datas['title']) === false || isset($datas['description']) === false || isset($datas['category']) === false) {
    return false;
  }
  
  // On teste le fichier de RSS, s'il y a une erreur on initialise la variable
  if (file_exists(dirname(__FILE__).'/rss.php') === false) {
    $rss = array();
  } else {
    require_once dirname(__FILE__).'/rss.php';
    if (is_array($rss) === false) {
      $rss = array();
    }
  }
  
  // On ajoute l'élément
  $time = time();
  $rss[$time] = array(
    'title'       => $datas['title'],
    'description' => $datas['description'],
    'category'    => $datas['category'],
    'link'        => WEBSITE_LINK,
    'pubDate'     => date('r', $time)
  );
  
  // On réduit le flux RSS au nombre voulu
  krsort($rss);
  while (count($rss) > NB_ITEM_RSS) {
    array_pop($rss);
  }
  
  // On met à jour le fichier de RSS
  $rss_string = '<?php $rss = '.var_export($rss, true).';';
  
  return (bool)file_put_contents(dirname(__FILE__).'/rss.php', $rss_string);
}
