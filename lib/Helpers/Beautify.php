<?php

namespace Andygrond\Hugonette\Helpers;

// formatter for XML string, JSON string and any data structure
// 2016-2020 Andy Grondziowski

class Beautify
{

  public static function xml($str)
  {
    $str = htmlspecialchars_decode($str);
    $str = str_replace(Array("\n", "\t", '    ', '   ', '  '), ' ', $str); 	// cleaning
    $str = str_replace(Array('> <', '><'), ">\n<", $str);
    $tmp = explode("\n", $str);		// one XML tag for each line
    $stab = array('');		// preparing array for list of tags

    foreach($tmp as $a=>$line) {
      $add = true;
      preg_match("/<([^\/\s>]+)/", $line, $match);
      $lan = '';
      if ($match) {
        $lan = trim(strtr($match[0], "<>", "  "));
      }
      $level = count($stab);
      if (in_array($lan, $stab) && substr_count($line, "</$lan") == 1) {
        $level--;
        $s = array_pop($stab);
        $add = false;
      }

      if (substr_count($line, "<$lan") == 1 && substr_count($line, "</$lan") == 1) {
        $add = false;
      }
      if (substr($line, -2) == '/>') {
        $add = false;
      }
      if (!strncmp($line, '<!', 2) || !strncmp($line, '<?', 2)) { // when first line of doc is <?xml...
        $add = false;
      }

      $newi = $level-1;
      if ($newi <0) {
        $newi = 0;
      }
      $tmp[$a] = str_repeat('  ', $newi) .$line;	// indentation - you can use also \t or more/less spaces
      if ($add && !@in_array($lan, $stab) && $lan != '') {
        array_push($stab, $lan);
      }
    }

    return join("\n", $tmp);
  }

  // formatuj strukturę danych
  public static function data($data)
  {
    $lines = explode("\n", print_r($data, true));
    $indent = 0;
    $str = '';

    foreach($lines as $val) {
      $val = trim($val);
      if ($val == '(') {
        $indent++;
      } elseif ($val == ')') {
        $indent--;
      } elseif ($val != 'Array') {
        $str .= str_repeat('&nbsp;&nbsp;', $indent) . $val ."\n";
      }
    }

    return $str;
  }

}
