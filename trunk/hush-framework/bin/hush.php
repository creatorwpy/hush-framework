<?php
/**
 * Ihush Console
 *
 * @author     James.Huang <shagoo@gmail.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    $Id$
 */

require_once '../etc/global.config.php';

////////////////////////////////////////////////////////////////////////////////////////////////////
// Constants definition

define('__SQL_INI_BE', __ETC . '/database.ihush_acl.ini');
define('__SQL_INI_FE', __ETC . '/database.ihush_app.ini');
define('__SQL_INIT_BE', __ROOT . '/doc/sql/ihush_acl.sql');
define('__SQL_INIT_FE', __ROOT . '/doc/sql/ihush_app.sql');
define('__SQL_BACKUP_BE', __DAT_DIR . '/dbsql/database.ihush_acl.' . date('Y-m-d') . '.sql');
define('__SQL_BACKUP_FE', __DAT_DIR . '/dbsql/database.ihush_app.' . date('Y-m-d') . '.sql');

define('__SQL_IMPORT_TOOL', 'd:/xampp/mysql/bin/mysql.exe');
define('__SQL_DUMPER_TOOL', 'd:/xampp/mysql/bin/mysqldump.exe');
define('__SQL_IMPORT_COMMAND', __SQL_IMPORT_TOOL . ' {PARAMS} < {SQLFILE}');
define('__SQL_DUMPER_COMMAND', __SQL_DUMPER_TOOL . ' {PARAMS} --add-drop-database > {SQLFILE}');

////////////////////////////////////////////////////////////////////////////////////////////////////
// Class definition

class Hush_Console
{
	public function getDBParams ($db_ini_file)
	{
		$db_config = parse_ini_file($db_ini_file, true);
		
		if (!isset($db_config['WRITE'])) {
			die("\nDB config file error, please check 'WRITE' section '" . $db_ini_file . "' ...\n");
		}
		
		$params = ' -h' . $db_config['WRITE']['HOST']
				. ' -P' . $db_config['WRITE']['PORT']
				. ' -u' . $db_config['WRITE']['USER']
				. ' -p' . $db_config['WRITE']['PASS']
				. '   ' . $db_config['WRITE']['NAME'];
		
		return $params;
	}
	
	public function db ($params)
	{
		switch ($params) {
			case 'backup' :
				$command = __SQL_DUMPER_COMMAND;
				$command = str_replace('{PARAMS}', $this->getDBParams($this->ini_file), $command);
				$command = str_replace('{SQLFILE}', $this->bak_file, $command);
				echo "\nCOMMAND : $command\n";
				$result = system($command);
				echo "\nEXECUTE : " . $result . "\n";
				echo "\nDB backup successfully ...\n";
				break;
			case 'recover' :
				$command = __SQL_IMPORT_COMMAND;
				$command = str_replace('{PARAMS}', $this->getDBParams($this->ini_file), $command);
				$command = str_replace('{SQLFILE}', $this->bak_file, $command);
				echo "\nCOMMAND : $command\n";
				$result = system($command);
				echo "\nEXECUTE : " . $result . "\n";
				echo "\nDB recover successfully ...\n";
				break;
			default :
				self::noAction();
				break;
		}
	}
	
	public function tpl ($params)
	{
		require_once 'Ihush/App/Page.php';
		Ihush_App_Page::closeAutoLoad();
		$page = new Ihush_App_Page();
		$page->__prepare();
		switch ($params) {
			case 'clean' :
				$smarty = $page->getView()->getSmarty();
				echo "\nTPL DIR : " . $smarty->compile_dir . "\n";
				$smarty->clear_compiled_tpl();
				echo "\nAll compiled templates cleaned ...\n";
				break;
			case 'cleancache':
				$smarty = $page->getView()->getSmarty();
				echo "\nTPL DIR : " . $smarty->compile_dir . "\n";
				$smarty->clear_compiled_tpl();
				echo "\nAll template caches cleaned ...\n";
				break;
			default :
				self::noAction();
				break;
		}
	}
	
	public function cache ($params)
	{
		require_once 'Ihush/Cache.php';
		switch ($params) {
			case 'clean' :
				echo "\nCACHE DIR : " . __FILECACHE_DIR . "\n";
				Ihush_Cache::factory('File')->clean();
				echo "\nAll caches cleaned ...\n";
				break;
			default :
				self::noAction();
				break;
		}
	}
	
	public function noAction ()
	{
		echo "\nDo nothing for Backend ...\n";
	}
}

class Hush_Console_Backend extends Hush_Console
{
	public function __construct ()
	{
		$this->ini_file = __SQL_INI_BE;
		$this->bak_file = __SQL_BACKUP_BE;
	}
}

class Hush_Console_Frontend extends Hush_Console
{
	public function __construct ()
	{
		$this->ini_file = __SQL_INI_FE;
		$this->bak_file = __SQL_BACKUP_FE;
	}
}

////////////////////////////////////////////////////////////////////////////////////////////////////
// Main process

$classn	= isset($argv[1]) ? $argv[1] : null;
$method	= isset($argv[2]) ? $argv[2] : null;
$params	= isset($argv[3]) ? $argv[3] : null;

$classo	= strcasecmp($classn, 'fe') ? new Hush_Console_Backend() : new Hush_Console_Frontend();

if (strcasecmp($classn, 'fe')) {
	require_once '../etc/backend.config.php';
} else {
	require_once '../etc/frontend.config.php';
}

// init action
if (!strcasecmp($classn, 'init')) {
	
	echo 
<<<NOTICE

**********************************************************
* Start to initialize the Hush Framework                 *
**********************************************************

Please pay attention to this action !!!

Because you will :

1. Import original databases (Please make sure your current databases were already backuped).
2. Check all the runtime environment variables and directories.
3. Clean all caches and runtime data.

Are you sure to do all above things [Y/N] : 
NOTICE;
	
	$input = fgets(fopen("php://stdin", "r"));
	
	// start do init process
	if (!strcasecmp(trim($input), 'y')) {
		
		$be_command = __SQL_IMPORT_COMMAND;
		$be_command = str_replace('{PARAMS}', Hush_Console::getDBParams(__SQL_INI_BE), $be_command);
		$be_command = str_replace('{SQLFILE}', __SQL_INIT_BE, $be_command);
		echo "\nBACKEND SQL COMMAND : $be_command\n";
		
		$fe_command = __SQL_IMPORT_COMMAND;
		$fe_command = str_replace('{PARAMS}', Hush_Console::getDBParams(__SQL_INI_FE), $fe_command);
		$fe_command = str_replace('{SQLFILE}', __SQL_INIT_FE, $fe_command);
		echo "\nFRONTEND SQL COMMAND : $fe_command\n\n";
		
		system($be_command, $be_res);
		system($fe_command, $fe_res);
		
		if (!$be_res && !$be_res) {
			echo "DB init successfully ...\n";
		} else {
			exit;
		}
		
		$check_dirs = array(
			__DAT_DIR . '/cache',
			__DAT_DIR . '/dbsql',
			__TPL_DIR . '/cache',
			__TPL_DIR . '/template_c'
		);
		
		foreach ($check_dirs as $dir) {
			if (!is_dir($dir) || !is_writable($dir)) {
				mkdir($dir, 0777);
			}
			echo "\nCHECK DIR : $dir";
		}
		
		echo "\n\nAll dirs are ok ...\n";
	}
	
	echo 
<<<NOTICE

**********************************************************
* Initialize successfully                                *
**********************************************************

Thank you for using Hush Framework !!!

NOTICE;
	
	exit;
}

// be & fe actions
if (!method_exists($classo, $method)) {
	echo 
<<<USAGE

Usage: hush [ENVIRONMENT] [OPTIONS] <ACTIONS>

Enviornment:
    init   :   Initialize the framework first time
    fe     :   Frontend relavant operation
    be     :   Backend relavant operation

Options: 
    db     :   Database operation (be|fe)
    tpl    :   Template (Smarty) operation (be|fe)
    cache  :   Cache operation (be|fe)

Actions:
    db     >   [backup|recover|import]
    tpl    >   [clean|cleancache]
    cache  >   [clean]

Example:
    hush cache clean

    This example will pass parameters to hush.php 
    And execute relevant method to implement the specific tasks.
    If you want to check the logic, please see hush.php

USAGE;
	exit;
}

$classo->$method($params);
