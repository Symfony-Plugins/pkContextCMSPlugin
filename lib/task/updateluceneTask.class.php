<?php

class pkContextCMSupdateluceneTask extends sfBaseTask
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
    $this->name             = 'update-lucene';
    $this->briefDescription = 'update lucene indexes for recently modified pages';
    $this->detailedDescription = <<<EOF
The [pkContextCMS:update-lucene|INFO] task updates the Lucene search indexes for
recently modified pages in the CMS. You should call it from cron or another
scheduled task manager on a regular basis (for instance, every
five minutes).

Call it like this:

  [php /path/to/your/project/symfony pkContextCMS:update-lucene|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'] ? $options['connection'] : null)->getConnection();
    // PDO connection not so useful, get the doctrine one
    $conn = Doctrine_Manager::connection();
    $updates = $conn->getTable("pkContextCMSLuceneUpdate")->findAll();
    foreach ($updates as $update)
    {
      $page = pkContextCMSPageTable::retrieveByIdWithSlots($update->page_id, $update->culture);
      // Careful, pages die
      if ($page)
      {
        $page->updateLuceneIndex(); 
      }
      $update->delete();
    }
  }
}
