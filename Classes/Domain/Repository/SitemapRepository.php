<?php
namespace DieMedialen\Dynamicgooglesitemap\Domain\Repository;
/***************************************************************
 *  Copyright notice
*
*  (c) 2010-2013 Extbase Team (http://forge.typo3.org/projects/typo3v4-mvc)
*  Extbase is a backport of TYPO3 Flow. All credits go to the TYPO3 Flow team.
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * A Frontend User repository
*
* @api
*/
class SitemapRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
	
	/**
	 * Set Default Query Settings
	 *
	 * @return void
	 */
	public function initializeObject() {
		/** @var $querySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
		$querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
		$querySettings->setRespectStoragePage(FALSE);
		$querySettings->setIgnoreEnableFields(FALSE);
		$querySettings->setRespectSysLanguage(FALSE);
		$this->setDefaultQuerySettings($querySettings);
		
		$this->setDefaultOrderings(array(
			'for_page' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
			'request_uri' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
		));
		
	}
	
	/**
	 * Returns all objects of this repository.
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array
	 * @api
	 */
	public function findAll() {
		$query = $this->createQuery();
		$result = $query->execute();
		return $result;
	}

	/**
	 * Finds an object matching the given identifier.
	 *
	 * @param integer $uid The identifier of the object to find
	 * @return object The matching object if found, otherwise NULL
	 * @api
	 */
	public function findByUid($uid) {
		
		$query = $this->createQuery();
		$result = $query->matching($query->equals('uid', $uid))->execute()->getFirst();
		
		return $result;
	}

	/**
	 * Finds an object matching the given identifier.
	 *
	 * @param integer $uid The identifier of the object to find
	 * @return object The matching object if found, otherwise NULL
	 * @api
	 */
	public function findByDomain($domain) {
		
		$query = $this->createQuery();
		$result = $query->matching($query->like('http_host', '%'.$domain))->execute();
		
		return $result;
	}
	
	/**
	 * Removes all objects for the given domain
	 *
	 * @return void
	 * @api
	 */
	public function removeAllByDomain($domain) {
		foreach ($this->findByDomain($domain) AS $object) {
			$this->remove($object);
		}
	}
	
	/**
	 * Return all Sitemap entrys for a given page and domain.
	 * 
	 * @param integer $uid the id of the page
	 * @param string $domain damain for filtering.
	 * @return object The matching object if found, otherwise NULL
	 * @api
	 */
	public function findForPage($uid,$domain) {

		$query = $this->createQuery();
		$query->matching(
			$query->logicalAnd(
				$query->equals('for_page', $uid),
				$query->like('http_host', '%'.$domain)
			)
		);
		$query->setOrderings(array(
			'sys_language_uid' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
		));
		
		return $query->execute();
	}
}

?>