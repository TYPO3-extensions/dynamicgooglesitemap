<?php
namespace DieMedialen\Dynamicgooglesitemap\Domain\Repository;

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
 * PagesRepository
 *
 * @package dynamicgooglesitemap
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @api
 */
class PagesRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * @param integer $pid
	 * @return TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
	 */
	public function findByPid($pid) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE)->setIgnoreEnableFields(TRUE);
		$query->matching($query->equals('pid', $pid));

		return $query->execute();
	}

	/**
	 * @param mixed $page
	 * @return multitype:
	 */
	function getSubpages($page) {
		$pagesArray = array();
		if(is_int($page)) {
			$currentPage = $this->findByUid($page);
		} else {
			$currentPage = $page;
		}

		$subpages = $this->findByPid($currentPage->getUid());
		$subpagesArray = array();

		foreach($subpages as $key => $subpage) {
			$subpagesArray[] = $this->getSubpages($subpage);
		}

		$pagesArray['page'] = $currentPage;
		$pagesArray['subpages'] = $subpagesArray;

		return $pagesArray; 
	}
}
