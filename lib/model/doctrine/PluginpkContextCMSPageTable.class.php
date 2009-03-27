<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginpkContextCMSPageTable extends Doctrine_Table
{
  // Is this the best place to keep snippets like this?

  // If culture is null you get the current user's culture,
  // or sf_default_culture if none is set or we're running in a task context
  static public function retrieveBySlug($slug, $culture = null)
  {
    if (is_null($culture))
    {
      $culture = pkContextCMSTools::getUserCulture();
    }
    $query = new Doctrine_Query();
    $page = $query->
      from('pkContextCMSPage p')->
      where('p.slug = ?', $slug)->
      fetchOne();
    // In case Doctrine is clever and returns the same page object
    if ($page)
    {
      $page->clearSlotCache();
    }
    $page->setCulture($culture);
    return $page;
  }

  // If culture is null you get the current user's culture,
  // or sf_default_culture if none is set or we're running in a task context
  static public function retrieveBySlugWithTitles($slug, $culture = null)
  {
    if (is_null($culture))
    {
      $culture = pkContextCMSTools::getUserCulture();
    }
    $query = self::queryWithTitles($culture);
    $page = $query->
      where('p.slug = ?', $slug)->
      fetchOne();
    // In case Doctrine is clever and returns the same page object
    if ($page)
    {
      $page->clearSlotCache();
    }
    $page->setCulture($culture);
    return $page;
  }

  // If culture is null you get the current user's culture,
  // or sf_default_culture if none is set or we're running in a task context
  static public function retrieveBySlugWithSlots($slug, $culture = null)
  {
    if (is_null($culture))
    {
      $culture = pkContextCMSTools::getUserCulture();
    }
    $query = self::queryWithSlots(false, $culture);
    $page = $query->
      where('p.slug = ?', $slug)->
      fetchOne();
    // In case Doctrine is clever and returns the same page object
    if ($page)
    {
      $page->clearSlotCache();
    }
    $page->setCulture($culture);
    return $page;
  }
  // If culture is null you get the current user's culture,
  // or sf_default_culture if none is set or we're running in a task context

  static public function queryWithTitles($culture = null)
  {
    if (is_null($culture))
    {
      $culture = pkContextCMSTools::getUserCulture();
    }
    return Doctrine_Query::Create()->
      select("p.*, a.*, v.*, avs.*, s.*")->
      from("pkContextCMSPage p")->
      leftJoin('p.Areas a WITH (a.name = ? AND a.culture = ?)', array('title', $culture))->
      leftJoin('a.AreaVersions v WITH (a.latest_version = v.version)')->
      leftJoin('v.AreaVersionSlots avs')->
      leftJoin('avs.Slot s');
  }
 
  // If culture is null you get the current user's culture,
  // or sf_default_culture if none is set or we're running in a task context

  static public function retrieveByIdWithSlots($id, $culture = null)
  {
    return self::retrieveByIdWithSlotsForVersion($id, false, $culture);
  }
  // If culture is null you get the current user's culture,
  // or sf_default_culture if none is set or we're running in a task context

  static public function retrieveByIdWithSlotsForVersion($id, $version, $culture = null)
  {
    if (is_null($culture))
    {
      $culture = pkContextCMSTools::getUserCulture();
    }
    $page = self::queryWithSlots($version, $culture)->
      where('p.id = ?', array($id))->
      fetchOne();
    // In case Doctrine is clever and returns the same page object
    if ($page)
    {
      $page->clearSlotCache();
    }
    $page->setCulture($culture);
    return $page;
  }

  // If culture is null you get the current user's culture,
  // or sf_default_culture if none is set or we're running in a task context

  static public function queryWithSlots($version = false, $culture = null)
  {
    if (is_null($culture))
    {
      $culture = pkContextCMSTools::getUserCulture();
    }
    $query = Doctrine_Query::Create()->
      select("p.*, a.*, v.*, avs.*, s.*")->
      from("pkContextCMSPage p")->
      leftJoin('p.Areas a WITH a.culture = ?', array($culture));
    if ($version === false)
    {
      $query = $query->
        leftJoin('a.AreaVersions v WITH (a.latest_version = v.version)');
    }
    else
    {
      $query = $query->
        leftJoin('a.AreaVersions v WITH (v.version = ?)', array($version));
    }
    return $query->leftJoin('v.AreaVersionSlots avs')->
      leftJoin('avs.Slot s')->
      orderBy('avs.rank asc');
  }
  
  static private $treeObject = null;
  static public function treeTitlesOn()
  {
    $query = pkContextCMSPageTable::queryWithTitles();
    self::$treeObject = Doctrine::getTable('pkContextCMSPage')->getTree();
    // I'm not crazy about how I have to set the base query and then
    // reset it, instead of simply passing it to getChildren. A
    // Doctrine oddity
    self::$treeObject->setBaseQuery($query);
  }
  static public function treeTitlesOff()
  {
    self::$treeObject->resetBaseQuery();
  }
 
  public function getLuceneIndexFile()
  {
    return pkZendSearch::getLuceneIndexFile($this);
  }

  public function getLuceneIndex()
  {
    return pkZendSearch::getLuceneIndex($this);
  }

  public function rebuildLuceneIndex()
  {
    pkZendSearch::purgeLuceneIndex($this);
    $pages = $this->findAll();
    foreach ($pages as $page)
    {
      $cultures = array();
      foreach ($page->Areas as $area)
      {
        $cultures[$area->culture] = true; 
      }
      $cultures = array_keys($cultures);
      foreach ($cultures as $culture)
      {
        $cpage = self::retrieveByIdWithSlots($page->id, $culture);
        $cpage->updateLuceneIndex();
      }
    }
  }
  public function searchLucene($query)
  {
    return pkZendSearch::searchLucene($this, $query);
  }
  public function addSearchQuery(Doctrine_Query $q = null, $luceneQuery)
  {
    return pkZendSearch::addSearchQuery($this, $q, $luceneQuery);
  }
}
