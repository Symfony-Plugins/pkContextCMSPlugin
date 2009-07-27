<?php

// Used by engine pages

class pkContextCMSRoute extends sfRoute
{
  public function __construct($pattern, array $defaults = array(), array $requirements = array(), array $options = array())
  {
    parent::__construct($pattern, $defaults, $requirements, $options);  
  }

  /**
   * Returns true if the URL matches this route, false otherwise.
   *
   * @param  string  $url     The URL
   * @param  array   $context The context
   *
   * @return array   An array of parameters
   */
  public function matchesUrl($url, $context = array())
  {
    $remainder = false;
    // Modifies $remainder if it returns a matching page
    $page = pkContextCMSPageTable::getMatchingEnginePage($url, $remainder);
    if (!$page)
    {
      return false;
    }
    // Engine pages can't have subpages, so if the longest matching path for any engine page
    // has the wrong engine type for this route, this route definitely doesn't match
    if ($page->engine !== $this->defaults['module'])
    {
      return false;
    }
    // Allows pkContextCMSRoute URLs to be written like ordinary URLs rather than
    // specifying an empty URL, which seems prone to lead to incompatibilities
    
    // Remainder comes back as false, not '', for an exact match
    if (!strlen($remainder))
    {
      $remainder = '/';
    }
    $url = $remainder;
    $result = parent::matchesUrl($url, $context);
    sfContext::getInstance()->getLogger()->info("ZQ *" . $this->pattern . "* $url");
    return $result;
  }

  /**
   * Generates a URL from the given parameters.
   *
   * @param  mixed   $params    The parameter values
   * @param  array   $context   The context
   * @param  Boolean $absolute  Whether to generate an absolute URL
   *
   * @return string The generated URL
   */
  public function generate($params, $context = array(), $absolute = false)
  {
    // Never a good idea when we're going to append it to a page URL
    $absolute = false;
    $url = parent::generate($params, $context, $absolute);
    $page = pkContextCMSTools::getCurrentPage();
    if (!$page)
    {
      throw new sfException('Attempt to generate pkContextCMSRoute URL without a current page');
    }
    if ($url === '/')
    {
      $url = '';
    }
    $pageUrl = $page->getUrl(false);
    // Strip controller off again. TODO: this is gross, is there a better way
    // that preserves the ability to map CMS pages to someplace other than the root?
    if (preg_match("/^\/[^\/]+\.php(.*)$/", $pageUrl, $matches))
    {
      $pageUrl = $matches[1];
    }
    return $pageUrl . $url;
  } 
}
