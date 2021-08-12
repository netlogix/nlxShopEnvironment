<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Serializer\Normalizer;

use Shopware\Models\Search\CustomFacet;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CustomFacetNormalizer extends ObjectNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return CustomFacet::class === $type;
    }
}
