<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class PluginpkContextCMSPage extends BasepkContextCMSPage
{
  const NEXT_PERMID = -1;
  public $culture;
  
  // Keep all cached information here for easy reference and inclusion 
  // in the reset code in hydrate()
  public $privilegesCache = null;
  private $slotCache = false;
  private $childrenCache = null;
  private $childrenCacheLivingOnly = null;
  private $childrenCacheSlot = null;
  private $ancestorsCache = false;
  private $parentCache = false;

  public function hydrate(array $data, $overwriteLocalChanges = true)
  {
    // Purge all caches when Doctrine refreshes the object
    $this->slotCache = false;
    $this->privilegesCache = array();
    $this->childrenCache = null;
    $this->childrenCacheLivingOnly = null;
    $this->childrenCacheSlot = null;
    $this->ancestorsCache = false;
    $this->parentCache = false;
    $this->ancestorsInfo = null;
    $this->peerInfo = null;
    $this->childrenInfo = null;
    $this->tabsInfo = null;
    return parent::hydrate($data, $overwriteLocalChanges);
  }

  // Not a typo. Doctrine calls construct() for you as an alternative
  // to __construct(), which it won't let you override.
  public function construct()
  {
    $this->culture = pkContextCMSTools::getUserCulture();
    $this->privilegesCache = array();
  }
  private function log($message)
  {
    sfContext::getInstance()->getLogger()->info("PAGE: $message");
  }

  // Note: for best performance don't pass the user explicitly
  // unless it's NOT the current user.

  public function userHasPrivilege($privilege, $user = false)
  {
    // Individual pages can be conveniently locked for 
    // viewing purposes on an otherwise public site. This is
    // implemented as a separate permission. 
    if (($privilege === 'view') && $this->view_is_secure)
    {
      $privilege = 'view_locked';
    }
    
    // This was nice logic for granting delete privileges if you
    // have the privilege of editing the parent of the page. 
    // A good idea, but not what the client wants. We've gone
    // with a separate 'admin' privilege instead
//    if ($privilege === 'delete')
//    {
//      // If you can EDIT the parent, then you can DELETE
//      // its children (after all, you could create them).
//      $parent = $this->getParent();
//      if (!$parent)
//      {
//        // Nobody, not even the superadmin, can delete the home page
//        return false;
//      }
//      // Make sure we pass the user on!
//      return $parent->userHasPrivilege('edit', $user);
//    }

    if ($user === false)
    {
      $user = sfContext::getInstance()->getUser();
    }
    
    $username = false;
    if ($user->getGuardUser())
    {
      $username = $user->getGuardUser()->getUsername();
    }
    
    
    if (!isset($this->privilegesCache[$username][$privilege]))
    {
      $this->privilegesCache[$username][$privilege] = $this->userHasPrivilegeBody(
        $privilege, $user);
    }
    return $this->privilegesCache[$username][$privilege];
  }

  protected function userHasPrivilegeBody($privilege, $user, $debug = false)
  {
    // Some privileges can be defined in terms of other privileges on certain ancestor pages
    if ($privilege === 'move-up')
    {
      $parent = $this->getParent();
      if (!$parent)
      {
        return false;
      }
      $grandparent = $parent->getParent();
      if (!$grandparent)
      {
        return false;
      }
      return $grandparent->userHasPrivilegeBody('manage', $user, true);
    }
    if ($privilege === 'move-down')
    {
      $parent = $this->getParent();
      if (!$parent)
      {
        return false;
      }
      return $parent->userHasPrivilegeBody('manage', $user, true);
    }
    // Rule 1: admin can do anything
    // Work around a bug in some releases of sfDoctrineGuard: users sometimes
    // still have credentials even though they are not logged in
    if ($user->isAuthenticated() && $user->hasCredential('cms_admin'))
    {
      return true;
    }
    $privileges = explode("|", $privilege);
    foreach ($privileges as $privilege)
    {
      $key = "app_pkContextCMS_$privilege" . "_sufficient_credentials";
      $sufficientCredentials = sfConfig::get(
          "app_pkContextCMS_$privilege" . "_sufficient_credentials", false);
      $sufficientGroup = sfConfig::get(
          "app_pkContextCMS_$privilege" . "_sufficient_group", false);
      $candidateGroup = sfConfig::get(
          "app_pkContextCMS_$privilege" . "_candidate_group", false);
      // By default users must log in to do anything, except for viewing an unlocked page
      $loginRequired = sfConfig::get(
          "app_pkContextCMS_$privilege" . "_login_required", 
          ($privilege === 'view' ? false : true));

      // Rule 2: if no login is required for the site as a whole for this
      // privilege, anyone can do it...
      if (!$loginRequired)
      {
        return true;
      }

      // Corollary of rule 2: if login IS required and you're not
      // logged in, bye-bye
      if (!$user->isAuthenticated())
      {
        continue;
      }

      // Rule 3: if there are no sufficient credentials and there is no
      // required or sufficient group, then login alone is sufficient. Common 
      // on sites with one admin
      if (($sufficientCredentials === false) && ($candidateGroup === false) && ($sufficientGroup === false))
      {
        // Logging in is the only requirement
        return true; 
      }

      // Rule 4: if the user has sufficient credentials... that's sufficient!
      // Many sites will want to simply say 'editors can edit everything' etc
      if ($sufficientCredentials && 
        ($user->hasCredential($sufficientCredentials)))
      {
        return true;
      }
      if ($sufficientGroup && 
        ($user->hasGroup($sufficientGroup)))
      {
        return true;
      }

      // Rule 5: if there is a candidate group, make sure the user is a member
      // before checking for explicit privileges for that user
      if ($candidateGroup && 
        (!$user->hasGroup($candidateGroup)))
      {
        continue;
      }

      // Rule 6: when minimum but not sufficient credentials are present,
      // check for an explicit grant of privileges to this user, on
      // this page or on any ancestor page.
      $result = $this->userHasExplicitPrivilege($privilege);
      if ($result)
      {
        return true;
      }
    }
    return false;
  }
  
  private function userHasExplicitPrivilege($privilege)
  {
    // Use caching proxy implementation
    $ancestors = $this->getAncestors(); 
    $ids = array();
    foreach ($ancestors as $page)
    {
      $ids[] = $page->id;
    }
    $ids[] = $this->id;
    $user_id = sfContext::getInstance()->getUser()->getGuardUser()->getId();
    // One "yes" answer is enough.
    $result = Doctrine_Query::create()->
      from('pkContextCMSAccess a')->
      where("a.page_id IN (" . implode(",", $ids) . ") AND " .
        "a.user_id = $user_id AND a.privilege = ?", array($privilege))->
      limit(1)->
      execute();
    return (count($result) > 0);
  }

  // The new API:
  //
  // getArea(name)
  // newAreaVersion(name, action, params)
 

  private function populateSlotCache()
  {
    if ($this->slotCache === false)
    {
      $this->slotCache = array();
      // We have $this->Areas courtesy of whatever query
      // fetched the page in the first place
      foreach ($this->Areas as $area)
      {
        $areaVersion = $area->AreaVersions[0];
        foreach ($areaVersion->AreaVersionSlots as $areaVersionSlot)
        {
          $slot = $areaVersionSlot->Slot;
          $this->slotCache[$this->culture][$area->name][$areaVersionSlot->permid] = $slot;
        }
      }
    }
  }
  public function hasSlot($name, $permid = 1)
  {
    $this->populateSlotCache();
    if (isset($this->slotCache[$this->culture][$name][$permid]))
    {
      return true;
    }
    return false;
  }
  public function getSlot($name, $permid = 1)
  {
    if ($this->hasSlot($name, $permid))
    {
      return $this->slotCache[$this->culture][$name][$permid];
    }
    return false;
  }
  // $new can be a slot class name, an already-created slot object, or false.
  // If it is false no new slot is added to the list to be returned.
  // If it is a class name the slot is constructed for you. 
  //
  // If $newFirst is true the new slot will be at the top of the area,
  // otherwise the bottom.
  public function getArea($name, $new = false, $newFirst = false)
  {
    $this->populateSlotCache();
    $results = array();
    if ($new)
    {
      $permidAndRank = $this->getNextPermidAndRank($name, $newFirst);
      if (!($new instanceof pkContextCMSSlot))
      {
        // It's a class name, make one
        $new = $this->createSlot($new);
      }
      else
      {
        // We passed one in
      }
    }
    if ($new && $newFirst)
    {
      $results[$permidAndRank['permid']] = $new;
    }
    if (isset($this->slotCache[$this->culture][$name]))
    {
      foreach ($this->slotCache[$this->culture][$name] as $permid => $slot)
      {
        $results[$permid] = $slot;
      }
    }
    if ($new && (!$newFirst))
    {
      $results[$permidAndRank['permid']] = $new;
    }
    return $results;
  }

  public function getNextPermidAndRank($name, $first = false)
  {
    $query = Doctrine_Query::create()->
        select('max(s.permid) as m, ' 
          . ($first ? 'min' : 'max') . '(s.rank) as r')->
        from('pkContextCMSArea a')->
        leftJoin('a.AreaVersions v')->
        leftJoin('v.AreaVersionSlots s')->
        where('a.name = ? AND a.page_id = ?', array($name, $this->id));
    $result = $query->execute();
         
    if (isset($result[0]['m']))
    {
      $permid = $result[0]['m'] + 1;
    }
    else
    {
      $permid = 1;
    }
    // Negative ranks = perfectly fine and useful for
    // implementing "new slots on top"
    if (isset($result[0]['r']))
    {
      if ($first)
      {
        $rank = $result[0]['r'] - 1;
      }
      else
      {
        $rank = $result[0]['r'] + 1;
      }
    }
    else
    {
      $rank = 1;
    }
    return array(
      'permid' => $permid, 
      'rank' => $rank);
  }

  public function createSlot($type)
  {
    $class = $type . "Slot";
    $slot = new $class;
    $slot->type = $type;
    return $slot;
  }

  public function getTitle()
  {
    $titleSlot = $this->getSlot('title');
    if ($titleSlot)
    {
      $result = $titleSlot->value;
    }
    else
    {
      $result = '';
    }
    $title = trim($result);
    if (!strlen($result))
    {
      // Don't break the UI, return something reasonable
      $slug = $this->slug;
      $title = substr(strrchr($slug, "/"), 1);
      if (!strlen($title))
      {
        $title = "Home";
      }
    }
    return $title;
  }

  public function getAreaVersions($name, $selectOptions = true)
  {
    $results = Doctrine_Query::create()->
      from("pkContextCMSArea a")->
      leftJoin("a.AreaVersions v")->
      where("a.page_id = ? AND a.name = ? AND a.culture = ?",
        array($this->id, $name, $this->culture))->
      orderBy("v.version asc")->
      execute();
    $last = false;
    $versions = array();
    $area = $results[0];
    foreach ($area->AreaVersions as $areaVersion)
    {
			if ($selectOptions)
			{
	      $versions[$areaVersion->version] = 
	        $areaVersion->created_at . " " . ($areaVersion->Author ? 
	            $areaVersion->Author->username : "NONE") . " " . $areaVersion->diff;
			}
			else
			{
				$versions[$areaVersion->version] =
					array("created_at" => $areaVersion->created_at, "author" => $areaVersion->Author ? $areaVersion->Author->username : "NONE", "diff" => $areaVersion->diff);
			}
    }
    return $versions;
  }

  public function getAreaCurrentVersion($name)
  {
    $area = Doctrine_Query::create()->
      from("pkContextCMSArea a")->
      where("a.page_id = ? AND a.name = ? AND a.culture = ?",
        array($this->id, $name, $this->culture))->
      fetchOne();
    if ($area)
    {
      return $area->latest_version;
    }
    return 0;
  }

  // This is not the most efficient way to learn about the child pages of the current page.
  // See getChildrenInfo. This method is now primarily for backwards compatibility and relatively rare cases where 
  // you need a slot other than the title.
    
  // Returns an array even when there are zero children.
  // Who in the world wants to special case that as if it
  // were the end of the world?
  public function getChildren($livingOnly = true, $withSlot = 'title')
  {
    if ($this->childrenCache !== null)
    {
      if (($livingOnly === $this->childrenCacheLivingOnly) && ($this->childrenCacheSlot === $withSlot))
      {
        return $this->childrenCache;
      }
    }
    // TODO: consider whether it's possible to get the base query to
    // exclude archived children. That would result in multiple
    // calls to where(), but perhaps Doctrine can combine them for us.
    if ($withSlot !== false)
    {
      pkContextCMSPageTable::treeSlotOn($withSlot);      
    }
    $children = $this->getNode()->getChildren();
    
    if ($children === false)
    {
      $children = array();
    }
    
    if ($withSlot !== false)
    {
      pkContextCMSPageTable::treeSlotOff();
    }
    
    // Don't let Doctrine's clever reuse of objects prevent us from seeing
    // the results if we fetch a different slot this time... unless the child
    // is also the current page. In that case we assume that we have superior
    // data in the cache already (inclusive of all slots). Discarding that
    // was leading to disappearing data on emap
      
    $current = pkContextCMSTools::getCurrentPage();
    foreach ($children as $child)
    {
      if ($current && ($current->id === $child->id))
      {
        continue;
      }
      $child->clearSlotCache();
    }
    if ($children !== false)
    {
      $living = array();
      $dead = array();
      foreach ($children as $child)
      {
        if ($child->archived)
        {
          $dead[] = $child;
        }
        else
        {
          $living[] = $child;
        }
      }
      if ($livingOnly)
      {
        $children = $living;
      }
      else
      {
        $children = array_merge($living, $dead);
      }
    }
    else
    {
      $children = array();
    }
    $this->childrenCache = $children;
    $this->childrenCacheLivingOnly = $livingOnly;
    $this->childrenCacheSlot = $withSlot;
    return $children;
  }

  // Optimized methods returning information about related pages.
  
  // All of these methods return an array of associative arrays, as follows:
  
  // array(
  //   array('id' => page1id, 'title' => page1title, 'slug' => page1slug, 'view_is_secure' => bool, 'archived' => bool, 'level' => level),
  //   array('id' => page2id, 'title' => page2title, 'slug' => page2slug, 'view_is_secure' => bool, 'archived' => bool, 'level' => level) ...
  // )
  
  // The getTreeInfo and getAccordionInfo methods return nested arrays. If a page has children that
  // are suitable to return, then the associative array for that page will have a 'children' key, and
  // the value will be an array of child pages, which may have children of their own. If a page has
  // no children there will not be a 'children' key (you may test isset($info['children'])).
  
  // To generate a URL for a page use: pkContextCMSTools::urlForPage($info['slug'])
  
  protected $ancestorsInfo;
  
  public function getAncestorsInfo()
  {
    if (!isset($this->ancestorsInfo))
    {
      $id = $this->id;
      $this->ancestorsInfo = $this->getPagesInfo(false, '( p.lft < ' . $this->lft . ' AND p.rgt > ' . $this->rgt . ' )');
    }
    return $this->ancestorsInfo;
  }

  public function getParentInfo()
  {
    $info = $this->getAncestorsInfo();
    if (count($info))
    {
      return $info[count($info) - 1];
    }
    return false;
  }

  protected $peerInfo;
  
  public function getPeerInfo($livingOnly = true)
  {
    if (!isset($this->peersInfo))
    {
      $parentInfo = $this->getParentInfo();
      if (!$parentInfo)
      {
        // TODO: we should stub in the current page here, but it's going to be the home page, 
        // and we're not very interested in the peers of the home page (i.e. it has none)
        $this->peerInfo = array();
      }
      else
      {
        $lft = $parentInfo['lft'];
        $rgt = $parentInfo['rgt'];
        $level = $parentInfo['level'] + 1;
        $this->peerInfo = $this->getPagesInfo($livingOnly, '(( p.lft > ' . $lft . ' AND p.rgt < ' . $rgt . ' ) AND (level = ' . $level . '))');        
      }       
    }   
    return $this->peerInfo;
  }

  protected $childrenInfo;
  
  public function getChildrenInfo($livingOnly = true)
  {
    if (!isset($this->childrenInfo))
    {
      $lft = $this->lft;
      $rgt = $this->rgt;
      $level = $this->level + 1;
      $this->childrenInfo = $this->getPagesInfo($livingOnly, '(( p.lft > ' . $lft . ' AND p.rgt < ' . $rgt . ' ) AND (level = ' . $level . '))');
    }
    return $this->childrenInfo;
  }

  protected $tabsInfo;
  
  public function getTabsInfo($livingOnly = true)
  {
    if (!isset($this->tabsInfo))
    {
      $id = $this->id;
      $this->tabsInfo = $this->getPagesInfo($livingOnly, '(level = 1)');
    }
    return $this->tabsInfo;
  }
  
  // If $depth is null we get all of the descendants
  public function getTreeInfo($livingOnly = true, $depth = null)
  {
    // Recursively builds a page tree. If a page has children, the info array for that
    // page will have a 'children' element containing an array of info arrays for its
    // children, etc.
    
    // Efficiently fetches only to the appropriate depth

    // Sometimes trees will have enabled children of disabled parents. When
    // we don't want disabled pages, we have to exclude those pages too, so we'll
    // do the exclusion at a higher level, not in the SQL query

    $infos = $this->getDescendantsInfo(false, $depth);
    $offset = 0;
    $level = 0;
    return $this->getTreeInfoBody($this->lft, $this->rgt, $infos, $offset, $level + 1, $depth, $livingOnly);
  }
  
  protected function getTreeInfoBody($lft, $rgt, $infos, &$offset, $level, $depth, $livingOnly)
  {
    $count = count($infos);
    $result = array();
    if ($depth === 0)
    {
      // Limit depth 
      return $result;
    }
    while ($offset < $count)
    {      
      $info = $infos[$offset];
      if (($info['lft'] <= $lft) || ($info['rgt'] >= $rgt))
      {
        break;
      }
      $offset++;
      $children = $this->getTreeInfoBody($info['lft'], $info['rgt'], $infos, $offset, $level + 1, isset($depth) ? ($depth - 1) : null, $livingOnly);
      if (count($children))
      {
        $info['children'] = $children;
      }
      if ($livingOnly && isset($info['archived']) && $info['archived'])
      {
        continue;
      }
      else
      {
        $result[] = $info;
      }
    }
    return $result;
  }
  
  
  // Accordion nav 
  // Always starts with the children of the root and comes down to the level of this page's children,
  // listing peers of this page's ancestors at every level. That is:
  
  // Home
  //   One
  //     1a
  //     1b
  //       1bx  <-- the current page
  //         1bxA
  //         ibxB
  //     1c
  //   Two
  
  // Note that children of Two, 1a, and 1c are NOT returned. Only the siblings of
  // the current page's ancestors, the current page and its siblings, and the immediate
  // children of the current page are returned. For a full tree use getTreeInfo().
  
  public function getAccordionInfo($livingOnly = true, $depth = null)
  {
    // As far as I can tell there is no super-elegant, single-query way to do this
    // without fetching a lot of extra pages. So do a peer fetch at each level.
    
    // First build an array of arrays listing the peers at each level

    // If you have enabled children of archived ancestors and you don't
    // want the ancestors to show up, you probably shouldn't be using
    // an accordion contro. in the first place
    $ancestors = $this->getAncestorsInfo();
    $result = array();
    // Ancestor levels
    foreach ($ancestors as $ancestor)
    {
      $lineage[] = $ancestor['id'];
      if ($ancestor['level'] == 0)
      {
        $result[] = array($ancestor);
      }
      else
      {
        // TODO: this is inefficient, come up with a way to call getPeerInfo for an
        // alternate ID without fetching that entire page
        $result[] = pkContextCMSPageTable::retrieveBySlug($ancestor['slug'])->getPeerInfo($livingOnly);
      }
    }
    // Current page peers level
    $result[] = $this->getPeerInfo($livingOnly);
    $lineage[] = $this->id;
    // Current page children level
    $result[] = $this->getChildrenInfo($livingOnly);
    
    // Now fix it up to be a properly nested array like that
    // returned by getTreeInfo(). On each pass take a reference
    // to the child that will own the children of the next pass
    $accordion = $result[0][0];
    $current = &$accordion;
    for ($i = 0; ($i < (count($result) - 1)); $i++)
    {
      $current['children'] = $result[$i + 1];
      if ($i + 1 < count($lineage))
      {
        // We've already started returning the kids as a flat array so 
        // we need to scan for it unfortunately. This entire method could
        // use more attention to performance
        foreach ($current['children'] as &$child)
        {
          if ($child['id'] == $lineage[$i + 1])
          {
            $current = &$child;
            break;
          }
        }
      }
    }
    
    // Don't return the home page itself, start with the tabs.
    // This is consistent with getTreeInfo() which should simplify implementations.
    // It's easy to add the home page in at a higher level if desired.
    return $accordion['children'];
  }

  // Used by the reorganize feature. Return value is compatible with jstree. 
  // See getTreeInfo for something more appropriate for front end navigation
  
  public function getTreeJSONReady($livingOnly = true)
  {
    // Recursively builds a page tree ready to be JSON-encoded and sent to
    // the jsTree object (yes this is rather specific to jsTree for the model layer,
    // but this would be a reasonable input format for any JS tree implementation).
    
    // Sometimes trees will have enabled children of archived parents. When
    // we don't want disabled pages, we have to exclude those pages too, so we'll
    // do the exclusion at a higher level, not in the SQL query
    $infos = $this->getDescendantsInfo(false);
    $offset = 0;
    $level = 0;
    $tree = array("attributes" => array("id" => "tree-" . $this->id),
      "data" => $this->getTitle(),
      "state" => 'open',
      "children" => $this->getTreeJSONReadyBody($this->lft, $this->rgt, $infos, $offset, $level + 1, $livingOnly)
    );
    if (!count($tree['children']))
    {
      unset($tree['children']);
    }
    else
    {
      $item['state'] = 'open';
    }
  return $tree;
  }

  protected function getTreeJSONReadyBody($lft, $rgt, $infos, &$offset, $level, $livingOnly)
  {
    $count = count($infos);
    $result = array();
    while ($offset < $count)
    {      
      $info = $infos[$offset];
      if (($info['lft'] <= $lft) || ($info['rgt'] >= $rgt))
      {
        break;
      }
      $offset++;
      $class = ($info['archived'])? 'archived' : 'alive';
      $item = array(
        "attributes" => array("id" => "tree-" . $info['id'], "class" => $class), 
        "data" => $info['title'],
        "children" => $this->getTreeJSONReadyBody($info['lft'], $info['rgt'], $infos, $offset, $level + 1, $livingOnly)
      );
      if (!count($item['children']))
      {
        unset($item['children']);
      }
      else
      {
        $item['state'] = ($level < 2) ? "open" : "closed";
      }
      if ($livingOnly && isset($info['archived']) && $info['archived'])
      {
        // Skip it (and therefore its children as well) in the final result
      }
      else
      {
        $result[] = $item;
      }
    }
    return $result;
  }
  
  // Low level access to all info for all descendants. You probably don't want this. For an interface that
  // gives you back a hierarchy see getTreeInfo. 
  protected function getDescendantsInfo($livingOnly = true, $depth = null)
  {
    $where = '( p.lft > ' . $this->lft . ' AND p.rgt < ' . $this->rgt . ' )';
    if (isset($depth))
    {
      $where = '(' . $where . ' AND (p.level <= ' . ($this->level + $depth) . '))';
    }
    return $this->getPagesInfo($livingOnly, $where);
  }
  
  // This is the low level query method used to implement the above. You won't call this directly
  // unless you're implementing a new type of query for related pages
  
  protected function getPagesInfo($livingOnly = true, $where)
  {
    // Raw PDO for performance
    $connection = Doctrine_Manager::connection();
    $pdo = $connection->getDbh();
    $query = "SELECT p.id, p.slug, p.view_is_secure, p.archived, p.lft, p.rgt, p.level, s.value AS title FROM pk_context_cms_page p
      LEFT JOIN pk_context_cms_area a ON a.page_id = p.id AND a.name = 'title'
      LEFT JOIN pk_context_cms_area_version v ON v.area_id = a.id AND a.latest_version = v.version 
      LEFT JOIN pk_context_cms_area_version_slot avs ON avs.area_version_id = v.id
      LEFT JOIN pk_context_cms_slot s ON s.id = avs.slot_id ";
    $whereClauses = array();
    if ($livingOnly)
    {
      // Watch out, p.archived IS NULL in some older dbs
      
      // = FALSE is not SQL92 correct. IS FALSE is. And so it works in SQLite. Learn something
      // new every day. 
      $whereClauses[] = '(p.archived IS FALSE OR p.archived IS NULL)';
    }
    // Pay attention to the current culture. Thanks to virtualize
    $whereClauses[] =  '(a.culture = ' . $connection->quote($this->getCulture()) . ')';
    $whereClauses[] = $where;
    $query .= "WHERE " . implode(' AND ', $whereClauses);
    $query .= " ORDER BY p.lft";
    $resultSet = $pdo->query($query);
    // Turn it into an actual array (what would happen if we didn't bother?)
    $results = array();
    foreach ($resultSet as $result)
    {
      $results[] = $result;
    }
    return $results;
  }
 
  public function hasChildren($livingOnly = true)
  {
    // not as inefficient as it looks because of the caching feature
    return (count($this->getChildren($livingOnly)) != 0);
  }

  public function getUrl($absolute = true)
  {
    return pkContextCMSTools::urlForPage($this->getSlug(), $absolute);
  }

  public function getAncestors()
  {
    // Home page has no ancestors; save a query on a popular page
    if ($this->level == 0)
    {
      return array();
    }
    if ($this->ancestorsCache !== false)
    {
      return $this->ancestorsCache;
    } 
    pkContextCMSPageTable::treeTitlesOn();
    $this->ancestorsCache = $this->getNode()->getAncestors();
    pkContextCMSPageTable::treeTitlesOff();
    if ($this->ancestorsCache === false)
    {
      // Empty lists are not evil!
      $this->ancestorsCache = array();
    }
    return $this->ancestorsCache;
  }
  public function isEqualTo($page)
  {
    return ($page->getSlug() === $this->getSlug());
  }

  public function begin()
  {
    $conn = Doctrine_Manager::connection();
    $conn->beginTransaction();
  }

  public function end()
  {
    $conn = Doctrine_Manager::connection();
    $conn->commit();
  }

  // SAVE ANY CHANGES to the actual page object FIRST before you call this method.

  // 20090505: you must pass valid HTML text (i.e. pre-escaped entities)
  public function setTitle($title)
  {
    $slot = $this->createSlot('pkContextCMSText');
    $slot->value = $title;
    $slot->save();
    $this->newAreaVersion('title', 'update', 
      array(
        'permid' => 1, 
        'slot' => $slot));
  }

  // SAVE ANY CHANGES to the actual page object FIRST before you call this method.
  
  public function newAreaVersion($name, $action, $params = false)
  {
    $diff = '';
    if ($params === false)
    {
      $params = array();
    }
    $this->begin();
    // We use the slots already queried as a basis for the new version,
    // because that makes rollback easy to implement etc. But we
    // MUST fetch the latest copy of the area object to make sure
    // we don't create duplicate versions.

    // When we're adding a new slot to an area we need to make sure it
    // it is first in the hash so it gets ranked first
    if ($action === 'add')
    {
      $diff = '<strong>' . pkString::limitCharacters($params['slot']->getSearchText(), 20) . "</strong>";
      $newSlots = $this->getArea($name, $params['slot'], true);
    }
    else
    {
      $newSlots = $this->getArea($name);
    }
    $area = pkContextCMSAreaTable::retrieveOrCreateByPageIdAndName(
      $this->id,
      $name);
    if (!$area->id)
    {
      // We need an ID established
      $area->save();
    }
    $areaVersion = new pkContextCMSAreaVersion();
    $areaVersion->area_id = $area->id;
    $areaVersion->version = $area->latest_version + 1;
    $areaVersion->author_id = 
      sfContext::getInstance()->getUser()->getGuardUser()->getId();
    if ($action === 'delete')
    {
      if (isset($newSlots[$params['permid']]))
      {
        $diff = '<strike>' . pkString::limitCharacters($newSlots[$params['permid']]->getSearchText(), 20) . '</strike>';
        unset($newSlots[$params['permid']]);
      }
    }
    elseif ($action === 'update')
    {
      $oldText = '';
      if (isset($newSlots[$params['permid']]))
      {
        $oldText = $newSlots[$params['permid']]->getSearchText();
      }
      $newText = $params['slot']->getSearchText();
      $fullDiff = pkString::diff($oldText, $newText);
      $diff = '';
      if (!empty($fullDiff['onlyin1']))
      {
        $diff .= '<strike>' . pkString::limitCharacters($fullDiff['onlyin1'][0], 20) . '</strike>';
      }
      if (!empty($fullDiff['onlyin2']))
      {
        $diff .= '<strong>' . pkString::limitCharacters($fullDiff['onlyin2'][0], 20) . '</strong>';
      }
      $newSlots[$params['permid']] = $params['slot']; 
    }
    elseif ($action === 'add')
    {
      // We took care of this in the getArea call
    }
    elseif ($action === 'sort')
    {
      $diff = '[Reordered slots]';
      $newerSlots = array();
      foreach ($params['permids'] as $permid)
      {
        $newerSlots[$permid] = $newSlots[$permid];
      }
      $newSlots = $newerSlots;
    }
    elseif ($action === 'revert')
    {
      // TODO: actually represent the changes carried out by the reversion
      // in the diff. That's rather expensive because many slots in the area
      // may have changed all at once.
      $diff = '[Reverted to older version]';
      # We just want whatever is in the slot cache copied to a new version
    }
    $areaVersion->diff = $diff;
    $areaVersion->save();

    $rank = 1;
    foreach ($newSlots as $permid => $slot)
    {
      // After unset, foreach shows keys but has null values
      if (!$slot)
      {
        continue;
      }
      $areaVersionSlot = new pkContextCMSAreaVersionSlot();
      $areaVersionSlot->slot_id = $slot->id;
      $areaVersionSlot->permid = $permid;
      $areaVersionSlot->area_version_id = $areaVersion->id;
      $areaVersionSlot->rank = $rank++;
      $areaVersionSlot->save();
    }
    $area->latest_version++;
    $area->save();
    if (sfConfig::get('app_pkContextCMS_defer_search_updates', false))
    {
      // Deferred updates are sometimes nice for performance...
      pkContextCMSLuceneUpdateTable::requestUpdate($this);
    }
    else
    {
      // ... But the average developer hates cron.
      // Without this the changes we just made aren't visible to getSearchText,
      // we need to trigger a thorough recaching
      pkContextCMSPageTable::retrieveByIdWithSlots($this->id);
      $this->updateLuceneIndex();
    }
    $this->end();
  }
  public function clearSlotCache()
  {
    $this->slotCache = false;
  }
  public function getAccessesById($privilege)
  {
    $candidateGroup = sfConfig::get('app_pkContextCMS_' . $privilege . '_candidate_group', false);
    $sufficientGroup = sfConfig::get('app_pkContextCMS_' . $privilege . '_sufficient_group', false);
    $query = Doctrine_Query::create();
    $query->from("sfGuardUser u");
    $withClauses = array();
    $withParameters = array();
    if ($candidateGroup)
    {
      $candidateGroup = Doctrine::getTable('sfGuardGroup')->findOneByName($candidateGroup);
      if (!$candidateGroup)
      {
        throw new Exception("Candidate group for $privilege was set but does not exist");
      }
      $withClauses[] = "g.id = ?";
      $withParameters[] = $candidateGroup->id;
    }
    if ($sufficientGroup)
    {
      $sufficientGroup = Doctrine::getTable('sfGuardGroup')->findOneByName($sufficientGroup);
      if (!$sufficientGroup)
      {
        throw new Exception("Sufficient group for $privilege was set but does not exist");
      }
      $withClauses[] = "g.id = ?";
      $withParameters[] = $sufficientGroup->id;
    }
    if (count($withClauses))
    {
      $query->innerJoin("u.groups g with " . implode(" OR ", $withClauses),
        $withParameters);
    } 
    $query->orderBy("u.username asc");
    $allResults = $query->execute();
    $all = array();
    $sufficient = array();
    foreach ($allResults as $actor)
    {
      $all[$actor->id] = $actor->username;
      if ($sufficientGroup && ($actor->hasGroup($sufficientGroup->getName())))
      {
        $sufficient[] = $actor->id;
      }
    }
    $query = Doctrine_Query::create();
    $query->from("sfGuardUser u");
    $ancestors = $this->getAncestors();
    $ancestorIds = array();
    foreach ($ancestors as $ancestor)
    {
      $ancestorIds[] = $ancestor->id;
    }
    $ancestorIds[] = $this->id;
    $query->innerJoin("u.Accesses a with a.page_id IN (" .
      implode(",", $ancestorIds) . ") and a.privilege = ?", 
      array($privilege));
    $query->orderBy("u.username asc");
    $selectedResults = $query->execute();
    $selected = array();
    $inherited = array();
    $found = array();
    foreach ($selectedResults as $user)
    {
      foreach ($user->Accesses as $access)
      {
        if (!isset($found[$user->id]))
        {
          if ($access->page_id !== $this->id)
          {
            $inherited[] = $user->id;
            $found[$user->id] = true;
          }
          else
          {
            $selected[] = $user->id;
            $found[$user->id] = true;
          }
        }
      }
    }
    return array($all, $selected, $inherited, $sufficient);
  }
  public function setAccessesById($privilege, $ids)
  {
    // Could probably be more elegant using Doctrine collections
    $query = Doctrine_Query::create();
    // Make sure we select() only a.* so that we don't wind up
    // reloading the page object and causing problems in updateObject().
    $query->select('a.*')
      ->from('pkContextCMSAccess a')
      ->innerJoin('a.Page p')
      ->where('a.privilege = ? AND p.id = ?', array($privilege, $this->id));
    $accesses = $query->execute();
    foreach ($accesses as $access)
    {
      if ($access->privilege === $privilege)
      {
        $access->delete();
      }
    }
    foreach ($ids as $id)
    {
      $access = new pkContextCMSAccess();
      $access->user_id = $id;
      $access->privilege = $privilege;
      $access->page_id = $this->id;
      $access->save();
    }
  }
 
  // The parent object comes back with a populated title slot.
  // The other slots are NOT populated for performance reasons
  // (is there a scenario where this would be a problem?)
  public function getParent($with = false)
  {
    if ($this->parentCache === false)
    {
      pkContextCMSPageTable::treeTitlesOn();
      $this->parentCache = $this->getNode()->getParent();
      pkContextCMSPageTable::treeTitlesOff();
    }
    return $this->parentCache;
  }

  public function delete(Doctrine_Connection $conn = null)
  {
    // TODO: must delete outstanding indexing requests here
    return pkZendSearch::deleteFromDoctrineAndLucene($this, null, $conn);
  }

  public function doctrineDelete(Doctrine_Connection $conn)
  {
    return parent::delete($conn);
  }

  public function save(Doctrine_Connection $conn = null)
  {
    // We don't use saveInDoctrineAndLucene here because there are
    // too many side effects and the performance is terrible. Asynchronous 
    // indexing is the way to go
    return parent::save($conn);
  }

  public function doctrineSave(Doctrine_Connection $conn)
  {
    return parent::save($conn);
  }

  public function updateLuceneIndex()
  {
    $title = $this->getTitle();
    $summary = $this->getSearchSummary();
    $text = $this->getSearchText();
    $slug = $this->getSlug();
    pkZendSearch::updateLuceneIndex($this, 
      array('text' => $text),
      $this->getCulture(),
      array(
        'title' => $title,
        'summary' => $summary,
        'slug' => $slug,
        'view_is_secure' => $this->getViewIsSecure()));
  }

  public function getSearchSummary()
  {
    return pkString::limitWords($this->getSearchText(false), sfConfig::get('app_pkContextCMS_search_summary_wordcount', 50), "...");
  }

  public function getSearchText($withTitle = true)
  {
    $text = "";
    $this->populateSlotCache();
    if (isset($this->slotCache[$this->culture]))
    {
      foreach ($this->slotCache[$this->culture] as $name => $area)
      {
        if (!$withTitle)
        {
          if ($name === 'title')
          {
            continue;
          }
        }
        foreach ($area as $permid => $slot)
        {
          $text .= $slot->getSearchText() . "\n";
        }
      }
    }
    return $text;
  }

  // Pages can contain slots for all cultures, but this returns the
  // culture associated with the slots that were retrieved with
  // the page in this particular case.
  public function getCulture()
  {
    return $this->culture;
  }

  // You don't call this ordinarily. It's part of the implementation of
  // fetching a page along with slots for a particualr culture.
  public function setCulture($culture)
  {
    $this->culture = $culture;
  }
  
  public function getPeersAsOptionsArray()
  {
    $peers = array();
    $parent = $this->getParent();
    if (!$parent)
    {
      return $peers;
    }
    $children = $parent->getChildren();
    foreach ($children as $child)
    {
      if ($child->id === $this->id)
      {
        continue;
      }
      $peers[$child->id] = $child->getTitle();
    }
    return $peers;
  }
  
}
