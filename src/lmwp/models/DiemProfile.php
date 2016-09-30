<?php

namespace lmwp\models;

/**
 * Class DiemProfile
 *
 * Value Object
 *
 * @package lmwp\models
 */
class DiemProfile
{
    /** @var  string */
    private $credential = '';

    /** @var  string */
    private $database = '';

    /** @var string */
    private $storage = '';

    /** @var string */
    private $email = '';

    /** @var  string */
    private $labelId = '';

    /** @var  string */
    private $archivePath = '';

    /** @var  string */
    private $timezone = '';

    /** @var  string */
    private $profilePath = '';

    /**
     * @return string
     */
    public function getCredential(): string
    {
        return $this->credential;
    }

    /**
     * @param string $credential
     */
    public function setCredential(string $credential)
    {
        $this->credential = $credential;
    }

    /**
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /**
     * @param string $database
     */
    public function setDatabase(string $database)
    {
        $this->database = $database;
    }

    /**
     * @return string
     */
    public function getStorage(): string
    {
        return $this->storage;
    }

    /**
     * @param string $storage
     */
    public function setStorage(string $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getLabelId(): string
    {
        return $this->labelId;
    }

    /**
     * @param string $labelId
     */
    public function setLabelId(string $labelId)
    {
        $this->labelId = $labelId;
    }

    /**
     * @return string
     */
    public function getArchivePath(): string
    {
        return $this->archivePath;
    }

    /**
     * @param string $archivePath
     */
    public function setArchivePath(string $archivePath)
    {
        $this->archivePath = $archivePath;
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone(string $timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @return string
     */
    public function getProfilePath(): string
    {
        return $this->profilePath;
    }

    /**
     * @param string $profilePath
     */
    public function setProfilePath(string $profilePath)
    {
        $this->profilePath = $profilePath;
    }
}