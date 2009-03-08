<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginpkContextCMSPageTable extends Doctrine_Table
{
  // Is this the best place to keep snippets like this?
  static public function retrieveBySlug($slug)
  {
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
    return $page;
  }
  static public function retrieveBySlugWithTitles($slug)
  {
    $query = self::queryWithTitles();
    $page = $query->
      where('p.slug = ?', $slug)->
      fetchOne();
    // In case Doctrine is clever and returns the same page object
    if ($page)
    {
      $page->clearSlotCache();
    }
    return $page;
  }
  static public function retrieveBySlugWithSlots($slug)
  {
    $query = self::queryWithSlots();
    $page = $query->
      where('p.slug = ?', $slug)->
      fetchOne();
    // In case Doctrine is clever and returns the same page object
    if ($page)
    {
      $page->clearSlotCache();
    }
    return $page;
  }

  static public function queryWithTitles()
  {
    $culture = pkContextCMSTools::getUserCulture();
    return Doctrine_Query::Create()->
      select("p.*, a.*, v.*, avs.*, s.*")->
      from("pkContextCMSPage p")->
      leftJoin('p.Areas a WITH (a.name = ? AND a.culture = ?)', array('title', $culture))->
      leftJoin('a.AreaVersions v WITH (a.latest_version = v.version)')->
      leftJoin('v.AreaVersionSlots avs')->
      leftJoin('avs.Slot s');
  }
  
  static public function retrieveByIdWithSlots($id)
  {
    return self::retrieveByIdWithSlotsForVersion($id, false);
  }

  static public function retrieveByIdWithSlotsForVersion($id, $version)
  {
    $page = self::queryWithSlots($version)->
      where('p.id = ?', array($id))->
      fetchOne();
    // In case Doctrine is clever and returns the same page object
    if ($page)
    {
      $page->clearSlotCache();
    }
    return $page;
  }

  static public function queryWithSlots($version = false)
  {
    $culture = pkContextCMSTools::getUserCulture();
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
}
