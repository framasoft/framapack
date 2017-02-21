<?php include TPL_FOLDER.'/header.php'; ?>
    <script type="text/javascript">
      var url_framapack = '<?php echo ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>';
    </script>
    <div class="container ombre">
        <header>
            <div class="row">
                <div class="col-md-8">
                    <h1><span class="frama">Frama</span><span class="logiciel">pack</span></h1>
                    <p class="lead">Installez un peu de liberté…</p>
                </div>
                <div class="col-md-4 hidden-sm hidden-xs">
                    <p class="text-center"><img src="images/pingouin.png" alt="" /></p>
                </div>
            </div>
            <hr class="trait" role="presentation" />
        </header>
        <main id="content">
            <form action="index.php" method="post">
            <div id="select" class="col-md-8">
                <h2 class="sr-only">Sélectionner les applications</h2>
                <div id="category" role="tabpanel">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
          <?php
          $i = 0;
          $applications_list = '';
          while ($category->next()) {
            $class = '';
            if ($i === 0){
              $class = ' active';
          ?>
          <script type="text/javascript">
            var current = <?php echo $category->getId() ?>;
            var nb_item = <?php echo $category->getNbApps() ?>;
          </script>
          <?php
              $i++;
            }
          ?>
                        <li role="presentation" id="bloc-category-<?php echo $category->getId() ?>" class="<?php echo $class ?>"><a href="#category-<?php echo $category->getId() ?>" aria-controls="category-<?php echo $category->getId() ?>" role="tab" data-toggle="tab"><?php echo $category ?></a></li>
          <?php
            ob_start();
          ?>
                    </ul>
                    <div role="tabpanel" class="tab-pane<?php echo $class ?>" id="category-<?php echo $category->getId() ?>" class="list-applications">
          <?php
            $application = $category->getApplications('position ASC');
            while ($application->next()) {
          ?>
                        <div class="application col-md-4 col-sm-6 text-center">
                            <p class="text-left"><?php if ($application->hasNotice()) { ?><a class="fiche pull-right" href="<?php echo str_replace('&', '&amp;', $application->getNoticeUrl()) ?>" title="Notice d&eacute;taill&eacute;e du logiciel <?php echo $application ?>"><span class="fa fa-fw fa-lg fa-info-circle"></span><span class="sr-only">Notice d&eacute;taill&eacute;e du logiciel <?php echo $application ?></span></a><?php } ?>
                            <input type="checkbox" name="applications[]" rel="<?php echo $application ?>" value="<?php echo $application->getId() ?>" id="application_<?php echo $application->getId() ?>" /></p>
                            <label class="btn-block" for="application_<?php echo $application->getId() ?>">
                                <img src="logo/<?php echo $application->getLogo() ?>" alt="" class="logo" /><br />
                                <b><?php echo $application ?></b> <small><?php echo $application->getVersion() ?></small><br />
                                <span class="description"><?php echo $application->getDescription() ?></span>
                            </label>
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
                <!-- Tab panes -->
                <div id="application" class="tab-content">
                    <?php echo $applications_list ?>
                </div>
                <div class="clearfix"></div>
            </div>
                <div class="col-md-4">
                    <h2>Qu’est-ce que Framapack ?</h2>
                    <p>Framapack est un outil qui vous permet d’installer une collection de logiciels libres (pour Windows) de votre choix en une seule fois.</p>
                    <ol>
                        <li>S&eacute;lectionnez les applications</li>
                        <li>T&eacute;l&eacute;chargez Framapack</li>
                        <li>Executez le fichier .exe pour procéder à l’installation</li>
                    </ol>
                    <p>Pour en savoir plus, vous pouvez <a href="https://contact.framasoft.org/foire-aux-questions/#framapack">consulter la F.A.Q.</a></p>

                    <h2>Partager</h2>
                    <p>Vous pouvez partager votre s&eacute;lection en copiant le lien ci-dessous.</p>
                    <div data-toggle="popover" data-placement="left" data-trigger="hover"
                         data-html="true" id="share-popover"
                         data-content="<p>Envoyez-le &agrave; vos amis afin qu’ils obtiennent Framapack avec les logiciels que vous avez choisis.<br/>
                    Vous pouvez &eacute;galement sauvegarder ce lien afin d’installer de nouveau les m&ecirc;mes logiciels lorsque vous en aurez besoin.</p>">
                        <input type="text" class="form-control" id="share_framapack" value="" aria-describedby="helpBlock" />
                    </div>

                    <h2>Télécharger</h2>
                    <p>Votre s&eacute;lection :</p>
                    <div id="cart"></div>
                    <p class="download">
                        <button type="submit" class="btn btn-lg btn-info btn-block" name="submit" value=" "><span class="fa fa-fw fa-lg fa-download"></span>T&eacute;l&eacute;charger<br /><span id="nb_apps">0 application</span></button>
                    </p>
                </div>
            </div>
            </form>
        </main>
    </div>
<?php include TPL_FOLDER.'/footer.php'; ?>
