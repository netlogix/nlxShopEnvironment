<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Services;

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
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $message
     */
    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return false === empty($this->errors);
    }

    /**
     * @return array|string[]
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * @param string $message
     */
    public function addWarning(string $message): void
    {
        $this->warnings[] = $message;
    }

    /**
     * @return bool
     */
    public function hasWarnings(): bool
    {
        return false === empty($this->warnings);
    }

    /**
     * @return array|string[]
     */
    public function getInfos(): array
    {
        return $this->infos;
    }

    /**
     * @param string $message
     */
    public function addInfo(string $message)
    {
        $this->infos[] = $message;
    }

    /**
     * @return bool
     */
    public function hasInfos(): bool
    {
        return false === empty($this->infos);
    }

    /**
     * @return bool
     */
    public function hasLogs(): bool
    {
        return $this->hasInfos() || $this->hasWarnings() || $this->hasErrors();
    }
}
