<?php

namespace backend\models\clickhouse_models;

use backend\models\settings\Country;
use backend\models\Project;
use bashkarev\clickhouse\ActiveRecord;
use common\helpers\LinkTypeHelper;
use common\helpers\TypeHelper;
use Yii;

/**
 * This is the model class for table "complaint_account".
 *
 * @property mixed $dttm
 * @property int $id_project
 * @property int $id_url
 * @property int $type_detection
 */
class LinkType extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'link_detection_type_mv';
    }

    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey()
    {
        return ["id_project", "id_url"];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_project', 'id_url', 'type_detection'], 'required'],
            [['id_project', 'id_url', 'type_detection'], 'integer'],
            [['dttm'], 'safe'],
            //['phone', 'match', 'pattern' => '/^(38)[(](0)(\d{2})[)][-](\d{3})[-](\d{2})[-](\d{2})/', 'message' => 'Телефон, должен быть в формате 38 (0XX) XXX XX XX']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dttm' => 'Date',
            'id_project' => 'Project ID',
            'id_url' => 'Url ID',
            'type_detection' => 'Type detection',
        ];
    }

    public function getText()
    {
        return LinkTypeHelper::getTypeName($this->type_detection);
    }

}