<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Services;

trait LoggingTrait
{
    /** @var array|string[] */
    private $errors   = [];

    /** @var array|string[] */
    private $warnings = [];

    /** @var array|string[] */
    private $infos    = [];

    /**
     * @return array|string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function addError($message)
    {
        $this->errors[] = $message;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return false === empty($this->errors);
    }

    /**
     * @return array|string[]
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    public function addWarning($message)
    {
        $this->warnings[] = $message;
    }

    /**
     * @return bool
     */
    public function hasWarnings()
    {
        return false === empty($this->warnings);
    }

    /**
     * @return array|string[]
     */
    public function getInfos()
    {
        return $this->infos;
    }

    public function addInfo($message)
    {
        $this->infos[] = $message;
    }

    /**
     * @return bool
     */
    public function hasInfos()
    {
        return false === empty($this->infos);
    }

    /**
     * @return bool
     */
    public function hasLogs()
    {
        return $this->hasInfos() || $this->hasWarnings() || $this->hasErrors();
    }
}
