<?php
namespace Serializers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

abstract class BaseSerializer
{
    /**
     * @var Model $resource
     */
    protected $model;

    /**
     * @var Model[]|LengthAwarePaginator $resources
     */
    protected $models;

    /**
     * @var array $mapRelationSerializer
     * map relation with arbitrary serializer
     */
    protected $mapRelationSerializer = [];

    /**
     * @var array $mapFieldWithCustomName
     * when
     */
    protected $mapFieldWithCustomName = [];

    /**
     * @param Model|Model[] $resource
     * @throws Exception
     */
    public function __construct($model)
    {
        if ($this->validModel($model))  {

            $this->model = $model;
        }
        elseif (
            (is_array($model) || $model instanceof LengthAwarePaginator || $model instanceof Collection)
            && $this->validModels($model)
        ) {

            $this->models = $model;
        }
        else {

            $message = "Serializer model not valid";

            if (is_array($model))
            {
                $message = "one of serializer models isn't valid";
            }

            throw new Exception("$message.");
        }
    }

    /**
     * @param array $resources
     * @return bool
     */
    private function validModels($models)
    {
        foreach ($models as $model)
        {
            if(! $this->validModel($model)) {

                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public abstract function validModel();

    /**
     * @return array
     */
    public abstract function getSerializationFields();

    protected function fillData($serialize_only)
    {
        $data = $this->toArray();

        foreach ($data as $key => $value)
        {
            if (! in_array($key, $serialize_only) && key_exists($key, $data))
            {
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * @return string
     */
    protected function handleSerializerClassConvention($name)
    {
        $name = str_replace("\\", "", $name);
        $name = str_replace("Serializers", "", $name);
        $name = str_replace("Serializer", "", $name);

        $singular_name = Str::singular($name);

        return
            sprintf(
                "%s%s%s",
                "Serializers\\",
                ucfirst(Str::camel($singular_name)),
                "Serializer"
            );
    }

    /**
     * @return array
     */
    protected function toArray()
    {
        return [];
    }

    /**
     * @return array{}
     * @throws Exception when try to access un-existing serializer class
     */
    public function serialize($serialize_only=[])
    {
        if (! $serialize_only)
        {
           $serialize_only = $this->getSerializationFields();
        }

        $serialized_data = $this->fillData($serialize_only);

        foreach ($serialize_only as $field)
        {
            $field_name = $this->mapFieldWithCustomName[$field] ?? $field;

            if(key_exists($field, $serialized_data))
            {
                if ($field_name != $field   )
                {
                    // set value in new field name
                    $serialized_data[$field_name] = $serialized_data[$field];
                    unset($serialized_data[$field]);
                }

                continue;
            }

            /**
             * serialized relation like 'model:field1,field2,..'
             * support serialize relations just for one level
             */
            if (str_contains($field, ':')) {

                $_ = explode(':', $field);
                $_relation = array_shift($_);

                $_ = array_shift($_);
                $_fields = $_ ? explode(',', $_) : [];

                /**
                 * @var Model $_relation
                 */
                if (
                    method_exists($this->model, $_relation)
                    && $this->model->$_relation() instanceof Relation
                ) {
                    /**
                     * @var BaseSerializer $_serializer
                     */
                    if (key_exists($_relation, $this->mapRelationSerializer)) {

                        $_serializer = $this->mapRelationSerializer[$_relation];
                    } else {

                        $_serializer = $this->handleSerializerClassConvention($_relation);
                    }

                    if (! class_exists($_serializer)) {

                        throw new Exception("$_serializer not found.");
                    }

                    /**
                     * @var Collection $_models
                     * @var Model $_model
                     */
                    $_models = $_model = $this->model->$_relation;

                    if ($_models instanceof Collection) {

                        $serialized_data[$_relation] =
                            (new $_serializer($_models))->serializeMany($_fields);
                    } else {

                        $serialized_data[$_relation] =
                            (new $_serializer($_model))->serialize($_fields);
                    }
                }

            } else {

                $serialized_data[$field_name] = $this->model->$field;
            }
        }

        return $serialized_data;
    }

    /**
     * @return array
     * @throws Exception if models not filled
     */
    public function serializeMany($serialize_only=[])
    {
        $items = [];

        if (!$this->models)
        {
            throw new Exception("The Serializer hasn't a collection of model.");
        }

        foreach ($this->models as $model)
        {
            $this->model = $model;
            $items[] = $this->serialize($serialize_only);
        }

        // reset value
        $this->model = null;

        return $items;
    }

    /**
     * @return array
     * @throws Exception if registered model property isn't instance of LengthAwarePaginator
     */
    public function paginatorSerialize($serialize_only=[])
    {
        if (! $this->models instanceof LengthAwarePaginator)
        {
            throw new Exception("models should be instance of LengthAwarePaginator.");
        }

        /**
         * @var LengthAwarePaginator $paginator
         */
        $paginator = $this->models;

        return [
            'data' => $this->serializeMany($serialize_only),
            'pagination' => [
                "current_page" => $paginator->currentPage(),
                "per_page" => $paginator->perPage(),
                "last_page" => $paginator->lastPage(),
                "total" => $paginator->total()
            ]
        ];
    }
}