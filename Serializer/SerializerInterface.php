<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Serializer;

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

use Shopware\Components\Model\ModelEntity;

interface SerializerInterface
{
    /**
     * Transforms Shopware entity to array
     *
     * @return mixed[]
     */
    public function serialize(ModelEntity $entity);

    /**
     * Assigns array data to Shopware entity
     *
     * @param mixed[] $data
     *
     * @return ModelEntity
     */
    public function deserialize(ModelEntity $targetEntity, $data);
}
