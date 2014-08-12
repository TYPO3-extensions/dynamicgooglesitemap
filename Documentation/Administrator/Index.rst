.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _admin-manual:

Administrator Manual
====================

Target group: **Administrators**

.. _admin-installation:

Installation
------------

|extension_name| is easy to implement. Just install and it is ready to go.


How it works
------------

This extension has three main sections: the backend module, a hook in the page renderer and a small renderer for the sitemap XML.
The backend module is designed for a nice overview of all sitemap entries, managing the priority and deleting unwanted or old entries.
The hook in the page renderer is the main code responsible for gathering all required information (page id, request parameters, request url, language and last changed). This information is then insert into the database.
The small XML renderer is implemented using the eID directive and renders the sitemap XML based on the data in the database.

.. _admin-configuration:

Configuration
-------------

The main configuration is handled on the Extension Manager Configuration page of the extension.

**Ignor Parameters** (``basic.ignorParams``)
   
   Since the extension identifies an entry by `pid`, `url params` and `language` it might be required to ignore some of the parameters. Parameters that are random or don't change the content of the page can be added here in a comma seperated list.

**Sort Sitemap by Field** (``basic.sorting``)
   
   Here you can choose how the sitemap should be sorted:
   
* ``UID`` : this is the uid of the page.
* ``PageTitle`` : Alphabetical by page title.
* ``URL`` : Alphabetical by URL.
* ``LastChanged`` : Sort from newest to oldest changed page.
   
.. important::

   If your site is multilingual: Make sure that your typoscript configuration is correct.
   |extension_name| uses the TypoScript configuration ``config.language = __`` to get the correct language key.
   If the language is not configured the sitemap will use ``x-default`` as language key.

Human readable URL to the Sitemap.
----------------------------------

If you want to rename the URL to the Sitemap you can use mod_rewrite in the .htaccess.

.. code-block::	htaccess

	RewriteRule sitemap.xml$ /index.php?eID=dynamicgooglesitemap [L,R=301]