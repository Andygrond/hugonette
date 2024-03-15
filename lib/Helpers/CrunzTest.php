<?php

namespace Andygrond\Hugonette\Helpers;

/** Crunz schedule maintenance helper
 * @author Andy Grondziowski 2024
 */

use Andygrond\Hugonette\Env;

class CrunzTest
{
    private $done;  // informacja o wykonaniu

    public function __construct()
    {

    }
  
    // execute CLI command 
    public function exec($task, $force)
    {
        $command = 'cd ' .SYS_DIR .' && vendor' .DIRECTORY_SEPARATOR .'bin' .DIRECTORY_SEPARATOR .'crunz ';

        if ($force) {
            $command .= 'schedule:run --task ' .$task .' --force -vvv 2>&1';
          } elseif ($task) {
            $command .= 'task:debug ' .$task .' 2>&1';
          } else {
            $command .= 'schedule:list 2>&1';
          }
      
          $this->done = (exec($command, $output, $retval) !== false)? 'Executed' : 'Failed';
          echo "<b>$command</b><br>";
      
          if ($force) {
            $output = $this->format($output);
          }
      
          echo '<pre>' .implode('<br>', $output) .'</pre>';
          return $this->done .' with status ' .$retval;
    }
  
    private function format($output)
    {
        $error = true;
        $relevant = true;
        $response = [];
  
        foreach ($output as $line) {
          if (strpos($line, '/crunz.yml')) {
            $relevant = false;
          }
          if (strpos($line, 'status: success.')) {
            $error = false;
          }
          if ($relevant) {
            $response[] = $line;
          }
        }
        if ($error) {
          $this->done = 'Errors found! Check crunz_error.log for details. ' .$this->done;
        }
        return $response;
    }

}
