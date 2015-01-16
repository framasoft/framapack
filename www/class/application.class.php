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

require_once dirname(__FILE__).'/database.class.php';

class Application extends Database
{
  protected $table = 'application';
  
  private $category_id;
  private $name;
  private $label;
  private $link;
  private $options;
  private $logo;
  private $description;
  private $version;
  private $is_msi;
  private $position;
  private $position_install;
  private $notice;
  private $updated_at;
  
  private $upload_logo = false;
  
  
  public function __construct()
  {
    parent::__construct();
    
    $this->init();
  }
  
  
  /**
   * Surcharge de la méthode toString afin d'afficher
   * le nom de l'application lors d'un echo de l'objet
   *
   * @return  string    Le nom de l'application
   * @access  public
   */
  public function __toString()
  {
    return $this->getName();
  }
  
  
  /**
   * Surcharge de la fonction pour prendre en charge le flux RSS
   *
   * @return  int     L'identifiant de l'enregistrement
   * @access  public
   */
  public function save()
  {
    if ($this->getId() === 0) {
      $datas['title'] = 'Ajout de '.$this->getName();
      $datas['description'] = 'L\'application '.$this->getName().' est maintenant disponible via Framapack';
      $datas['category'] = 'add';
    } else {
      $datas['title'] = 'Mise à jour de '.$this->getName();
      $datas['description'] = 'L\'application '.$this->getName().' a été mise à jour dans sa dernière version';
      $datas['category'] = 'update';
    }
    
    $return_value = parent::save();
    
    addInRss($datas);
    
    return $return_value;
  }
  
  
  /**
   * Met à jour l'objet avec le tableau passé en paramètre
   *
   * @param   array     $row    Le tableau contenant les valeurs pour les attributs de l'objet
   * @access  public
   */
  public function populate($row)
  {
    $this->init();
    
    array_walk($row, 'Database::escapeInput');
    
    if (isset($row['id']) === true && empty($row['id']) === false) {
      $this->setId($row['id']);
    }
    
    $this->setCategoryId($row['category_id']);
    $this->setName($row['name']);
    $this->setLink($row['link']);
    $this->setOptions($row['options']);
    if ($this->upload_logo === false) {
      $this->setLogo($row['logo']);
    }
    $this->setDescription($row['description']);
    $this->setVersion($row['version']);
    if (isset($row['is_msi']) === true) {
      $this->setIsMsi($row['is_msi']);
    } else {
      $this->setIsMsi(0);
    }
    $this->setPosition($row['position']);
    $this->setPositionInstall($row['position_install']);
    $this->setNotice($row['notice']);
    $this->setUpdatedAt($row['updated_at']);
  }
  
  
  /**
   * Initialise les champs de l'application
   *
   * @access  protected
   */
  protected function init()
  {
    $this->id           = 0;
    $this->category_id  = 0;
    $this->name         = '';
    $this->label        = '';
    $this->link         = '';
    $this->options      = '';
    if ($this->upload_logo === false) {
      $this->logo         = '';
    }
    $this->description  = '';
    $this->version      = '';
    $this->is_msi       = 0;
    $this->position     = 0;
    $this->position_install = 1;
    $this->notice       = '';
    $this->updated_at   = time();
  }
  
  
  /**
   * Ajoute les valeur dans la requête de manière sécurisée
   *
   * @param   PDOStatement  $statement    L'objet PDOStatement de la requête
   * @access  protected
   */
  protected function bindValue(PDOStatement &$statement)
  {
    $i = 1;
    $statement->bindValue($i, $this->getCategoryId(), PDO::PARAM_INT); $i++;
    $statement->bindValue($i, $this->getName(), PDO::PARAM_STR); $i++;
    $statement->bindValue($i, $this->getLabel(), PDO::PARAM_STR); $i++;
    $statement->bindValue($i, $this->getLink(), PDO::PARAM_STR); $i++;
    $statement->bindValue($i, $this->getOptions(), PDO::PARAM_STR); $i++;
    $statement->bindValue($i, $this->getLogo(), PDO::PARAM_STR); $i++;
    $statement->bindValue($i, $this->getDescription(), PDO::PARAM_STR); $i++;
    $statement->bindValue($i, $this->getVersion(), PDO::PARAM_STR); $i++;
    $statement->bindValue($i, $this->getIsMsi(), PDO::PARAM_INT); $i++;
    $statement->bindValue($i, $this->getposition(), PDO::PARAM_INT); $i++;
    $statement->bindValue($i, $this->getPositionInstall(), PDO::PARAM_INT); $i++;
    $statement->bindValue($i, $this->getNotice(), PDO::PARAM_STR); $i++;
    $statement->bindValue($i, $this->getUpdatedAt(), PDO::PARAM_INT); $i++;
  }
  
  
  /**
   * Retourne le code SQL pour l'insertion
   *
   * @return  array   Un tableau contenant les champs et les valeurs pour l'insertion SQL
   * @access  public
   */
  protected function getInsertSQL()
  {
    $sql = 'category_id, name, label, link, options, logo, description, version, is_msi, position, position_install, notice, updated_at';
    $value = '?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?';
    
    // On initialise la position de l'application
    $this->setPosition($this->getMaxPosition());
    
    return array($sql, $value);
  }
  
  
  /**
   * Retourne le code SQL pour la mise à jour
   *
   * @return  string      Le code SQL pour la mise à jour
   * @access  protected
   */
  protected function getUpdateSQL()
  {
    $sql = 'category_id = ?, name = ?, label = ?, link = ?, options = ?, logo = ?, description = ?, version = ?, is_msi = ?, position = ?, position_install = ?, notice = ?, updated_at = ?';
    return $sql;
  }
  
  
  // Les getters
  public function getCategoryId() { return $this->category_id; }
  public function getCategory() { $category = new Category(); if ($category->getById($this->category_id)) { return $category; } else { return null; } }
  public function getName() { return $this->name; }
  public function getLabel() { return $this->label; }
  public function getLink() { return $this->link; }
  public function getOptions() { return $this->options; }
  public function getLogo() { return $this->logo; }
  public function getDescription() { return $this->description; }
  public function getVersion() { return $this->version; }
  public function getIsMsi() { return $this->is_msi; }
  public function getPosition() { return $this->position; }
  public function getPositionInstall() { return $this->position_install; }
  public function getNotice() { return $this->notice; }
  public function getUpdatedAt() { return $this->updated_at; }
  
  
  /**
   * Retourne le nom de l'application épuré (juste les caractères alphabetiques et en majuscule)
   *
   * @return  string    Le nom en majuscule et contenant uniquement des caractères alphabetique
   * @access  public
   */
  public function getNameClear()
  {
    return strtoupper(preg_replace('/([^a-z]*)/i', '', $this->getName())).$this->getId();
  }
  
  
  /**
   * Vérifie si l'application contient un lien et les options d'installation
   *
   * @return  bool      Vrai si le lien et les options d'installation sont présents, Faux sinon
   * @access  public
   */
  public function isEmpty()
  {
    $link = $this->getLink();
    $options = $this->getOptions();
    return empty($link) || empty($options);
  }
  
  
  /**
   * Upload un fichier et met à jour le champ logo
   *
   * @access  public
   */
  public function uploadFile()
  {
    $this->upload_logo = true;
    
    if ($_FILES['logo']['size'] > 0 && $_FILES['logo']['error'] == 0 && $_FILES['logo']['type'] == 'image/png') {
      $filename = strtolower(preg_replace('/([^a-z]*)/i', '', $_POST['name'])).'.png';
      $filename_path = dirname(dirname(__FILE__)).'/logo/'.$filename;
      
      if (move_uploaded_file($_FILES['logo']['tmp_name'], $filename_path)) {
        $this->setLogo($filename);
        return true;
      } else {
        return false;
      }
    }
  }
  
  
  /**
   * Retourne l'extension du fichier en fonction du type de l'executable (is_msi)
   *
   * @return  string    L'extension du fichier (.msi ou .exe)
   * @access  public
   */
  public function getExt()
  {
    if ($this->getIsMsi() === 1) {
      return '.msi';
    } else {
      return '.exe';
    }
  }
  
  
  /**
   * Surcharge la méthode doUpdate afin d'ajouter la
   * suppression des fichiers de cache de compilation
   *
   * @access  protected
   */
  protected function doUpdate()
  {
    parent::doUpdate();
    
    $assoc = new AssocApplicationCompilation();
    $assoc->doSelect('id_application = '.$this->getId());
    
    while ($assoc->next()) {
      $assoc->deleteFile();
    }
  }
  
  
  /**
   * Récupère une application par son label
   *
   * @param   string    $label    Le label sur lequel on fait la recherche
   * @return  bool                Vrai si on trouve une correspondance, faux sinon
   * @throw   Exception           S'il y a une erreur dans la requête de sélection
   * @access  public
   */
  public function getByLabel($label)
  {
    $sql = 'SELECT * FROM '.$this->table.' WHERE label = ?';
    
    $this->result = self::$conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    if ($this->result === false) {
      throw new Exception('Error in the request "'.$sql.'" <br />'.print_r(self::$conn->errorInfo(), true));
    }
    
    $this->result->bindValue(1, $label, PDO::PARAM_STR);
    
    $this->result->execute();
    
    $row = $this->result->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
      return false;
    }
    
    $this->populate($row);
    return true;
  }
  
  
  /**
   * Met à jour le champ category_id
   *
   * @param   int       $id       La valeur pour le champ category
   * @throw   Exception           Si $id n'est pas un numérique, une exception est levée
   * @access  public
   */
  public function setCategoryId($id)
  {
    if (is_numeric($id) === false) {
      throw new Exception('category_id must be a numeric');
    }
    
    $this->category_id = (int)$id;
  }
  
  
  /**
   * Met à jour le champ version
   *
   * @param   Category  $category La categorie a associer à l'application
   * @access  public
   */
  public function setCategory(Category $category)
  {
    $this->category_id = $category->getId();
  }
  
  
  /**
   * Met à jour le champ name
   *
   * @param   string    $value    La valeur pour le champ name
   * @throw   Exception           Si $value n'est pas une chaine, une exception est levée
   * @access  public
   */
  public function setName($value)
  {
    if (is_string($value) === false) {
      throw new Exception('name must be a string');
    }
    
    $this->name = (string)$value;
    $this->setLabel(strtolower(preg_replace('/([^a-z]*)/i', '', $this->name)));
  }
  
  
  /**
   * Met à jour le champ label
   *
   * @param   string    $value    La valeur pour le champ label
   * @throw   Exception           Si $value n'est pas une chaine, une exception est levée
   * @access  public
   */
  public function setLabel($value)
  {
    if (is_string($value) === false) {
      throw new Exception('label must be a string');
    }
    
    $this->label = (string)$value;
  }
  
  
  /**
   * Met à jour le champ link
   *
   * @param   string    $value    La valeur pour le champ link
   * @throw   Exception           Si $value n'est pas une chaine, une exception est levée
   * @access  public
   */
  public function setLink($value)
  {
    if (is_string($value) === false) {
      throw new Exception('link must be a string');
    }
    
    $this->link = (string)$value;
  }
  
  
  /**
   * Met à jour le champ options
   *
   * @param   string    $value    La valeur pour le champ options
   * @throw   Exception           Si $value n'est pas une chaine, une exception est levée
   * @access  public
   */
  public function setOptions($value)
  {
    if (is_string($value) === false) {
      throw new Exception('options must be a string');
    }
    
    $value = str_replace('&quot;', '"', $value);
    
    $this->options = trim((string)$value);
  }
  
  
  /**
   * Met à jour le champ logo
   *
   * @param   string    $value    La valeur pour le champ logo
   * @throw   Exception           Si $value n'est pas une chaine, une exception est levée
   * @access  public
   */
  public function setLogo($value)
  {
    if (is_string($value) === false) {
      throw new Exception('logo must be a string');
    }
    
    $this->logo = (string)$value;
  }
  
  
  /**
   * Met à jour le champ description
   *
   * @param   string    $value    La valeur pour le champ description
   * @throw   Exception           Si $value n'est pas une chaine, une exception est levée
   * @access  public
   */
  public function setDescription($value)
  {
    if (is_string($value) === false) {
      throw new Exception('description must be a string');
    }
    
    $this->description = (string)$value;
  }
  
  
  /**
   * Met à jour le champ version
   *
   * @param   string    $value    La valeur pour le champ version
   * @throw   Exception           Si $value n'est pas une chaine, une exception est levée
   * @access  public
   */
  public function setVersion($value)
  {
    if (is_string($value) === false) {
      throw new Exception('version must be a string');
    }
    
    $this->version = (string)$value;
  }
  
  
  /**
   * Met à jour le champ is_msi
   *
   * @param   int   $value    La valeur pour le champ is_msi
   * @throw   Exception       Si $value n'est pas un numérique, une exception est levée
   * @access  public
   */
  public function setIsMsi($value)
  {    
    $this->is_msi = (int)$value;
  }
  
  
  /**
   * Met à jour le champ position
   *
   * @param   int   $value    La valeur pour le champ position
   * @throw   Exception       Si $value n'est pas un numérique, une exception est levée
   * @access  public
   */
  public function setPosition($value)
  {
    if (is_numeric($value) === false) {
      throw new Exception('position must be a numeric');
    }
    
    $this->position = (int)$value;
  }
  
  
  /**
   * Met à jour le champ position_install
   *
   * @param   int   $value    La valeur pour le champ position_install
   * @throw   Exception       Si $value n'est pas un numérique, une exception est levée
   * @access  public
   */
  public function setPositionInstall($value)
  {
    if (is_numeric($value) === false) {
      throw new Exception('position_install must be a numeric');
    }
    
    $this->position_install = (int)$value;
  }
  
  
  /**
   * Met à jour le champ notice
   *
   * @param   int   $value    La valeur pour le champ notice
   * @throw   Exception       Si $value n'est pas une chaine, une exception est levée
   * @access  public
   */
  public function setNotice($value)
  {
    if (is_string($value) === false) {
      throw new Exception('notice must be a string');
    }
    
    $this->notice = (string)$value;
  }
  
  
  /**
   * Met à jour le champ update_at avec la date courante
   *
   * @access  public
   */
  public function setUpdatedAt()
  {
    $this->updated_at = time();
  }
  
  
  public function hasNotice() { return !empty($this->notice); }
  
  public function getNoticeUrl()
  {
    return str_replace('article', 'spip.php?page=article_framapack&id_article=', str_replace('.html', '&iframe', $this->notice));
  }
  
  /**
   * Met une position moins élevée à l'application afin de la faire monter dans la liste
   *
   * @access  public
   */
  public function up()
  {
    $new_position = $this->getPosition() - 1;
    if ($this->movePosition($new_position)) {
      $this->setPosition($new_position);
      $this->save();
    }
  }
  
  
  /**
   * Met une position plus élevée à l'application afin de la faire baisser dans la liste
   *
   * @access  public
   */
  public function down()
  {
    $new_position = $this->getPosition() + 1;
    if ($this->movePosition($new_position)) {
      $this->setPosition($new_position);
      $this->save();
    }
  }
  
  
  /**
   * Change la position de l'application et met à jour les autres applications impactées
   *
   * @param   int   $new_position     La nouvelle position souhaitée
   * @return  bool                    Vrai si la position a été modifié, Faux sinon
   * @access  private
   */
  private function movePosition($new_position)
  {
    if (is_numeric($new_position) === false || $new_position < 1 || $new_position > $this->getMaxPosition() - 1) {
      return false;
    }
    
    if ($new_position === $this->getPosition()) {
      return false;
    }
    
    if ($new_position < $this->getPosition()) {
      $sql = 'UPDATE '.$this->table.' SET position = position + 1 WHERE position >= ? AND position < ? AND category_id = ?';
    } else {
      $sql = 'UPDATE '.$this->table.' SET position = position - 1 WHERE position <= ? AND position > ? AND category_id = ?';
    }
    
    $result = self::$conn->prepare($sql);
    if ($result === false) {
      throw new Exception('Error in the request "'.$sql.'" <br />'.print_r(self::$conn->errorInfo(), true));
    }
    
    $result->bindValue(1, $new_position, PDO::PARAM_INT);
    $result->bindValue(2, $this->getPosition(), PDO::PARAM_INT);
    $result->bindValue(3, $this->getCategoryId(), PDO::PARAM_INT);
    
    $result->execute();
    
    return true;
  }
  
  
  /**
   * Récupère la valeur maximale de la position pour la catégorie de l'application
   *
   * @return  int     La valeur maximale de la position
   * @access  private
   */
  private function getMaxPosition()
  {
    $sql = 'SELECT max(position) as max_position FROM '.$this->table.' WHERE category_id = ?';
    
    $result = self::$conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    if ($result === false) {
      throw new Exception('Error in the request "'.$sql.'" <br />'.print_r(self::$conn->errorInfo(), true));
    }
    
    $result->bindValue(1, $this->getCategoryId(), PDO::PARAM_INT);
    
    $result->execute();
    
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
      // S'il n'y a pas de résultat, on renvoie 1 qui correspond à la position la plus basse
      return 1;
    }
    
    return $row['max_position'] + 1;
  }
}