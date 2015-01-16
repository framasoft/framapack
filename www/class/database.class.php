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

abstract class Database
{
  static protected $conn = null;
  protected $result = null;
  
  protected $id;
  
  protected $table;
  
  /**
   * Constructeur
   */
  public function __construct()
  {
    if (self::$conn === null) {
      self::connect();
    }
    
    $this->result = null;
  }
  
  
  abstract public function populate($row);
  abstract protected function init();
  abstract protected function bindValue(PDOStatement &$statement);
  abstract protected function getInsertSQL();
  abstract protected function getUpdateSQL();
  
  /**
   * Connexion à la base de données
   *
   * @access  private
   * @static
   */
  static private function connect()
  {
    if (DB_USER !== null) {
      if (DB_OPTS !== null) {
        self::$conn = new PDO(DB_CONN, DB_USER, (string) DB_PASS, unserialize(DB_OPTS));
      } else {
        self::$conn = new PDO(DB_CONN, DB_USER, (string) DB_PASS);
      }
    } else {
      self::$conn = new PDO(DB_CONN);
    }
  }
  
  
  /**
   * Prépare la sélection d'un ensemble de ligne d'une table
   *
   * @param   string  $where    La clause where
   * @param   string  $order    L'ordre de tri
   * @throws  Exception         Si une erreur survient dans la requête, une exception est levée
   * @access  public
   */
  public function doSelect($where = '', $order = '')
  {
    $sql = 'SELECT * FROM '.$this->table;
    if ($where !== '') {
      $sql .= ' WHERE '.$where;
    }
    if ($order !== '') {
      $sql .= ' ORDER BY '.$order;
    }
    
    $this->result = self::$conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    if ($this->result === false) {
      throw new Exception('Error in the request "'.$sql.'" <br />'.print_r(self::$conn->errorInfo(), true));
    }
    
    $this->result->execute();
  }
  
  
  /**
   * Se positionne sur la ligne suivant de la base de données
   *
   * @return  bool    Vrai si la ligne existe, faux sinon
   * @access  public
   */
  public function next()
  {
    $row = $this->result->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
      return false;
    }
    
    $this->populate($row);
    return true;
  }
  
  
  /**
   * Se positionne sur la ligne de la base de données correspondant à l'identifiant
   *
   * @param   int   $id   L'identifiant voulu
   * @return  bool        Vrai si la ligne existe, faux sinon
   * @throws  Exception   Si l'identifiant n'est pas un numérique, une exception est levée
   * @access  public
   */
  public function getById($id)
  {
    if (is_numeric($id) === false) {
      throw new Exception('id must be an integer');
    }
    
    $this->doSelect('id = '.(int)$id);
    return $this->next();
  }
  
  
  /**
   * Sauvegarde un élément dans la base de données
   *
   * @return  int     L'identifiant de l'enregistrement
   * @access  public
   */
  public function save()
  {
    if ($this->getId() === 0) {
      return $this->doInsert();
    } else {
      return $this->doUpdate();
    }
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
    if ($this->getId() === 0) {
      throw new Exception('You can\'t delete arow with id == 0');
    }
    
    $sql = 'DELETE FROM '.$this->table.' WHERE id = ?';
    $stmt = self::$conn->prepare($sql);
    if ($stmt === false) {
      throw new Exception('Error in the request "'.$sql.'" <br />'.print_r(self::$conn->errorInfo(), true));
    }
    
    $stmt->bindValue(1, $this->getId(), PDO::PARAM_INT);
    
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
   * Ajoute un enregistrement dans la base de données
   *
   * @return  int         L'identifiant de l'enregistrement
   * @throws  Exception   Si l'identifiant est différent de zéro, une exception est levée
   * @throws  Exception   Si une erreur survient dans la requête, une exception est levée
   * @access  protected
   */
  protected function doInsert()
  {
    if ($this->getId() !== 0) {
      throw new Exception('You can\'t insert a row with id != 0');
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
    
    $this->setId(self::$conn->lastInsertId('id'));
    
    return $this->getId();
  }
  
  
  /**
   * Modifie un enregistrement dans la base de données
   *
   * @return  int         L'identifiant de l'enregistrement
   * @throws  Exception   Si l'identifiant est égal de zéro, une exception est levée
   * @throws  Exception   Si une erreur survient dans la requête, une exception est levée
   * @access  protected
   */
  protected function doUpdate()
  {
    if ($this->getId() === 0) {
      throw new Exception('You can\'t update a row with id == 0');
    }
    
    $sql = 'UPDATE '.$this->table.' SET '.$this->getUpdateSQL().' WHERE id = '.$this->getId();
    
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
    
    return $this->getId();
  }
  
  public function getId() { return $this->id; }
  
  public function setId($id)
  {
    if (is_numeric($id) === false) {
      throw new Exception('id must be numeric');
    }
    
    $this->id = (int)$id;
  }
  
  
  /**
   * Échappe les caractères spéciaux (pour les formulaires)
   *
   * @param   string  $value    La valeur dans le tableau
   * @param   string  $key      L'identifiant dans le tableau
   * @access  public
   * @static
   */
  static public function escapeInput(&$value, $key)
  {
    $value = str_replace("\'", "'", $value);
    $value = str_replace('\"', '"', $value);
  }
}