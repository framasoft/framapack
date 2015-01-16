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

class AssocApplicationCompilation extends Database
{
  protected $table = 'assoc_application_compilation';
  
  private $id_application;
  private $id_compilation;
  
  public function __construct()
  {
    parent::__construct();
    
    $this->init();
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
    
    $this->setIdApplication($row['id_application']);
    $this->setIdCompilation($row['id_compilation']);
  }
  
  
  /**
   * Initialise les champs de l'association
   *
   * @access  protected
   */
  protected function init()
  {
    $this->id_application = 0;
    $this->id_application = 0;
  }
  
  
  /**
   * Ajoute les valeur dans la requête de manière sécurisée
   *
   * @param   PDOStatement  $statement    L'objet PDOStatement de la requête
   * @access  protected
   */
  protected function bindValue(PDOStatement &$statement)
  {
    $statement->bindValue(1, $this->getIdApplication(), PDO::PARAM_INT);
    $statement->bindValue(2, $this->getIdCompilation(), PDO::PARAM_INT);
  }
  
  
  /**
   * Retourne le code SQL pour l'insertion
   *
   * @return  array   Un tableau contenant les champs et les valeurs pour l'insertion SQL
   * @access  public
   */
  protected function getInsertSQL()
  {
    $sql = 'id_application, id_compilation';
    $value = '?, ?';
    
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
    $sql = 'id_application = ?, id_compilation = ?';
    return $sql;
  }
  
  
  // Les getters
  public function getIdApplication() { return $this->id_application; }
  public function getIdCompilation() { return $this->id_compilation; }
  
  
  /**
   * Met à jour le champ id_application
   *
   * @param   int   $value    La valeur pour le champ id_application
   * @throw   Exception       Si $value n'est pas un numérique, une exception est levée
   * @access  public
   */
  public function setIdApplication($value)
  {
    if (is_numeric($value) === false) {
      throw new Exception('id_application must be a numeric');
    }
    
    $this->id_application = (int)$value;
  }
  
  
  /**
   * Met à jour le champ id_compilation
   *
   * @param   int   $value    La valeur pour le champ id_compilation
   * @throw   Exception       Si $value n'est pas un numérique, une exception est levée
   * @access  public
   */
  public function setIdCompilation($value)
  {
    if (is_numeric($value) === false) {
      throw new Exception('id_compilation must be a numeric');
    }
    
    $this->id_compilation = (int)$value;
  }
  
  
  /**
   * Sauvegarde un élément dans la base de données
   *
   * @access  public
   */
  public function save()
  {
    $this->doInsert();
  }
  
  
  /**
   * Supprime un enregistrement de la base de données
   *
   * @throws  Exception   Si l'identifiant est égal à zéro, une exception est levée
   * @throws  Exception   Si une erreur survient dans la requête, une exception est levée
   * @access  public
   */
  public function delete()
  {
    if ($this->getIdApplication() === 0 || $this->getIdCompilation() === 0) {
      throw new Exception('You can\'t delete arow with id == 0');
    }
    
    $sql = 'DELETE FROM '.$this->table.' WHERE id_application = ? AND id_compilation = ?';
    $stmt = self::$conn->prepare($sql);
    if ($stmt === false) {
      throw new Exception('Error in the request "'.$sql.'" <br />'.print_r(self::$conn->errorInfo(), true));
    }
    
    $this->bindValue($stmt);
    
    $result = $stmt->execute();
    if ($result === false) {
      ob_start();
      print_r($stmt->errorInfo());
      echo '<hr />';
      $stmt->debugDumpParams();
      $error = ob_get_contents();
      ob_end_clean();
      
      throw new Exception('Error in the insert request "'.$sql.'" <br />'.$error);
    }
  }
  
  
  /**
   * Supprime les fichiers de cache de la compilation
   *
   * @access  public
   */
  public function deleteFile()
  {
    $compilation = new Compilation();
    if ($compilation->getById($this->getIdCompilation())) {
      removeCacheCompilation($compilation->getMd5());
    }
  }
  
  
  /**
   * Ajoute un enregistrement dans la base de données
   *
   * @return  int         L'identifiant de l'enregistrement
   * @throws  Exception   Si l'identifiant est différent de zéro, une exception est levée
   * @throws  Exception   Si une erreur survient dans la requête, une exception est levée
   * @access  protected
   */
  protected function doInsert()
  {
    if ($this->getIdApplication() === 0 || $this->getIdCompilation() === 0) {
      throw new Exception('You can\'t insert a row with id == 0');
    }
    
    list($sql_field, $sql_value) = $this->getInsertSQL();
    
    $sql = 'INSERT INTO '.$this->table.' ('.$sql_field.') VALUES ('.$sql_value.')';
    
    $stmt = self::$conn->prepare($sql);
    if ($stmt === false) {
      throw new Exception('Error in the request "'.$sql.'" <br />'.print_r(self::$conn->errorInfo(), true));
    }
    
    $this->bindValue($stmt);
    
    $this->result = $stmt->execute();
    if ($this->result === false) {
      ob_start();
      print_r($stmt->errorInfo());
      echo '<hr />';
      $stmt->debugDumpParams();
      $error = ob_get_contents();
      ob_end_clean();
      
      throw new Exception('Error in the insert request "'.$sql.'" <br />'.$error);
    }
  }
}