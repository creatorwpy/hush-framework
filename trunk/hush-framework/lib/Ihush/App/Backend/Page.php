<?php
/**
 * Ihush Page
 *
 * @category   Ihush
 * @package    Ihush_App_Backend
 * @author     James.Huang <james@ihush.com>
 * @copyright  Copyright (c) iHush Technologies Inc. (http://www.ihush.com)
 * @version    $Id$
 */
 
require_once 'Ihush/App/Backend.php';
require_once 'Ihush/Acl/Backend.php';
require_once 'Ihush/Dao/Acl.php';

/**
 * @package Ihush_App_Backend
 */
class Ihush_App_Backend_Page extends Ihush_App_Backend
{
	/**
	 * Do something before dispatch
	 * @see Hush_App_Dispatcher
	 */
	public function __init ()
	{
		// Super admin
		$this->view->_sa = $this->sa = defined('__ACL_SA') ? __ACL_SA : 'sa';
		
		// Auto load dao
		$this->dao->acl = new Ihush_Dao_Acl();
	}
	
	/**
	 * See if the user is logined
	 * @uses subclasses redirect to login page if user is not logined
	 * @return unknown
	 */
	public function authenticate ()
	{
		// check if login
		if (!$this->session('admin')) {
			$this->forward($this->root . 'auth/');
		}
		
		// set admin info object
		$this->view->_admin = $this->admin = $this->session('admin');
		
		// Setting acl control object
		$this->view->_acl = $this->acl = Ihush_Acl_Backend::getInstance();
		
		// check if this path is accessable
		$path = parse_url($_SERVER['REQUEST_URI']);
		if ($this->acl->has($path['path'])) {
			if (!$this->acl->isAllowed($this->admin['role'], $path['path'])) {
				$this->forward($this->root . 'common/');
			}
		}
		

	}
}