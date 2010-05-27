<?php
/**
 * Ihush Dao
 *
 * @category   Ihush
 * @package    Ihush_Dao
 * @author     James.Huang <james@ihush.com>
 * @copyright  Copyright (c) iHush Technologies Inc. (http://www.ihush.com)
 * @version    $Id$
 */
 
require_once 'Ihush/Dao.php';

/**
 * @package Ihush_Dao
 */
class Ihush_Dao_App extends Ihush_Dao
{
	public function __construct ($db_type = 'READ')
	{
		// read database ini file and build pool
		$this->db_pool = Hush_Db::pool(__DB_INI_FILE_APP);
		$this->db = Hush_Db::rand($db_type);
		$this->db->query('set names utf8');
		
		// do some preparation in subclass
		parent::__construct();
	}
}