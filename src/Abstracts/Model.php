<?php
namespace Zenderator\Abstracts;

use Camel\CaseTransformer;
use Camel\Format;

abstract class Model
{
    protected $_primary_keys;
    
    public function __construct(array $data = [])
    {
        if ($data) {
            $this->exchangeArray($data);
        }
    }

    /**
     * @return \Interop\Container\ContainerInterface
     */
    public function getDIContainer()
    {
        return App::Container();
    }

    /**
     * @param array $data
     *
     * @return Model $this
     */
    public function exchangeArray(array $data)
    {
        $transformer = new CaseTransformer(new Format\CamelCase(), new Format\StudlyCaps());

        foreach ($data as $key => $value) {
            $method = 'set' . $transformer->transform($key);
            $originalProperty = "original_" . $transformer->transform($key);

            // @todo Query $this->getListOfProperties() instead of methods list..
            if (method_exists($this, $method)) {
                if (is_numeric($value)) {
                    $value = doubleval($value);
                }
                $this->$method($value);
                #echo "Writing into \$this->{$originalProperty}: exchangeArray\n";
                $this->$originalProperty = $value;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        $array = [];

        $transformer = new CaseTransformer(new Format\StudlyCaps(), new Format\StudlyCaps());

        foreach($this->getListOfProperties() as $property){
            $getFunction = "get{$property}";
            $currentValue = $this->$getFunction();
            $array[$transformer->transform($property)] = $currentValue;
        }

        return array_merge($array);
    }

    /**
     * Return primary key values in an associative array.
     *
     * @return array
     */
    public function getPrimaryKeys()
    {
        $primaryKeyValues = [];
        foreach ($this->_primary_keys as $primary_key) {
            $getFunction = "get{$primary_key}";
            $primaryKeyValues[$primary_key] = $this->$getFunction();
        }
        return $primaryKeyValues;
    }

    /**
     * Returns true if the primary key isn't null.
     *
     * @return bool
     */
    public function hasPrimaryKey()
    {
        $notNull = false;
        foreach ($this->getPrimaryKeys() as $primaryKey) {
            if ($primaryKey != null) {
                $notNull = true;
            }
        }
        return $notNull;
    }

    protected function getProtectedMethods()
    {
        return ['getPrimaryKeys', 'getProtectedMethods', 'getDIContainer'];
    }
    
    public function getListOfProperties()
    {
        throw new \Exception("getListOfProperties in Abstract Model should never be used.");
    }

    /**
     * Returns whether or not the data has been modified inside this model.
     */
    public function hasDirtyProperties()
    {
        return count($this->getListOfDirtyProperties()) > 0;
    }

    /**
     * Returns an array of dirty properties
     */
    public function getListOfDirtyProperties()
    {
        $transformer = new CaseTransformer(new Format\CamelCase(), new Format\StudlyCaps());
        $dirtyProperties = [];
        foreach($this->getListOfProperties() as $property){
            $originalProperty = "original_" . $transformer->transform($property);
            #echo "Writing into \$this->{$originalProperty}: getListOfDirtyProperties\n";
            if($this->$property != $this->$originalProperty){
                $dirtyProperties[$property] = [
                    'before' => $this->$originalProperty,
                    'after' => $this->$property,
                ];
            }
        }
        return $dirtyProperties;
    }
}
