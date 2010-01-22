<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_pmseitwert_conf'] = array (
	'ctrl' => $TCA['tx_pmseitwert_conf']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,apikey'
	),
	'feInterface' => $TCA['tx_pmseitwert_conf']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'apikey' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pmseitwert/locallang_db.xml:tx_pmseitwert_conf.apikey',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, apikey')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);



$TCA['tx_pmseitwert_data'] = array (
	'ctrl' => $TCA['tx_pmseitwert_data']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'url,seitwert,google,alexa,social,technical,yahoo,other,checktime'
	),
	'feInterface' => $TCA['tx_pmseitwert_data']['feInterface'],
	'columns' => array (
		'url' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pmseitwert/locallang_db.xml:tx_pmseitwert_data.url',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'seitwert' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pmseitwert/locallang_db.xml:tx_pmseitwert_data.seitwert',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'google' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pmseitwert/locallang_db.xml:tx_pmseitwert_data.google',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'alexa' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pmseitwert/locallang_db.xml:tx_pmseitwert_data.alexa',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'social' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pmseitwert/locallang_db.xml:tx_pmseitwert_data.social',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'technical' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pmseitwert/locallang_db.xml:tx_pmseitwert_data.technical',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'yahoo' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pmseitwert/locallang_db.xml:tx_pmseitwert_data.yahoo',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'other' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pmseitwert/locallang_db.xml:tx_pmseitwert_data.other',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'checktime' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:pmseitwert/locallang_db.xml:tx_pmseitwert_data.checktime',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'url;;;;1-1-1, seitwert, google, alexa, social, technical, yahoo, other, checktime')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>