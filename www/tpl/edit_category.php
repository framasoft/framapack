<?php include TPL_FOLDER.'/header.php'; ?>

<?php include TPL_FOLDER.'/left.php'; ?>
  <div id="content">
    <form action="edit_category.php?id=<?php echo $category->getId() ?>" method="post">
      <div class="input">
        <label for="name">Nom</label>
        <input name="name" id="name" value="<?php echo $category->getName() ?>" />
      </div>
      <div class="submit">
        <input type="submit" name="submit" value="Enregistrer" />
      </div>
    </form>
  </div>

<?php include TPL_FOLDER.'/footer.php'; ?>