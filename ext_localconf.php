<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['dynamicgooglesitemap'] = 'EXT:dynamicgooglesitemap/Classes/Utility/SitemapRenderer.php';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess']['get_page_data'] = 'EXT:dynamicgooglesitemap/Classes/Utility/PageData.php:DieMedialen\\Dynamicgooglesitemap\\Utility\\PageData->getData';
