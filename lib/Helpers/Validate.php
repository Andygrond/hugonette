<?php

namespace Andygrond\Hugonette\Helpers;

/* User input validation / sanitization helper for Hugonette
 * works according to filter_var_array and filter_input_array functions
 * validate filter types = [v: or empty][int|float|email|url|domain|ip|mac|regexp|boolean]
 * sanitize filter types = s:[string|int|float|email|url|encoded|magic_quotes|special_chars|full_special_chars]
 * filter type null = do nothing
 * @author Andygrond 2020
**/

class Validate {

  /**
  * validate or sanitize $_GET | $_POST etc. array
  * @param source [get|post|cookie|server|env]
  * @param definition array of <variable_name> => [string <filter_type> or array filter_input_array like argument]
  * @return array of values on success, false on failure, null if source not populated
  * array values: false if filter fails or null when not set
  */
  public static function input(string $source, array $definition): mixed
  {
    foreach ($definition as &$def) {
      $def = self::getSingleDef($def);
    }
    return filter_input_array(self::getConstant('INPUT_' .$source), $definition);
  }

  /**
  * validate or sanitize indexed array and replace keys to strings
  * @param values indexed array of values
  * @param definition array of <variable_name> => [string <filter_type> or array filter_input_array like argument]
  * @return array of named values on success, false on failure
  * array values: false if filter fails or null when not set
  */
  public static function values(array $values, array $definition)
  {
    $n = 0;
    foreach ($definition as $key => &$def) {
      $response[$key] = $values[$n++];
      $def = self::getSingleDef($def);
    }
    return filter_var_array($response, $definition);
  }

  // translate single variable definition
  private static function getSingleDef($def)
  {
    if (is_string($def)) {
      $def = self::getFilterType($def);
    } else {
      $def['filter'] = self::getFilterType($def['filter']);
    }
    return $def;
  }

  // return constant value or false
  private static function getConstant(string $name)
  {
    $name = '\\' .strtoupper($name);
    return defined($name)? constant($name) : false;
  }

  // translate short filter type to constant value
  private static function getFilterType(string $type)
  {
    if ($type === null) {
      return FILTER_DEFAULT;
    }
    $type = strtoupper($type);
    $prefix = 'FILTER_VALIDATE_';

    if ($type[1] = ':') {
      if ($type[0] == 'S') {
        $prefix = ($type == 'INT' || $type == 'FLOAT')? 'FILTER_SANITIZE_NUMBER' : 'FILTER_SANITIZE_';
      }
      $type = substr($type, 2);
    }

    return self::getConstant($prefix .$type);
  }

}
