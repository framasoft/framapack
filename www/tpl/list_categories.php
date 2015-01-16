<?php include TPL_FOLDER.'/header.php'; ?>

<?php include TPL_FOLDER.'/left.php'; ?>
  <div id="content">
    <form action="list_categories.php" method="get" onsubmit="return confirm('Voulez-vous vraiment supprimer la sélection ?');">
      <table>
        <tr>
          <th>Supprimer</th>
          <th>Nom de la catégorie</th>
          <th>Action</th>
        </tr>
        <?php while ($category->next()) { ?>
        <tr>
          <td><input type="checkbox" name="delete[]" value="<?php echo $category->getId() ?>" /></td>
          <td><?php echo $category->getName() ?></td>
          <td>
            <a href="list_categories.php?delete[]=<?php echo $category->getId() ?>" onclick="return confirm('Voulez-vous vraiment supprimer \'<?php echo $category->getName() ?>\' ?');">Supprimer</a>
            &nbsp;&nbsp;
            <a href="edit_category.php?id=<?php echo $category->getId() ?>">Modifier</a>
          </td>
        </tr>
        <?php } ?>
      </table>
      <div class=""><input type="submit" name="submit" value="Supprimer la sélection" /></div>
    </form>
  </div>

<?php include TPL_FOLDER.'/footer.php'; ?>