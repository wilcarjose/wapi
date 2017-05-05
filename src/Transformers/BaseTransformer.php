<?php

namespace WilcarJose\Wapi\Transformers;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;

class BaseTransformer extends TransformerAbstract
{
    protected $fields = [];

    private $validParams = ['fields'];

    /**
     * Transforms the condition object to array
     *
     * @param  Model    $condition
     *
     * @return array
     */
    public function transform(Model $object = null)
    {
        if (is_null($object)) {
            return [];
        }

        $this->object = $object;

        return empty($this->fields) ? $this->getAllFields() : $this->getSelectedFields();
    }

    protected function getAllFields()
    {
        $data = [];

        foreach ($this->getValues() as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }

    protected function getSelectedFields()
    {
        $data = [];

        foreach ($this->fields as $value) {
            if (isset($this->getValues()[$value])) {
                $data[$value] = $this->getValues()[$value];
            }
        }

        return $data;
    }

    public function filterFields($fields, $delimiter = ',')
    {
        if (is_array($fields)) {
            return $this->fields = $fields;
        }

        return $this->fields = empty($fields) ? [] : explode($delimiter, $fields);
    }
}
