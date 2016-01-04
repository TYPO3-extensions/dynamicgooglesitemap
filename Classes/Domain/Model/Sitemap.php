<?php
namespace DieMedialen\Dynamicgooglesitemap\Domain\Model;

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
 * Sitemap
 *
 * @package dynamicgooglesitemap
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Sitemap extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * Tablename.
	 *
	 * @var string
	 */
	const TABLE = 'tx_dynamicgooglesitemap_domain_model_sitemap';

	/**
	 * The coresponding page ID
	 *
	 * @var int
	 */
	protected $forPage;

	/**
	 * The URL.
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $requestUri;

	/**
	 * The HTTP Host
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $httpHost;

	/**
	 * The Content Hash
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $contentHash;

	/**
	 * Priority of the url in context to the whole  sitemap.
	 *
	 * @var int
	 */
	protected $priority;

	/**
	 * Date when the content of the url was last changed.
	 *
	 * @var \DateTime
	 */
	protected $lastmod;

	/**
	 * $_GET Params for this URL
	 * 
	 * @var string
	 */
	protected $urlParams;

	/**
	 * sys_language_uid 
	 * 
	 * @var int
	 */
	protected $sysLanguageUid;

	/**
	 * Flag short name from language
	 * 
	 * @var string
	 */
	protected $sysLanguageFlag;

	/**
	 * Returns the forPage
	 *
	 * @return int $forPage
	 */
	public function getForPage() {
		return $this->forPage;
	}

	/**
	 * Sets the forPage
	 *
	 * @param int $forPage
	 *
	 * @return void
	 */
	public function setForPage($forPage) {
		$this->forPage = $forPage;
	}

	/**
	 * Returns the url
	 *
	 * @return string $requestUri
	 */
	public function getRequestUri() {
		return $this->requestUri;
	}

	/**
	 * Sets the url
	 *
	 * @param string $requestUri
	 *
	 * @return void
	 */
	public function setRequestUri($requestUri) {
		$this->requestUri = $requestUri;
	}

	/**
	 * Returns the HTTP Host
	 *
	 * @return string $httpHost
	 */
	public function getHttpHost() {
		return $this->httpHost;
	}

	/**
	 * Sets the HTTP Host
	 *
	 * @param string $httpHost
	 *
	 * @return void
	 */
	public function setHttpHost($httpHost) {
		$this->httpHost = $httpHost;
	}

	/**
	 * Returns the Content Hash
	 *
	 * @return string $contentHash
	 */
	public function getContentHash() {
		return $this->contentHash;
	}

	/**
	 * Sets the Content Hash
	 *
	 * @param string $contentHash
	 *
	 * @return void
	 */
	public function setContentHash($contentHash) {
		$this->contentHash = $contentHash;
	}

	/**
	 * Returns the priority
	 *
	 * @return int $priority
	 */
	public function getPriority() {
		return $this->priority;
	}

	/**
	 * Sets the priority
	 *
	 * @param int $priority
	 *
	 * @return void
	 */
	public function setPriority($priority) {
		$this->priority = $priority;
	}

	/**
	 * Returns the lastmod
	 *
	 * @return \DateTime $lastmod
	 */
	public function getLastmod() {
		return $this->lastmod;
	}

	/**
	 * Sets the lastmod
	 *
	 * @param \DateTime $lastmod
	 *
	 * @return void
	 */
	public function setLastmod($lastmod) {
		$this->lastmod = $lastmod;
	}

	/**
	 * Returns the URL Params
	 * 
	 * @return string
	 */
	public function getUrlParams(){
		return $this->urlParams;
	}

	/**
	 * Sets the URL Params
	 * 
	 * @param string $urlParams
	 *
	 * @return void
	 */
	public function setUrlParams($urlParams) {
		$this->urlParams = $urlParams;
	}

	/**
	 * Returns the sysLanguageUid
	 * 
	 * @return int
	 */
	public function getSysLanguageUid(){
		return $this->sysLanguageUid;
	}

	/**
	 * Sets the sysLanguageUid
	 * 
	 * @param int $sysLanguageUid
	 *
	 * @return void
	 */
	public function setSysLanguageUid($sysLanguageUid) {
		$this->sysLanguageUid = $sysLanguageUid;
	}

	/**
	 * Returns the sysLanguageFlag
	 * 
	 * @return string
	 */
	public function getSysLanguageFlag(){
		return $this->sysLanguageFlag;
	}

	/**
	 * Sets the sysLanguageFlag
	 * 
	 * @param string $sysLanguageFlag
	 *
	 * @return void
	 */
	public function setSysLanguageFlag($sysLanguageFlag) {
		$this->sysLanguageFlag = $sysLanguageFlag;
	}

	/**
	 * Returns a trimed string of the URL Params.
	 * 
	 * @return string
	 */
	public function getUrlParamsTrimed(){
		return \TYPO3\CMS\Core\Utility\GeneralUtility::fixed_lgd_cs($this->urlParams, 50);
	}

	/**
	 * Returns a trimed string of the Request URI.
	 * 
	 * @return string
	 */
	public function getRequestUriTrimed(){
		return \TYPO3\CMS\Core\Utility\GeneralUtility::fixed_lgd_cs($this->requestUri, 50);
	}
}
