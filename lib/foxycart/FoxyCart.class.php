<?php

class FoxyCart {

 private static $secret = 'lSZBbQaQ8kJQx9fX6cFDeLuFXyGPBV3MDWeICepEtZkU3qIYwetBUMbHfInG';
 
 protected static $cart_url = 'https://carpetbeggers.foxycart.com/cart';

 public static function getHashQuery($qs, $output = false) 
 {
   $hash = hash_hmac('sha256', urldecode($qs), self::$secret);

   $url = self::$cart_url . '?' . $qs . '&amp;hash=' . $hash;

   if ($output) 
   {
     echo $url;
   } 
   else 
   {
     return $url;
   }
 }

 public static function getHashValue($product_code, $option_name, $option_value = '', $output = true) 
 {
  if (!$product_code || !$option_name) 
  {
   return false;
  }

  if ($option_value == '--OPEN--') 
  {
   $hash = hash_hmac('sha256', $product_code . $option_name . $option_value, self::$secret);
   $value = $option_name . '||' . $hash . '||open';
  }
  else 
  {
   $hash = hash_hmac('sha256', $product_code . $option_name . $option_value, self::$secret);
   $value = $option_value . '||' . $hash;
  }

  if ($output) 
  {
   echo $value;
  } 
  else
  {
   return $value;
  }
 }
}