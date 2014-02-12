<?php

class logFlappyMattScores {
    
    protected $logfile;
    protected $version = '1.0';

    /**
     * Constructor
     */
    public function __construct($logfile) {
        $this->setLogfile($logfile);
    }

    /**
     * Save the score
     *
     * @param int $score The score.
     * @return void
     */
    public function log($score) {

        // consideration for forwarded situations
        $ip = 0;
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        $date = new DateTime();

        // build the log string
        $items = array();
        $items[] = $date->format(DateTime::RFC2822);
        $items[] = $score;
        $items[] = $ip;
        $line = '['.implode('] [', $items).']';

        return $this->writeToLog($line);

    }

    /**
     * Returns the full path to the log file.
     * @return string The full path to the log file.
     */
    protected function getLogfile() {
        return $this->logfile;
    }

    /**
     * Sets the log file location.
     * @param string $logfile The full path to the log file.
     */
    protected function setLogfile($logfile) {
        $this->logfile = trim($logfile);
    }

    /**
     * Appends the given line to the log file.
     * @param  string $line The line to append to the log file.
     * @return boolean Returns true on success, false otherwise.
     */
    protected function writeToLog($line) {
        if ($this->validateLogfile($this->getLogfile()) === true) {
            $line = $this->removeNewLines($line) . "\n";
            return file_put_contents($this->getLogfile(), $line, FILE_APPEND);
        } else {
            return false;
        }
    }

    /**
     * Removes new lines from a string.
     * @param  string $s The string to modify.
     * @return string The string with newlines removed.
     */
    protected function removeNewLines($s) {
        return trim(preg_replace('/\s\s+/', ' ', $s));
    }

    /**
     * Validate the location of the log file
     * @param  string $filename The full path to the log file.
     * @return boolean|string   Returns true if the filename is writable, or a string with an error message.
     */
    protected function validateLogfile($filename) {

        if (file_exists($filename) && is_writable($filename)) {
            // file already exists and is writable
            return true;
        } else if (!file_exists($filename) && is_writable(dirname($filename))) {
            // file does not exist, but we should be able to create it
            return true;
        }

        switch ($filename) {
            case '':
                $error = 'There is no log filename specified.';
                break;
            case is_dir($filename):
                $error = 'The log filename cannot be a directory: ' . $filename;
                break;
            case (!file_exists($filename) && !is_writable(dirname($filename))):
                $error = 'The log file directory is not writable, so the log file cannot be created. You may need to create the file by hand with writable permissions for your web server.';
                break;
            case (file_exists($filename) && !is_writable($filename)):
                $error = 'The log file exists, but is not writable: ' . $filename;
                break;
            default:
                $error = 'Invalid log filename: ' . $filename;
        }

        return $error;

    }

}

if (isset($_POST) && isset($_POST['score'])) {
    $logger = new logFlappyMattScores('scores.log');
    $score = intval($_POST['score']);
    $logger->log($score);
}

