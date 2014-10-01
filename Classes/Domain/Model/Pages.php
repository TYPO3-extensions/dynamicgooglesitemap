<?php
namespace DieMedialen\Dynamicgooglesitemap\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Javor Issapov <javor.issapov@diemedialen.de>, Die Medialen GmbH
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package dynamicgooglesitemap
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Pages extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	
	/**
	 * uid
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $uid;
	
	/**
	 * pid
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $pid;
	
	/**
	 * sorting
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $sorting;
	
	/**
	 * title
	 * @var string
	 *
	 */
	protected $title;
	
	/**
	 * doktype
	 * @var integer
	 */
	protected $doktype;
	
	/**
	 * shortcutMode
	 * @var integer
	 */
	protected $shortcutMode;
	
	/**
	 * hidden
	 * @var integer
	 */
	protected $hidden;
	
	/**
	 * noSearch
	 * @var \integer
	 */
	protected $noSearch;
	
	/**
	 * navHide
	 * @var integer
	 */
	protected $navHide;
	
	/**
	 * feGroup
	 * @var integer
	 */
	protected $feGroup;
	
	/**
	 * isSiteroot
	 * @var integer
	 */
	protected $isSiteroot;
	
	/**
	 * Returns the pid
	 *
	 * @return int $pid
	 */
	public function getPid() {
		return $this->pid;
	}
	
	/**
	 * Returns the sorting
	 *
	 * @return int $sorting
	 */
	public function getSorting() {
		return $this->sorting;
	}
	
	/**
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * Returns the Doktime as Integer
	 * 
	 * @return integer $doktype
	 */
	public function getDoktype(){
		return $this->doktype;
	}
	
	/**
	 * @return integer $shortcutMode
	 */
	public function getShortcutMode(){
		return $this->shortcutMode;
	}
	
	/**
	 * @return integer $hidden
	 */
	public function getHidden(){
		return $this->hidden;
	}
	
	/**
	 * @param \integer $noSearch
	 */
	public function getNoSearch(){
		return $this->noSearch;
	}
	
	/**
	 * @return integer $navHide
	 */
	public function getNavHide(){
		return $this->navHide;
	}
	
	/**
	 * @return integer $feGroup
	 */
	public function getFeGroup(){
		return $this->feGroup;
	}
	
	/**
	 * @return integer $isSiteroot
	 */
	public function getIsSiteroot(){
		return $this->isSiteroot;
	}
	
	/**
	 * Returns the CSS Class for the page icon 
	 * depending on the page Doctype
	 *
	 * @return string $css
	 */
	public function getPageCssClass(){
		
		$doktype = array(
			1 => '-page-default',
			3 => '-page-shortcut-external',
			4 => '-page-shortcut',
			6 => '-page-backend-users',
			7 => '-page-mountpoint',
			199 => '-spacer',
			254 => '-folder-default',
			255 => '-page-recycler',
		);
		
		$suffix = '';
		
		if($this->isSiteroot) {
			if($this->doktype == 4 && $this->shortcutMode > 0) {
				$suffix = '-page-shortcut-root';
			} else {
				$suffix = '-page-domain';
			}
		} else {
			if($this->doktype == 1 && $this->navHide) {
				$suffix = '-page-not-in-menu';
			} else {
				$suffix = $doktype[$this->doktype];
			}
		} 
		
		$css = 't3-icon-pagetree' . $suffix;
		
		if($this->doktype != 1 && $this->navHide) {
			$css .= '-hideinmenu';
		}
		
		return $css;
	}
	
}

?>