<?php
namespace lmwp\services;


class DiemWrapperService
{
    private $pythonPath = '';

    private $diemPath = '';

    private $profilePath = '';

    private $logLevel = '';

    private $logFile = '';


    public function __construct($pythonPath, $diemPath, $profilePath, $logLevel, $logFile)
    {
        $this->pythonPath = $pythonPath;

        $this->diemPath = $diemPath;

        $this->profilePath = $profilePath;

        $this->logLevel = $logLevel;

        $this->logFile = $logFile;
    }

    private function checkPaths()
    {
        if ( ! $this->pythonPath || ! file_exists($this->pythonPath) || ! is_readable($this->pythonPath)) {
            return false;
        }

        if ( ! $this->diemPath || ! file_exists($this->diemPath) || ! is_readable($this->diemPath)) {
            return false;
        }

        if ( ! $this->profilePath || ! file_exists($this->profilePath) || ! is_readable($this->profilePath)) {
            return false;
        }

        if ( ! $this->logFile || ! file_exists($this->logFile) || ! is_writable($this->logFile)) {
            return false;
        }

        return true;
    }

    public function fetchIncrementally()
    {
        if ($this->checkPaths() == false) {
            return null;
        }

        $command = $this->getDiemCommand('fetch-incrementally');

        return $this->runDiemScript($command, 2);
    }

    public function export($mid)
    {

        if ($this->checkPaths() == false) {
            return null;
        }

        $command = $this->getDiemCommand('export', "--mid $mid");

        return $this->runDiemScript($command, 1);
    }

    public function extractAttachments($mid, $destDir, $all = false, $attachmentId = '')
    {
        if ($all) {
            $command = $this->getDiemCommand(
                'extract-attachments',
                "--mid $mid --dest-dir $destDir --all"
            );
        } else {
            $command = $this->getDiemCommand(
                'extract-attachments',
                "--mid $mid --attachment-id $attachmentId --dest-dir $destDir"
            );
        }

        return $this->runDiemScript($command, 1);
    }

    private function getDiemCommand($diemTask, $otherOptions = '')
    {
        return sprintf(
            '%s run.py --log-level %s --log-file %s %s --profile %s %s',
            $this->pythonPath,
            $this->logLevel,
            $this->logFile,
            $diemTask,
            $this->profilePath,
            $otherOptions
        );
    }

    private function runDiemScript($command, $pipeIndex)
    {
        $descriptorSpec = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        );

        $handle     = proc_open($command, $descriptorSpec, $pipes, $this->diemPath);
        $output     = stream_get_contents($pipes[$pipeIndex]);
        $returnCode = proc_close($handle);

        if ($returnCode !== 0) {
            return null;
        }

        return $output;
    }
}