<?php
/**
 * @todo implement escape char if PHP >= 5.3
 * 
 * 
 * @author Tomasz Muras
 * @license LGPL 
 */


/**
 * 
 * tmCsvReader
 * @author Tomasz Muras
 */
class tmCsvReader
{
  const UNINITIALIZED = 1;
  const ALL_READ = 2;
  
  const ERR_WRONG_OPTION = 1;
  const ERR_IO = 2;
  const ERR_DOCTRINE = 3;
  const ERR_HEADER = 4;
  
  private $hasHeader;
  
  /**
   * @var array The header as parsed from the CSV file in a format array('column'=>'column'). We do not allow for 2 columns with the same name.
   */
  private $header, $schema;
  
  /**
   * @var array The header that will actually be used
   */
  private $currentHeader;
  
  private $fhandle, $path, $opened;
  
  private $length, $delimiter, $enclosure, $to, $from;
  
  private $state;
  
  private $content;
  
  /**
   * Options:
   * * delimiter
   * * enclosure
   * * length
   * * escape - works only from PHP 5.3
   * * header - first row is a header and contains column names (default: true)
   * * from - encoding
   * * to - encoding
   * * ignoreMissingFields - do not ignore a row if it has less fields than a header (default: true - missing fields will be returned as NULLs)
   * * ignoreExtraFields   - do not ignore a row if it has more fields than a header (default: true - extra fields will not be used)
   * @param string  $path      Path to the file to read
   * @param array	$options   Array with the options (delimiter, enclosure, length, header, from, to)
   */
  public function __construct($path, $options = array())
  {
    $this->path = $path;
    if(isset($options['delimiter'])) {
      $this->delimiter = $options['delimiter'];
    } else {
      $this->delimiter = ',';
    }
    
    if(isset($options['enclosure'])) {
      $this->enclosure = $options['enclosure'];
    } else {
      $this->enclosure = '"';
    }
    
    if(isset($options['length'])) {
      $this->length = $options['length'];
    } else {
      $this->length = 0;
    }
    
    if(isset($options['escape'])) {
      $this->escape = $options['escape'];
    } else {
      $this->escape = null;
    }
    
    if(isset($options['from'])) {
      $this->from = $options['from'];
    } else {
      $this->from = 'auto';
    }
    
    if(isset($options['to'])) {
      $this->to = $options['to'];
    } else {
      $this->to = 'utf-8';
    }
    
    if(strlen($this->enclosure) > 1 || strlen($this->delimiter) > 1 || strlen($this->escape) > 1) {
      new Exception('enclosure, delimiter and escape must be 1 character', self::ERR_WRONG_OPTION);
    }
    
    if(isset($options['header'])) {
      $this->hasHeader = $options['header'];
    } else {
      $this->hasHeader = true;
    }
    
    if(! is_bool($this->hasHeader)) {
      throw new Exception('Header option must equal to true or false', self::ERR_WRONG_OPTION);
    }
    
    $this->header = null;
    $this->currentHeader = null;
    $this->schema = null;
    
    $this->content = null;
    $this->opened = false;
    $this->state = self::UNINITIALIZED;
    $this->open();
  }
  
  /**
   * Returns whole content of teh CSV file as an array
   *
   * @return array
   */
  public function toArray()
  {
    //it means that well, we need it all in the memory
    $this->readIntoMemory();
    
    return $this->content;
  }
  
  public function setHeader(&$data)
  {
    if(! $this->hasHeader) {
      throw new Exception('Header is not set to be parsed', self::ERR_WRONG_OPTION);
    }
    if($this->header == null) {
      $this->readHeader();
    }
    
    $this->currentHeader = $data;
    $this->header = $data;
  }
  
  public function removeHeader($columns)
  {
    if(! $this->hasHeader) {
      throw new Exception('Header is not set to be parsed', self::ERR_WRONG_OPTION);
    }
    if($this->header == null) {
      $this->readHeader();
    }
    
    if(! is_array($columns)) {
      $columns = array (
        $columns => $columns );
    }
    
    foreach($columns as $c => $column) {
      unset($this->currentHeader[$c]);
      unset($this->currentHeader[$column]);
    }
  
  }
  
  /**
   * Returns columns removed from a header - the ones that we've decided to ignore 
   */
  public function getRemoved()
  {
    if(! $this->hasHeader) {
      throw new Exception('Header is not set to be parsed', self::ERR_WRONG_OPTION);
    }
    if($this->header == null) {
      $this->readHeader();
    }
    
    return array_diff_assoc($this->header, $this->currentHeader);
  }
  
  private function readIntoMemory()
  {
    if($this->state == self::ALL_READ)
      return true;
    
    $this->open();
    rewind($this->fhandle);
    if($this->hasHeader)
      $this->readHeader();
    $this->content = array ();
    
    $row = 0;
    
    while(($data = $this->readLine()) !== false) {
      $num = count($data);
      $row ++;
      for($c = 0; $c < $num; $c ++) {
        //echo $data[$c] . "<br />\n";
      }
      $this->content[] = $data;
    }
    $this->state = self::ALL_READ;
    
    return true;
  }
  
  public function getHeader()
  {
    if(! $this->hasHeader) {
      throw new Exception('Header is not set to be parsed', self::ERR_WRONG_OPTION);
    }
    if($this->header == null) {
      $this->readHeader();
    }
    
    return $this->currentHeader;
  }
  
  /**
   * Read one line from a CSV file
   *
   * @return array
   */
  private function readLine()
  {
    
    if($this->state == self::ALL_READ)
      return false;
      
    //for PHP 5.3: fgetcsv($this->fhandle, $this->length, $this->delimiter, $this->enclosure, $this->escape)
    $data = fgetcsv($this->fhandle, $this->length, $this->delimiter, $this->enclosure);
    
    if($data === false || count($data) < 2) {
      $this->state = self::ALL_READ;
      return false;
    }
    
    //encoding
    foreach($data as $k => $field) {
      $data[$k] = $this->encode($field);
    }
    
    if($this->hasHeader) {
      //we need to consider removed columns
      //first, we compare data we've got with the original header
      

      //@todo check the option on what do we do in this case
      if(count($data) > count($this->header)) {
        array_splice($data, count($this->header));
      }
      
      //@todo check the option on what do we do in this case
      if(count($data) < count($this->header)) {
        $data = array_merge($data, array_fill(0, count($this->header) - count($data), null));
      }
      
      $structuredData = array ();
      reset($this->header);
      //next, current
      foreach($data as $field) {
        if(array_key_exists(current($this->header), $this->currentHeader)) {
          $structuredData[current($this->header)] = $field;
        }
        next($this->header);
        //current();
      }
      return $structuredData;
      //return array_combine($this->header, $data);
    } else {
      return $data;
    }
  }
  /**
   * Rewind but do not re-read the header.
   *
   */
  public function rewind()
  {
    rewind($this->fhandle);
    if($this->hasHeader) {
      fgetcsv($this->fhandle, $this->length, $this->delimiter, $this->enclosure);
    }
  }
  
  /**
   * Rewind the file to the beginning.
   *
   */
  private function readHeader()
  {
    rewind($this->fhandle);
    
    $this->header = array ();
    $data = fgetcsv($this->fhandle, $this->length, $this->delimiter, $this->enclosure);
    foreach($data as $field) {
      if(key_exists($field, $this->header)) {
        throw new Exception("Field: '$field' is repeated twice in a header.", self::ERR_HEADER);
      }
      $this->header[$field] = $field;
    }
    $this->currentHeader = $this->header;
  }
  
  public function next()
  {
    if($this->hasHeader && $this->header === null) {
      $this->readHeader();
    }
    return $this->readLine();
  }
  
  private function open()
  {
    if($this->opened) {
      return true;
    }
    
    if(! ($this->fhandle = fopen($this->path, "r"))) {
      throw new Exception("File can not be opened ({$this->path}).", self::ERR_IO);
    }
    
    return true;
  }
  
  public function close()
  {
    if(! $this->opened) {
      return;
    }
    
    fclose($this->fhandle);
    $this->fhandle = null;
    $this->opened = false;
  }
  
  function __destruct()
  {
    $this->close();
  }
  
  private function encode($str)
  {
    
    if($this->from == 'auto') {
      $this->from = mb_detect_encoding($str);
    }
    
    if(function_exists('iconv')) {
      return iconv($this->from, $this->to, $str);
    } else {
      return mb_convert_encoding($str, $this->to, $this->from);
    }
  }

}