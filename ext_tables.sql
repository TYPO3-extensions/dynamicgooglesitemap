#
# Table structure for table 'tx_dynamicgooglesitemap_domain_model_sitemap'
#
CREATE TABLE tx_dynamicgooglesitemap_domain_model_sitemap (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	for_page int(11) DEFAULT '0' NOT NULL,
	url_params varchar(255) DEFAULT '' NOT NULL,
	http_host  varchar(255) DEFAULT '' NOT NULL,
	request_uri varchar(255) DEFAULT '' NOT NULL,
	content_hash varchar(32) DEFAULT '' NOT NULL,
	priority int(11) DEFAULT '5' NOT NULL,
	lastmod int(11) DEFAULT '0' NOT NULL,
	lang_key varchar(10) DEFAULT '' NOT NULL,
	https tinyint(4) unsigned DEFAULT '0' NOT NULL,

	t3_origuid int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY for_page (for_page,url_params),
	KEY parent (pid),
	KEY language (l10n_parent,sys_language_uid)
);
