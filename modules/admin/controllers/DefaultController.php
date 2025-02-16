<?php

namespace app\modules\admin\controllers;

use app\models\Bid;
use app\models\BidSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UploadForm;
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
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) return $this->goHome();
        $searchModel = new BidSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $modelComplete= new Bid();
        $modelConfirm = new Bid();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'modelComplete'=>$modelComplete,
            'modelConfirm'=>$modelConfirm,
        ]);
    }

    /**
     * Displays a single Bid model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Bid model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    

    public function actionUpdateComment($id)
    { 
        $model = $this->findModel($id);
         if($model->status_id!=1) {return $this->goHome();}
       
        if ($this->request->isPost) {
             
            if ($model->load($this->request->post())) { 
                 
                  $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
                  $model->status_id=2;
        
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
        if($model->status_id!=1) {return $this->goHome();}
        if ($this->request->isPost) {
           
        
            if ($model->load($this->request->post())) {  
                  $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
                   $model->status_id=3;
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
     * Updates an existing Bid model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
