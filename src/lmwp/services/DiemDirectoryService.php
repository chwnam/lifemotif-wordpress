<?php
namespace lmwp\services;

use lmwp\models\DiemProfile;


class DiemDirectoryService
{
    /** @var DiemProfile */
    private $profile;

    public function __construct(DiemProfile $profile)
    {
        $this->profile = $profile;
    }

    public function getArchiveCount()
    {
        if ($this->isArchiveOk()) {
            $fi = new \FilesystemIterator($this->profile->getArchivePath());
            $ri = new \RegexIterator($fi, '/\/[0-9a-f]+\.gz$/');

            return iterator_count($ri);
        }

        return 0;
    }

    public function isArchiveOk()
    {
        $archivePath = $this->profile->getArchivePath();

        return $archivePath && file_exists($archivePath) && is_dir($archivePath);
    }
}