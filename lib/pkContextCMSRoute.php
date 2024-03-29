<?php

// Used by engine pages

// NOT YET: implements pkContextCMSRouteClass

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
    $url = pkContextCMSRouteTools::removePageFromUrl($this, $url);
    return parent::matchesUrl($url, $context);
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
    // Note that you must pass false to parent::generate for the $absolute parameter
    return pkContextCMSRouteTools::addPageToUrl($this, parent::generate($params, $context, false), $absolute);
  } 
}
