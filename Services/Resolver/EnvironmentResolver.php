<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Services\Resolver;

class EnvironmentResolver implements Resolver
{
    /**
     * {@inheritdoc}
     */
    public function resolve(array $content): array
    {
        foreach ($content as $key => $value) {
            if (\is_array($value)) {
                $content[$key] = $this->resolve($value);
                continue;
            }
            if (false === \is_string($value)) {
                continue;
            }
            if (false === $this->hasEnvironmentPlaceholder($value)) {
                continue;
            }
            $resolvedValue = $this->getResolvedValue($value);

            $content[$key] = $resolvedValue;
        }

        return $content;
    }

    private function hasEnvironmentPlaceholder(string $value): bool
    {
        return 0 === \strpos($value, '%env(');
    }

    private function getResolvedValue(string $value): string
    {
        \preg_match('/\(([^\)]*)\)/', $value, $matches);
        $resolvedValue = \getenv($matches[1]);

        if (false === $resolvedValue) {
            return '';
        }

        return $resolvedValue;
    }
}
