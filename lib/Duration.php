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
  public function timeLen(): array
  {
    $this->times['run']['duration'] = microtime(true) - $this->times['run']['start'];

    $len = [];
    if ($this->times) {
      foreach ($this->times as $name => $frame) {
        $len[] = $name .':' .round(1000 * $frame['duration']);
      }
    }
    return $len;
  }

}
