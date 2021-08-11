<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Shop\Shop;

class ShopConfigDumper implements DumperInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        $shopConfigs = [];

        $shopRepo = $this->entityManager->getRepository(Shop::class);
        $shops = $shopRepo->findAll();

        /** @var Shop $shop */
        foreach ($shops as $shop) {
            $shopConfigs[$shop->getId()] = [
                'Name'          => $shop->getName(),
                'Title'         => $shop->getTitle(),
                'Host'          => $shop->getHost(),
                'BasePath'      => $shop->getBasePath(),
                'BaseUrl'       => $shop->getBaseUrl(),
                'Hosts'         => $shop->getHosts(),
                'Secure'        => $shop->getSecure(),
                'CustomerScope' => $shop->getCustomerScope(),
                'Default'       => $shop->getDefault(),
                'Active'        => $shop->getActive(),
                'CustomerGroup' => $shop->getCustomerGroup()->getId(),
                'Category'      => $shop->getCategory()->getName(),
                'Locale'        => $shop->getLocale()->getLocale(),
                'Currency'      => $shop->getCurrency()->getCurrency(),
            ];

            if (null !== $shop->getMain()) {
                $shopConfigs[$shop->getId()]['Main'] = $shop->getMain()->getId();
            }
            if (null !== $shop->getFallback()) {
                $shopConfigs[$shop->getId()]['Fallback'] = $shop->getFallback()->getId();
            }
        }

        return $shopConfigs;
    }
}
