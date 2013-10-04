<?php
/**
 * @(#) db.php
 */

class DB_Result {

  var $_db = NULL;     # handle to the db object
  var $_result = NULL; # handle to the result resource

  var $_rows = NULL;
  var $_cols = NULL;

  //  var $_info = array('name' => array(), 'type' => array());
  var $_info = NULL;

  /**
   * create a new result object
   */
  function DB_Result($db, $result) {
    $this->_db = $db;
    $this->_result = $result;
  }

  /**
   * get the number of rows in this result set
   */
  function rows() {
    if (is_null($this->_rows)) {
      $this->_rows = $this->_db->_num_rows($this->_result);
    }

    return $this->_rows;
  } // rows()

  /**
   * get the number of columns in this result set
   */
  function cols() {
    if (is_null($this->_cols)) {
      $this->_cols = $this->_db->_num_cols($this->_result);
    }

    return $this->_cols;
  } // cols()

  /**
   * get the column information for this result set
   */
  function info() {
    if (is_null($this->_info)) {
      $this->_info = $this->_db->_field_info($this->_result);
    }

    return $this->_info;
  } // info()

  /**
   * reset the pointer to the beginning of this result set
   */
  function reset() {
    return $this->_db->_reset($this->_result);
  } // reset()

  /**
   * free any data associated with this result set
   */
  function free() {
    $this->_rows = NULL;
    $this->_cols = NULL;
    $this->_info = NULL;
    $return = $this->_db->_free_result($this->_result);
    $this->_result = NULL;

    return $return;
  } // free()

  /**
   * fetch the next row in this result set as an enumerated array
   */
  function fetch_row() {
    return $this->_db->_fetch_row($this->_result);
  } // fetch_row()

  /**
   * fetch the next row in this result set as an associated array
   */
  function fetch_assoc() {
    return $this->_db->_fetch_assoc($this->_result);
  } // fetch_assoc()

  /**
   * fetch the next row in this result set as both an associated and
   * enumerated array
   */
  function fetch_array() {
    return $this->_db->_fetch_array($this->_result);
  } // fetch_array()
} // DB_Result


class DB {

  /* public variables */
  var $DB_MYSQL = 'mysql';
  var $handle = NULL;

  /* private variables */
  var $_type;
  var $_query = '';
  var $_connected = false;

  /**
   * Connect to the given database
   *
   * @return true on successful connect; false otherwise
   */
  function connect($type = '', $host = '', $user = '', $pass = '', $database = '') {

    $this->_query = '';

    if ($this->is_connected()) {
      $this->disconnect();
    }

    if ($this->is_connected()) {
      return false;
    }

    $this->_type = $type     ? $type     : $GLOBALS['_SYS']['db']['type'];
    $host        = $host     ? $host     : $GLOBALS['_SYS']['db']['host'];
    $user        = $user     ? $user     : $GLOBALS['_SYS']['db']['user'];
    $pass        = $pass     ? $pass     : $GLOBALS['_SYS']['db']['pass'];
    $database    = $database ? $database : $GLOBALS['_SYS']['db']['database'];

    switch ($this->_type) {
    case $this->DB_MYSQL:
      $this->handle = mysql_connect($host, $user, $pass);

      if ($this->handle && mysql_select_db($database, $this->handle)) {
        $this->_connected = true;
      }

      break;

    default:
      break;
    }

    return $this->is_connected();
  } // connect(type, host, user, pass, database)

  /**
   * Disconnect from the given database
   *
   * @return true on successful disconnect; false otherwise
   */
  function disconnect() {

    $this->_query = '';

    if (!$this->is_connected()) {
      return false;
    }

    switch ($this->_type) {
    case $this->DB_MYSQL:
      $this->_connected = !mysql_close($this->handle);
      break;

    default:
      break;
    }

    if (!$this->is_connected()) {
      $this->_type = NULL;
      $this->handle = NULL;
    }

    return (!$this->is_connected());
  } // disconnect()

  /**
   * @return if currently connected to database
   */
  function is_connected() {

    return $this->_connected;
  } // is_connected()

  /**
   *
   */
  function query($query) {

    $this->_query = '';

    if (!$this->is_connected()) {
      return false;
    }

    $this->_query = $query;

    switch ($this->_type) {
    case $this->DB_MYSQL:
      $res = mysql_query($this->_query, $this->handle);

      if (is_resource($res)) {
        $result = new DB_Result($this, $res);
      } else {
        $result = $res;
      }

      break;

    default:
      break;
    }

    return $result;
  } // query()

  function insert_id() {
    switch($this->_type) {
    case $this->DB_MYSQL:
      return intval(mysql_insert_id($this->handle));
      break;

    default:
      return NULL;
      break;
    }
  } // last_insert_id()

  /**
   *
   */
  function error() {

    $error = '';

    if (!$this->is_connected()) {
      return $error;
    }

    switch ($this->_type) {
    case $this->DB_MYSQL:
      $error .= '<b>Error #'.mysql_errno($this->handle).':</b> '.mysql_error($this->handle);
      break;

    default:
      break;
    }

    if ($this->_query) {
      $error .= '<br /><b>Query:</b>'."\n".'<pre>'.$this->_query.'</pre>';
    }

    return $error;
  } // error()

  /**
  * Escape the input string for use in a SQL Query
  *
  * @param $input unescaped input string
  *
  * @return escaped string
  */
  function escape_string($input) {
    switch ($this->_type) {
      case $this->DB_MYSQL:
        $output = $input;

        if (get_magic_quotes_gpc()) {
          $output = stripslashes($input);
        }

        if (!is_numeric($output)) {
          $output = '"'.mysql_real_escape_string($output).'"';
        }
        break;

    default:
      break;
  }

  return $output;
  } // escape_string($text)

  /**
   * get the number of rows in this result set
   */
  function affected_rows() {
    switch($this->_type) {
    case $this->DB_MYSQL:
      return mysql_affected_rows($this->handle);
      break;

    default:
      return NULL;
      break;
    }
  } // rows($result)




  /**************************************************************************/
  /********* functions strictly for use within the result-set class *********/
  /**************************************************************************/

  /**
   * get the number of rows in this result set
   */
  function _num_rows($result) {
    switch($this->_type) {
    case $this->DB_MYSQL:
      return mysql_num_rows($result);
      break;

    default:
      return NULL;
      break;
    }
  } // rows($result)

  /**
   * get the number of columns in this result set
   */
  function _num_cols($result) {
    switch($this->_type) {
    case $this->DB_MYSQL:
      return mysql_num_fields($result);
      break;

    default:
      return NULL;
      break;
    }
  } // cols($result)

  /**
   * get the column information for this result set
   */
  function _field_info($result) {
    switch($this->_type) {
    case $this->DB_MYSQL:
      $info = array('name' => array(), 'type' => array());
      $cols = $this->_num_cols($result);

      for ($i = 0; $i < $cols; ++$i) {
        $field = mysql_fetch_field($result);
        $info['name'][$i] = $field->name;
        $info['type'][$i] = $field->type;
      }

      return $info;
      break;

    default:
      return NULL;
      break;
    }
  } // info($result)

  /**
   * reset the pointer to the beginning of this result set
   */
  function _reset($result) {
    switch($this->_type) {
    case $this->DB_MYSQL:
      return mysql_data_seek($result, 0);
      break;

    default:
      return NULL;
      break;
    }
  } // reset($result)

  /**
   * free any data associated with this result set
   */
  function _free_result($result) {
    switch($this->_type) {
    case $this->DB_MYSQL:
      return mysql_free_result($result);
      break;

    default:
      return NULL;
      break;
    }
  } // free($result)

  /**
   * fetch the next row in this result set as an enumerated array
   */
  function _fetch_row($result) {
    switch($this->_type) {
    case $this->DB_MYSQL:
      return mysql_fetch_row($result);
      break;

    default:
      return NULL;
      break;
    }
  } // fetch_row($result)

  /**
   * fetch the next row in this result set as an associated array
   */
  function _fetch_assoc($result) {
    switch($this->_type) {
    case $this->DB_MYSQL:
      return mysql_fetch_assoc($result);
      break;

    default:
      return NULL;
      break;
    }
  } // fetch_assoc($result)

  /**
   * fetch the next row in this result set as both an associated and
   * enumerated array
   */
  function _fetch_array($result) {
    switch($this->_type) {
    case $this->DB_MYSQL:
      return mysql_fetch_array($result);
      break;

    default:
      return NULL;
      break;
    }
  } // fetch_array($result)

} // DB
?>
