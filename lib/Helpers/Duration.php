<?php

namespace Andygrond\Hugonette\Helpers;

/* Time measurement for performance tests
 * @author Andygrond 2020
**/

use Andygrond\Hugonette\Log;

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
    if (@$this->times[$name]['start']) {
      Log::warning("Job $name double start. Duration values will be wrong.", debug_backtrace());
    } else {
      $this->times[$name]['start'] = microtime(true);
      if (!isset($this->times[$name]['duration'])) {
        $this->times[$name]['duration'] = 0;
      }
    }
  }

  public function stop(string $name)
  {
    if (!isset($this->times[$name]) || !$this->times[$name]['start']) {
      Log::warning("Job $name done but not started. Duration values will be wrong.", debug_backtrace());
    } else {
      $this->times[$name]['duration'] += microtime(true) - $this->times[$name]['start'];
      $this->times[$name]['start'] = 0;
    }
  }

  // get array of all duration times
  public function timing(): array
  {
    $this->times['run']['duration'] = microtime(true) - $this->times['run']['start'];

    $len = [];
    if ($this->times) {
      foreach ($this->times as $name => $frame) {
        $len[$name] = $this->msToTime($frame['duration']);
      }
    }
    return $len;
  }

  // Time distance given in a user-friendly form
  private static function msToTime($delta)
	{
		$lo = ($delta > 10)? 1 : (($delta > 1)? 2 : 3);
		$answer = ($delta > 0.8)? round($delta, $lo) .' s' : 1000*round($delta, 3) .' ms';
		$info = ($delta > 90)? round($delta/60, 1) .' min' : $answer;
		return $info;
	}

}
