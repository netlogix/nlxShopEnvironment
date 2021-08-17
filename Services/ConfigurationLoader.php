<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Services;

use nlxShopEnvironment\DataTypes\DataTypeCollectorInterface;
use nlxShopEnvironment\Services\Resolver\Resolver;
use Symfony\Component\Yaml\Yaml;

class ConfigurationLoader implements ConfigurationLoaderInterface
{
    use LoggingTrait;

    /** @var DataTypeCollectorInterface */
    private $dataTypeCollector;

    /** @var Resolver */
    private $resolver;

    public function __construct(
        DataTypeCollectorInterface $dataTypeCollector,
        Resolver $resolver
    ) {
        $this->dataTypeCollector = $dataTypeCollector;
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration(string $pathToFile): bool
    {
        if (false === \is_readable($pathToFile) && 'php://stdin' !== $pathToFile) {
            throw new \RuntimeException('file not found - ' . $pathToFile);
        }

        $contentOfYamlFile = Yaml::parse(\file_get_contents($pathToFile, false));
        foreach ($contentOfYamlFile as $rootName => $content) {
            $type = $this->dataTypeCollector->get($rootName);
            $content = $this->resolver->resolve($content);

            if (null === $type) {
                $this->addError('Configuration file contains unknown root entry `' . $rootName . '`. Aborting.');
                continue;
            }

            $loader = $type->getLoader();
            if (null === $loader) {
                $this->addError('Data of type `' . $rootName . '` cannot be loaded. Aborting.');
                continue;
            }

            $loader->load($content);
        }

        return false === $this->hasErrors();
    }
}
