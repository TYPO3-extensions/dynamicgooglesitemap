<?php
namespace DieMedialen\Dynamicgooglesitemap\Utility;

class PageData {
	
	/**
	 * Table name where the Sitemap are stored.
	 * @var string
	 */
	private $table = 'tx_dynamicgooglesitemap_domain_model_sitemap';
	
	/**
	 * Gather all needed Data and Insert or Update into Database.
	 * 
	 * @param array $params
	 * @return void
	 */
	public function getData(&$params) {
		
		$tsConfig = $GLOBALS['TSFE']->tmpl->setup['config.'];
		$gpVars = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET();
		
		// Exit early if page is viewed in preview mode from TYPO3
		if($GLOBALS['TSFE']->fePreview || isset($gpVars['ADMCMD_view']) && isset($gpVars['ADMCMD_editIcons'])) { return; }
		
		// Exit early if page type is exluded from sitemap.
		$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dynamicgooglesitemap']);
		$ignoreTypes = explode(',', $confArray['ignorePageType']);
		if(isset($GLOBALS['TSFE']->type) && in_array($GLOBALS['TSFE']->type, $ignoreTypes)) { return; }
		
		$pageUid = intval($GLOBALS['TSFE']->page['uid']);
		$doktype = intval($GLOBALS['TSFE']->page['doktype']);
		$feAuth = intval($GLOBALS['TSFE']->page['fe_group']);
		$gpVars['id'] = $pageUid;
		$contentHash = md5($params['bodyContent']);
		
		$lang = 0;
		if(isset($gpVars['L'])){
			$lang = intval($gpVars['L']);
		}
		
		$langKey = 'x-default';
		if(
			!empty($tsConfig['sys_language_uid']) && 
			intval($tsConfig['sys_language_uid']) == $lang && 
			!empty($tsConfig['language'])
		) {
			$langKey = $tsConfig['language'];
		}
		
		// Ignore non-standard pages or pages where a login is required.
		// We don't want secured pages to appear on the sitemap.
		if(empty($pageUid) || $doktype != 1 || $feAuth != 0) {return;}
		
		$lastChanged = intval($GLOBALS['TSFE']->page['SYS_LASTCHANGED']);
		if($lastChanged == 0) {$lastChanged = time();}
		
		$httpHost = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('HTTP_HOST');
		$requestUri = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REQUEST_URI');
		$https = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SSL');
		
		$urlParams = $this->getGetParams($gpVars);

		$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow ('*', $this->table, 'http_host = \'' .  $httpHost . '\' AND for_page = ' . $pageUid . ' AND url_params = \'' . $urlParams . '\' AND sys_language_uid = \'' . $lang . '\' ');
		if($row == NULL) {
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
			$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->table, $insertArray);
		} else if($row['content_hash'] != $contentHash || $row['request_uri'] != $requestUri ) {

			$where_clause = 'for_page = ' . $pageUid. ' AND url_params = \'' . $urlParams . '\' AND sys_language_uid = ' . $tsConfig['sys_language_uid'];
			$field_values = array(
				'request_uri' => $requestUri,
				'lastmod' => $lastChanged,
				'https' => $https
			);
			$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery( $this->table, $where_clause, $field_values);
		}
		
	}
	
	/**
	 * 
	 * This function rebuilds the GET Parameters some of the parameters can be ignored. 
	 *  Configurable through the Extensions Configuration.
	 * 
	 * @param array $params
	 * @return string
	 */
	private function getGetParams($params){
		
		$confArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dynamicgooglesitemap']);
		$ignoreParams = explode(',', $confArray['ignoreParams']);
		$ignoreParams[] = 'L';
		
		ksort($params);
		$str = "";
		foreach ($params as $key => $value) {
			#if($key == 'id') {continue;}
			if(in_array($key,$ignoreParams)) {continue;}
			if(is_array($value)){
				ksort($value);
				foreach ($value as $k => $v) {
					if(!empty($v)){ $str .= '&' . $key . '['. $k .']='. $v; }
				}
			} else {
				if(!empty($value)){ $str .= '&' . $key . '=' . $value ; }
			}
		}
		return urldecode($str);
	}
	
}
?>