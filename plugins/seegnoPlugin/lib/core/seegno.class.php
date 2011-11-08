<?php

class seegno
{
  public static function mailSend($mailer, $mail)
  {
    $fp = fopen(sfConfig::get('sf_log_dir') . DIRECTORY_SEPARATOR . 'email.log', "a" );
    fwrite($fp, "[" . strftime('%Y-%m-%d %H:%M:%S') . "] Sending mail\n");
    fwrite($fp, "  subject : " . $mail->getSubject() . "\n");
    fwrite($fp, "  address : " . implode(",", array_keys($mail->getTo())) . "\n");

    try
    {
      if (!$mailer->send($mail, $failures))
      {
        fwrite($fp, "  rejected: " . implode(",", $failures) . "\n");
      }
      else
      {
        fwrite($fp, "  success\n");
      }
    }
    catch(Exception $e)
    {
      fwrite($fp, "  error   : " . $e->getMessage() . "\n");
    }

    fclose($fp);
  }

  public static function indexObjects($objects, $fields = array('id'), $options = array())
  {
    $results = array();

    if ($options === false || $options === true)
    {
      $options = array('array' => $options);
    }

    $toArray = (isset($options['array'])) ? $options['array'] : true;
    $unique = (isset($options['unique'])) ? $options['unique'] : true;

    // ensure it's an array
    if (!is_array($fields))
      $fields = array($fields);

    // pop 1st element of the array
    $field  = array_shift($fields);

    // index 1st and last level
    if (!count($fields))
    {
      foreach($objects as $object)
      {
        $id = $object;
        foreach(explode(".", $field) as $f)
          $id = $id[$f];
        if (!$unique)
        {
          $results[$id][] = (is_object($object) && $toArray) ? $object->toArray() : $object;
        }
        else
        {
          $results[$id] = (is_object($object) && $toArray) ? $object->toArray() : $object;
        }
      }

      return $results;
    }

    foreach($objects as $object)
    {
      $id = $object;

      foreach(explode(".", $field) as $f)
         $id = $id[$f];

      if(!isset($results[$id]))
        $results[$id] = array();

      $results[$id][] = (is_object($object) && $toArray) ? $object->toArray() : $object;
    }

    // for each level, index again
    foreach($results as $id => $objects)
    {
      $results[$id] = self::indexObjects($objects, $fields, $toArray);
    }

    return $results;
  }
    
  public static function load($plugin)
  {
    return sfYAML::load(sfConfig::get('sf_config_dir') . '/seegno/' . $plugin . '.yml');
  }
  
  static public function urlify($text)
  {
    $text = self::slugify(str_replace('/', '0slash01234567890', $text));

    $text = str_replace('0slash01234567890', '/', $text);

    return $text;
  }

  static public function slugify($text)
  {
    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

    // trim
    $text = trim($text, '-');

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // lowercase
    $text = strtolower($text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    if (empty($text))
    {
      //echo 'in';
      return 'n-a';
    }

    return $text;
  }
  
  public static function getCache($filename, $hours = 24)
  {
    $file = sfConfig::get('sf_cache_dir') . DIRECTORY_SEPARATOR . $filename;

    if (!file_exists($file))
    {
      return false;
    }

    include $file;

    if ($timestamp < (time()-($hours*3600)))
    {
      return false;
    }

    return $value;
  }

  public static function setCache($filename, $value)
  {
    $fp = fopen(sfConfig::get('sf_cache_dir') . DIRECTORY_SEPARATOR . $filename, 'w');
    fwrite($fp, "<?php \n");
    fwrite($fp, '  $timestamp = ' . time() . ";\n");
    fwrite($fp, '  $value = ' . var_export($value, true) . ';');
    fclose($fp);
  }

  public static function getImage($url)
  {
    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );// return web page
    curl_setopt( $ch, CURLOPT_HEADER, false );// don't return headers
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );// follow redirects
    curl_setopt( $ch, CURLOPT_ENCODING, "" );// handle all encodings
    curl_setopt( $ch, CURLOPT_USERAGENT, "spider" );// who am i
    curl_setopt( $ch, CURLOPT_AUTOREFERER, true );// set referer on redirect
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 120 );// timeout on connect
    curl_setopt( $ch, CURLOPT_TIMEOUT, 120 );// timeout on response
    curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );// stop after 10 redirects
    $content = curl_exec( $ch );
    $err = curl_errno( $ch );
    $errmsg = curl_error( $ch );
    $header = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno'] = $err;
    $header['errmsg'] = $errmsg;
    $header['content'] = $content;
    return $header["content"];
  }
  
  public static function getGravatar($email, $s = 50, $d = 'mm', $r = 'g', $img = true, $atts = array()) 
  {
    $url = 'http://www.gravatar.com/avatar/';
    
    $url .= md5(strtolower(trim($email)));
    
    $url .= "?s=$s&d=$d&r=$r";
    
    if ($img)
    {
      $url = '<img src="' . $url . '"';
      
      foreach ($atts as $key => $val)
      {
        $url .= ' ' . $key . '="' . $val . '"';
      }
      
      $url .= ' />';
    }

    return $url;
  }
}
