<?php

namespace backend\models\clickhouse_models;

use backend\models\complaint\ComplaintStatus;
use backend\models\complaint\ComplaintType;
use backend\models\Project;
use backend\models\User;
use bashkarev\clickhouse\ActiveRecord;
use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "dmca_monitoring_table_mv".
 *
 * @property string complaint_text
 * @property string complaint_our_link
 * @property integer id_complaint
 * @property integer id_type
 * @property integer id_status
 * @property integer id_url
 * @property integer id_user
 * @property integer id_project
 * @property integer id_account
 * @property integer id_our_link
 * @property integer id_text_complaint
 * @property mixed dttm
 * @property string id_google_complaint
 */
class Complaint extends ActiveRecord
{

    public $headline;
    public $description;
    public $published;
    public $type_detection;
    public $data;

    public $url;
    public $complaint_text;
    public $complaint_our_link;
    public $complaint_count;

    public $id_tone;

    const SCENARIO_SINGLE = 'single';
    const SCENARIO_MULTIPLE = 'multiple';
    const SCENARIO_SEARCH = 'search';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return "complaint";
    }

    public function afterSave($insert, $changedAttributes)
    {
        if(isset(Yii::$app->user->id)) {
            parent::afterSave($insert, $changedAttributes);
            $message = 'Пользователь ' . Yii::$app->user->identity->getFullName() . ' добавил жалобу: ' . $this->id_complaint;
            Yii::$app->customLog->log($message, $changedAttributes, $this->toArray(), Yii::$app->controller->id, $insert ? 1 : 2);
        }
    }

    public function behaviors() : array
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_INIT => ['dttm'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey()
    {
        return ["id_complaint", "id_project"];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_status', 'id_url', 'id_user', 'id_project'], 'required', 'on' => [self::SCENARIO_SINGLE, self::SCENARIO_MULTIPLE]],
            [['id_type'], 'required', 'on' => self::SCENARIO_SINGLE],
            [['complaint_our_link'], 'url', 'on' => self::SCENARIO_SINGLE],
            [['complaint_our_link'], 'url', 'on' => self::SCENARIO_MULTIPLE],
            [['id_type', 'published', 'dttm'], 'required',
                'when' => function($model) {
                    return strlen($model->url) > 0;
                }, 'whenClient' => "function (attribute, value) {
                        return $('#'+$(attribute)[0].id).parents('.item_complaint').find('.item-url').val().length > 0;
                    }",
                'on' => self::SCENARIO_MULTIPLE
            ],
            [['complaint_our_link', 'complaint_text'], 'required',
                'when' => function($model) {
                    return $model->id_type == 1 && strlen($model->url) > 0;
                }, 'whenClient' => "function (attribute, value) {
                        return $('#'+$(attribute)[0].id).parents('.item_complaint').find('.item-url').val().length > 0 && $('#'+$(attribute)[0].id).parents('.item_complaint').find('.selectType').val() == 1;
                    }",
                'on' => self::SCENARIO_MULTIPLE
            ],

            [['id_complaint','id_type', 'id_status', 'id_url', 'id_user', 'id_project', 'id_account', 'id_our_link', 'id_text_complaint', 'type_detection', 'id_tone'], 'integer'],
            [['id_google_complaint', 'complaint_text', 'complaint_our_link'], 'string'],

            [['complaint_our_link', 'complaint_text'], 'required',
                'when' => function($model) {
                    return $model->id_type == 1;
                }, 'whenClient' => "function (attribute, value) {
                        return $('#selectType').val() == 1;
                    }"
            ],

            [['dttm'], 'safe'],
        ];
    }



    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_SEARCH] = ['id_complaint', 'id_type', 'id_status', 'id_user', 'id_project'];
        $scenarios[self::SCENARIO_SINGLE] = ['id_complaint', 'id_type', 'id_status', 'id_url', 'id_user', 'id_project', 'id_account', 'complaint_text', 'complaint_our_link'];
        $scenarios[self::SCENARIO_MULTIPLE] = ['complaint_text', 'complaint_our_link', 'url', 'id_type', 'headline', 'description', 'published', 'dttm', 'type_detection', 'id_tone'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'complaint_text'      => 'Текст жалобы',
            'complaint_our_link'  => 'Ссылка на наш источник',
            'id_complaint'        => 'ID жалобы',
            'id_type'             => 'Тип жалобы',
            'id_status'           => 'Статус',
            'id_url'              => 'URL',
            'id_user'             => 'Пользователь',
            'id_project'          => 'Проект',
            'id_account'          => 'Аккаунт',
            'id_our_link'         => 'Наша ссылка',
            'id_text_complaint'   => 'Текст жалобы',
            'dttm'                => 'Дата жалобы',
            'id_google_complaint' => 'Google complaint'
        ];
    }

    public function isComplaintWasSend($url)
    {
        return self::find();
    }

    public function setIdUrl()
    {
        $connection = Yii::$app->clickhouse;
        $SQL = "SELECT sipHash64(replaceRegexpOne('".trim($this->url)."', '[\r\n ]+', '')) as id_url";
        $command = $connection->createCommand($SQL);

        $this->id_url = $command->queryOne()['id_url'];
    }

    public function setIdComplaint()
    {
        $connection = Yii::$app->clickhouse;
        $SQL = "SELECT sipHash64(CONCAT('".$this->id_type."', '".$this->id_url."', '".$this->id_project."', '".$this->dttm."')) as id_complaint";
        $command = $connection->createCommand($SQL);

        $this->id_complaint = $command->queryOne()['id_complaint'];
    }

    public function getComplaintText()
    {
        return $this->hasOne(ComplaintText::class, ['id_text_complaint' => 'id_text_complaint']);
    }

    public function getComplaintOurLink()
    {
        return $this->hasOne(ComplaintOurLink::class, ['id_our_link' => 'id_our_link']);
    }

    public function getLink()
    {
        return $this->hasOne(Link::class, ['id_url' => 'id_url']);
    }

    public function getAccount()
    {
        return $this->hasOne(ComplaintAccount::class, ['id_account' => 'id_account']);
    }

    public function getProject()
    {
        return $this->hasOne(ProjectDefaultModel::class, ['id_project' => 'id_project']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'id_user']);
    }

    public function getStatus()
    {
        return $this->hasOne(ComplaintStatus::class, ['id' => 'id_status']);
    }

    public function getType()
    {
        return $this->hasOne(ComplaintType::class, ['id' => 'id_type']);
    }

    public function getComplaintStatuses()
    {
        return $this->hasMany(ComplaintType::class, ['id_type' => 'id_type']);
    }

    public function getProjectToGroup()
    {
        return $this->hasOne(ProjectToGroup::class, ['id_project' => 'id_project']);
    }
}