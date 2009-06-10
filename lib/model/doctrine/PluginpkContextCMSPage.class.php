<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class PluginpkContextCMSPage extends BasepkContextCMSPage
{
  const NEXT_PERMID = -1;
  public $culture;
  public $privileges;
  // Not a typo. Doctrine calls construct() for you as an alternative
  // to __construct(), which it won't let you override.
  public function construct()
  {
    $this->culture = pkContextCMSTools::getUserCulture();
    $this->privileges = array();
  }
  private function log($message)
  {
    sfContext::getInstance()->getLogger()->info("PAGE: $message");
  }

  // Note: for best performance don't pass the user explicitly
  // unless it's NOT the current user.

  public function userHasPrivilege($privilege, $user = false)
  {
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
    // Caching for speed when answering the same query about the
    // current user over and over again during the lifetime of
    // a single request (to check editing privs on slots etc)
    if ($user === false)
    {
      $user = sfContext::getInstance()->getUser();
      if (!isset($this->privileges[$privilege]))
      {
        $this->privileges[$privilege] = $this->userHasPrivilegeBody(
          $privilege, $user);
      }
      return $this->privileges[$privilege];
    }
    else
    {
      // If we're asking about a specific user we presumably have
      // something less frequent in mind
      $this->userHasPrivilegeBody(
        $privilege, $user);
    }
  }

  protected function userHasPrivilegeBody($privilege, $user)
  {
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
      // By default users must log in to do anything except view
      $loginRequired = sfConfig::get(
          "app_pkContextCMS_$privilege" . "_login_required", 
          ($privilege === 'view' ? false : true));

      // Rule 2: if no login is required for the site as a whole for this
      // privilege, anyone can do it...
      if (!$loginRequired)
      {
        // Except for rule 2a: individual pages can be conveniently locked for 
        // viewing purposes on an otherwise public site
        if (($privilege === 'view') && $this->view_is_secure)
        {
          if ($user->isAuthenticated())
          {
            return true;
          }
          continue;
        } 
        else
        {
          return true;
        }
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
 

  private $slotCache = false;
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

  private $childrenCache = null;
  private $childrenCacheLivingOnly = null;
  // Returns an array even when there are zero children.
  // Who in the world wants to special case that as if it
  // were the end of the world?
  public function getChildren($livingOnly = true)
  {
    if ($this->childrenCache !== null)
    {
      if ($livingOnly === $this->childrenCacheLivingOnly)
      {
        return $this->childrenCache;
      }
    }
    $this->childrenCacheLivingOnly = $livingOnly;
    // TODO: consider whether it's possible to get the base query to
    // exclude archived children. That would result in multiple
    // calls to where(), but perhaps Doctrine can combine them for us.
    pkContextCMSPageTable::treeTitlesOn();
    $children = $this->getNode()->getChildren();
    pkContextCMSPageTable::treeTitlesOff();
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
    return $children;
  }
  public function hasChildren($livingOnly = true)
  {
    // not as inefficient as it looks because of the caching feature
    return (count($this->getChildren($livingOnly)) != 0);
  }

  public function getUrl()
  {
    return pkContextCMSTools::urlForPage($this->getSlug());
  }

  private $ancestorsCache = false;
  public function getAncestors()
  {
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
      ob_start();
      var_dump($fullDiff);
      sfContext::getInstance()->getLogger()->info("GG: " . str_replace("\n", " ", ob_get_clean()));
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
    pkContextCMSLuceneUpdateTable::requestUpdate($this);
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
 
  private $parentCache = false;
  public function getParent()
  {
    if ($this->parentCache === false)
    {
      $this->parentCache = $this->getNode()->getParent();
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
    $text = $this->getSearchText();
    pkZendSearch::updateLuceneIndex($this, 
      array('text' => $text),
      $this->getCulture());
  }

  public function getSearchSummary()
  {
    return pkString::limitWords($this->getSearchText(false), 100, "...");
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
}
