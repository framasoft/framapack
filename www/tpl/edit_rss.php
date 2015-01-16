<?php include TPL_FOLDER.'/header.php'; ?>

<?php include TPL_FOLDER.'/left.php'; ?>
<div id="content">
    <form action="edit_rss.php" method="post">
      <div class="input">
        <label for="title">Titre</label>
        <input type="text" name="title" id="title" value="" />
      </div>
      <div class="input">
        <label for="category">Catégorie</label>
        <input type="text" name="category" id="category" value="life" />
      </div>
      <div class="input textarea">
        <label for="description">Description</label>
        <textarea name="description" id="description"></textarea>
      </div>
      <div class="submit">
        <input type="submit" name="submit" value="Enregistrer" />
      </div>
    </form>
  </div>

<?php include TPL_FOLDER.'/footer.php'; ?>