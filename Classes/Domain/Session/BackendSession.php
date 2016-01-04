<?php
namespace DieMedialen\Dynamicgooglesitemap\Domain\Session;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Javor Issapov <javor.issapov@diemedialen.de>, Die Medialen GmbH
 *  (c) 2015 Patrick Schriner <patrick.schriner@diemedialen.de>, Die Medialen GmbH
 *  (c) 2016 Kai Ratzeburg <kai.ratzeburg@diemedialen.de>, Die Medialen GmbH
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
 * BackendSession
 *
 * @package dynamicgooglesitemap
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BackendSession extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	
	/**
	 * @var string
	 */
	protected $sessionKey = 'tx_dynamicgooglesitemap';
	
	/**
	 * @param string $sessionKey
	 * @return void
	 */
	public function setSessionKey($sessionKey) {
		$this->sessionKey = $sessionKey;
	}
	
	/**
	 * @return string
	 */
	public function getSessionKey() {
		return $this->sessionKey;
	}
	
	/**
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function save($key, $value) {
		$data = $GLOBALS['BE_USER']->getSessionData($this->sessionKey);
		$data[$key] = $value;
		$GLOBALS['BE_USER']->setAndSaveSessionData($this->sessionKey, $data);
	}
	
	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		$data = $GLOBALS['BE_USER']->getSessionData($this->sessionKey);
		if(isset($data[$key])){
			return $data[$key];
		}

		return NULL;
	}
	
	/**
	 * @param string $key
	 * @return void
	 */
	public function delete($key) {
		$data = $GLOBALS['BE_USER']->getSessionData($this->sessionKey);
		unset($data[$key]);
		$GLOBALS['BE_USER']->setAndSaveSessionData($this->sessionKey, $data);
	}
}
