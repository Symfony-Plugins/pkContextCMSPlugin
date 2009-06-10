<?php

if (isset($_SERVER['SERVER_NAME']))
{
  // Head folks off at the pass if they don't realize it's a command line tool
?>
<title>Command Line Installer</title>
<h1>Command Line Installer</h1>
<p>
  This installer is meant to be invoked at the command line. For example, if you are building a
  site with a MAMP test server on your Mac, you might launch the Terminal application, cd to the
  folder containing this script, and type:
  
  php installer.php
</p>
<?php
  exit(0);  
}
e("pkContextCMS Installer");
e("Copyright 2009 P'unk Avenue LLC");
e("www.apostrophenow.com");
e("This installer is released under the terms of the MIT License (see LICENSE).");
e("");

$stdin = fopen("php://stdin","r"); 

// Let them pick their Symfony

$symfonyPath = dirname(trim(outputOfCommand("which symfony")));
if (!$symfonyPath)
{
  $symfonyPath = false;
}

while (true)
{
  e("You must install symfony before running this script.");
  $symfonyPath = p("Where is the symfony command located on your system?", $symfonyPath);
  // Tolerate a full path to the script itself
  $symfonyPath = preg_replace("/\/symfony$/", "", $symfonyPath);
  if (checkVersion($symfonyPath))
  {
    break;
  }
}

e("This script can install pkContextCMS in two ways:");
e("1. By creating an entirely new Symfony project, preconfigured as a CMS
(a standalone CMS site). This is the right choice if you are not 
a Symfony developer.

2. By adding pkContextCMSPlugin and all of the related plugins to an 
existing Symfony project, and leaving the rest of the configuration 
to you (see the sample config files in plugins/pkContextCMSPlugin).
This is the right choice if you are a Symfony developer who wishes to 
use the CMS as part of a larger site.

If you answer yes to the following question, this script will prompt
you for additional information and create a new CMS site. If you
answer no, this script will just install the pkContextCMS plugin
and its related plugins (you should already be in a Symfony
project directory before doing this).
");
if (c("Do you want to create a standalone CMS site?"))
{
  standalone();
  
}
else
{
  netpbm();
  pkWritableFolder();
  plugins();
}

function standalone()
{
  global $symfonyPath;
  netpbm();
  $path = makeProject();
  pkWritableFolder();
  plugins();
  configuration();
  userClass();
  task("doctrine:build-all-reload");
  task("cc");
  e("\nYour site has been generated.\n");
  e("You will need to configure Apache to recognize $path/web");
  e("(note the /web at the end!) as the document root folder");
  e("for your new pkContextCMS site. Also, for best results,");
  e("you will want an Alias directive in your VirtualHost block");
  e("for this site which points to the location");
  e("of Symfony's /sf web assets folder on YOUR system:");
  e("");
  $aliasPath = "/path/to/symfony/data/web/sf";
  if (strpos($symfonyPath, "data/bin") !== false)
  {
    $aliasPath = str_replace("data/bin", "data/web/sf", $symfonyPath);
  }
  e("Alias /sf $aliasPath");
  e("");
  e("Thanks for installing pkContextCMS!");
}

function netpbm()
{ 
  global $netpbmPath;
  $pnmtopngPath = trim(outputOfCommand("which pnmtopng"));  
  $netpbmPath = false;
  if ($pnmtopngPath)
  {
    $netpbmPath = dirname($pnmtopngPath);
  }
  while (true)
  {
    e("You must install the netpbm utilities before running this script.");
    if (!$netpbmPath)
    {
      e("The netpbm utilities are image conversion tools that do not require any");
      e("extra memory even when opening large images. Many shared hosts already");
      e("have them. If you are administering a Linux system you can typically install");
      e("them with a command like:\n");
      e("yum install netpbm (for red hat derivatives)");
      e("apt-get install netpbm (for debian derivatives)\n");
      e("Mac/MAMP users who use fink can use:\n");
      e("fink install netpbm\n");
      e("If you haven't done this yet take care of it now in a separate");
      e("terminal window or restart the script.\n");
    }
    $netpbmPath = p("Where are the netpbm utilities located on your system?", $netpbmPath);
    if (!file_exists("$netpbmPath/pnmtopng"))
    {
      e("The pnmtopng utility was not found in the directory $netpbmPath");
      $netpbmPath = false;
    }
    else
    {
      break;
    }
  }
}

function makeProject()
{
  global $symfonyPath;
  while (true)
  {
    $parent = p("Enter the existing parent folder directory beneath which the new 
site's folder should be created. This is usually the parent folder of all web 
sites hosted on the system.

Example: /var/www/virtualhosts");
    if (!file_exists($parent))
    {
      e("$parent is not a valid, existing folder.");
    }
    break;
  }
  $name = p("Enter the name of the new project. This will be the name of the new 
subfolder created for it. There should not be any slashes, spaces or other 
unusual punctuation, only characters that are valid in a domain name.

Example: cmssite");
  $path = preg_replace("/\/$/", "", $parent) . "/" . $name;
  if (file_exists($path))
  {
    if (!c("WARNING: that folder already exists! Installing Symfony on top of an existing 
web site is not a good idea. Are you sure you want to do this?"))
    {
      exit(1);
    }
  }
  if (!ensureFolder($path, 0700))
  {
    die("Unable to create project folder, exiting.");
  }
  chdir($path);
  // Can't call task() because the ./symfony script doesn't exist yet.
  // Call the systemwide symfony script. TODO: let them indicate
  // where that script is manually. 
  system("$symfonyPath/symfony generate:project " . escapeshellarg($name), $result);
  if ($result != 0)
  {
    die("Unable to execute the symfony generate:project task. Did you install Symfony?");
  }
  task("generate:app frontend");
  // So far so easy, but now: the joy of doctrineization
  // Yes, this is right: the configuration class enables
  // everything EXCEPT Doctrine by default, and we're changing
  // that to enabling everything EXCEPT propel
  replaceInFile("config/ProjectConfiguration.class.php", "sfDoctrinePlugin", "sfPropelPlugin");
  $db = preg_replace("/[^\w]/", "_", $name);
  e("Please specify your database credentials. You should use a unique MySQL");
  e("database for this specific site. It is usually OK to accept the default for");
  e("the database server name. You should create the database FIRST, then answer");
  e("these prompts. Do not use \\ or ' characters in these fields.");
  $host = p("Database server name:", "localhost");
  $username = p("Database username:", "root");
  e("We don't recommend using root as your root SQL password on a production server!");
  e("However, it is common in test environments like MAMP, so we default to it here.");
  $password = p("Database password:", "root");
  $db = p("Database name:", $db);
  file_put_contents("config/databases.yml", 
"all:
  doctrine:
    class: sfDoctrineDatabase
    param:
      dsn: 'mysql:host=$host;dbname=$db'
      username: '$username'
      password: '$password'
");
  return $path;
}

function replaceInFile($file, $old, $new)
{
  $conf = file_get_contents($file);
  $conf = str_replace($old, $new, $conf);
  file_put_contents($file, $conf);
}

function configuration()
{
  global $netpbmPath;
  $configSources = "plugins/pkContextCMSPlugin/config";
  copy("$configSources/settings.yml.sample", "apps/frontend/config/settings.yml");
  copy("$configSources/app.yml.sample", "apps/frontend/config/app.yml");
  copy("$configSources/routing.yml.sample", "apps/frontend/config/routing.yml");
  replaceInFile("apps/frontend/config/app.yml", "# path: /opt/local/bin # typical netpbm location for macports", "path: \"$netpbmPath\"");
  e("By default, the CMS will have one user with total control over the site.");
  e("More sophisticated setups with many editors given control over specific parts");
  e("of the site are possible. See plugins/pkContextCMS/README for more information.");
  e("");
  e("The admin user's username will be: admin");
  e("Do not use \" marks in the password.");
  $password = p("Admin user's password?");
  file_put_contents("data/fixtures/fixtures.yml", "
sfGuardUser:
  sgu_admin:
    username:       admin
    password:       \"$password\"
    is_super_admin: true

sfGuardPermission:
  sgp_admin:
    name:           admin
    description:    Administrator permission

sfGuardGroup:
  sgg_admin:
    name:           admin
    description:    Administrator group
  sgg_editor:
    name:           editor
    description:    Editor group

sfGuardGroupPermission:
  sggp_admin:
    sfGuardGroup:       sgg_admin
    sfGuardPermission:  sgp_admin

sfGuardUserGroup:
  sgug_admin:
    sfGuardGroup:       sgg_admin
    sfGuardUser:        sgu_admin
");
}

function userClass()
{
  replaceInFile("apps/frontend/lib/myUser.class.php", "sfBasicSecurityUser", "sfGuardSecurityUser");
}

function plugins()
{
  $plugins = array(
    'pkToolkitPlugin' => array('stability' => 'beta'),
    'sfJqueryReloadedPlugin' => array(),
    'sfDoctrineGuardPlugin' => array(),
    'pkContextCMSPlugin' => array(
      'stability' => 'beta'),
    'pkContextCMSPlugin' => array('stability' => 'beta'),
    'sfDoctrineActAsTaggablePlugin' => array('stability' => 'beta'),
    'pkImageConverterPlugin' => array(),
    'pkMediaPlugin' => array('stability' => 'beta'),
    'pkMediaCMSSlotsPlugin' => array('stability' => 'beta'),
    'pkPersistentFileUploadPlugin' => array()
  );
  foreach ($plugins as $name => $options)
  {
    task("plugin:install", array($name), $options);
  }
  #
  ## Required for media support. Image format conversion with netpbm
  ## TODO: test for netpbm availability 
  #symfony plugin:install pkImageConverterPlugin
  #
  ## Required for media support. The media repository itself (which can be used separately from the CMS)
  #symfony plugin:install pkMediaPlugin --stability=beta
  #
  ## Required for media support. CMS slots for the media plugin
  #symfony plugin:install pkMediaCMSSlotsPlugin --stability=beta
  #
  ## Required for media support. Allows <input type="file"> widgets to persist just like normal widgets do
  ## through multiple passes of form validation
  #symfony plugin:install pkPersistentFileUploadPlugin  
}

function pkWritableFolder()
{
  ensureFolder('data/pk_writable', 0777); 
}

function ensureFolder($folder, $permissions)
{
  if (file_exists($folder))
  {
    return true;
  }  
  e("Creating the $folder folder");
  if (!mkdir($folder, $permissions, true))
  {
    e("WARNING: could not create $folder");
    return false;
  }
  return true;
}

function checkVersion($symfonyPath)
{
  $result = outputOfCommand("$symfonyPath/symfony --version");
  if (preg_match("/symfony version ([\d\.]+)/", $result, $matches))
  {
    $version = $matches[1];
    if ($version < 1.2)
    {
      e("The symfony script at $symfonyPath is only version $version. You need");
      e("at least version 1.2. Perhaps you have a newer version unpacked in a");
      e("different location. You can specify that location now.");
      return false;
    }
    e("Symfony version $version was found. Good.");
    return true;
  }
  else
  {
    e("There is no symfony script in the $symfonyPath folder, or it is not");
    e("responding correctly. Make sure you have at least Symfony 1.2.");
    return false;
  }
}

function task($task, $args = array(), $options = array())
{
  $cmd = "php symfony $task";
  foreach ($args as $arg)
  {
    $cmd .= " $arg";
  }
  foreach ($options as $key => $val)
  {
    $cmd .= " --$key=" . escapeshellarg($val);
  }
  e("Executing command: $cmd");
  system($cmd, $result);
  if ($result != 0)
  {
    e("WARNING: command was not successful");
  }
}

function e($m)
{
  echo($m . "\n");
}

function c($m)
{
  global $stdin;
  while (true)
  {
    echo("$m [Y or N]\n");
    $response = fgets($stdin);
    $response = trim($response);
    if (preg_match("/^[Yy]$/", $response))
    {
      return true;
    }
    if (preg_match("/^[Nn]$/", $response))
    {
      return false;
    }
    e("Please respond with Y or N.");
  }
}

function p($m, $d = false)
{
  global $stdin;
  while (true)
  {
    e($m);
    if ($d !== false)
    {
      e("[$d]");
    }
    $response = fgets($stdin);
    $response = trim($response);
    if (!strlen($response))
    {
      if ($d !== false)
      {
        return $d;
      }
      else
      {
        e("A response is required.");
      }
    }
    else
    {
      return $response;
    }
  }
}

function outputOfCommand($cmd)
{
  $in = popen($cmd, "r");
  $result = stream_get_contents($in);
  pclose($in);
  return $result;
}

