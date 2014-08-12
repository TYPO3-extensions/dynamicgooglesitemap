<?php
namespace DieMedialen\Dynamicgooglesitemap\Utility;

header('Content-Type: application/xml; charset=utf-8');

class SitemapRenderer {
	
	private $table = 'tx_dynamicgooglesitemap_domain_model_sitemap';
	
	// mapping the names form extension configuration (sorting) to actual table fields.
	private $field = array('UID' => 's.for_page', 'PageTitle' => 'p.title', 'URL' => 's.request_uri', 'LastChanged' => 's.lastmod' );
	
	function main() {
		
		$this->initTSFE();
		$httpHost = $_SERVER['HTTP_HOST'];
		$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dynamicgooglesitemap']);
		$orderBy = $this->field[$confArray['sorting']];
		
		$respectEnableFields = ' AND p.hidden=0 AND (p.starttime<=' . time() . ') AND (p.endtime=0 OR p.endtime>' . time() . ') AND p.deleted=0';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*', 
			'pages p, tx_dynamicgooglesitemap_domain_model_sitemap s', 
			'p.uid = s.for_page AND http_host = \'' . $httpHost . '\'' . $respectEnableFields,
			'', // Group By
			$orderBy
		);
		
		// collect all sitemap entryies in one array for easy rendering if site has more than one language.
		$sitemap = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
			$sitemap[$row['url_params']][$row['sys_language_uid']] = $row;
		}
		
		echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
		
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
					
					echo "\t" . '<url>' . "\n";
						echo "\t\t" . '<loc><![CDATA[http://' . $lang['http_host'] . $lang['request_uri'] . ']]></loc>' . "\n";
						foreach($page as $ref) {
							echo "\t\t" . '<xhtml:link rel="alternate" hreflang="' . $ref['lang_key'] . '" href="http://' . $ref['http_host'] . str_replace('&', '&amp;', str_replace('&amp;', '&', $ref['request_uri'])) . '" />' . "\n";
						}
						echo "\t\t" . '<lastmod>' . $date->format('Y-m-d') . '</lastmod>' . "\n";
						echo "\t\t" . '<changefreq>monthly</changefreq>' . "\n";
						echo "\t\t" . '<priority>' . $prio . '</priority>' . "\n";
					echo "\t" . '</url>' . "\n";
				}
				
			} else {
				foreach($page as $lang) {
					$date->setTimestamp($lang['SYS_LASTCHANGED']);
					$prio = sprintf('%0.1F', intval($lang['priority']) / 10);
					echo "\t" . '<url>' . "\n";
						echo "\t\t" . '<loc><![CDATA[http://' . $lang['http_host'] . $lang['request_uri'] . ']]></loc>' . "\n";
						echo "\t\t" . '<lastmod>' . $date->format('Y-m-d') . '</lastmod>' . "\n";
						echo "\t\t" . '<changefreq>monthly</changefreq>' . "\n";
						echo "\t\t" . '<priority>' . $prio . '</priority>' . "\n";
					echo "\t" . '</url>' . "\n";
				}
			}
		}
		echo '</urlset>';
	}
	
	/**
	 * Taken form http://typo3.org/documentation/snippets/sd/466/
	 */
	function initTSFE(){
		
		/** @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $TSFE */
		$TSFE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController', $GLOBALS['TYPO3_CONF_VARS'], 0, 0);
		 
		// Initialize Language
		\TYPO3\CMS\Frontend\Utility\EidUtility::initLanguage();
		 
		// Initialize FE User.
		$TSFE->initFEuser();
		 
		// Important: no Cache for Ajax stuff
		$TSFE->set_no_cache();
		$TSFE->checkAlternativeIdMethods();
		$TSFE->determineId();
		$TSFE->initTemplate();
		$TSFE->getConfigArray();
		
		// not instancieated??
		//\TYPO3\CMS\Core\Core\Bootstrap::getInstance()->loadCachedTca();
		$TSFE->cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
		$TSFE->settingLanguage();
		$TSFE->settingLocale();
	}
}

$sitemap = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('DieMedialen\Dynamicgooglesitemap\Utility\SitemapRenderer');
$sitemap->main();

?>