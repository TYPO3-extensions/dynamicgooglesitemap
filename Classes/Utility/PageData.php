<?php
namespace DieMedialen\Dynamicgooglesitemap\Utility;

use DieMedialen\Dynamicgooglesitemap\Domain\Model\Sitemap;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * PageData
 *
 * @package dynamicgooglesitemap
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PageData {

	/**
	 * Gather all needed Data and Insert or Update into Database.
	 *
	 * @param array $params
	 *
	 * @return void
	 */
	public function getData(&$params) {
		$tsConfig = $GLOBALS['TSFE']->tmpl->setup['config.'];
		$gpVars = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET();

		// Exit early if page is viewed in preview mode from TYPO3
		if($GLOBALS['TSFE']->fePreview || isset($gpVars['ADMCMD_view']) && isset($gpVars['ADMCMD_editIcons'])) {
			return;
		}

		// Exit early if page type is exluded from sitemap.
		$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dynamicgooglesitemap']);
		$ignoreTypes = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $confArray['ignorePageType'], TRUE);
		
		if(isset($GLOBALS['TSFE']->type) && in_array($GLOBALS['TSFE']->type, $ignoreTypes)) { // Ignored types
			return;
		}
		if(isset($gpVars['M']) && $gpVars['M'] !== '') { // Module links
			return;
		} 

		$page = $GLOBALS['TSFE']->page;
		$pageUid = isset($page['uid']) ? intval($page['uid']) : 0;
		$doktype = isset($page['doktype']) ? intval($page['doktype']) : 0;
		$feAuth = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $page['fe_group'], TRUE);
		$gpVars['id'] = $pageUid;
		$contentHash = md5($params['bodyContent']);

		$lang = 0;
		if(isset($gpVars['L'])) {
			$lang = $GLOBALS['TYPO3_DB']->quoteStr(intval($gpVars['L']), Sitemap::TABLE);
		}

		$langKey = 'x-default';
		if(!empty($tsConfig['sys_language_uid']) &&
		   intval($tsConfig['sys_language_uid']) == $lang &&
		   !empty($tsConfig['language'])) {
			$langKey = $tsConfig['language'];
		}

		// Ignore non-standard pages or pages where a login is required.
		// We don't want secured pages to appear on the sitemap.
		if(empty($pageUid) || $doktype !== 1 || count($feAuth) > 0) {
			return;
		}
		$lastChanged = intval($GLOBALS['TSFE']->page['SYS_LASTCHANGED']);
		if(0 === $lastChanged) {
			$lastChanged = time();
		}

		$httpHost = $GLOBALS['TYPO3_DB']->quoteStr(GeneralUtility::getIndpEnv('HTTP_HOST'), Sitemap::TABLE);
		$requestUri = $GLOBALS['TYPO3_DB']->quoteStr(GeneralUtility::getIndpEnv('REQUEST_URI'), Sitemap::TABLE);
		$https = GeneralUtility::getIndpEnv('TYPO3_SSL');
		$urlParams = $GLOBALS['TYPO3_DB']->quoteStr($this->getGetParams($gpVars), Sitemap::TABLE);

		$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow (
			'*',
			Sitemap::TABLE,
			'http_host = "' .  $httpHost . '" AND for_page = ' . $pageUid . ' AND url_params = "' . $urlParams . '" AND sys_language_uid = "' . $lang . '"'
		);
		if(!$row || NULL === $row) {
			$insertArray = array(
					'for_page' => $pageUid,
					'url_params' => $urlParams,
					'http_host' => $httpHost,
					'request_uri' => $requestUri,
					'content_hash' => $contentHash,
					'lastmod' => $lastChanged,
					'sys_language_uid' => $lang,
					'lang_key' => $tsConfig['language'],
					'https' => $https
			);
			$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery(Sitemap::TABLE, $insertArray);
		} else if($row['content_hash'] !== $contentHash || $row['request_uri'] !== $requestUri) {
			$where_clause = 'for_page = ' . $pageUid. ' AND url_params = "' . $urlParams . '" AND sys_language_uid = "' . $tsConfig['sys_language_uid'] . '"';
			$field_values = array(
				'request_uri' => $requestUri,
				'lastmod' => $lastChanged,
				'https' => $https
			);
			$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(Sitemap::TABLE, $where_clause, $field_values);
		}
	}

	/**
	 * This function rebuilds the GET Parameters some of the parameters can be ignored.
	 * Configurable through the Extensions Configuration.
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	private function getGetParams($params){
		$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dynamicgooglesitemap']);
		$ignoreParams = GeneralUtility::trimExplode(',', $confArray['ignoreParams'], TRUE);

		ksort($params);
		$str = '';
		foreach ($params as $key => $value) {
			if(in_array($key, $ignoreParams)) {
				continue;
			}
			if(is_array($value)) {
				ksort($value);
				foreach ($value as $k => $v) {
					if(!empty($v)) {
						$str .= '&' . $key . '['. $k .']='. $v;
					}
				}
			} else {
				if(!empty($value)) {
					$str .= '&' . $key . '=' . $value;
				}
			}
		}

		return urldecode($str);
	}
}
