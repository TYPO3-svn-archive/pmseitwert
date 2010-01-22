<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Marco Ziesing <mz@puremedia-online.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
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

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Seitwert' for the 'pmseitwert' extension.
 *
 * @author	Marco Ziesing <mz@puremedia-online.de>
 * @package	TYPO3
 * @subpackage	tx_pmseitwert
 */
class tx_pmseitwert_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_pmseitwert_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_pmseitwert_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'pmseitwert';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf)	{
    $this->conf = $conf;
    $this->pi_setPiVarDefaults();
    $this->pi_loadLL();
    $this->pi_initPIflexForm();
    $this->lang = $GLOBALS["TSFE"]->sys_language_uid;
    $this->db_obj = $this->getClass('db');

    // flexform-Values
    $this->ffData = array(
            'domain'   => $this->pi_getFFvalue(
        $this->cObj->data['pi_flexform'],
                'domain'
      ),
            'templateFile'   => $this->pi_getFFvalue(
        $this->cObj->data['pi_flexform'],
                'templateFile'
      )
    );

    $data_array = $this->db_obj->get_data(array('url_id' => $this->ffData['domain']), true);

    $tmpl_wrap = $this->getTemplate('###TEMPLATE_SEITWERT###');

    $marker_array = array();
    $marker_array['###TITLE###']           = $this->pi_getLL('ratings_for') . ' ' . $data_array['0']['url'];
    $marker_array['###LABEL_SEITWERT###']  = $this->pi_getLL('rating_seitwert');
    $marker_array['###VALUE_SEITWERT###']  = $data_array['0']['seitwert'];
    $marker_array['###LABEL_GOOGLE###']    = $this->pi_getLL('rating_google');
    $marker_array['###VALUE_GOOGLE###']    = $data_array['0']['google'];
    $marker_array['###LABEL_ALEXA###']     = $this->pi_getLL('rating_alexa');
    $marker_array['###VALUE_ALEXA###']     = $data_array['0']['alexa'];
    $marker_array['###LABEL_SOCIAL###']    = $this->pi_getLL('rating_social');
    $marker_array['###VALUE_SOCIAL###']    = $data_array['0']['social'];
    $marker_array['###LABEL_TECHNICAL###'] = $this->pi_getLL('rating_technical');
    $marker_array['###VALUE_TECHNICAL###'] = $data_array['0']['technical'];
    $marker_array['###LABEL_YAHOO###']     = $this->pi_getLL('rating_yahoo');
    $marker_array['###VALUE_YAHOO###']     = $data_array['0']['yahoo'];
    $marker_array['###LABEL_OTHER###']     = $this->pi_getLL('rating_other');
    $marker_array['###VALUE_OTHER###']     = $data_array['0']['other'];

    $content = $this->cObj->substituteMarkerArrayCached($tmpl_wrap,$marker_array);
	
		return $this->pi_wrapInBaseClass($content);
	}


  /**
   * Substitutes marker
   *
   * @param  array  $singleRecord:
   * @return array  $markerArray: markers from html template
   */
  function getItemMarkerArray($singleRecord) {
    $markerArray = array();

    //local configuration and local cObj
    $lConf = $this->conf['templates.'][$this->conf['templateName'].'.'];
    $lcObj = t3lib_div::makeInstance('tslib_cObj');
    $lcObj->data = $singleRecord;

    return $markerArray;
  }


  /**
   * Get subpart from HTML template
   *
   * @param  string     $template_subPart: Which Subpart to fetch
   * @return string     $subPart: HTML template code
   */
  function getTemplate($template_subPart){
    if(!empty($this->ffData['templateFile'])){
      $templateFile = 'uploads/tx_' . $this->extKey . '/'.$this->ffData['templateFile'];
    } elseif (!empty($this->conf['templateFile'])){
      $templateFile = $this->conf['templateFile'];
    } else {
      die('Template Error!');
    }

    $templateCode = $this->cObj->fileResource($templateFile);

    // Subpart - z.B. ###TEMPLATE_SINGLERECORDS###
    $subPart = $this->cObj->getSubpart(
      $templateCode, $template_subPart
    );
    return $subPart;
  }


  function getClass($class, $extKey = '')
  {
    $extKey = (!empty($extKey)) ? $extKey : $this->extKey;
    require_once(t3lib_extMgm::extPath($extKey).'res/class.tx_'.$extKey.'_'.$class.'.php');
    $classHandle = 'tx_'.$extKey.'_'.$class;
    $class = new $classHandle;
    $class->extKey = $extKey;
    $class->cObj = $this->cObj;
    $class->prefixId = $this->prefixId;
    $class->conf = $this->conf;

    return $class;
  }

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmseitwert/pi1/class.tx_pmseitwert_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmseitwert/pi1/class.tx_pmseitwert_pi1.php']);
}

?>