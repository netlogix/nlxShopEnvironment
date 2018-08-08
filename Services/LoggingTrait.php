<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright: 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Services;

trait LoggingTrait
{
    private $errors   = [];
    private $warnings = [];
    private $infos    = [];

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function addError($message)
    {
        $this->errors[] = $message;
    }

    public function hasErrors()
    {
        return false === empty($this->errors);
    }

    /**
     * @return array
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    public function addWarning($message)
    {
        $this->warnings[] = $message;
    }

    public function hasWarnings()
    {
        return false === empty($this->warnings);
    }

    /**
     * @return array
     */
    public function getInfos()
    {
        return $this->infos;
    }

    public function addInfo($message)
    {
        $this->infos[] = $message;
    }

    public function hasInfos()
    {
        return false === empty($this->infos);
    }

    public function hasLogs()
    {
        return $this->hasInfos() || $this->hasWarnings() || $this->hasErrors();
    }
}
