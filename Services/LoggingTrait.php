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

    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }

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

    public function addWarning(string $message): void
    {
        $this->warnings[] = $message;
    }

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

    public function addInfo(string $message): void
    {
        $this->infos[] = $message;
    }

    public function hasInfos(): bool
    {
        return false === empty($this->infos);
    }

    public function hasLogs(): bool
    {
        return $this->hasInfos() || $this->hasWarnings() || $this->hasErrors();
    }
}
