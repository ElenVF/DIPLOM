<?php

namespace app\modules\admin\controllers;

use app\models\Bid;
use app\models\BidSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * BidController implements the CRUD actions for Bid model.
 */
class DefaultController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Bid models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BidSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Bid model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id){
    if(Bid::findOne(['id'=>$id,'user_id'=>Yii::$app->user->identity->id]))
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

else return $this->goHome();
}

    /**
     * Creates a new Bid model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
  
   
    /**
     * Updates an existing Bid model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateComment($id)
    {
        $model = $this->findModel($id);
        $model->scenario=Bid::SCENARIO_COMMENT;
        if($model->status_id!=1)return $this->goHome();
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) ) {
                $model->status_id=2;
                $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
                $model->date_start=Yii::$app->formatter->asDate($model->date_start,'php:Y-m-d H:i:s');
                $model->date_end=Yii::$app->formatter->asDate($model->date_end,'php:Y-m-d H:i:s');
                if($model->save()){
                $model->upload();
                return $this->redirect(['view', 'id' => $model->id]);}
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('update_comment', [
            'model' => $model,
        ]);
    }
    public function actionUpdateImage($id)
    {
        $model = $this->findModel($id);
        $model->scenario=Bid::SCENARIO_COMMENT;
        if($model->status_id!=2) return $this->goHome();
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) ) {
                $model->status_id=3;
                $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
                $model->date_start=Yii::$app->formatter->asDate($model->date_start,'php:Y-m-d H:i:s');
                $model->date_end=Yii::$app->formatter->asDate($model->date_end,'php:Y-m-d H:i:s');
                if($model->save()){
                $model->upload();
                return $this->redirect(['view', 'id' => $model->id]);}
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('update_image', [
            'model' => $model,
        ]);
    }

    

    /**
     * Deletes an existing Bid model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if($bid=Bid::find(['id'=>$id,'user_id'=>Yii::$app->user->identity->id,'status_id'=>1])){
        $bid->delete();

        return $this->redirect(['index']);}
        else return $this->goHome();
    }

    /**
     * Finds the Bid model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Bid the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bid::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actions(){
        if(Yii::$app->user->isGuest || !Yii::$app->user->identity->isAdmin) $this->goHome();
    
    }
    
}
