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
 * SitemapRenderer
 *
 * @package dynamicgooglesitemap
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SitemapRenderer {

	/**
	 * Mapping the names form extension configuration (sorting) to actual table fields.
	 *
	 * @var array
	 */
	private $field = array(
		'UID' => 's.for_page',
		'PageTitle' => 'p.title',
		'URL' => 's.request_uri',
		'LastChanged' => 's.lastmod'
	);

	/**
	 * Render sitemap.
	 */
	public function main() {
		$this->initTSFE();
		$httpHost = GeneralUtility::getIndpEnv('HTTP_HOST');
		$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dynamicgooglesitemap']);
		$orderBy = $this->field[$confArray['sorting']];
		$respectNoSearch = (boolean) $confArray['respectNoSearch'];

		$respectRequestPath = '';
		if ((boolean)$confArray['respectRequestPath']) {
			$requestPath = preg_replace(
				'/\?.*/',
				'',
				str_replace('index.php', '', ltrim(GeneralUtility::getIndpEnv('REQUEST_URI'), '/'))
			);
			if (!empty($requestPath)) {
				$requestPath = $GLOBALS['TYPO3_DB']->quoteStr('/' . $requestPath, Sitemap::TABLE);
				$respectRequestPath = ' AND s.request_uri LIKE "' . $requestPath . '%"';
			}
		}

		$noSearchSql = '';
		if($respectNoSearch) {
			$noSearchSql = ' AND p.no_search = 0';
		}

		$time = time();
		$respectEnableFields = ' AND p.hidden=0 AND (p.starttime<=' . $time . ') AND (p.endtime=0 OR p.endtime>' . $time . ') AND p.deleted=0';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*', 
			'pages p, ' . Sitemap::TABLE . ' s',
			'p.uid = s.for_page AND s.http_host = "' . $httpHost . '"' . $respectRequestPath . $respectEnableFields . $noSearchSql,
			'', // Group By
			$orderBy
		);

		// collect all sitemap entryies in one array for easy rendering if site has more than one language.
		$sitemap = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$sitemap[$row['url_params']][$row['sys_language_uid']] = $row;
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		if (!headers_sent()) {
			header('Content-Type: application/xml; charset=utf-8');
		}

		echo '<?xml version="1.0" encoding="UTF-8"?>'. PHP_EOL;
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . PHP_EOL;

		foreach($sitemap as $page) {
			$date = new \DateTime();

			/**
			 * Google Sitemap needs a entry for each language with cross reference to all other languages
			 * For mor informations visit: 
			 *		https://support.google.com/webmasters/answer/189077
			 *		https://support.google.com/webmasters/answer/2620865
			 *
			 */
			if(count($page) > 1) {
				foreach($page as $lang) {
					$date->setTimestamp($lang['SYS_LASTCHANGED']);
					$prio = sprintf('%0.1F', intval($lang['priority']) / 10);
					$protocol = 'http';
					if($lang['https']){ $protocol .= 's'; }

					echo "\t" . '<url>' . PHP_EOL;
						echo "\t\t" . '<loc>' . $protocol . '://' . $lang['http_host'] . htmlentities($lang['request_uri']) . '</loc>' . PHP_EOL;
						foreach($page as $ref) {
							$protocolRef = 'http';
							if($ref['https']){ $protocolRef .= 's'; }
							echo "\t\t" . '<xhtml:link rel="alternate" hreflang="' . $ref['lang_key'] . '" href="' . $protocolRef . '://' . $ref['http_host'] . str_replace('&', '&amp;', str_replace('&amp;', '&', $ref['request_uri'])) . '" />' . PHP_EOL;
						}
						echo "\t\t" . '<lastmod>' . $date->format('Y-m-d') . '</lastmod>' . PHP_EOL;
						echo "\t\t" . '<changefreq>monthly</changefreq>' . PHP_EOL;
						echo "\t\t" . '<priority>' . $prio . '</priority>' . PHP_EOL;
					echo "\t" . '</url>' . PHP_EOL;
				}
			} else {
				foreach($page as $lang) {
					$date->setTimestamp($lang['SYS_LASTCHANGED']);
					$prio = sprintf('%0.1F', intval($lang['priority']) / 10);
					$protocol = 'http';
					if($lang['https']){ $protocol .= 's'; }
					
					echo "\t" . '<url>' . PHP_EOL;
						echo "\t\t" . '<loc>' . $protocol . '://' . $lang['http_host'] . htmlentities($lang['request_uri']) . '</loc>' . PHP_EOL;
						echo "\t\t" . '<lastmod>' . $date->format('Y-m-d') . '</lastmod>' . PHP_EOL;
						echo "\t\t" . '<changefreq>monthly</changefreq>' . PHP_EOL;
						echo "\t\t" . '<priority>' . $prio . '</priority>' . PHP_EOL;
					echo "\t" . '</url>' . PHP_EOL;
				}
			}
		}
		echo '</urlset>';
	}

	/**
	 * Init TSFE.
	 *
	 * Taken form http://typo3.org/documentation/snippets/sd/466/
	 */
	private function initTSFE() {
		/** @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $TSFE */
		$GLOBALS['TSFE'] = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController', $GLOBALS['TYPO3_CONF_VARS'], 0, 0);

		// Initialize Language
		\TYPO3\CMS\Frontend\Utility\EidUtility::initLanguage();
		\TYPO3\CMS\Frontend\Utility\EidUtility::initTCA();

		// Initialize FE User.
		$GLOBALS['TSFE']->initFEuser();

		// Important: no Cache for Ajax stuff
		$GLOBALS['TSFE']->set_no_cache();
		$GLOBALS['TSFE']->checkAlternativeIdMethods();

		$GLOBALS['TSFE']->determineId();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->getConfigArray();

		$GLOBALS['TSFE']->cObj = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
		$GLOBALS['TSFE']->settingLanguage();
		$GLOBALS['TSFE']->settingLocale();
	}
}

$sitemap = GeneralUtility::makeInstance('DieMedialen\Dynamicgooglesitemap\Utility\SitemapRenderer');
$sitemap->main();
