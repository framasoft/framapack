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

class Category extends Database
{
  protected $table = 'category';
  
  private $name;
  
  public function __construct()
  {
    parent::__construct();
    
    $this->init();
  }
  
  
  /**
   * Surcharge de la méthode toString afin d'afficher
   * le nom de la catégorie lors d'un echo de l'objet
   *
   * @return  string    Le nom de la catégorie
   * @access  public
   */
  public function __toString()
  {
    return $this->getName();
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
    
    $this->setName($row['name']);
  }
  
  
  /**
   * Initialise les champs de la catégorie
   *
   * @access  protected
   */
  protected function init()
  {
    $this->id   = 0;
    $this->name = '';
  }
  
  
  /**
   * Ajoute les valeur dans la requête de manière sécurisée
   *
   * @param   PDOStatement  $statement    L'objet PDOStatement de la requête
   * @access  protected
   */
  protected function bindValue(PDOStatement &$statement)
  {
    $statement->bindValue(1, $this->getName(), PDO::PARAM_STR);
  }
  
  
  /**
   * Retourne le code SQL pour l'insertion
   *
   * @return  array   Un tableau contenant les champs et les valeurs pour l'insertion SQL
   * @access  public
   */
  protected function getInsertSQL()
  {
    return array('name', '?');
  }
  
  
  /**
   * Retourne le code SQL pour la mise à jour
   *
   * @return  string      Le code SQL pour la mise à jour
   * @access  protected
   */
  protected function getUpdateSQL()
  {
    return 'name = ?';
  }
  
  
  // Le getter
  public function getName() { return $this->name; }
  
  
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
  }
  
  
  /**
   * Retourne les applications associées à la catégorie
   *
   * @param   string    $order    L'ordre de sélection des applications
   * @return  Application         L'objet Application permettant de parcourir les applications
   * @access  public
   */
  public function getApplications($order = '')
  {
    $application = new Application();
    $application->doSelect('category_id = '.$this->getId(), $order);

    return $application;
  }
  
  
  /**
   * Retourne le nombre d'applications associées à la catégorie
   * 
   * @return  int                 Le nombre applications associées à la catégorie
   * @access  public
   */
  public function getNbApps()
  {
    $sql = 'SELECT COUNT(id) as nb FROM application WHERE category_id = '.$this->getId();
    
    $result = self::$conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    if ($result === false) {
      throw new Exception('Error in the request "'.$sql.'" <br />'.print_r(self::$conn->errorInfo(), true));
    }
    
    $result->execute();
    
    $row = $result->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
      return false;
    } else {
      return $row['nb'];
    }
  }
}