<?php

namespace Andygrond\Hugonette;

/* Time measurement for performance tests
 * @author Andygrond 2019
**/

class Duration
{
  public $times = []; // table of durations

  public function __construct()
  {
    // calculate init time duration
    $runTime = microtime(true);
    $this->times['pre']['duration'] = $runTime - $_SERVER['REQUEST_TIME_FLOAT'];
    $this->times['run']['start'] = $runTime;

  }

  public function start($name)
  {
    if (isset($this->times[$name]['start'])) {
      throw new \InvalidArgumentException("Job $name double start.");
    }

    $this->times[$name]['start'] = microtime(true);
    if (!isset($this->times[$name]['duration'])) {
      $this->times[$name]['duration'] = 0;
    }
  }

  public function stop(string $name)
  {
    if (!isset($this->times[$name]) || !isset($this->times[$name]['start'])) {
      throw new \InvalidArgumentException("Job $name done but not started.");
    }

    $this->times[$name]['duration'] += microtime(true) - $this->times[$name]['start'];
    $this->times[$name]['start'] = 0;
  }

  // get array of all duration times
  public function timeLen()
  {
    $this->times['run']['duration'] = microtime(true) - $this->times['run']['start'];

    if ($this->times) {
      foreach ($this->times as $name => $frame) {
        $len[] = $name .':' .$this->easyTime($frame['duration']);
      }
    }
    return $len;
  }

  // get time duration in user friendly format
  // argument in milliseconds
  private function easyTime(float $duration): string
  {
    return round(1000 * $duration);
  }

  private function easyTime_alt(float $duration): string
  {
    if ($duration < .9) {
      return round(1000 * $duration) .' ms';
    } elseif ($duration < 9.) {
      return round($duration, 2) .' s';
    } elseif ($duration < 90.) {
      return round($duration, 1) .' s';
    } else {
      return round($duration/60, 1) .' min';
    }
  }

}
