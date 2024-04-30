<?php

namespace Andygrond\Hugonette\Access;

/* SMB protocol remote disk access driver
* Author: Andygrond 2020-2022
**/

use Andygrond\Hugonette\Log;
use Andygrond\Hugonette\Env;
use Andygrond\Hugonette\Helpers\Decrypt;

class Smb
{
  private $state;       // state of smb object
  private $mtimes = []; // mtimes collected by mtime method
  private $error = '';  // error message

  private $errorCode = [
    0 => 'Reason outside SMB',
    1 => 'Operation not permitted',
    2 => 'No such file or directory',
    9 => 'Bad file or directory resource',
    12 => 'Out of memory',
    13 => 'Permission denied',
    16 => 'Device or resource busy',
    17 => 'Resource exists',
    20 => 'Not a directory',
    21 => 'Is a directory',
    22 => 'Invalid argument',
    28 => 'No space left on device',
    39 => 'Directory not empty',
    111 => 'Connection refused (Samba not running?)',
  ];

/** Initialize and log in
 * smbclient_state_init returns true on bad login, nothing to check!
 * @param $profile - profile name of credentials
 */
  public function __construct(string $profile)
  {
    $this->state = smbclient_state_new();
    $file = Env::get('hidden.file.smb');
    $access = (array) Decrypt::data($file)->get($profile);
    if (extract($access) < 3) {
      $this->error = "Missing credentials";
    }
    if (!smbclient_state_init($this->state, $domain, $user, $pass)) {
      $this->error = "SMB state init error";
    }
  }

/** get last error in common format
 * @return - text message or SMB code
 */
  public function lastError(): string
  {
    if ($this->error) {
      return $this->error;
    }
    $errno = smbclient_state_errno($this->state);
    return 'SMB: ' .$this->errorCode[$errno]?? "error code $errno";
  }

/** get remote file last modification time
 * @param $file - file name
 * @return $timestamp
 */
  public function mtime(string $file)
  {
    if ($this->error) {
      return 0;
    }
    return $this->mtimes[$file] = (int) smbclient_getxattr($this->state, $file, 'system.dos_attr.write_time');
  }

/** get list of files in folder
 * @param $folderName - folder name
 * @param $mark - distinguishing string which must be found in file name (usually extension)
 * @return array of filenames
 */
  public function folderFiles(string $folderName, string $mark = ''): array
  {
    $files = [];

    foreach ($this->folder($folderName) as $item) {
      if ($item['type'] == 'file') {
        if (!$mark || strpos($item['name'], $mark)) {
          $files[] = $item['name'];
        }
      }
    }
    return $files;
  }

/** get full dir list
 * @param $folderName - folder name
 * @return array of ['name'] ['type']
 */
  public function folder(string $folderName): array
  {
    $list = [];

    if ($dir = smbclient_opendir($this->state, $folderName)) {
      while (($entry = smbclient_readdir($this->state, $dir)) !== false) {
        $list[] = $entry;
      }
      smbclient_closedir($this->state, $dir);
    }
    return $list;
  }

/** copy remote file to local destination
 * @param $orgFile - remote file name
 * @param $localFile - local copy file name
 */
  public function download(string $orgFile, string $localFile): bool
  {
    $file = smbclient_open($this->state, $orgFile, 'r');
    $localTempFile = $localFile .'.part';
    if (is_file($localTempFile)) {
      if (!unlink($localTempFile)) {
        Log::warning('cannot delete local file ' .$localTempFile);
        return false;
      }
    }

    if ($fp = fopen($localTempFile, 'w')) {
      $received = $this->transfer($file, $fp);
      fclose($fp);

      if ($mtime = @$this->mtimes[$orgFile]) {	// original mtime will be set only when $this->mtime() for source file was called first
        touch($localTempFile, $mtime);
      }

      if (!rename($localTempFile, $localFile)) {
        Log::warning('cannot rename local file to ' .$localFile);
        return false;
      }
    } else {
      Log::warning('cannot open local file ' .$localTempFile);
    }
    smbclient_close($this->state, $file);
    
    return $received;
  }

  private function transfer($remote, $fp)
  {
    while (true) {
      $data = smbclient_read($this->state, $remote, 100000);
      if ($data === false || strlen($data) === 0) {
        break;
      }
      if (fwrite($fp, $data)) {
        $received = true;
      }
    }
    return $received?? false;
  }

/** return remote file content
 * @param $orgFile - remote file name
 */
  public function read(string $orgFile): string
  {
    $file = smbclient_open($this->state, $orgFile, 'r');
    $content = '';

    while (true) {
      $data = smbclient_read($this->state, $file, 100000);
      if ($data === false || strlen($data) === 0) {
        break;
      }
      $content .= $data;
    }

    smbclient_close($this->state, $file);
    return $content;
  }

  // destroy SMB resource
  public function close()
  {
    smbclient_state_free($this->state);
  }
}
