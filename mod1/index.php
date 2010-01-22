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


// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');

$LANG->includeLLFile('EXT:pmseitwert/mod1/locallang.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);  // This checks permissions and exits if the users has no permission for entry.
// DEFAULT initialization of a module [END]



/**
 * Module 'Seitwert' for the 'pmseitwert' extension.
 *
 * @author  Marco Ziesing <mz@puremedia-online.de>
 * @package TYPO3
 * @subpackage  tx_pmseitwert
 */
class  tx_pmseitwert_module1 extends t3lib_SCbase
{

  var $prefixId      = 'tx_pmseitwert_module1';  // Same as class name
  var $scriptRelPath = 'mod1/index.php';  // Path to this script relative to the extension dir.
  var $extKey        = 'pmseitwert';  // The extension key.
  var $pageinfo;

  /**
   * Initializes the Module
   * @return  void
   */
  function init() {
    global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

    parent::init();

    /*
     if (t3lib_div::_GP('clear_all_cache'))  {
       $this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
     }
    */
  }

  /**
   * Adds items to the ->MOD_MENU array. Used for the function menu selector.
   *
   * @return  void
   */
  function menuConfig()   {
    global $LANG;
    $this->MOD_MENU = Array (
      'function' => Array (
        'stats'  => $LANG->getLL('stats'),
        'config' => $LANG->getLL('config'),
      )
    );
    parent::menuConfig();
  }

  /**
   * Main function of the module. Write the content to $this->content
   * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
   *
   * @return  [type]      ...
   */
  function main() {
    global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

    // Access check!
    // The page will show only if there is a valid page and if this page may be viewed by the user
    $this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
    $access = is_array($this->pageinfo) ? 1 : 0;

    $this->db_obj = $this->getClass('db');

    if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))  {

      // Draw the header.
      $this->doc = t3lib_div::makeInstance('mediumDoc');
      $this->doc->backPath = $BACK_PATH;
      $this->doc->form='<form action="" method="post">';
      $this->doc->styleSheetFile2 = '../'.substr(t3lib_extMgm::extPath('pmseitwert'),strlen(PATH_site)).'res/css/backend.css';

      // JavaScript
      $this->doc->JScode = '
        <script language="javascript" type="text/javascript">
          script_ended = 0;
          function jumpToUrl(URL) {
            document.location = URL;
          }
        </script>
        ';
      $this->doc->postCode='
        <script language="javascript" type="text/javascript">
          script_ended = 1;
          if (top.fsMod) top.fsMod.recentIds["web"] = 0;
        </script>
        ';

      $headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

      $this->content.=$this->doc->startPage($LANG->getLL('title'));
      $this->content.=$this->doc->header($LANG->getLL('title'));
      $this->content.=$this->doc->spacer(5);
      $this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
      $this->content.=$this->doc->divider(5);


      // Render content:
      $this->moduleContent();


      // ShortCut
      if ($BE_USER->mayMakeShortcut())    {
        $this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
      }

      $this->content.=$this->doc->spacer(10);
    } else {
      // If no access or if ID == zero

      $this->doc = t3lib_div::makeInstance('mediumDoc');
      $this->doc->backPath = $BACK_PATH;

      $this->content.=$this->doc->startPage($LANG->getLL('title'));
      $this->content.=$this->doc->header($LANG->getLL('title'));
      $this->content.=$this->doc->spacer(5);
      $this->content.=$this->doc->spacer(10);
    }
  }

  /**
   * Prints out the module HTML
   *
   * @return  void
   */
  function printContent() {
    $this->content.=$this->doc->endPage();
    echo $this->content;
  }

  /**
   * Generates the module content
   *
   * @return  void
   */
  function moduleContent() {
    global $BE_USER, $LANG;

    switch((string)$this->MOD_SETTINGS['function']) {
      case 'stats':

        $url_array = $this->db_obj->get_urls();

        if($url_array) {

          // show select if more than one domain is configured
          if(count($url_array) > 1) {
            $content = '<select name="url_id" onchange="jumpToUrl(\'index.php?&amp;id=0&amp;SET[function]=stats&amp;url_id=\'+this.options[this.selectedIndex].value,this);">
              <option>-- ' . $LANG->getLL('select_domain') . ' --</option>';
            for($i=0; $i<count($url_array); $i++) {
              $selected = ($url_array[$i]['uid'] == $_REQUEST['url_id']) ? 'selected="selected"' : '';
              $content .= '<option value="' . $url_array[$i]['uid'] . '" ' . $selected . '>' . $url_array[$i]['url'] . '</option>';
            }
            $content .= '</select>';
          }

          // get configuration values
          if(strlen($_REQUEST['url_id'])) {
            $item_array = $this->db_obj->get_urls(array('uid' => $_REQUEST['url_id']));
            $url_id = $item_array['0']['uid'];
            $url    = $item_array['0']['url'];
            $apikey = $item_array['0']['apikey'];
          } else {
            $url_id = $url_array['0']['uid'];
            $url    = $url_array['0']['url'];
            $apikey = $url_array['0']['apikey'];
          }

          // get last values for domain
          $values_array = $this->db_obj->get_data(array('url_id' => $url_id));

          $last_entry   = count($values_array)-1;


          // get new xml data from seitwert.de if possible
          if(($values_array[$last_entry]['tstamp'] + 86400) <= mktime() &&
             ($values_array[$last_entry]['checktime'] + 86400) <= mktime()) {

            #$file = '../res/example.xml';
            $file = 'http://www.seitwert.de/api/getseitwert.php?url=' . $url . '&api=' . $apikey;
            $handle = fopen($file, 'r');
            $xml = fread($handle, 8192);

            // parse xml result
            $xml_parser = xml_parser_create();
            xml_parse_into_struct($xml_parser, $xml, $index, $vals);
            xml_parser_free($xml_parser);

            if($index['0']['tag'] != 'ERROR') {
              $data_array              = array();
              $data_array['url']       = $index['1']['value'];
              $data_array['seitwert']  = $index['2']['value'];
              $data_array['google']    = $index['3']['value'];
              $data_array['alexa']     = $index['4']['value'];
              $data_array['social']    = $index['5']['value'];
              $data_array['technical'] = $index['6']['value'];
              $data_array['yahoo']     = $index['7']['value'];
              $data_array['other']     = $index['8']['value'];
              $data_array['checktime'] = $index['9']['value'];

              // save data
              $table = 'tx_pmseitwert_data';
              $fields_values = array(
                    'tstamp'    => mktime(),
                    'crdate'    => mktime(),
                    'cruser_id' => $BE_USER->user['uid'],
                    'url_id'    => $url_id,
                    'seitwert'  => $data_array['seitwert'],
                    'google'    => $data_array['google'],
                    'alexa'     => $data_array['alexa'],
                    'social'    => $data_array['social'],
                    'technical' => $data_array['technical'],
                    'yahoo'     => $data_array['yahoo'],
                    'other'     => $data_array['other'],
                    'checktime' => $data_array['checktime']
              );
              fclose($handle);

              $res = $GLOBALS['TYPO3_DB']->exec_INSERTquery($table, $fields_values);

              if($res) {
                $content .= '<p><strong>' . $LANG->getLL('new_data') . '</strong></p>';
                $values_array = $this->db_obj->get_data(array('url_id' => $url_id));
              } else {
                $content .= '<p><strong>' . $LANG->getLL('new_data_error') . '</strong></p>';
              }

            } else {
              $content .= '<p><strong>' . $LANG->getLL('error') . ': ' .  $index['2']['value'] . '</strong></p>';
            }

          } else {
            $data_array              = array();
            $data_array['url']       = $values_array[$last_entry]['url'];
            $data_array['seitwert']  = $values_array[$last_entry]['seitwert'];
            $data_array['google']    = $values_array[$last_entry]['google'];
            $data_array['alexa']     = $values_array[$last_entry]['alexa'];
            $data_array['social']    = $values_array[$last_entry]['social'];
            $data_array['technical'] = $values_array[$last_entry]['technical'];
            $data_array['yahoo']     = $values_array[$last_entry]['yahoo'];
            $data_array['other']     = $values_array[$last_entry]['other'];
            $data_array['checktime'] = $values_array[$last_entry]['checktime'];
          }

          $content .= '<h2>' . $LANG->getLL('last_ratings') . ' ' . $data_array['url'] . '</h2>
                  <table>
                    <tr>
                      <td>' . $LANG->getLL('rating_seitwert') . '</td>
                      <td align="right" style="width:50px;">' . $data_array['seitwert'] . '</td>
                    </tr>
                    <tr>
                      <td>' . $LANG->getLL('rating_google') . '</td>
                      <td align="right">' . $data_array['google'] . '</td>
                    </tr>
                    <tr>
                      <td>' . $LANG->getLL('rating_alexa') . '</td>
                      <td align="right">' . $data_array['alexa'] . '</td>
                    </tr>
                    <tr>
                      <td>' . $LANG->getLL('rating_social') . '</td>
                      <td align="right">' . $data_array['social'] . '</td>
                    </tr>
                    <tr>
                      <td>' . $LANG->getLL('rating_technical') . '</td>
                      <td align="right">' . $data_array['technical'] . '</td>
                    </tr>
                    <tr>
                      <td>' . $LANG->getLL('rating_yahoo') . '</td>
                      <td align="right">' . $data_array['yahoo'] . '</td>
                    </tr>
                    <tr>
                      <td>' . $LANG->getLL('rating_other') . '</td>
                      <td align="right">' . $data_array['other'] . '</td>
                    </tr>
                  ';

          $content .= '</table><br>';


          // draw chart if more than one dataset available
          if(count($values_array) > 1) {
            include "../res/libchart/classes/libchart.php";

            $chart     = new LineChart(700, 250);
            $seitwert  = new XYDataSet();
            $google    = new XYDataSet();
            $alexa     = new XYDataSet();
            $social    = new XYDataSet();
            $technical = new XYDataSet();
            $yahoo     = new XYDataSet();
            $other     = new XYDataSet();

            for($i=0; $i<count($values_array); $i++) {
              $tstamp = date("d.m.Y", $values_array[$i]['checktime']);

              $seitwert->addPoint(new Point($tstamp, $values_array[$i]['seitwert']));
              $google->addPoint(new Point($tstamp, $values_array[$i]['google']));
              $alexa->addPoint(new Point($tstamp, $values_array[$i]['alexa']));
              $social->addPoint(new Point($tstamp, $values_array[$i]['social']));
              $technical->addPoint(new Point($tstamp, $values_array[$i]['technical']));
              $yahoo->addPoint(new Point($tstamp, $values_array[$i]['yahoo']));
              $other->addPoint(new Point($tstamp, $values_array[$i]['other']));
            }

            $dataSet = new XYSeriesDataSet();
            $dataSet->addSerie($LANG->getLL('rating_seitwert'), $seitwert);
            $dataSet->addSerie($LANG->getLL('rating_google'), $google);
            $dataSet->addSerie($LANG->getLL('rating_alexa'), $alexa);
            $dataSet->addSerie($LANG->getLL('rating_social'), $social);
            $dataSet->addSerie($LANG->getLL('rating_technical'), $technical);
            $dataSet->addSerie($LANG->getLL('rating_yahoo'), $yahoo);
            $chart->setDataSet($dataSet);

            $chart->setTitle($LANG->getLL('ratings') . ' ' . $data_array['url']);
            $chart->getPlot()->setGraphCaptionRatio(0.62);
            $chart->render("../res/chart.png");
            $content .= '<h2>' . $LANG->getLL('title_chart') . '</h2>
              <img src="../res/chart.png" />';
          }

        } else {
          $content = '<p><strong>' . $LANG->getLL('no_config') . '</strong></p>
                      <p><a href="index.php?&amp;id=0&amp;SET[function]=config">' . $LANG->getLL('config') . '</a></p>';
        }

        $this->content.=$this->doc->section('Stats:',$content,0,1);
      break;

      case 'config':
        $url_array = $this->db_obj->get_urls();

        if($_REQUEST['save']) {
          $regexp =  '/^([a-z0-9]([-a-z0-9]*[a-z0-9])?\.)+((a[cdefgilmnoqrstuwxz]|aero|arpa)|(b[abdefghijmnorstvwyz]|biz)|(c[acdfghiklmnorsuvxyz]|cat|com|coop)|d[ejkmoz]|(e[ceghrstu]|edu)|f[ijkmor]|(g[abdefghilmnpqrstuwy]|gov)|h[kmnrtu]|(i[delmnoqrst]|info|int)|(j[emop]|jobs)|k[eghimnprwyz]|l[abcikrstuvy]|(m[acdghklmnopqrstuvwxyz]|mil|mobi|museum)|(n[acefgilopruz]|name|net)|(om|org)|(p[aefghklmnrstwy]|pro)|qa|r[eouw]|s[abcdeghijklmnortvyz]|(t[cdfghjklmnoprtvwz]|travel)|u[agkmsyz]|v[aceginu]|w[fs]|y[etu]|z[amw])$/i';

          if(!preg_match($regexp, $_REQUEST['url'])) $error = '<p>' . $LANG->getLL('invalid_domain') . '</p>';
          if(strlen($_REQUEST['apikey']) != '32') $error .= '<p>' . $LANG->getLL('invalid_apikey') . '</p>';

          if(!$error) {
            if($_POST['url_id']) {
              $res = $this->db_obj->update_url($_REQUEST['url_id'], $_REQUEST['url'], $_REQUEST['apikey'], $_REQUEST['days2keep']);
            } else {
              $res = $this->db_obj->save_url($_REQUEST['url'], $_REQUEST['apikey'], $_REQUEST['days2keep']);
            }
          } else {
            $content .= $error;
          }
        }

        if($_REQUEST['delete']) {
          $res = $this->db_obj->delete_url($_REQUEST['url_id']);

          if($res) {
            $content .= '<p>' . $LANG->getLL('delete_success') . '</p>';
          } else {
            $content .= '<p>' . $LANG->getLL('delete_error') . '</p>';
          }
        }

        if($_REQUEST['save'] || $_REQUEST['delete']) {
          $url_array = $this->db_obj->get_urls();
        }

        // show select if configured domain(s) available
        if(count($url_array)) {
          $content .= $this->get_html_select($url_array);
        }

        if((int) $_REQUEST['url_id']) {
          $res = $this->db_obj->get_urls(array('uid' => $_REQUEST['url_id']));
          $content .= '<a href="index.php?&amp;id=0&amp;SET[function]=config&amp;delete=1&amp;url_id=' . $_REQUEST['url_id'] . '" class="button">' . $LANG->getLL('delete_entry') . '</a>';
        }

        $content .= '
          <input type="hidden" name="url_id" value="' . $res['0']['uid'] . '">
          <label for="url">Domain/Hostname:</label>
          <input type="text" name="url" id="url" size="60" value="' . $res['0']['url'] . '">
          <label for="apikey">' . $LANG->getLL('apikey') . ':</label>
          <input type="text" name="apikey" id="apikey" size="32" value="' . $res['0']['apikey'] . '">
          <!--
          <label for="days2keep">' . $LANG->getLL('hold_back_time') . ':</label>
          <input type="text" name="days2keep" id="days2keep" maxlength="3" size="3" value="' . $res['0']['days2keep'] . '"> ' . $LANG->getLL('days') . '
          -->
          <input type="submit" name="save" value="' . $LANG->getLL('save') . '">
        ';

        $this->content.=$this->doc->section('Configuration:',$content,0,1);
      break;
    }
  }


  function get_html_select($url_array = array()) {
    global $LANG;

    $html_select = '<select name="url_id" onchange="jumpToUrl(\'index.php?&amp;id=0&amp;SET[function]=config&amp;url_id=\'+this.options[this.selectedIndex].value,this);">
            <option>-- ' . $LANG->getLL('select_domain') . ' --</option>';
    for($i=0; $i<count($url_array); $i++) {
      $selected = ($url_array[$i]['uid'] == $_REQUEST['url_id']) ? 'selected="selected"' : '';
      $html_select .= '<option value="' . $url_array[$i]['uid'] . '" ' . $selected . '>' . $url_array[$i]['url'] . '</option>';
    }
    $html_select .= '</select>';

    return $html_select;
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



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmseitwert/mod1/index.php'])    {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmseitwert/mod1/index.php']);
}


// Make instance:
$SOBE = t3lib_div::makeInstance('tx_pmseitwert_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)   include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>