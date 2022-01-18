<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\helpers\ProjectHelper;

/**
 * This is the model class for table "project_group".
 *
 * @property int $group_id
 * @property string $title
 * @property int $sort_order
 * @property int $status
 *
 * @property ProjectToGroup[] $projectToGroups
 */
class ProjectGroup extends \yii\db\ActiveRecord
{

    public function afterSave($insert, $changedAttributes)
    {
        if(isset(Yii::$app->user->id)) {
            parent::afterSave($insert, $changedAttributes);
            $message = 'Пользователь ' . Yii::$app->user->identity->getFullName() . ($insert ? ' добавил ' : ' редактировал ') . ' Группу проектов: ' . $this->title ;
            $dataOld = !$insert ? $changedAttributes : null;
            Yii::$app->customLog->log($message, $dataOld, $this->toArray(), Yii::$app->controller->id, $insert ? 1 : 2);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Yii::$app->customLog->log('Пользователь: ' . Yii::$app->user->identity->getFullName() . ' удалил группу проектов: ' . $this->title, $this->toArray(), Yii::$app->controller->id,3);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'sort_order'], 'required'],
            [['sort_order', 'status_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'group_id' => 'Группы',
            'title' => 'Название',
            'sort_order' => 'Порядок сортировки',
            'status_id' => 'Статус',
        ];
    }

    /**
     * Gets query for [[ProjectToGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectToGroups()
    {
        return $this->hasMany(ProjectToGroup::class, ['group_id' => 'group_id']);
    }

    /**
     * Get all project that was asigned to this group
     *
     * @param integer $id group id
     * @return array;
     */
    public function getAsignedProjects($id)
    {
        return Project::find()->joinWith('projectToGroups')->where(['project_to_group.group_id' => $id])->asArray()->all();
    }

    /**
     * Get all project that wasn`t asigned to this group
     *
     * @param integer $id group id
     * @return array;
     */
    public function getNotAsignedProjects($id)
    {
        return Project::find()->where(['NOT IN', 'id', ArrayHelper::getColumn($this->getAsignedProjects($id), 'id')])->asArray()->all();
    }

    /**
     * Get structured data for return on view file
     *
     * @param integer $id group id
     * @return array;
     */
    public function getAsignedAndNotAsignedData($id)
    {
        return [
            'assigned' => ArrayHelper::map($this->getAsignedProjects($id), 'id', 'title'),
            'available' => ArrayHelper::map($this->getNotAsignedProjects($id), 'id', 'title'),
        ];
    }

    /**
     * Get all project group data in format ['id' => 'title']
     *
     * @return array;
     */
    public static function getProjectGroupArrayMap()
    {
        return ArrayHelper::map(static::find()->select(['group_id', 'title'])->where(['status_id' => ProjectHelper::ACTIVE])->asArray()->all(),'group_id', 'title');
    }

    /**
     * Add projects to oroject group
     *
     * @param integer $id of project group
     * @param array $projects list of projects id
     */
    public function addProjects($id, $projects) : void
    {
        foreach ($projects as $project_id) {
            $proj2Group = new ProjectToGroup();
            $proj2Group->project_id = $project_id;
            $proj2Group->group_id = $id;
            $proj2Group->save();
        }
    }

    /**
     * Remove projects from project group
     *
     * @param integer $id of project group
     * @param array $projects list of projects id
     */
    public function removeProjects($id, $projects) : void
    {
        ProjectToGroup::deleteAll(['and',
            ['group_id' => $id],
            ['in', 'project_id', $projects]
        ]);
    }
}
