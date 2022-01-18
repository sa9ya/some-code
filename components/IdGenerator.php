<?php

namespace backend\components;

use bashkarev\clickhouse\ActiveRecord;
use Yii;
use yii\base\InvalidArgumentException;
use yii\behaviors\AttributeBehavior;

/**
 * Module that generates id for all data what will be inserted in clickhouse database
 * That module gets all variables write in 1 string and generate id with sipHash64
 *
 * ```php
 *
 * public function behaviors() : array
 * {
 *      return [
 *          'clickhouseId' => [
 *              'class' => 'backend\components\IdGenerator',
 *              'create_ids_by' => ['name'],
 *              'out_attribute' => 'id_source'
 *          ]
 *      ];
 * }
 * ```
 *
 */
class IdGenerator extends AttributeBehavior
{
    public $create_ids_by;
    public $out_attribute;
    private $data = '';

    /**
     * Here you can add different events when will be this module works
     */
    public function events()
    {
        parent::init();

        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'generateId'
        ];
    }

    /**
     * Method that generates id and set id for model
     *
     * @throws InvalidArgumentException
     */
    public function generateId()
    {
        if(!empty($this->owner->{$this->out_attribute})) {
            return true;
        }
        if(!is_array($this->create_ids_by)) {
            throw new InvalidArgumentException('out_attribute must be an array');
        }

        foreach ($this->create_ids_by as $key => $attribute) {
            $this->data .= $this->owner->{$attribute};
            if ($key !== key($this->create_ids_by)) {
                $this->data .= ',';
            }
        }

        $connection = Yii::$app->clickhouse;
        $SQL = "SELECT sipHash64('".$this->data."') as id";
        $command = $connection->createCommand($SQL);

        $this->owner->{$this->out_attribute} = $command->queryOne()['id'];
    }
}