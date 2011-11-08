<?php

function convertInchesToFeet($inches, $expand, $round)
{
  $feet = $inches/12;
  $feet = round($feet, 2);

  $parts = explode(".", $feet);
  $whole_feet = $parts[0];

  $remaining_inches = round(($parts[1]/100)*12);
  
  if (isset($expand) && $expand)
  {
    $sep1 = 'ft';
    $sep2 = 'in';
  }
  else 
  {
    //$sep1 = "&#39;";
    $sep1 = 'ft ';
    $sep2 = "&quot;";
  }
  
  if (isset($round) && $round)
  {
    return $whole_feet.$sep1;
  }
  else 
  {
    return $whole_feet.$sep1.$remaining_inches.$sep2;
  }
}

function in_multiarray($elem, $array)
{
  $top = sizeof($array) - 1;
  $bottom = 0;

  while($bottom <= $top)
  {
    if ($array[$bottom] == $elem)
    {
      return true; 
    }
    else
    {
      if (is_array($array[$bottom]))
      {
        if (in_multiarray($elem, ($array[$bottom])))
        {
          return true;
        }
      }
    }

    $bottom++;
  }        
  return false;
}

?>