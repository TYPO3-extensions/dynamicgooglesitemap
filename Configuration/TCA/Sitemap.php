<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_dynamicgooglesitemap_domain_model_sitemap'] = array(
	'ctrl' => $TCA['tx_dynamicgooglesitemap_domain_model_sitemap']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, for_page, http_host, request_uri, content_hash, url_params, priority, lastmod, lang_key',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, for_page, http_host, request_uri, content_hash,  url_params, priority, lastmod, lang_key'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_dynamicgooglesitemap_domain_model_sitemap',
				'foreign_table_where' => 'AND tx_dynamicgooglesitemap_domain_model_sitemap.pid=###CURRENT_PID### AND tx_dynamicgooglesitemap_domain_model_sitemap.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
		'for_page' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dynamicgooglesitemap/Resources/Private/Language/locallang_db.xlf:tx_dynamicgooglesitemap_domain_model_sitemap.for_page',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			),
		),
		'http_host' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dynamicgooglesitemap/Resources/Private/Language/locallang_db.xlf:tx_dynamicgooglesitemap_domain_model_sitemap.url',
			'config' => array(
				'type' => 'input',
				'size' => 255,
				'eval' => 'trim,required'
			),
		),
		'request_uri' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dynamicgooglesitemap/Resources/Private/Language/locallang_db.xlf:tx_dynamicgooglesitemap_domain_model_sitemap.url',
			'config' => array(
				'type' => 'input',
				'size' => 255,
				'eval' => 'trim,required'
			),
		),
		'content_hash' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dynamicgooglesitemap/Resources/Private/Language/locallang_db.xlf:tx_dynamicgooglesitemap_domain_model_sitemap.url',
			'config' => array(
				'type' => 'input',
				'size' => 32,
				'eval' => 'trim,required'
			),
		),
		'lang_key' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dynamicgooglesitemap/Resources/Private/Language/locallang_db.xlf:tx_dynamicgooglesitemap_domain_model_sitemap.url',
			'config' => array(
				'type' => 'input',
				'size' => 10,
				'eval' => 'trim,required'
			),
		),
		'url_params' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dynamicgooglesitemap/Resources/Private/Language/locallang_db.xlf:tx_dynamicgooglesitemap_domain_model_sitemap.url_params',
			'config' => array(
				'type' => 'input',
				'size' => 255,
				'eval' => 'trim'
			),
		),
		'priority' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dynamicgooglesitemap/Resources/Private/Language/locallang_db.xlf:tx_dynamicgooglesitemap_domain_model_sitemap.priority',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			),
		),
		'lastmod' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dynamicgooglesitemap/Resources/Private/Language/locallang_db.xlf:tx_dynamicgooglesitemap_domain_model_sitemap.lastmod',
			'config' => array(
				'type' => 'input',
				'size' => 7,
				'eval' => 'date',
				'checkbox' => 1,
				'default' => time()
			),
		),
	),
);

?>