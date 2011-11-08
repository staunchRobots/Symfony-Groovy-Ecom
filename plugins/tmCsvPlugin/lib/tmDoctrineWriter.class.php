<?php

/**
 * @todo Support different encodings
 *
 */
class tmDoctrineWriter
{
  /**
   * 
   *
   * @var tmCsvWriter
   */
  private $writer;
  
  public function __construct()
  {
    $this->writer = new tmCsvWriter();
  }
  
  public function exportQuery(Doctrine_Query $query)
  {
    $result = $query->execute(array(),Doctrine::HYDRATE_ARRAY);
        //echo '<pre>';
        //var_dump($result);
    if(count($result) == 0) {
      return false;
    }
    $this->writer->setHeader(array_keys($result[0]));
    foreach($result as $k=>$v) {
      $this->writer->add($v);
    }
    //$this->writer->save('/tmp/a.csv');
    $this->writer->stream();
  }
}

?>