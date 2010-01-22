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
 * Class 'Database' for the 'pmseitwert' extension.
 *
 * @author  Marco Ziesing <mz@puremedia-online.de>
 */

class tx_pmseitwert_db
{

  /**
   * Get list of URLs and API-Keys
   *
   */
  function get_urls($search_params = array()) {
    // search parameter to define the returning content
    $uid    = isset($search_params['uid'])    ? $this->quoteStr($search_params['uid']) : '';
    $url    = isset($search_params['url'])    ? $this->quoteStr($search_params['url']) : '';
    $apikey = isset($search_params['apikey']) ? $this->quoteStr($search_params['apikey']) : '';

    $tables = array(
        'conf' => 'tx_pmseitwert_conf'
    );

    $select = "
        DISTINCT
        `{$tables['conf']}`.`uid`,
        `{$tables['conf']}`.`url`,
        `{$tables['conf']}`.`apikey`,
        `{$tables['conf']}`.`days2keep`";

    $where = "1 ";

    if(strlen($uid))    $where .= "AND `{$tables['conf']}`.`uid`    = $uid";
    if(strlen($url))    $where .= "AND `{$tables['conf']}`.`url`    = $url";
    if(strlen($apikey)) $where .= "AND `{$tables['conf']}`.`apikey` = $apikey";

    $res = $this->exec_SELECTgetRows(array(
            'select'       => $select,
            'tables'       => $tables,
            'where'        => $where,
            'group_by'     => $group_by,
            'order_by'     => $order_by,
            'limit'        => $limit,
            'page'         => $page,
            'enablefields' => false,
            'debug'        => false
      )
    );

    return $res;
  }


  /**
   * Save data for URL
   *
   */
  function save_url($url='', $apikey='', $days2keep='') {
    $url       = $this->quoteStr($url);
    $apikey    = $this->quoteStr($apikey);
    $days2keep = $this->quoteStr($days2keep);

    if(strlen($apikey) == 32) {
      // insert in this table
      $table = 'tx_pmseitwert_conf';

      // fields and values
      $fields_values = array(
              'url'       => $url,
              'apikey'    => $apikey,
              'days2keep' => $days2keep
              );

      $res = $this->exec_INSERTquery(array(
              'table'         => $table,
              'fields_values' => $fields_values,
              'debug'         => false
              )
            );
    }

    return $res;
  }


  /**
   * Update data of URL
   *
   */
  function update_url($url_id='', $url='', $apikey='', $days2keep='') {
    $url_id    = $this->quoteStr($url_id);
    $url       = $this->quoteStr($url);
    $apikey    = $this->quoteStr($apikey);
    $days2keep = $this->quoteStr($days2keep);

    if(strlen($apikey) == 32) {
      // insert in this table
      $table = 'tx_pmseitwert_conf';

      $where = 'uid = ' . $url_id;

      // fields and values
      $fields_values = array(
              'url'       => $url,
              'apikey'    => $apikey,
              'days2keep' => $days2keep
              );

      $res = $this->exec_UPDATEquery(array(
              'table'         => $table,
              'where'         => $where,
              'fields_values' => $fields_values,
              'debug'         => false
              )
            );
    }

    return $res;
  }


  /**
   * Update data of URL
   *
   */
  function delete_url($url_id='') {
    $url_id    = $this->quoteStr($url_id);

    $table = 'tx_pmseitwert_conf';

    $where = "uid = $url_id";

    $res = $this->exec_DELETEquery(array(
              'table' => $table,
              'where' => $where,
              'debug' => false
              )
            );

    return $res;
  }


  /**
   * Get list of URLs and API-Keys
   *
   */
  function get_data($search_params = array(), $last = false) {
    // search parameter to define the returning content
    $uid      = isset($search_params['uid'])     ? $this->quoteStr($search_params['uid']) : '';
    $url_id   = isset($search_params['url_id'])  ? $this->quoteStr($search_params['url_id']) : '';
    $apikey   = isset($search_params['apikey'])  ? $this->quoteStr($search_params['apikey']) : '';

    $tables = array(
        'conf' => 'tx_pmseitwert_conf',
        'data' => 'tx_pmseitwert_data'
    );

    $select = "
        `{$tables['conf']}`.`url`,
        `{$tables['conf']}`.`apikey`,
        `{$tables['data']}`.`tstamp`,
        `{$tables['data']}`.`seitwert`,
        `{$tables['data']}`.`google`,
        `{$tables['data']}`.`alexa`,
        `{$tables['data']}`.`social`,
        `{$tables['data']}`.`technical`,
        `{$tables['data']}`.`yahoo`,
        `{$tables['data']}`.`other`,
        `{$tables['data']}`.`checktime`";

    $where = "`{$tables['conf']}`.`uid` = `{$tables['data']}`.`url_id` ";

    if(strlen($url_id)) $where .= "AND `{$tables['data']}`.`url_id` = $url_id";

    if($last == true) {
      $order_by = "`{$tables['data']}`.`uid` DESC ";
      $limit    = '1 ';
    }

    $res = $this->exec_SELECTgetRows(array(
            'select'       => $select,
            'tables'       => $tables,
            'where'        => $where,
            'group_by'     => $group_by,
            'order_by'     => $order_by,
            'limit'        => $limit,
            'page'         => $page,
            'enablefields' => false,
            'debug'        => false
      )
    );

    return $res;
  }



  /**
   * Executes SELECT and wraps the t3lib_DB::exec_SELECTgetRows
   *
   * $params is an associative array with the following keys:
   *  select:        string with the selected fields (mendatory)
   *  tables:        array with 'tableshort' => 'tablename' elements (mandatory)
   *  where:         string where clause
   *  order_by:      string order by statement
   *  group_by:      string group by statement
   *  limit:         int with limit (per page)
   *  page:          int active page (calculated from limit)
   *  enabledfields: boolean recognize enabledfields, default true
   *  debug:         boolean prints SQL debug information
   *
   * @param array $params
   * @return array Each element of the array represents one pager part. Can be "arrows" or "placeholder".
   */
  function exec_SELECTgetRows($params) {
    $select              = isset($params['select']) ? $params['select'] : '';
    $tables              = isset($params['tables']) ? $params['tables'] : '';
    $where               = isset($params['where']) ? $params['where'] : '1';
    $order_by            = isset($params['order_by']) ? $params['order_by'] : '';
    $group_by            = isset($params['group_by']) ? $params['group_by'] : '';
    $limit_param         = isset($params['limit']) ? $params['limit'] : '';
    $page_param          = isset($params['page']) ? $params['page'] : '';
    $enablefields        = isset($params['enablefields']) ? $params['enablefields'] : true;
    $debug               = isset($params['debug']) ? $params['debug'] : '';
    $ignore_enablefields = isset($params['ignore_enablefields']) ? $params['ignore_enablefields'] : array();

    if (empty($select) || empty($tables) || !is_array($tables))
    return false;

    // prepare the limit string for the SQL
    $limit = '';
    if (!empty($limit_param)) {
      $limit = $limit_param;
      if (!empty($page_param)) $limit = ($page_param * $limit_param - $limit_param) . ', ' . $limit_param;
    }

    // extend the where clause with enableFields per table
    $table_names = array_values($tables);
    $from = implode(',', $table_names);

    // debug output of the SQL statement
    if ($debug) {
      t3lib_div::debug($params, 'exec_SELECTgetRows Parameter');
      $sql = 'select ' . $select . ' from  ' . $from . ' where ' . $where;
      if (!empty($order_by)) $sql .= ' order by ' . $order_by;
      if (!empty($group_by)) $sql .= ' group by ' . $group_by;
      if (!empty($limit)) $sql .= ' limit ' . $limit;
      t3lib_div::debug($sql, 'SQL-Statement');
    }

    // make it typo3 safe
    if (true == $enablefields) {
      for ($i = 0; $i < count($tables); $i++) {
        if(!in_array($table_names[$i], $ignore_enablefields)) {
          $where .= ' ' . $this->cObj->enableFields($table_names[$i]);
        }
      }
    }

    // execute the select
    $res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
      $select,
      $from,
      $where,
      $group_by,
      $order_by,
      $limit
    );

    // debug output of the result set
    if ($debug)
    t3lib_div::debug($res, 'SQL Result');

    return $res;
  }


/**
 * Executes INSERT and wraps the t3lib_DB::exec_INSERTquery
 *
 * $params is an associative array with the following keys:
 *  table:           string   tablename (mandatory)
 *  fields_values:   array    fields and values
 *  no_quote_fields: string/array fields not to quote
 *  debug:           boolean  prints SQL debug information
 *
 * @param   array    $params
 * @return  boolean
 */
  function exec_INSERTquery($params) {
    $table         = isset($params['table']) ? $params['table'] : '';
    $fields_values = isset($params['fields_values']) ? $params['fields_values'] : '';
    $debug         = isset($params['debug']) ? $params['debug'] : '';

    if (empty($table) || !is_array($fields_values))
    return false;

    // debug output of the SQL statement
    if ($debug) {
      t3lib_div::debug($params, 'exec_INSERTquery Parameter');
      $sql = 'insert ' . $fields_values . ' into ' . $table;
      t3lib_div::debug($sql, 'SQL-Statement');
    }

    // execute the insert
    $res = $GLOBALS['TYPO3_DB']->exec_INSERTquery(
      $table,
      $fields_values
    );

    // debug output of the result set
    if ($debug)
    t3lib_div::debug($res, 'SQL Result');

    return $res;
  }


  /**
   * Executes UPDATE and wraps the t3lib_DB::exec_UPDATEquery
   *
   * $params is an associative array with the following keys:
   *  table: string   tablename (mandatory)
   *  fields_values: array fields and values
   *  where: string  filter
   *  debug: boolean prints SQL debug information
   *
   * @param   array    $params
   * @return  boolean
   */
  function exec_UPDATEquery($params) {
      $table           = isset($params['table']) ? $params['table'] : '';
      $where           = isset($params['where']) ? $params['where'] : '';
      $fields_values   = isset($params['fields_values']) ? $params['fields_values'] : '';
      $no_quote_fields = isset($params['no_quote_fields']) ? $params['no_quote_fields'] : '';
      $debug           = isset($params['debug']) ? $params['debug'] : '';

      if (empty($table) || empty($where) || !is_array($fields_values))
          return false;

      // debug output of the SQL statement
      if ($debug) {
          t3lib_div::debug($params, 'exec_UPDATEquery Parameter');
          $sql = 'update ' . $table . ' set ' . $fields_values . ' where ' . $where;
          t3lib_div::debug($sql, 'SQL-Statement');
      }

      // execute the insert
      $res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
          $table,
          $where,
          $fields_values,
          $no_quote_fields
      );

      // debug output of the result set
      if ($debug)
          t3lib_div::debug($res, 'SQL Result');

      return $res;
  }


  /**
   * Executes DELETE and wraps the t3lib_DB::exec_DELETEquery
   *
   * $params is an associative array with the following keys:
   *  table: string  tablename (mandatory)
   *  where: string  filter
   *  debug: boolean prints SQL debug information
   *
   * @param   array    $params
   * @return  boolean
   */
  function exec_DELETEquery($params) {
    $table = isset($params['table']) ? $params['table'] : '';
    $where = isset($params['where']) ? $params['where'] : '';
    $debug = isset($params['debug']) ? $params['debug'] : '';

    if (empty($table) || is_array($where))
    return false;

    // debug output of the SQL statement
    if ($debug) {
      t3lib_div::debug($params, 'exec_DELETEquery Parameter');
      $sql = "delete from $table where $where";
      t3lib_div::debug($sql, 'SQL-Statement');
    }

    // execute the insert
    $res = $GLOBALS['TYPO3_DB']->exec_DELETEquery(
      $table,
      $where
    );

    // debug output of the result set
    if ($debug)
    t3lib_div::debug($res, 'SQL Result');

    return $res;
  }


  /**
   * quote complete array - element by element
   *
   * @param array
   * @param string
   * @return array ready to use quoted array
   */
  function quoteArray($array, $table = '') {
    $newarray = array();
    if (is_array($array))
    for ($i = 0; $i < count($array); $i++) {
      $newarray[] = $GLOBALS['TYPO3_DB']->quoteStr($array[$i], $table);
    }
    return $newarray;
  }

  /**
   * quote string or make it empty
   *
   * @param mixed (but not array)
   * @param string
   * @return string ready to use quoted array
   */
  function quoteStr($string, $table = '') {
    $newstring = '';
    if (!is_array($string))
    $newstring = $GLOBALS['TYPO3_DB']->quoteStr($string, $table);
    return $newstring;
  }

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmseitwert/res/class.tx_pmseitwert_db.php'])    {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmseitwert/res/class.tx_pmseitwert_db.php']);
}

?>