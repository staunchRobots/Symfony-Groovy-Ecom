<?php
/**
 * @todo implement escape char if PHP >= 5.3
 * @todo add validation of the foreign keys - they are neither validated nor removed from CSV currently
 * 
 * 
 * @author Tomasz Muras
 * @license LGPL 
 */

function array_change_key_name( $orig, $newKey, $newValue, &$array )
{
    foreach ( $array as $k => $v )
        $return[ ( $k === $orig ) ? $newKey : $k ] = ( $k === $orig ) ? $newValue : $v;
    return $return;
}

interface tmiDoctrineCsvStrategy
{
  public function getPK();
  public function cleanData(array &$data);
  public function create();
  public function getTable();
  public function matchCase(array &$data);
}


class tmTemplateStrategy implements tmiDoctrineCsvStrategy
{
  /**
   * @var Doctrine_Record
   */
  private $record;
  
  public function __construct(Doctrine_Record $record)
  {
    $this->record = $record;
  }
  
  public function getPK()
  {
    $pks = $this->getTable()->getIdentifierColumnNames();
    $ret = array ();
    foreach($pks as $pk) {
      $ret[$pk] = $pk;
    }
    return $ret;  
  }

  /**
   * 
   *
   * @return Doctrine_Table
   */
  public function getTable()
  {
    return $this->record->getTable();     
  }
  
  public function create()
  {
    return $this->record->copy();
  }
  
  public function matchCase(array &$data) {
    $columns = $this->getTable()->getColumnNames();
    $new = $data;
    
    foreach($columns as $column) {
      foreach($data as $k => $v) {
        if((strcasecmp($column,$v) == 0) && ($column != $v)) {
          $new = array_change_key_name($v,$column,$column,$new);
        }
      }
    }
    
    return $new;
    
  }
  
  /**
   * Modifies $data array
   *
   * @param array $data
   * @return array removed fields
   */
  public function cleanData(array &$data)
  {
     return $this->record->cleanData($data);
  }
}

class tmTableStrategy implements tmiDoctrineCsvStrategy
{
  /**
   * @var Doctrine_Table
   */
  private $table;
  
  public function __construct(Doctrine_Table $table)
  {
    $this->table = $table;
  }
  
  public function getPK()
  {
    $pks = $this->table->getIdentifierColumnNames();
    $ret = array ();
    foreach($pks as $pk) {
      $ret[$pk] = $pk;
    }
    return $ret;
  }
  
  public function matchCase(array &$data)
  {
    $columns = $this->table->getColumnNames();
    $new = $data;
    
    foreach($columns as $column) {
      foreach($data as $k => $v) {
        if((strcasecmp($column,$v) == 0) && ($column != $v)) {
          $new = array_change_key_name($v,$column,$column,$new);
        }
      }
    }
    
    return $new;
  }
  
  /**
   * Modifies $data array
   *
   * @param array $data
   * @return array removed fields
   */
  public function cleanData(array &$data)
  {
    $obj = $this->table->create();
    return $obj->cleanData($data);
  }
  
  public function create()
  {
    return $this->table->create();
  }
  
  public function getTable()
  {
    return $this->table;
  }
}

/**
 * 
 * tmCsvReader
 * @author Tomasz Muras
 */
class tmDoctrineReader
{
  const ERR_DOCTRINE = 3;
  
  /**
   * @var tmiDoctrineCsvStrategy
   */
  private $strategy;
  
  /**
   * @var tmCsvReader
   */
  private $parser;
  
  /**
   * @param string  $path      Path to the file to read
   * @param array	$options   Array with the options (delimiter, enclosure, length, header, from, to) 
   *                           plus doctrine-related (case_sensitive_columns)
   */
  public function __construct($path, $options = array())
  {
    
    if(isset($options['header']) && $options['header'] !== true) {
      throw new CsvException('Header is required for populating Doctrine objects.', self::ERR_DOCTRINE);
    }
    if(! isset($options['case_sensitive_columns'])) {
      $options['case_sensitive_columns'] = true;
    }
    $this->options = $options; 
    $this->parser = new tmCsvReader($path, $options);
  }
  
  /**
   * Create the records based on the given table
   *
   * @param Doctrine_Table $table
   */
  public function setTable(Doctrine_Table $table)
  {
    $this->strategy = new tmTableStrategy($table);
  }
  
  /**
   * Create the records based on the given template record
   *
   * @param Doctrine_Record
   */
  public function setTemplate(Doctrine_Record $record)
  {
    $this->strategy = new tmTemplateStrategy($record);
  }
  
  public function getRemoved()
  {
    return $this->parser->getRemoved();
  }
  
  private function fillObject(Doctrine_Record $r, $data)
  {
      //$r->synchronizeWithArray($data);
      foreach($data as $name=>$value) {
        if($value) {    
          $r[$name] = $value;
        }
      }
  }
  
  /**
   * 
   *
   * @param bool $all Add all records into a collection, even these that are didn't validate
   * @return mixed
   */
  public function parse($all = false)
  {
    if($this->strategy == null) {
      throw new tmCsvException('Table or record template must be set!');
    }

    $this->cleanHeader();
    if(! $this->parser->getHeader()) {
      throw new tmCsvException('There are no fields in the CSV header that could be used!');
    }
    
    $lineNumber = 1;
    //$result = array ('valid'=>array(), 'invalid'=>array());
    $this->parser->rewind();
    $collection = new Doctrine_Collection($this->strategy->getTable());
    $errors = array();
    while(($line = $this->parser->next())) {
      $lineNumber ++;
      $r = $this->strategy->create();
      $this->fillObject($r, $line);

      if($all || $r->isValid()) {
        $collection->add($r);
      } else {
        $errors[$lineNumber] = $this->errorsToString($r);
      }
    }
    
    if($all) {
      return $collection;
    } else {
      return array (
        'valid' => $collection, 'invalid' => $errors );
    }
  }
  
  /**
   * Parse CSV and check which lines will create valid objects.
   *
   * @param int $maxLines Test only this many lines from the CSV file 
   */
  public function testParse($maxLines = 0)
  {
    //for each row in the csv file:
    // create an object
    // validate it
    // destroy object - just log if record is ok
    

    //$collection = new Doctrine_Collection($table->getTableName());
    if($this->strategy == null) {
      throw new tmCsvException('Table or record template must be set!');
    }
    
    $this->cleanHeader();
    if(! $this->parser->getHeader()) {
      throw new tmCsvException('There are no fields in the CSV header that could be used!');
    }
    
    $lineNumber = 1;
    $result = array ();
    $this->parser->rewind();
    while(($line = $this->parser->next())) {
      if($maxLines == $line) {
        break;
      }
      $lineNumber ++;
      $result[$lineNumber] = array ();
      $r = $this->strategy->create();
      $this->fillObject($r,$line);
      
      $result[$lineNumber]['valid'] = $r->isValid(true);
      if(! $result[$lineNumber]['valid']) {
        $result[$lineNumber]['errors'] = $this->errorsToString($r);
      }
    }
    
    return $result;
  }
  
  /**
   * Convert validation errors into an array of strings. Each entry relates to one column (that contains at least one error).
   *
   * @param Doctrine_Record $r
   * @return array
   */
  private function errorsToString(Doctrine_Record $r) 
  {
     $ret = array();
     foreach($r->errorStack->toArray() as $column => $errors) {
       $ret[] = "Failed check(s) on column '$column': " . implode(',', $errors);
     }
    
     return $ret;
  }
  /**
   * Creates Doctrine Collection with a record created out of each CSV line.
   *
   * @param Doctrine_Record $template
   */


  public function close()
  {
    $this->parser->close();
  }
  /**
   * Similar to toDoctrineRecords but Doctrine Record given as an argument will be used as a template.
   * It means that some fields may be set to their default values before they are overwriteen with the CSV data. 
   *
   * @param Doctrine_Record $template
   */
  
  
  /**
   * Removes all the columns that either don't exist in the object or should not be overwriten (that's a primary key)
   */
  private function cleanHeader()
  {
    //clean up the header
    $header = $this->parser->getHeader();
    
    //if header should be case-insensitive
    if(!$this->options['case_sensitive_columns']){
      $header = $this->strategy->matchCase($header);
      $this->parser->setHeader($header);
    }
    
    $cleaned = $this->strategy->cleanData($header);
    
    $this->parser->removeHeader($cleaned);
    
    //also remove PKs
    $pks = $this->strategy->getPK();
    $this->parser->removeHeader($pks);
  }
  


}