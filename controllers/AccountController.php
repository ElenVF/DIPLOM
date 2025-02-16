<?php

namespace app\controllers;

use app\models\Bid;
use app\models\Book;
use app\models\Category;
use app\models\Delivery;
use app\models\Event;
use app\models\Status;
use app\models\User;
use Yii;
use yii\bootstrap5\ActiveForm;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * BidController implements the CRUD actions for Bid model.
 */
class AccountController extends Controller
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
                    'class' => VerbFilter::class,
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
        return $this->render('account');
    }
    public function actionFetchBids(): array
    {
        $items = [];
        $myId = Yii::$app->user->identity->id;
        $from = (int)$this->request->post('from');
        $field = 'to_user_id';
        if ($from == 1) $field = 'from_user_id';
        $countAll = Yii::$app->db->createCommand(
            'SELECT COUNT(*) FROM bid WHERE ' . $field . '=:user_id',
            [':user_id' => $myId])->queryScalar();
        $page = (int)$this->request->post('page');
        $limit = 20;
        $bids = Bid::find()->where([$field => $myId])
            ->offset($page * $limit)->limit($limit)->orderBy('created_at DESC')->all();
        if ($bids) {
            /** @var Bid $bid */
            foreach ($bids as $bid) {
                $item = $bid->toArray();
                $item['createdFormat'] = date( 'd.m.Y H:i:s', strtotime($bid->created_at));
                $item['fromBookName'] = $bid->from_book_id ? $bid->fromBook->author . ' ' . $bid->fromBook->name : 'Не выбрана';
                $item['from_book_id'] = $bid->from_book_id;
                $item['from_user_id'] = $bid->from_user_id;
                $item['fromUserName'] = $bid->fromUser->name;
                $item['toBookName'] = $bid->toBook->author . ' ' . $bid->toBook->name;
                $item['toUserName'] = $bid->toUser->name;
                $item['fromUserData'] = $bid->fromUser->phone . ' ' . $bid->fromUser->email;
                $item['toUserData'] = $bid->toUser->phone . ' ' . $bid->toUser->email;
                $item['deliveryName'] = $bid->delivery->name;
                $item['statusName'] = $bid->status->name;
                $item['status_id'] = $bid->status_id;
                $items[] = $item;
            }
        }
        return ['items' => $items, 'countAll' => $countAll];
    }

    public function actionSaveFromBook(): array
    {
        try {
            $model = $this->findBid((int)$this->request->post('bid_id'));
            $model->from_book_id = (int)$this->request->post('from_book_id');
            if ($model->save()) {
                return ['ok' => 1];
            }
        } catch (NotFoundHttpException $e) {
            return ['ok' => 0, 'message' => 'Заявка не найдена #' . (int)$this->request->post('bid_id')];
        }
        return ['ok' => 0];
    }

    public function actionChangeBidStatus(): array
    {
        try {
            $bid = $this->findBid((int)$this->request->post('bid_id'));
            $statusCode = $this->request->post('status');
            $status = Status::findByName($statusCode == 'complete' ? 'одобрена' : 'отклонена');
            //для подтверждения заявки обязательно надо выбрать книгу для обмена
            if ($statusCode == 'complete' && !$bid->from_book_id) {
                return ['ok' => 0, 'message' => 'Не выбрана книга для обмена!'];
            } elseif ($status && $status->id) {
                $bid->status_id = $status->id;
                if ($bid->save()) {
                    return ['ok' => 1];
                }
            }
        } catch (NotFoundHttpException $e) {
            return ['ok' => 0, 'message' => 'Заявка не найдена #' . (int)$this->request->post('bid_id')];
        }
        return ['ok' => 0];
    }

    /**
     * Получить книги из бд, либо текущего юзера, либо все книги для админа из раздела Все книги
     *
     * @return array
     * @throws Exception
     */
    public function actionFetchBooks(): array
    {
        $items = [];
        $all = (int)$this->request->post('all');
        $page = (int)$this->request->post('page');
        $limit = 12;

        //получаем общее кол-во книг $countAll чтобы показать или скрыть кнопку Показать еще
        if ($all && Yii::$app->user->identity->isAdmin) {
            $q = Yii::$app->db->createCommand('SELECT COUNT(*) FROM book');
        } else {
            $q = Yii::$app->db->createCommand(
                'SELECT COUNT(*) FROM book WHERE user_id=:user_id',
                [':user_id' => Yii::$app->user->identity->id]);
        }
        $countAll = $q->queryScalar();

        $q = Book::find();
        if (!$all || !Yii::$app->user->identity->isAdmin) {
            $q = $q->where(['user_id' => Yii::$app->user->identity->id]);
        }
        $books = $q->with('images')->orderBy('status ASC')->offset($page * $limit)->limit($limit)->all();
        if ($books) {
            /** @var Book $book */
            foreach ($books as $book) {
                $item = $book->toArray();
                $item['preview'] = $book->getPreview();
                $item['categoryName'] = $book->category->name;
                $items[] = $item;
            }
        }
        return ['items' => $items, 'countAll' => $countAll];
    }

    /**
     * Удалить книгу
     * @return bool[]|false[]
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionDeleteBook(): array
    {
        try {
            $book = Book::findById((int)$this->request->post('id'));
            //админ может удалить любую книгу, остальные только свою
            if ($book->user_id == Yii::$app->user->id || Yii::$app->user->identity->isAdmin) $book->delete();
            $ok = true;
        } catch (NotFoundHttpException $e) {
            return ['ok' => false];
        }
        return ['ok' => $ok];
    }

    public function actionProfileSave(): array
    {
        $errors = [];
        $model = User::findIdentity(Yii::$app->user->identity->id);
        $model->terms = 1;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->password) {
                $model->scenario = User::SCENARIO_CHECK_PASSWORDS;
            }
            $errors = ActiveForm::validate($model);
            if (!$errors) {
                if ($model->password) {
                    $model->auth_key = Yii::$app->getSecurity()->generateRandomString();
                    $model->password_hash = Yii::$app->getSecurity()->generatePasswordHash($model->password);
                }
                if ($model->save()) {
                    return ['ok' => 1, 'errors' => $errors];
                }
            }
        }
        return ['errors' => $errors];
    }
    public function actionProfileLoad(): array
    {
        if (!Yii::$app->user->isGuest) return [
            'login' => Yii::$app->user->identity->login,
            'name' => Yii::$app->user->identity->name,
            'email' => Yii::$app->user->identity->email,
            'phone' => Yii::$app->user->identity->phone,
            'address' => Yii::$app->user->identity->address,
        ];
        return [];
    }

    public function actionFetchCrudItems(): array
    {
        $class = $this->getCrudModel();
        if (!$class) return ['ok' => 0, 'message' => 'Ошибка'];
        $items = $class::find()->all();
        return ['items' => $items, 'ok' => 1];
    }
    public function actionSaveCrudItems(): array
    {
        $class = $this->getCrudModel();
        if (!$class) return ['ok' => 0, 'message' => 'Ошибка'];

        $ids = $this->request->post('ids');
        $names = $this->request->post('names');
        if ($ids && $names && is_array($ids) && is_array($names)) {
            foreach ($ids as $i => $id) {
                if ($id) $item = $class::findOne(['id' => $id]);
                else $item = new $class(['id' => $id]);
                $item->name = trim($names[$i]);
                if ($item->name) $item->save();
            }
        }
        return ['ok' => 1, 'message' => 'Данные сохранены!'];
    }
    public function actionDeleteCrudItem(): array
    {
        $class = $this->getCrudModel();
        if (!$class) return ['ok' => 0, 'message' => 'Ошибка'];
        $id = (int)$this->request->post('id');
        $item = $class::findOne(['id' => $id]);
        if ($item) $item->delete();
        return ['ok' => 1, 'message' => 'Данные сохранены!'];
    }
    public function getCrudModel()
    {
        $model = $this->request->post('model');
        switch ($model) {
            case 'categories': return Category::class;
            case 'delivery': return Delivery::class;
            default: return null;
        }
    }

    public function actionFetchEvents(): array
    {
        $items = [];
        $countAll = Yii::$app->db->createCommand('SELECT COUNT(*) FROM events')->queryScalar();

        $page = (int)$this->request->post('page');
        $limit = 20;
        $events = Event::find()->offset($page * $limit)->limit($limit)->orderBy('date_start DESC')->all();
        if ($events) {
            /** @var Event $event */
            foreach ($events as $event) {
                $item = $event->toArray();
                $item['dateStartFormat'] = date( 'd.m.Y H:i:s', strtotime($event->date_start));
                $item['dateEndFormat'] = date( 'd.m.Y H:i:s', strtotime($event->date_end));
                $items[] = $item;
            }
        }
        return ['items' => $items, 'countAll' => $countAll];
    }
    public function actionSaveEvent(): array
    {
        $errors = [];
        if (Yii::$app->user->identity->isAdmin) {
            $id = (int)$this->request->post('id');
            if ($id) $model = Event::findOne(['id' => $id]);
            else $model = new Event();
            if ($model) {
                $date_start = Yii::$app->request->post('date_start');
                $date_end = Yii::$app->request->post('date_end');
                $model->name = Yii::$app->request->post('name');
                $model->date_start = $date_start ? Yii::$app->formatter->asDate($date_start, 'php:Y-m-d H:i:s') : '';
                $model->date_end = $date_end ? Yii::$app->formatter->asDate($date_end, 'php:Y-m-d H:i:s') : '';
                $model->description = Yii::$app->request->post('description');
                $model->address = Yii::$app->request->post('address');
                $errors = ActiveForm::validate($model);
                if (!$errors) {
                    if ($model->save()) {
                        return ['ok' => 1, 'errors' => $errors];
                    }
                }
            }
        }
        return ['ok' => 0, 'errors' => $errors];
    }

    public function actionDeleteEvent(): array
    {
        if (Yii::$app->user->identity->isAdmin) {
            $item = Event::findOne(['id' => (int)$this->request->post('id')]);
            if ($item) $item->delete();
        }
        return ['ok' => 1, 'message' => 'Данные сохранены!'];
    }

    public function actionGetCounters(): array
    {
        return [
            'bookCounter' => Yii::$app->db->createCommand('SELECT COUNT(*) FROM book WHERE status=0')->queryScalar(),
            'bidCounter' => Yii::$app->db->createCommand('SELECT COUNT(*) FROM bid WHERE status_id=1 AND to_user_id=:user_id',
                [':user_id' => Yii::$app->user->identity->id])->queryScalar(),
        ];
    }

    /**
     * Finds the Bid model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Bid the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findBid($id): Bid
    {
        if (($model = Bid::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actions()
    {
        if (Yii::$app->request->isPost) Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) $this->goHome();
    }
}
