<?php
/**
 * Ihush Dao
 *
 * @category   Ihush
 * @package    Ihush_Dao_Acl
 * @author     James.Huang <shagoo@gmail.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @version    $Id$
 */
 
require_once 'Ihush/Dao/Acl.php';
require_once 'Ihush/Dao/Acl/AppRole.php';
require_once 'Ihush/Dao/Acl/Role.php';

/**
 * @package Ihush_Dao_Acl
 */
class Acl_App extends Ihush_Dao_Acl
{
	/**
	 * @static
	 */
	const TABLE_NAME = 'app';
	
	/**
	 * Initialize
	 */
	public function __init () 
	{
		$this->t1 = self::TABLE_NAME;
		$this->t2 = Acl_Role::TABLE_NAME;
		$this->rsh = Acl_AppRole::TABLE_NAME;
		
		$this->__bind($this->t1);
	}
	
	/**
	 * Get all applications from track_app
	 * @return array
	 */
	public function getAllApps ($is_app = false)
	{
		$sql = $this->db->select()->from($this->t1, "*");
		
		if ($is_app) $sql->where("{$this->t1}.is_app = ?", 'YES');
		
		return $this->db->fetchAll($sql);
	}
	
	/**
	 * Get acl data from relative tables
	 * For app's acl controls
	 * @see Ihush_Acl_Backend
	 * @return array
	 */
	public function getAclApps ()
	{
		$sql = $this->db->select()
			->from($this->t1, array("{$this->t1}.path as path", "{$this->t2}.id as role"))
			->join($this->rsh, "{$this->t1}.id = {$this->rsh}.app_id", null)
			->join($this->t2, "{$this->t2}.id = {$this->rsh}.role_id", null);
		
		return $this->db->fetchAll($sql);
	}
	
	/**
	 * Get all app data from track_acl_app
	 * Only for backend acl tools
	 * @return array
	 */
	public function getAppList ()
	{
		$sql = $this->db->select()
			->from($this->t1, array("{$this->t1}.*", "group_concat({$this->t2}.name) as role"))
			->join($this->rsh, "{$this->t1}.id = {$this->rsh}.app_id", null)
			->join($this->t2, "{$this->t2}.id = {$this->rsh}.role_id", null)
			->group("{$this->t1}.id");
		
		return $this->db->fetchAll($sql);
	}
	
	/**
	 * Get all app tree's data from track_acl_app
	 * Only for backend acl tools
	 * @return array
	 */
	public function getAppTree ()
	{		
		return $this->getAppListByRole(null);
	}
	
	/**
	 * Get application list by role
	 * For getting menus for whole application
	 * @param int $role_id
	 * @return array
	 */
	public function getAppListByRole ($role_id)
	{
		$sql = $this->db->select()
			->from($this->t1, array("{$this->t1}.*", "group_concat({$this->t2}.name) as role"))
			->joinLeft($this->rsh, "{$this->t1}.id = {$this->rsh}.app_id", null)
			->joinLeft($this->t2, "{$this->t2}.id = {$this->rsh}.role_id", null);
		
		if ($role_id) {
			if (is_array($role_id)) {
				$sql->where("{$this->rsh}.role_id in (?)", $role_id);
			} else {
				$sql->where("{$this->rsh}.role_id = ?", $role_id);
			}
		}
		
		$sql->group("{$this->t1}.id")
			->order(array("{$this->t1}.pid", "{$this->t1}.order", "{$this->t1}.id"));
		
		$rawAppList = $this->db->fetchAll($sql);
		
		// build app tree
		require_once 'Hush/Tree.php';
		$tree = new Tree();
		foreach ($rawAppList as $app) {
			$tree->setNode($app['id'], $app['pid'], $app);
		}
		
		// get top list
		$topAppList = array();
		$topAppListIds = $tree->getChild(0);
		foreach ($topAppListIds as $id) {
			$topAppList[$id] = $tree->getValue($id);
		}
		
		// get all list
		$allAppList = $topAppList;
		foreach ($topAppListIds as $tid) {
			$groupList = array(); // group list
			$groupListIds = $tree->getChild($tid);
			foreach ($groupListIds as $gid) {
				$groupAppList = array(); // group app list
				$groupList[$gid] = $tree->getValue($gid);
				$appListIds = $tree->getChild($gid);
				foreach ($appListIds as $aid) {
					$groupAppList[$aid] = $tree->getValue($aid);
				}
				$groupList[$gid]['list'] = $groupAppList;
			}
			$allAppList[$tid]['list'] = $groupList;
		}
		
//		Hush_Util::dump($allAppList);
		
		return $allAppList;
	}
	
	/**
	 * Update all app role from track_app_role
	 * @param int $id App ID
	 * @param array $roles Role ID's array
	 * @return bool
	 */
	 public function updateRoles ($id, $roles = array())
	 {
	 	if ($id) {
			$this->db->delete($this->rsh, $this->db->quoteInto("app_id = ?", $id));
	 	} else {
	 		return false;
	 	}
	 	
		if ($roles) {
			$cols = array('app_id', 'role_id');
			$vals = array();
			foreach ((array) $roles as $role) {
				$vals[] = array($id, $role);
			}
			if ($cols && $vals) {
				$this->db->insertMultiRow($this->rsh, $cols, $vals);
				return true;
			}
		} else {
			return true;
		}
		
		return false;
	 }
}