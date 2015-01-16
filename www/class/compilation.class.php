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

class Compilation extends Database
{
  protected $table = 'compilation';
  
  private $md5;
  private $nb_download;
  
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
    
    if (isset($row['id']) === true && empty($row['id']) === false) {
      $this->setId($row['id']);
    }
    
    $this->setMd5($row['md5']);
    $this->setNbDownload($row['nb_download']);
  }
  
  
  /**
   * Initialise les champs de la compilation
   *
   * @access  protected
   */
  protected function init()
  {
    $this->id           = 0;
    $this->md5          = '';
    $this->nb_download  = 0;
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
    $statement->bindValue($i, $this->getMd5(), PDO::PARAM_STR); $i++;
    $statement->bindValue($i, $this->getNbDownload(), PDO::PARAM_INT); $i++;
  }
  
  
  /**
   * Retourne le code SQL pour l'insertion
   *
   * @return  array   Un tableau contenant les champs et les valeurs pour l'insertion SQL
   * @access  public
   */
  protected function getInsertSQL()
  {
    $sql = 'md5, nb_download';
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
    $sql = 'md5 = ?, nb_download = ?';
    return $sql;
  }
  
  
  // Les getters
  public function getMd5() { return $this->md5; }
  public function getNbDownload() { return $this->nb_download; }
  
  
  /**
   * Met à jour le champ md5
   *
   * @param   string    $value    La valeur pour le champ md5
   * @throw   Exception           Si $value n'est pas une chaine, une exception est levée
   * @access  public
   */
  public function setMd5($value)
  {
    if (is_string($value) === false) {
      throw new Exception('md5 must be a string');
    }
    
    $this->md5 = (string)$value;
  }
  
  
  /**
   * Met à jour le champ nb_download
   *
   * @param   int   $value    La valeur pour le champ nb_download
   * @throw   Exception       Si $value n'est pas un numérique, une exception est levée
   * @access  public
   */
  public function setNbDownload($value)
  {
    if (is_numeric($value) === false) {
      throw new Exception('nb_download must be a numeric');
    }
    
    $this->nb_download = (int)$value;
  }
  
  
  /**
   * Récupère une compilation par son md5
   *
   * @param   string    $md5      Le md5 sur lequel on fait la recherche
   * @return  bool                Vrai si on trouve une correspondance, faux sinon
   * @throw   Exception           S'il y a une erreur dans la requête de sélection
   * @access  public
   */
  public function getByMd5($md5)
  {
    $sql = 'SELECT * FROM '.$this->table.' WHERE md5 = ?';
    
    $this->result = self::$conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    if ($this->result === false) {
      throw new Exception('Error in the request "'.$sql.'" <br />'.print_r(self::$conn->errorInfo(), true));
    }
    
    $this->result->bindValue(1, $md5, PDO::PARAM_STR);
    
    $this->result->execute();
    
    $row = $this->result->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
      return false;
    }
    
    $this->populate($row);
    return true;
  }
  
  
  /**
   * Récupère une compilation en fonction du md5 ou le crée s'il n'existe pas
   *
   * @param   string    $md5    Le md5 recherché
   * @param   array     $post   Un tableau contenant les identifiants des applications de la compilation
   * @return  Compilation       La compilation correspondante au md5
   * @access  private
   */
  private static function getCompilationOrCreate($md5, $post)
  {
    $compilation = new Compilation();
    if ($compilation->getByMd5($md5) === true) {
      return $compilation;
    } else {
      $compilation = new Compilation();
      $compilation->setMd5($md5);
      $compilation->setNbDownload(0);
      $compilation->save();
      
      foreach ($post['applications'] as $id_application) {
        $id_application = (int)$id_application;
        $assoc = new AssocApplicationCompilation();
        $assoc->setIdApplication($id_application);
        $assoc->setIdCompilation($compilation->getId());
        $assoc->save();
      }
      
      return $compilation;
    }
  }
  
  
  /**
   * Ajoute un téléchargement pour la compilation
   *
   * @param   string    $md5    Le md5 de la compilation sur laquelle on souhaite ajouter un téléchargement
   * @access  public
   */
  public static function increase($md5)
  {
    $compilation = self::getCompilationOrCreate($md5, $_POST);
    $compilation->setNbDownload($compilation->getNbDownload() + 1);
    $compilation->save();
  }
}