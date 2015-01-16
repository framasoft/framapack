<?php include TPL_FOLDER.'/header.php'; ?>

<?php include TPL_FOLDER.'/left.php'; ?>
  <div id="content">
    <form action="list_applications.php" method="get" onsubmit="return confirm('Voulez-vous vraiment supprimer la sélection ?');">
      <table>
        <tr>
          <th>Supprimer</th>
          <th>Nom de l'application</th>
          <th>Action</th>
        </tr>
        <?php while ($category->next()) { ?>
        <tr>
          <td colspan="3"><b><?php echo $category ?></b></td>
        </tr>
        <?php
          $application = $category->getApplications('position ASC');
          while ($application->next()) {
        ?>
        <tr>
          <td align="center"><input type="checkbox" name="delete[]" value="<?php echo $application->getId() ?>" /></td>
          <td><?php echo $application->getName() ?></td>
          <td>
            <a href="list_applications.php?delete[]=<?php echo $application->getId() ?>" onclick="return confirm('Voulez-vous vraiment supprimer \'<?php echo $application->getName() ?>\' ?');">Supprimer</a>
            &nbsp;&nbsp;
            <a href="edit_application.php?id=<?php echo $application->getId() ?>">Modifier</a>
            &nbsp;&nbsp;
            <a href="list_applications.php?id=<?php echo $application->getId() ?>&action=descendre">Descendre</a>
            &nbsp;&nbsp;
            <a href="list_applications.php?id=<?php echo $application->getId() ?>&action=monter">Monter</a>
          </td>
        </tr>
        <?php
          }
        }
        ?>
      </table>
      <div class=""><input type="submit" name="submit" value="Supprimer la sélection" /></div>
    </form>
  </div>

<?php include TPL_FOLDER.'/footer.php'; ?>