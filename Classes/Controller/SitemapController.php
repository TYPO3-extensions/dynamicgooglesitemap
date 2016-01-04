<?php
namespace DieMedialen\Dynamicgooglesitemap\Controller;

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
 * SitemapController
 *
 * @package dynamicgooglesitemap
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SitemapController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \DieMedialen\Dynamicgooglesitemap\Domain\Session\BackendSession
	 * @inject
	 */
	protected $backendSession;

	/**
	 * @var \DieMedialen\Dynamicgooglesitemap\Domain\Repository\SitemapRepository
	 * @inject
	 */
	protected $sitemapRepository;

	/**
	 * @var \DieMedialen\Dynamicgooglesitemap\Domain\Repository\PagesRepository
	 * @inject
	 */
	protected $pagesRepository;

	/**
	 * @var array
	 */
	protected $flags = array();

	/**
	 * action list
	 *
	 * @param string $domain
	 * @dontvalidate $domain
	 * 
	 * @return void
	 */
	public function listAction($domain = NULL) {
		$pid = \TYPO3\CMS\Core\Utility\MathUtility::convertToPositiveInteger($_GET['id']);

		if($pid == 0) {
			$this->view->assign('nopid', TRUE);
			$this->view->assign('nopid_msg', $this->translate('tx_dynamicgooglesitemap_domain_model_sitemap.nopage'));
		} else {
			if(empty($domain)) {
				$sessionDomain = $this->backendSession->get('domain');
				if(!empty($sessionDomain)){
					$domain = $sessionDomain;
				} else {
					$domain = $this->getDomain($pid);
					$this->backendSession->save('domain', $domain);
				}
			} else {
				$this->backendSession->save('domain', $domain);
			}

			$pages = $this->pagesRepository->getSubpages($pid);
			$pagesArray = $this->getSitemapForPages($pages, $domain);

			$this->view->assign('domain', $domain);
			$this->view->assign('domains', $this->getDomains());
			$this->view->assign('item', $pagesArray);
			$this->view->assign('nopid', FALSE);
		}
	}

	/**
	 * action edit
	 *
	 * @param \DieMedialen\Dynamicgooglesitemap\Domain\Model\Sitemap $sitemap
	 *
	 * @return void
	 */
	public function editAction(\DieMedialen\Dynamicgooglesitemap\Domain\Model\Sitemap $sitemap) {
		$this->view->assign('sitemap', $sitemap);
	}

	/**
	 * action update
	 *
	 * @param \DieMedialen\Dynamicgooglesitemap\Domain\Model\Sitemap $sitemap
	 *
	 * @return void
	 */
	public function updateAction(\DieMedialen\Dynamicgooglesitemap\Domain\Model\Sitemap $sitemap) {
		$this->sitemapRepository->update($sitemap);
		$this->addFlashMessage($this->translate('tx_dynamicgooglesitemap_domain_model_sitemap.sitemap.update'));
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param \DieMedialen\Dynamicgooglesitemap\Domain\Model\Sitemap $sitemap
	 *
	 * @return void
	 */
	public function deleteAction(\DieMedialen\Dynamicgooglesitemap\Domain\Model\Sitemap $sitemap) {
		$this->sitemapRepository->remove($sitemap);
		$this->addFlashMessage($this->translate('tx_dynamicgooglesitemap_domain_model_sitemap.sitemap.remove'));
		$this->redirect('list');
	}

	/**
	 * Delete All Sitemaps Action
	 * 
	 * @return void
	 */
	public function deleteAllAction(){
		$sessionDomain = $this->backendSession->get('domain');
		if(empty($sessionDomain)){
			$this->sitemapRepository->removeAll();
		} else {
			$this->sitemapRepository->removeAllByDomain($sessionDomain);
		}
		$this->addFlashMessage($this->translate('tx_dynamicgooglesitemap_domain_model_sitemap.sitemap.allremove'));
		$this->redirect('list');
	}

	/**
	 * Get All Sitemap Entrieys for pages.
	 * 
	 * @param array $pages
	 * @param string $domain
	 * @param integer $level
	 *
	 * @return array <boolean, multitype:Ambigous <> array >
	 */
	private function getSitemapForPages(array $pages, $domain, $level = 0) {
		$item = array();
		$sitemaps = $this->sitemapRepository->findForPage($pages['page']->getUid(), $domain);

		foreach($sitemaps as $sitemap) {
			$langId = $sitemap->getSysLanguageUid();
			if($langId  > 0) {
				if(empty($this->flags[$langId])){
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('flag', 'sys_language', 'uid = ' . $langId);
					$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					$this->flags[$langId] = $row['flag'];
				}
				$sitemap->setSysLanguageFlag($this->flags[$langId]);
			}
		}

		$item['page'] = $pages['page'];
		for ($i = 0; $i < $level; $i++) {
			$item['level'][] = TRUE;
		}
		if($sitemaps->count() > 0) {
			$item['sitemaps'] = $sitemaps; 
		}

		if(count($pages['subpages']) > 0) {
			++$level;
			foreach ($pages['subpages'] as $subpage) {
				$item['subpages'][] = $this->getSitemapForPages($subpage, $domain, $level);
			}
		}

		return $item;
	}

	/**
	 * Get Domain for current Page. 
	 * If there is no domain entry, return the PHP HTTP Host.
	 * 
	 * @param int $pid
	 *
	 * @return string
	 */
	private function getDomain($pid) {
		$rootLine = \TYPO3\CMS\Backend\Utility\BackendUtility::BEgetRootLine($pid, '', TRUE);
		$domainRecord = \TYPO3\CMS\Backend\Utility\BackendUtility::firstDomainrecord($rootLine);
		if (!empty($domainRecord)) {
			return $domainRecord;
		}

		return \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('HTTP_HOST');
	}

	/**
	 * Get Domain for current Page. 
	 * If there is no domain entry, return the PHP HTTP Host.
	 * 
	 * @return string
	 */
	private function getDomains(){
		$domains = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('domainName', 'sys_domain', 'redirectTo = "" AND hidden = 0');
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
			$domains[$row['domainName']] = $row['domainName'];
		}

		return $domains;
	}

	/**
	 * translation shortcut
	 *
	 * @param string $label
	 *
	 * @return Ambigous <string, NULL, string, unknown>
	 */
	private function translate($label) {
		return \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($label, 'dynamicgooglesitemap');
	}
}
