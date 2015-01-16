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

require_once dirname(__FILE__).'/inc/config.php';

header('Content-Type: application/rss+xml; charset=UTF-8');

function disabledRss()
{
  getHeader();
  echo '<item><title>Problème du flux RSS !</title></item>';
  getFooter();
}

function orderRss($rss)
{
  krsort($rss);
  
  return $rss;
}

function getLastDate($rss, $is_order = false)
{
  if ($is_order === false) {
    $rss = orderRss($rss);
  }
  
  reset($rss);
  $date = key($rss);
  
  return date('r', $date);
}

function getHeader($date = null)
{
  echo '<?xml version="1.0" encoding="utf-8"?>';
  echo '
<rss version="2.0">
  <channel>
    <title>Framapack</title>
    <description></description>
    <lastBuildDate>'.(($date === null) ? date('r') : $date).'</lastBuildDate>
    <link>'.WEBSITE_LINK.'</link>
';
}

function getFooter()
{
  echo '
  </channel>
</rss>';
}

if (file_exists(dirname(__FILE__).'/inc/rss.php') === true) {
  require_once dirname(__FILE__).'/inc/rss.php';
} else {
  disabledRss();
}

if (is_array($rss) === false) {
  disabledRss();
}

orderRss($rss);

getHeader(getLastDate($rss, true));

foreach ($rss as $item) {
?>
    <item>
      <?php foreach ($item as $markup => $value) { ?>
      <<?php echo $markup ?>><?php echo $value ?></<?php echo $markup ?>>
      <?php } ?>
    </item>
<?php
}

getFooter();
?>
