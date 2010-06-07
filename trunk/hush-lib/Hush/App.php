<?php
/**
 * Hush Framework
 *
 * @category   Hush
 * @package    Hush_App
 * @author     James.Huang <james@ihush.com>
 * @copyright  Copyright (c) iHush Technologies Inc. (http://www.ihush.com)
 * @version    $Id$
 */

/**
 * @see Hush_App_Dispatcher
 */
require_once 'Hush/App/Dispatcher.php';

/**
 * @see Hush_App_Mapper
 */
require_once 'Hush/App/Mapper.php';

/**
 * @see Hush_Util
 */
require_once 'Hush/Util.php';

/**
 * @package Hush_App
 */
class Hush_App
{
	/**
	 * App class dirs
	 * @var array
	 */
	private $_dirs = array();
	
	/**
	 * App mappings
	 * @var array
	 */
	private $_maps = array();
	
	/**
	 * Template dir
	 * @var string
	 */
	private $_tpls = '';
	
	/**
	 * Error page
	 * @var string
	 */
	private $_epage = '';
	
	/**
	 * Debug mode
	 * @var bool
	 */
	private $_debug = false;
	
	/**
	 * Set debug mode for display app's dispatch infomation
	 * @param bool $debug
	 * @return Hush_App
	 */
	public function setDebug ($debug = true)
	{
		$this->_debug = $debug;
		return $this;
	}
	
	/**
	 * Set App's error page (404 page)
	 * @param string $err_page (error page url)
	 * @return Hush_App
	 */
	public function setErrorPage ($err_page)
	{
		$this->_epage = $err_page;
		return $this;
	}
	
	/**
	 * Add App's classes dirs for dispatch
	 * @param string $dir
	 * @throws Hush_App_Exception
	 * @return Hush_App
	 */
	public function addAppDir ($dir) 
	{
		if (!is_dir($dir)) {
			require_once 'Hush/App/Exception.php';
			throw new Hush_App_Exception('Could not found app directory \'' . $dir . '\'');
		}
		$this->_dirs[] = $dir;
		return $this;
	}
	
	/**
	 * Get App's classes dirs
	 * @return array
	 */
	public function getAppDirs () 
	{
		return $this->_dirs;
	}
	
	/**
	 * Add App's router mapping files for dispatch
	 * @param string $map
	 * @throws Hush_App_Exception
	 * @return Hush_App
	 */
	public function addMapFile ($map) 
	{
		if (!is_file($map)) {
			require_once 'Hush/App/Exception.php';
			throw new Hush_App_Exception('Could not found map file \'' . $map . '\'');
		}
		$this->_maps[] = $map;
		return $this;
	}
	
	/**
	 * Get App's mapping files
	 * @return array
	 */
	public function getMapFiles () 
	{
		return $this->_maps;
	}
	
	/**
	 * Set App's template dir
	 * @param string $dir
	 * @throws Hush_App_Exception
	 * @return Hush_App
	 */
	public function setTplDir ($dir) 
	{
		if (!is_dir($dir)) {
			require_once 'Hush/App/Exception.php';
			throw new Hush_App_Exception('Could not found template directory \'' . $dir . '\'');
		}
		$this->_tpls = $dir;
		return $this;
	}
	
	/**
	 * Get App's template dirs
	 * @return array
	 */
	public function getTplDir () 
	{
		return $this->_tpls;
	}
	
	/**
	 * Start main router and dispatch process for App
	 * @see Hush_App_Dispatcher
	 * @throws Hush_App_Exception
	 * @return unknown
	 */
	public function run ()
	{
		if (!$this->getAppDirs()) {
			require_once 'Hush/App/Exception.php';
			throw new Hush_App_Exception('Please specify app directory first');
		}
		
		$dispatcher = new Hush_App_Dispatcher();
		
		// if open debug
		if ($this->_debug) {
			$dispatcher->setDebug(true);
		}
		
		// if set error page
		if ($this->_epage) {
			$dispatcher->setErrorPage($this->_epage);
		}
		
		// add page mappings if needed
		if ($this->getMapFiles()) {
			$mapper = new Hush_App_Mapper($this->getMapFiles());
			$dispatcher->setMapper($mapper);
		}

		// dispatch request to pages' actions
		$dispatcher->dispatch($this->getAppDirs(), $this->getTplDir());
	}
}