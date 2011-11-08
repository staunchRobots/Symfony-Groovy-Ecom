<?php

class tmCsvWriter
{
  private $hasHeader;
  
  private $header;
  
  private $length, $delimiter, $enclosure, $to, $from;
  
  private $tmpFile;
  
  private $state;
  
  const LINE_ZERO = 1;
  const WRITING_CONTENT = 2;
  const FINISHED = 3;
  
  public function __construct($options = array())
  {
    $this->tmpFile = tempnam(sys_get_temp_dir(), 'CSV');
    $this->tmpHandle = fopen($this->tmpFile, "w");
    
    $this->state = self::LINE_ZERO;
    //stdout
  //
  }
  
  /**
   * Add one row of data to the csv file
   *
   */
  public function add($row)
  {
    if($this->hasHeader) {
      if($this->state == self::LINE_ZERO) {
        fputcsv($this->tmpHandle, $this->header);
        $this->state = self::WRITING_CONTENT;
      }
      $ordered = array ();
      foreach($this->header as $h) {
        //how to handle many-to-many?
        if(is_array($row[$h])) {
          $ordered[$h] = '';
        } else {
          $ordered[$h] = @$row[$h];
        }
      }
      fputcsv($this->tmpHandle, $ordered);
    } else {
      fputcsv($this->tmpHandle, $row);
    }
  }
  
  public function close()
  {
    if($this->state == self::FINISHED) {
      return;
    }
    
    if($this->tmpHandle) {
      fclose($this->tmpHandle);
      $this->tmpHandle = null;
    }
    unlink($this->tmpFile);
    $this->state = self::FINISHED;
  }
  
  public function stream()
  {
    fclose($this->tmpHandle);
    $this->tmpHandle = null;
    $response = sfContext::getInstance()->getResponse();
    $response->clearHttpHeaders();
    $response->setHttpheader('Pragma: public', true);
    $response->addCacheControlHttpHeader('Cache-Control', 'must-revalidate');
    $response->setContentType('text/comma-separated-values', true);
    $response->setHttpHeader('Content-Description', 'File Transfer');
    $response->setHttpHeader('Content-Transfer-Encoding', 'binary', true);
    $response->setHttpHeader('Content-Length', filesize($this->tmpFile));
    $response->setHttpHeader('Content-Disposition', 'attachment; filename=export.csv');
    $response->sendHttpHeaders();
    readfile($this->tmpFile);
    flush();
    $this->close();

    throw new sfStopException();
  }
  
  /**
   * Set header fields. Only if no fields were saved already.
   * When header is set, data is expected as array('key'=>'value',...)
   *
   * @param array $header
   */
  public function setHeader($header)
  {
    $this->hasHeader = true;
    foreach($header as $h) {
      $this->header[$h] = $h;
    }
  }
  
  public function save($path)
  {
    if($this->state == self::FINISHED) {
      return false;
    }
    fclose($this->tmpHandle);
    if(! rename($this->tmpFile, $path)) {
      throw new tmCsvException("Can't save the file to $path");
    }
    $this->state = self::FINISHED;
    return true;
  }
}