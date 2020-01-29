<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

class Shopware_Controllers_Frontend_CleanOpcache extends \Enlight_Controller_Action
{
    public function indexAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $request = $this->request;
        $clientIp = $request->getClientIp();
        if ('127.0.0.1' === $clientIp || 'localhost' === $clientIp) {
            $this->get('sd_shop_environment.cache_cleaners.opcache_cleaner')->clean();
        }
    }
}
