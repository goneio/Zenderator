<?php

namespace Zenderator\Components;

use Gone\Inflection\Inflect;
use Zenderator\Zenderator;

class RelatedModel extends Entity
{
    protected $schema;
    protected $localTable;
    protected $remoteTable;
    protected $localBoundSchema;
    protected $localBoundColumn;
    protected $remoteBoundSchema;
    protected $remoteBoundColumn;
    protected $hasClassConflict = false;

    /**
     * @return self
     */
    public static function Factory(Zenderator $zenderator)
    {
        return parent::Factory($zenderator);
    }

    public function markClassConflict(bool $conflict)
    {
        #echo "  > Marked {$this->getLocalClass()}/{$this->getRemoteClass()} in conflict.\n";
        $this->hasClassConflict = $conflict;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param mixed $schema
     *
     * @return RelatedModel
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
        return $this;
    }

    public function getRemoteVariable()
    {
        if (Zenderator::isUsingClassPrefixes()) {
            return  $this->transCamel2Camel->transform($this->getRemoteBoundSchema()) .
                    $this->transCamel2Studly->transform($this->getRemoteTableSanitised());
        }
        return  $this->transCamel2Camel->transform($this->getRemoteTableSanitised());
    }

    /**
     * @return mixed
     */
    public function getRemoteBoundSchema()
    {
        return $this->remoteBoundSchema;
    }

    /**
     * @param mixed $remoteBoundSchema
     *
     * @return RelatedModel
     */
    public function setRemoteBoundSchema($remoteBoundSchema)
    {
        $this->remoteBoundSchema = $remoteBoundSchema;
        return $this;
    }

    public function getRemoteTableSanitised()
    {
        return $this->getZenderator()->sanitiseTableName($this->getRemoteTable());
    }

    /**
     * @return mixed
     */
    public function getRemoteTable()
    {
        return $this->remoteTable;
    }

    /**
     * @param mixed $remoteTable
     *
     * @return RelatedModel
     */
    public function setRemoteTable($remoteTable)
    {
        $this->remoteTable = $remoteTable;
        return $this;
    }

    public function getLocalVariable()
    {
        if (Zenderator::isUsingClassPrefixes()) {
            return  $this->transCamel2Camel->transform($this->getLocalBoundSchema()) .
                    $this->transCamel2Studly->transform($this->getLocalTableSanitised());
        }
        return  $this->transCamel2Camel->transform($this->getLocalTableSanitised());
    }

    /**
     * @return mixed
     */
    public function getLocalBoundSchema()
    {
        return $this->localBoundSchema;
    }

    /**
     * @param mixed $localBoundSchema
     *
     * @return RelatedModel
     */
    public function setLocalBoundSchema($localBoundSchema)
    {
        $this->localBoundSchema = $localBoundSchema;
        return $this;
    }

    public function getLocalTableSanitised()
    {
        return $this->getZenderator()->sanitiseTableName($this->getLocalTable());
    }

    /**
     * @return mixed
     */
    public function getLocalTable()
    {
        return $this->localTable;
    }

    /**
     * @param mixed $localTable
     *
     * @return RelatedModel
     */
    public function setLocalTable($localTable)
    {
        $this->localTable = $localTable;
        return $this;
    }

    public function getLocalTableGatewayName()
    {
        return $this->transCamel2Studly->transform(
            $this->getLocalClass()
            ."TableGateway"
        );
    }

    public function getRemoteTableGatewayName()
    {
        return $this->transCamel2Studly->transform(
            $this->getRemoteClass()
            ."TableGateway"
        );
    }

    public function getLocalModelName()
    {
        return $this->transCamel2Studly->transform(
            $this->getLocalClass()
            ."Model"
        );
    }

    public function getRemoteModelName()
    {
        return $this->transCamel2Studly->transform(
            $this->getRemoteClass()
            ."Model"
        );
    }

    public function getLocalFunctionName()
    {
        if ($this->hasClassConflict()) {
            return
#                $this->transCamel2Studly->transform(
                    self::singulariseCamelCaseSentence($this->getLocalClass()) .
                    "By" .
                    $this->transCamel2Studly->transform($this->getLocalBoundColumn())
#                )
            ;
        }
        return $this->transCamel2Studly->transform(
            #               self::singulariseCamelCaseSentence(
                $this->getLocalClass()
            #               )
        );
    }

    public function getRemoteFunctionName()
    {
        if ($this->hasClassConflict()) {
            return
                self::singulariseCamelCaseSentence($this->getRemoteClass()) .
                "By" .
                $this->transCamel2Studly->transform($this->getLocalBoundColumn());
        }
        return
                self::singulariseCamelCaseSentence(
                    $this->getRemoteClass()
                );
    }

    public function hasClassConflict() : bool
    {
        return $this->hasClassConflict;
    }

    public function getLocalClass()
    {
        if (Zenderator::isUsingClassPrefixes()) {
            return
                $this->transCamel2Studly->transform($this->getLocalBoundSchema()) .
                $this->transCamel2Studly->transform($this->getLocalTableSanitised());
        }
        return
                $this->transCamel2Studly->transform($this->getLocalTableSanitised());
    }

    /**
     * @return mixed
     */
    public function getLocalBoundColumn()
    {
        return $this->localBoundColumn;
    }

    /**
     * @param mixed $localBoundColumn
     *
     * @return RelatedModel
     */
    public function setLocalBoundColumn($localBoundColumn)
    {
        $this->localBoundColumn = $localBoundColumn;
        return $this;
    }

    public function getRemoteClass()
    {
        if (Zenderator::isUsingClassPrefixes()) {
            return  $this->transCamel2Studly->transform($this->getRemoteBoundSchema()) .
                    $this->transCamel2Studly->transform($this->getRemoteTableSanitised());
        }
        return $this->transCamel2Studly->transform($this->getRemoteTableSanitised());
    }

    public function getLocalBoundColumnGetter()
    {
        return "get" . $this->transCamel2Studly->transform($this->getLocalBoundColumn());
    }

    public function getRemoteBoundColumnGetter()
    {
        return "get" . $this->transCamel2Studly->transform($this->getRemoteBoundColumn());
    }

    public function getLocalBoundColumnSetter()
    {
        return "set" . $this->transCamel2Studly->transform($this->getLocalBoundColumn());
    }

    public function getRemoteBoundColumnSetter()
    {
        return "set" . $this->transCamel2Studly->transform($this->getRemoteBoundColumn());
    }

    /**
     * @return mixed
     */
    public function getRemoteBoundColumn()
    {
        return $this->remoteBoundColumn;
    }

    /**
     * @param mixed $remoteBoundColumn
     *
     * @return RelatedModel
     */
    public function setRemoteBoundColumn($remoteBoundColumn)
    {
        $this->remoteBoundColumn = $remoteBoundColumn;
        return $this;
    }

    /**
     * @param $localSchema
     * @param $localColumn
     * @param $remoteSchema
     * @param $remoteColumn
     *
     * @return RelatedModel
     */
    public function setBindings(
        string $localSchema,
        string $localColumn,
        string $remoteSchema,
        string $remoteColumn
    ) {
        return $this
            ->setLocalBoundSchema($localSchema)
            ->setLocalBoundColumn($localColumn)
            ->setRemoteBoundSchema($remoteSchema)
            ->setRemoteBoundColumn($remoteColumn);
    }

    /**
     * Singularise the very last word of a camelcase sentence: bigSmellyHorses => bigSmellyHorse
     *
     * @param string $camel
     *
     * @return string
     */
    private function singulariseCamelCaseSentence(string $camel): string
    {
        $snake = explode("_", $this->transCamel2Snake->transform($camel));
        $snake[count($snake) - 1] = Inflect::singularize($snake[count($snake) - 1]);
        return $this->transSnake2Camel->transform(implode("_", $snake));
    }
}
