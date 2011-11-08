<?php

class AuthenticationFilter extends sfFilter
{
  /**
    * Execute filter
    *
    * @param sfFilterChain $filterChain
    */
  public function execute($filterChain)
  {
    if ($this->isFirstCall())
    {
      $username = sfConfig::get('app_seegnoAuthentication_username');
      $password = sfConfig::get('app_seegnoAuthentication_password');
      $realm = sfConfig::get('app_seegnoAuthentication_realm', 'seegno');

      if (!isset($_SERVER["PHP_AUTH_USER"])) 
      {
        $this->sendHeadersAndExit($realm);
      }

      if (!($username and $password))
      {
        throw new Exception("You must define seegnoAuthentication in app.yml");
      }

      if (!(($_SERVER["PHP_AUTH_USER"] == $username) and ($_SERVER["PHP_AUTH_PW"] == $password)))
      {
        $this->sendHeadersAndExit($realm);
      }
    }

    $filterChain->execute();
  }

  /**
    * Sends HTTP Auth headers and exits
    *
    * @return null
    */
  private function sendHeadersAndExit($realm)
  {
    header("WWW-Authenticate: Basic realm='$realm'");
    header("HTTP/1.0 401 Unauthorized");
    exit;
  }
}
