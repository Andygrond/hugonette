<?php

namespace Andygrond\Hugonette\Access;

/* JSON cached data transport
 * Struktura danych źródłowych JSON przez URL:
 * mtime = czas ostatniej modyfikacji danych
 * data = dane zasadnicze
 * fault = opis błędu widzianego od strony serwera
**/

use Andygrond\Hugonette\Traits\JsonError;

class CacheRefresher
{
  use JsonError;

  private $cacheName; // nazwa pliku cache
  private $serialize; // format of saved data: serialization vs. JSON
  private $mtimeSrc;  // czas modyfikacji danych źródłowych
  public $mtimeLoc;   // czas modyfikacji pliku cache

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
    if (!is_file($this->cacheName)) {
      @touch($this->cacheName); // jesli nie ma pliku, załóż go
      chmod($this->cacheName, 0666); // nadaj uprawnienia dla cron i httpd
    }
    $dataString = $this->serialize? serialize($data) : json_encode($data, JSON_UNESCAPED_UNICODE);

    if ($data) {
      if (file_put_contents($this->cacheName, $dataString, LOCK_EX)) {
        $mtimeSrc = $mtimeSrc?? $this->mtimeSrc;
        if ($mtimeSrc) {
          touch($this->cacheName, $mtimeSrc);	// ustaw oryginalny czas
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
          } catch (Throwable $t) {
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
