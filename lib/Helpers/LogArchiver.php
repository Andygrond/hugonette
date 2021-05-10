<?php

namespace Andygrond\Hugonette\Helpers;

/**
 * Renumerate archive files of the same name
 * @author Andygrond 2021
 */

use Andygrond\Hugonette\Log;

class LogArchiver
{
  private $logName;

  public function __construct($filename)
  {
    $path = pathinfo($filename);
    chdir($path['dirname']);
    $this->logName = $path['basename'];
  }

  public function shift($shift = 1)
  {
    if (file_exists($this->logName .'.1')) {
      $shiftedLogs = $this->getShiftedLogs($shift);
      $this->renum($shiftedLogs);
    } else {
      if (!rename($this->logName, $this->logName .'.1')) {
        Log::error("LogArchive: {$this->logName} not renamed - check credentials");
      }
    }
  }

  // return table of log numbers to change in appropriate order
  private function getShiftedLogs($shift)
  {
    $shiftedLogs = [];
    if ($files = scandir('.')) {
      foreach ($files as $name) {
        if (strpos($name, $this->logName) === 0) {
          $num = (int) substr($name, strlen($this->logName)+1);
          $shiftedLogs[$num] = $num+$shift;
        }
      }
    }

    // sort so nothing gets deleted during renum
    if ($shift >0) {
    	krsort($shiftedLogs);
    } else {
    	ksort($shiftedLogs);
    }
    return $shiftedLogs;
  }

  // perform log renumbering
  private function renum($shiftedLogs)
  {
    foreach ($shiftedLogs as $old => $new) {
      $oldName = $old? $this->logName .'.'.$old : $this->logName;
      $newName = $this->logName .'.'.$new;

      if (!is_file($newName)) {
        if (!rename($oldName, $newName)) {
          Log::error("LogArchive: $oldName not renumbered - check credentials");
        }
      } else {
        Log::error("LogArchive: $oldName not renumbered - file $newName exists");
      }
    }
  }

}
