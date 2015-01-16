<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="fr" />
    <title><?php echo $title ?></title>
    <link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />
    <link rel="alternate" type="application/rss+xml" title="Flux RSS - Framapack" href="http://www.framapack.org/rss.php" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $style ?>.css?3" />
<?php if (isset($js) && $js === true) { ?>
    <!-- <link rel="stylesheet" type="text/css" media="screen" href="js/fancybox/jquery.fancybox-1.3.4.css" /> -->
    <link rel="stylesheet" type="text/css" media="screen" href="js/colorbox.css" />
    <script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
    <!-- <script type="text/javascript" src="js/fancybox/jquery.fancybox-1.3.4.pack.js"></script> -->
    <script type="text/javascript" src="js/jquery.colorbox-min.js"></script>
    
    <script type="text/javascript" src="functions.js?2"></script>
    <script src="/nav/nav.js" id="nav_js" type="text/javascript"></script>
<?php } ?>
  </head>
  <body>
    <!--
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
    -->
<?php //include_once dirname(__FILE__).'/../framanav/nav.php'; ?>
