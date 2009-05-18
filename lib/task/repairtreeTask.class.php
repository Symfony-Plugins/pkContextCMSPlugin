<?php

class repairtreeTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'pkContextCMS';
    $this->name             = 'repair-tree';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [repair-tree|INFO] task attempts to repair a damaged Doctrine
nested set tree (i.e. the hierarchy of pages in the CMS). This is not
guaranteed to work.

Call it with:

  [php symfony pkContextCMS:repair-tree|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();

    // add your code here
    
    // This is inefficient but we want to pull the information in the simplest
    // way possible and then rebuild the tree, playing dumb as we go
    
    $tree = array();
    $home = pkContextCMSPageTable::retrieveBySlug('/');
    $tree = $this->getSubtree($home);
    $this->hits = array();
    $this->setSubtree($home, $tree, 1, 0, '');
  }
  
  protected function getSubtree($page)
  {
    $result = array();
    $children = $page->getNode()->getChildren();
    if (!empty($children))
    {
      foreach ($children as $child)
      {
        $result[$child->getId()] = $this->getSubtree($child);
      }
    }
    return $result;
  }
  
  protected function setSubtree($page, $tree, $lft, $level, $prefix = '')
  {
    if (isset($this->hits[$page->id]))
    {
      echo("Second visit to " . $page->id . ", presumably due to damage. Skipping\n");
      return $lft;
    }
    $this->hits[$page->id] = true;
    echo($prefix . $page->slug);
    $page->lft = $lft;
    $lft++;
    foreach ($tree as $id => $subTree)
    {
      $subPage = Doctrine::getTable('pkContextCMSPage')->find($id);
      if (!$subPage)
      {
        echo("$id does not exist anymore, skipping\n");
      }
      $lft = $this->setSubtree($subPage, $subTree, $lft, $level + 1, $prefix . '  ');
      $last = $subPage;
    }
    $page->rgt = $lft;
    $page->level = $level;
    $page->save();
    echo(' '. $page->lft . "-" . $page->rgt . "\n");
    return $page->rgt + 1;
  }
}
