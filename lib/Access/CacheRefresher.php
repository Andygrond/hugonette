<?php

namespace Andygrond\Hugonette\Access;

/** Hugonette JSON cached data management
* @author Andygrond 2020
*/

use Andygrond\Hugonette\Traits\JsonErrorTrait;
use Andygrond\Hugonette\Log;

class CacheRefresher
{
  use JsonErrorTrait;

  private $cacheName; // cache file name
  private $serialize; // format of saved data: serialization vs. JSON
  private $mtimeSrc;  // source modification time
  public $mtimeLoc;   // cache file modification time

  // $cacheName = nazwa wynikowego pliku cache
  public function __construct($cacheName, $serialize = false)
  {
    $this->mtimeLoc = file_exists($cacheName)? filemtime($cacheName) : 0;
    $this->cacheName = $cacheName;
    $this->serialize = $serialize;
  }

  // refresh the cache, return status
  public function refresh($data, $mtimeSrc = null): bool
  {
    $dataString = $this->serialize? serialize($data) : json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

    if ($data) {
      $tempFile = $this->cacheName .'.part';

      if (file_put_contents($tempFile, $dataString, LOCK_EX)) {
        chmod($tempFile, 0666);
        $mtimeSrc = $mtimeSrc?? $this->mtimeSrc;
        if ($mtimeSrc) {
          touch($tempFile, $mtimeSrc);	// ustaw oryginalny czas
        }

        if (!rename($tempFile, $this->cacheName)) {
          Log::warning('cannot rename file.part to ' .$this->cacheName);
          return false;
        }
        return true;
      }
    }
    return false;
  }

  // save source mtime and determine if refresh should be done
  // ATTENTION: cache mtime will be set to $mtimeSrc
  public function newer($mtimeSrc)
  {
    $this->mtimeSrc = $mtimeSrc;
    return ($mtimeSrc && $mtimeSrc > $this->mtimeLoc);
  }

  // check validity of the cache (in minutes)
  public function isCurrent($minutes): bool
  {
    $timeframe = time() - 60 * $minutes;
    return (is_file($this->cacheName) && $timeframe < filemtime($this->cacheName));
  }

  // refresh file mtime only
  public function touch()
  {
    touch($this->cacheName);
  }

  // get data from cache
  public function get($arrays = false)
  {
    if (is_file($this->cacheName)) {
      if ($content = file_get_contents($this->cacheName)) {
        if ($this->serialize) {
          try {
            $data = unserialize($content);
          } catch (\Throwable $t) {
            Log::warning('CacheRefresher: file: ' .$this->cacheName .' data not unserialized: ' .$t->getMessage());
          }
        } else {
          $data = json_decode($content, $arrays);
          $data or Log::warning('CacheRefresher: file: ' .$this->cacheName .' JSON data not decoded: ' .$this->jsonError());
        }
        return $data;
      }
    }
    return null;
  }

}
