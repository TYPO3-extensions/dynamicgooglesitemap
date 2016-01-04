<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (\TYPO3\CMS\Core\Utility\GeneralUtility::compat_version('7.6')) {
    $moduleIcon = 'EXT:' . $_EXTKEY . '/ext_icon.png';
} else {
    $moduleIcon = 'EXT:' . $_EXTKEY . '/ext_icon.gif';
}

if (TYPO3_MODE === 'BE') {
	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'DieMedialen.' . $_EXTKEY,
		'web',	 // Make module a submodule of 'web'
		'dynamicgooglesitemap',	// Submodule key
		'',						// Position
		array(
			'Sitemap' => 'list, edit, update, delete, deleteAll',
		),
		array(
			'access' => 'user,group',
			'icon'   => $moduleIcon,
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_pagelist.xlf',
		)
	);
}
unset($moduleIcon);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Google Sitemap');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_dynamicgooglesitemap_domain_model_sitemap', 'EXT:dynamicgooglesitemap/Resources/Private/Language/locallang_csh_tx_dynamicgooglesitemap_domain_model_sitemap.xlf');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_dynamicgooglesitemap_domain_model_sitemap');