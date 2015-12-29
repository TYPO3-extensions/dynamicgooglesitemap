<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {

	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'DieMedialen.' . $_EXTKEY,
		'web',	 // Make module a submodule of 'web'
		'pagelist',	// Submodule key
		'',						// Position
		array(
			'Sitemap' => 'list, edit, update, delete, deleteAll',
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_pagelist.xlf',
		)
	);

}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Google Sitemap');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_dynamicgooglesitemap_domain_model_sitemap', 'EXT:dynamicgooglesitemap/Resources/Private/Language/locallang_csh_tx_dynamicgooglesitemap_domain_model_sitemap.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_dynamicgooglesitemap_domain_model_sitemap');
$TCA['tx_dynamicgooglesitemap_domain_model_sitemap'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:dynamicgooglesitemap/Resources/Private/Language/locallang_db.xlf:tx_dynamicgooglesitemap_domain_model_sitemap',
		'label' => 'for_page',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'searchFields' => 'for_page,url,priority,lastmod,lastmod_hash,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Sitemap.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_dynamicgooglesitemap_domain_model_sitemap.gif'
	),
);

?>