<?php

namespace backend\controllers;

use backend\models\ProjectToGroup;
use Yii;
use backend\models\ProjectGroup;
use backend\models\ProjectGroupSearch;
use yii\base\BaseObject;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Project;

/**
 * ProjectGroupController implements the CRUD actions for ProjectGroup model.
 */
class ProjectGroupController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ProjectGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectGroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProjectGroup model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $projects = new ProjectGroup();

        return $this->render('view', [
            'model' => $this->findModel($id),
            'assigned_and_not' => $projects->getAsignedAndNotAsignedData($id)
        ]);
    }

    /**
     * Creates a new ProjectGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProjectGroup();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->group_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProjectGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->group_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProjectGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Asign project group to project
     * @param integer $id
     * @return array
     */
    public function actionAssign($id)
    {
        $projects = Yii::$app->getRequest()->post('projects', []);
        $model = new ProjectGroup();
        $model->addProjects($id, $projects);
        Yii::$app->getResponse()->format = 'json';
        return $model->getAsignedAndNotAsignedData($id);
    }

    /**
     * Remove project group from project
     * @param integer $id
     * @return array
     */
    public function actionRemove($id)
    {
        $projects = Yii::$app->getRequest()->post('projects', []);
        $model = new ProjectGroup();
        $model->removeProjects($id, $projects);
        Yii::$app->getResponse()->format = 'json';
        return $model->getAsignedAndNotAsignedData($id);
    }

    /**
     * Finds the ProjectGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProjectGroup::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
