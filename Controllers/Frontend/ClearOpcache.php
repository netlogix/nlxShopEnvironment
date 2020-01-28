<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

class Shopware_Controllers_Frontend_ClearOpcache extends \Enlight_Controller_Action
{
    public function indexAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $request = $this->request;
        $clientIp = $request->getClientIp();
        if ($clientIp === '127.0.0.1' || $clientIp === 'localhost') {
            if (\function_exists('opcache_reset') && \extension_loaded('Zend OPcache')) {
                \opcache_reset();
            }
        }
    }
}
