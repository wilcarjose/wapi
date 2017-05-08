<?php

namespace Wilcar\Wapi\Transformers;

use League\Fractal\Serializer\ArraySerializer as Serializer;

class DataArraySerializer extends Serializer
{
    /**
     * Serialize a collection.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function collection($resourceKey, array $data)
    {
        if ($resourceKey == false) {
            return $data;
        }

        return [$resourceKey ?: 'data' => $data];
    }

    /**
     * Serialize an item.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function item($resourceKey, array $data)
    {
        if ($resourceKey == false) {
            return $data;
        }

        return [$resourceKey ?: 'data' => $data];
    }
}
