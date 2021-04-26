<?php

namespace Andygrond\Hugonette\Traits;

/* Base64 encoder/decoder for URL GET parameters
 * Produces string which is not expanded by url encoding
 * @author Andygrond 2020
**/

trait UrlCoder
{
  function smartUrlEncode($input) {
   return strtr(base64_encode($input), '+/=', '._$');
  }

  function smartUrlDecode($input) {
   return base64_decode(strtr($input, '._$', '+/='));
  }
}
