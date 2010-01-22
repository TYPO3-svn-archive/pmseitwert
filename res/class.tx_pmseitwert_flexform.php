<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2008 puremedia (info@puremedia-online.de)
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


/**
 * Class 'Flexform' for the 'pmseitwert' extension.
 *
 * @author  Marco Ziesing <mz@puremedia-online.de>
 */

class tx_pmseitwert_flexform
{

	var $prefixId      = 'tx_pmseitwert_flexform';		// Same as class name
	var $scriptRelPath = 'res/class.tx_pmseitwert_flexform.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'pmseitwert';	// The extension key.

  function select_domain ($config) {
    $this->db_obj = $this->getClass('db');

    $url_array = $this->db_obj->get_urls();

    $optionList = array();
    for($i=0; $i<count($url_array);$i++) {
      $optionList[$i] = array(0 => $url_array[$i]['url'], 1 => $url_array[$i]['uid']);
    }

    $config['items'] = array_merge($config['items'],$optionList);
    return $config;
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


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmseitwert/res/class.tx_pmseitwert_flexform.php'])    {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmseitwert/res/class.tx_pmseitwert_flexform.php']);
}

?>