<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Subscriber;

use Enlight\Event\SubscriberInterface;

class BackendTheme implements SubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            // After \Shopware\Components\Theme\EventListener\BackendTheme with priority 2
            'Enlight_Controller_Front_RouteShutdown' => ['registerBackendTheme', 10],
        ];
    }

    public function registerBackendTheme(\Enlight_Controller_EventArgs $args): void
    {
        if ('backend' !== $args->getRequest()->getModuleName()) {
            return;
        }

        $template = Shopware()->Container()->get('template');
        \assert($template instanceof \Enlight_Template_Manager);
        // By default shopware loads the engine files from the /vendor directory
        // Try to resolve engine/Library files from root directory first
        $template->addTemplateDir(Shopware()->DocPath() . 'engine/Library', 'engine_library', \Enlight_Template_Manager::POSITION_PREPEND);
    }
}
