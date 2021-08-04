<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Subscriber;

use Enlight\Event\SubscriberInterface;

class BackendTheme implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            // After \Shopware\Components\Theme\EventListener\BackendTheme with priority 2
            'Enlight_Controller_Front_RouteShutdown' => ['registerBackendTheme', 10],
        ];
    }

    public function registerBackendTheme(\Enlight_Controller_EventArgs $args)
    {
        if ($args->getRequest()->getModuleName() !== 'backend') {
            return;
        }

        $template = Shopware()->Container()->get('template');
        assert($template instanceof \Enlight_Template_Manager);
        // By default shopware loads the engine files from the /vendor directory
        // Try to resolve engine/Library files from root directory first
        $template->addTemplateDir(Shopware()->DocPath() . 'engine/Library', 'engine_library', \Enlight_Template_Manager::POSITION_PREPEND);
    }

}
