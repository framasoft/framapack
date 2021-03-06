<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="fr" />
    <title><?php echo $title ?></title>
    <link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />
    <link rel="alternate" type="application/rss+xml" title="Flux RSS - Framapack" href="http://www.framapack.org/rss.php" />
    <link rel="stylesheet" type="text/css" media="all" href="static/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" media="all" href="static/font-awesome/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $style ?>.css?3" />
<?php if (isset($js) && $js === true) { ?>
    <script type="text/javascript" src="static/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="static/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="functions.js?2"></script>
<?php } ?>
  </head>
  <body>
    <script src="https://framasoft.org/nav/nav.js" type="text/javascript"></script>
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