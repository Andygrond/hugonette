<?php

namespace Andygrond\Hugonette;

/** Central store of environment attributes for Hugonette
 * name of array attribute should be given in the form of "keys.dotted"
 * attrib value set from inside of Route group will be valid for that group only
 * hidden attribute name - prepended with "hidden." - can be get/set but will not be restored()
 * once set hidden value persists - even if set from inside of Route group
 * @author Andygrond 2020
 */

class Env
{
  // regular environment attributes
  private static $attrib = [];
  // hidden environment attributes
  private static $hidden = [];

  /** set initial values of attributes - can be set only once
  * @param $sysDir framework base directory
  * @param $filename initial attributes file path; standard values are provided if not set
  */
  public static function init(string $sysDir, string $filename = null)
  {
    if (!self::$attrib) {
      self::$attrib = require($filename?? __DIR__ .DIRECTORY_SEPARATOR .'Data' .DIRECTORY_SEPARATOR .'env.php');
      self::$hidden = self::$attrib['hidden'];
      unset(self::$attrib['hidden']);
    }
    // path to framework
    self::$attrib['base']['system'] = $sysDir;
  }

  /** get attribute value
  * @param $attrName attribute name - when not set get all but hidden
  * @return mixed value of variable
  */
  public static function get(string $attrName = null)
  {
    return $attrName? self::getAttrValue($attrName) : self::$attrib;
  }

  /** set attribute value
  * @param $attrName attribute name
  * @param $attrValue value to be set
  */
  public static function set(string $attrName, $attrValue)
  {
    $attr =& self::findAttr($attrName);
    $attr = $attrValue;
  }

  /** concatenate string argument with an attribute
  * @param $prepended value to be prepended
  * @param $attrName attribute name
  */
  public static function prepend(string $prepended, string $attrName)
  {
    $attr =& self::findAttr($attrName);
    if (is_string($attr)) {
      $attr = $prepended .$attr;
    }
  }

  /** concatenate attribute with a string argument
  * @param $attrName attribute name
  * @param $appended value to be appended
  */
  public static function append(string $attrName, string $appended)
  {
    $attr =& self::findAttr($attrName);
    if (is_string($attr)) {
      $attr .= $appended;
    }
  }

  /** add the last element od an array
  * @param $attrName attribute name
  * @param $pushed variable to be pushed
  */
  public static function push(string $attrName, string $pushed)
  {
    $attr =& self::findAttr($attrName);
    if (is_array($attr)) {
      $attr[] = $pushed;
    }
  }

  /** replace full set of attributes
  * call is not effective outside Route class
  * @param $attrib full set of attributes (all but hidden)
  */
  public static function restore(array $attrib)
  {
    if (debug_backtrace()[1]['class'] == 'Andygrond\\Hugonette\\Route') {
      self::$attrib = $attrib;
    }
  }

  /** find attribute element by reference
  * @param $attrName attribute name
  */
  private static function &findAttr(string $attrName)
  {
    $attrParts = explode('.', $attrName);
    if ($attrParts[0] == 'hidden') {
      $attr =& self::$hidden;
      array_shift($attrParts);
    } else {
      $attr =& self::$attrib;
    }

    foreach ($attrParts as $name) {
      if (!array_key_exists($name, $attr)) {
        $attr[$name] = [];
      }
      $attr =& $attr[$name];
    }
    return $attr;
  }

  /** get attribute value
  * @param $attrName attribute name
  */
  private static function getAttrValue(string $attrName)
  {
    $attrParts = explode('.', $attrName);
    if ($attrParts[0] == 'hidden') {
      $attr = self::$hidden;
      array_shift($attrParts);
    } else {
      $attr = self::$attrib;
    }

    foreach ($attrParts as $name) {
      if (!array_key_exists($name, $attr)) {
        return null;
      }
      $attr = $attr[$name];
    }
    return $attr;
  }

  // prevented instantiating
  private function __construct(){}

  // prevented cloning
  private function __clone(){}

  // prevented unserialization
  public function __wakeup(){}

}
