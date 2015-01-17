<?php include TPL_FOLDER.'/header.php'; ?>
    <script type="text/javascript">
      var url_framapack = '<?php echo ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>';
    </script>
    <div id="header">
      <div class="bloc">
        <div class="logo"><img src="images/logo_pack2.png" alt="Logo de Framapack" /></div>
        <div class="description">
          <ul>
            <li>1. S&eacute;lectionnez les applications.</li>
            <li>2. T&eacute;l&eacute;chargez Framapack.</li>
            <li>3. Installez vos applications.</li>
          </ul>
        </div>
      </div>
    <div id="content" class="border">
      <form action="index.php" method="post">
      <div id="select">
        <div id="category">
          <?php
          $i = 0;
          $applications_list = '';
          while ($category->next()) {
            $class = '';
            if ($i === 0){
              $class = ' selected';
          ?>
          <script type="text/javascript">
            var current = <?php echo $category->getId() ?>;
            var nb_item = <?php echo $category->getNbApps() ?>;
          </script>
          <?php
              $i++;
            }
          ?>
          <div id="bloc-category-<?php echo $category->getId() ?>" class="category border<?php echo $class ?>" onclick="show(<?php echo $category->getId() ?>, <?php echo $category->getNbApps() ?>);"><?php echo $category ?></div>
          <?php
            ob_start();
          ?>
          <div id="category_<?php echo $category->getId() ?>" class="list-applications">
          <?php 
            $application = $category->getApplications('position ASC');
            while ($application->next()) {
          ?>
            <div class="application border">
              <div class="logo"><img src="logo/<?php echo $application->getLogo() ?>" alt="Logo de <?php echo $application ?>" /></div>
              <div class="name"><?php echo $application ?> <span class="version"><?php echo $application->getVersion() ?></span></div>
              <div class="description"><?php echo $application->getDescription() ?></div>
              <div class="checkbox"><input type="checkbox" name="applications[]" rel="<?php echo $application ?>" value="<?php echo $application->getId() ?>" id="application_<?php echo $application->getId() ?>" /></div>
              <?php if ($application->hasNotice()) { ?><div class="information"><a class="fiche iframe cboxElement" href="<?php echo str_replace('&', '&amp;', $application->getNoticeUrl()) ?>" title="Notice d&eacute;taill&eacute;e du logiciel <?php echo $application ?>"><img src="images/information.png" alt="Bouton affichant la notice d&eacute;taill&eacute;e" /></a></div><?php } ?>
              
            </div>
          <?php
            }
          ?>
          </div>
          <?php
            $applications_list .= ob_get_contents();
            ob_end_clean();
          }
          ?>
        </div>
        <div id="application">
          <?php echo $applications_list ?>
        </div>
      </div>
      <div id="right">
        <div class="share">
          <div class="explain">
            Partagez votre s&eacute;lection en envoyant le lien ci-dessous
            <div class="popup border">
              Copier-coller l'adresse et envoyer la &agrave; vos amis afin qu'ils puissent obtenir Framapack avec
              la s&eacute;lection de logiciels que vous avez effectu&eacute;.<br /><br />
              Vous pouvez &eacute;galement sauvegarder ce lien afin d'installer de nouveau les m&ecirc;mes logiciels lorsque vous en aurez besoin.
            </div>
          </div>
          <div class="link"><input type="text" id="share_framapack" value="" /></div>
        </div>
        <div class="selection">Votre s&eacute;lection : <span id="nb_apps">0 application</span></div>
        <div id="cart"></div>
        <div class="download">
          <input type="submit" name="submit" value=" " />
          <span class="text-download">T&eacute;l&eacute;charger</span>
        </div>
      </div>
      </form>
      <br class="clear" />
    </div>
    <div id="footer" class="border">
      <div class="details">
        <p>Framapack est un projet de <a href="http://framasoft.org/" title="Acc&eacute;der &agrave; l'annuaire de logiciels libres de Framasoft">Framasoft</a>. Si vous avez des questions, consultez la <a href="faq.php" title="Voir la foire aux questions de Framapack">F.A.Q</a> ou contactez nous <a href="https://contact.framasoft.org/" title="Contacter Framasoft">ici</a>.</p>
        <p>Framapack est un logiciel libre compos&eacute; :</p>
        <ul>
          <li>d'un site Internet sous licence libre <a href="http://www.gnu.org/licenses/agpl-3.0.html" title="Voir le texte de la licence GNU/AGPL version 3">GNU/AGPL v3</a>.</li>
          <li>d'un installateur sous licence libre <a href="http://www.gnu.org/licenses/gpl-2.0.html" title="Voir le texte de la licence GNU/GPL version 2">GNU/GPL v2</a>.</li>
        </ul>
        <p>Les sources de Framapack sont disponibles selon les termes de leur licence respective sur <a href="https://git.framasoft.org/framasoft/framapack" title="Acc&eacute;der au d&eacute;p&ocirc;t git du projet Framapack">SourceForge</a>.</p>
        <p>Les pingouins qui se promènent sur le site nous viennent de <a href="http://www.le-terrier.net/pingouin/pingouin.html" title="Acc&eacute;der au site de L.L. de Mars">L.L. de Mars</a> et sont placés sous licence <a href="http://artlibre.org/licence/lal" title="Voir le texte de la licence Art Libre">Art Libre</a>.</p>
        <p>Les images d'information et de suppression sont des &oelig;uvres de <a href="http://www.famfamfam.com/lab/icons/silk/" title="Acc&eacute;der au site de Mark James">Mark James</a> et sont plac&eacute;es sous licence <a href="http://creativecommons.org/licenses/by/2.5/" title="Voir le texte de la licence Creative Commons BY 2.5">Creative Commons Attribution</a>.</p>
        <p>L'image de t&eacute;l&eacute;chargement est une &oelig;uvre de <a href="http://www.futurosoft.es/" title="Acc&eacute;der au site de Sergio Sanchez Lopez">Sergio Sanchez Lopez</a> et est plac&eacute;e sous licence <a href="http://www.gnu.org/licenses/gpl.html" title="Voir le texte de la licence GNU/GPL">GNU/GPL</a>.</p>
      </div>
    </div>
<?php include TPL_FOLDER.'/footer.php'; ?>
