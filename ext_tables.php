<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

include_once(t3lib_extMgm::extPath($_EXTKEY).'res/class.tx_pmseitwert_flexform.php');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages,recursive';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY .'_pi1', 'FILE:EXT:pmseitwert/pi1/flexform.xml');


t3lib_extMgm::addPlugin(array('LLL:EXT:pmseitwert/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY, 'pi1/static/', 'Seitwert');


if (TYPO3_MODE == 'BE')	{
		
	t3lib_extMgm::addModule('web','txpmseitwertM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
}

$TCA['tx_pmseitwert_conf'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:pmseitwert/locallang_db.xml:tx_pmseitwert_conf',		
		'label'     => 'uid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY crdate",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_pmseitwert_conf.gif',
	),
);

$TCA['tx_pmseitwert_data'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:pmseitwert/locallang_db.xml:tx_pmseitwert_data',		
		'label'     => 'uid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY crdate",	
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_pmseitwert_data.gif',
	),
);
?>