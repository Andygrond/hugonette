<?php

namespace Andygrond\Hugonette;

/* Central store of environment attributes for Hugonette
 * Attention: agent name is roughly estimated and can be not always accurate
 * @author Andygrond 2020
**/

class Env
{
  // initial environment attributes
  private static $attrib = [
    'view' => 'plain',  // view mode [ plain | latte | json | upload | redirect ]
    'namespace' => [
      'presenter' => 'App\\Presenters\\',
      'view' => 'Andygrond\\Hugonette\\Views\\',
    ],
    'bots' => __DIR__ .DIRECTORY_SEPARATOR .'Data' .DIRECTORY_SEPARATOR .'bots.ini',
  ];

  /** get attribute value
  * @param attrName variable name - array keys.dotted
  * @return mixed value of variable
  */
  public static function get(string $attrName = null)
  {
    if ($attrName) {
      return self::findAttr($attrName);
    } else {
      return self::$attrib;
    }
  }

  /** set attribute value
  * @param attrName variable name - array cells.dotted
  * @param attrValue value to be set
  */
  public static function set(string $attrName, $attrValue)
  {
    $attr =& self::findAttr($attrName);
    $attr = $attrValue;
  }

  /** set attribute value
  * @param attrName variable name - array cells.dotted
  * @param append value to be appended
  */
  public static function append(string $attrName, string $append)
  {
    $attr =& self::findAttr($attrName);
    if (is_string($attr)) {
      $attr .= $append;
    }
  }

  /** replace full set of env attributes - allowed call from Route class only
  * @param attrib full set of attributes
  */
  public static function restore(array $attrib)
  {
    if (debug_backtrace()[1]['class'] == 'Andygrond\\Hugonette\\Route') {
      self::$attrib = $attrib;
    }
  }

  /** find attribute element
  * @param attrName variable name - array cells.dotted
  */
  private static function &findAttr(string $attrName)
  {
    $attr =& self::$attrib;
    foreach (explode('.', $attrName) as $name) {
      if (!array_key_exists($name, $attr)) {
        $attr[$name] = null;
      }
      $attr =& $attr[$name];
    }
    return $attr;
  }

}
