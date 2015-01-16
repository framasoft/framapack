<?php include TPL_FOLDER.'/header.php'; ?>

<?php include TPL_FOLDER.'/left.php'; ?>
  <div id="content">
    <form action="edit_application.php?id=<?php echo $application->getId() ?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="position" value="<?php echo $application->getPosition() ?>" />
      <div class="input">
        <label for="name">Nom</label>
        <input type="text" name="name" id="name" value="<?php echo $application->getName() ?>" />
      </div>
      <div class="input">
        <label for="category_id">Catégorie</label>
        <select name="category_id" id="category_id">
          <?php while ($category->next()) { ?>
          <option value="<?php echo $category->getid() ?>"<?php if ($category->getId() === $application->getCategoryId()) { ?> selected="selected"<?php } ?>><?php echo $category ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="input">
        <label for="link">Lien de l'installateur</label>
        <input type="text" name="link" id="link" value="<?php echo $application->getLink() ?>" />
      </div>
      <div class="input">
        <label for="options">Ligne de commande</label>
        <input type="text" name="options" id="options" value="<?php echo str_replace('"', '&quot;', $application->getOptions()) ?>" />
      </div>
      <div class="input">
        <label for="logo">Logo</label>
        <input type="file" name="logo" id="logo"  /><span class="help">Le logo doit faire 32px * 32px</span>
      </div>
      <div class="input textarea">
        <label for="description">Description</label>
        <textarea name="description" id="description"><?php echo $application->getDescription() ?></textarea>
      </div>
      <div class="input">
        <label for="version">Version de l'application</label>
        <input type="text" name="version" id="version" value="<?php echo $application->getVersion() ?>" />
      </div>
      <div class="input">
        <label for="notice">Lien vers la notice</label>
        <input type="text" name="notice" id="notice" value="<?php echo $application->getNotice() ?>" />
      </div>
      <div class="input">
        <label for="is_msi">L'installateur est un MSI</label>
        <input type="checkbox" name="is_msi" id="is_msi" value="1" <?php if ($application->getIsMsi() == 1) { ?>checked="checked" <?php } ?>/>
      </div>
      <div class="input">
        <label for="position_install">Type de logiciel</label>
        <select name="position_install" id="position_install">
          <option value="0"<?php if ($application->getPositionInstall() === 0) { ?> selected="selected"<?php } ?>>Framework</option>
          <option value="1"<?php if ($application->getPositionInstall() === 1) { ?> selected="selected"<?php } ?>>Logiciel</option>
          <option value="2"<?php if ($application->getPositionInstall() === 2) { ?> selected="selected"<?php } ?>>Complément d'un logiciel</option>
          <option value="3"<?php if ($application->getPositionInstall() === 3) { ?> selected="selected"<?php } ?>>Complément d'un complément</option>
        </select>
      </div>
      <div class="submit">
        <input type="submit" name="submit" value="Enregistrer" />
      </div>
    </form>
  </div>

<?php include TPL_FOLDER.'/footer.php'; ?>