<?php

namespace app\controllers;

use app\models\Bid;
use app\models\Book;
use app\models\Delivery;
use app\models\Event;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;
use yii\bootstrap5\ActiveForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionBooks()
    {
        return $this->render('books');
    }

    /**
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionFetchBook()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /** @var Book $book */

        try {
            $book = Book::findById((int)$this->request->post('id'));
        } catch (NotFoundHttpException $e) {
            return [];
        }
        if ($book->status != 1) return [];

        return [
            'book' => [
                'id' => $book->id,
                'name' => $book->name,
                'author' => $book->author,
                'year' => $book->year,
                'categoryName' => $book->category->name,
                'userName' => $book->user->name,
                'description' => $book->description,
                'preview' => $book->getPreview(),
                'user_id' => $book->user_id,
            ],
            'user_id' => Yii::$app->user->id,
        ];
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->login()) {
                Yii::$app->session->setFlash('success','Вы успешно авторизованы');
                return $this->redirect('/account');
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionRegistration()
    {
        $model = new User();
        $model->scenario = User::SCENARIO_CHECK_PASSWORDS;
        if ($model->load(Yii::$app->request->post())) {

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if($model->save()){
                Yii::$app->session->setFlash('success','вы успешно зарегистрированы');
                if(Yii::$app->user->login($model)) return $this->redirect('/account');
       } }
        return $this->render('registration', [
            'model' => $model,
        ]);
    }

    public function actionFetchBooks()
    {
        $items = [];
        $countAll = 0;
        if ($this->request->isPost) { // && $this->request->isAjax
            Yii::$app->response->format = Response::FORMAT_JSON;

            $user_id = (int)$this->request->post('user_id');

            //получаем общее кол-во книг $countAll для того чтобы показать или скрыть кнопку Показать еще
            if (!$user_id) {
                $q = Yii::$app->db->createCommand('SELECT COUNT(*) FROM book WHERE status=1');
            } else {
                $q = Yii::$app->db->createCommand(
                    'SELECT COUNT(*) FROM book WHERE status=1 AND user_id=:user_id',
                    [':user_id' => $user_id]);
            }
            $countAll = $q->queryScalar();

            $page = (int)$this->request->post('page');
            $orderBy = $this->request->post('orderBy');
            $limit = 12;

            $q = Book::find()->where(['status' => 1]);
            if ($user_id) {
                $q = $q->andWhere(['user_id' => $user_id]);
            }
            $books = $q->with('images')->joinWith('category')
                ->offset($page * $limit)->limit($limit)->orderBy($orderBy)->all();

            if ($books) {
                /** @var Book $book */
                foreach ($books as $book) {
                    $item = $book->toArray();
                    $item['preview'] = $book->getPreview();
                    $items[] = $item;
                }
            }
        }
        return ['items' => $items, 'countAll' => $countAll];
    }

    /**
     * Создание заявки, отправка письма юзерам
     *
     * @return array
     */
    public function actionBidSubmit(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Bid();
        $model->from_book_id = null;
        $model->to_book_id = (int)$this->request->post('to_book_id');
        $model->from_user_id = Yii::$app->user->id;
        $model->to_user_id = (int)$this->request->post('to_user_id');
        $model->delivery_id = (int)$this->request->post('delivery');
        $model->description = '';
        $errors = ActiveForm::validate($model);

        if (!$model->fromUser->books) {
            $errors[] = 'У Вас нет ни одной книги для обмена! Добавьте книги в профиль!';
        }
        if (!$model->fromUser->address && $model->delivery_id != Delivery::SELF_TAKE_ID) {
            $errors[] = 'У Вас не указан адрес доставки! Заполните адрес в профиле!';
        }

        if (!$errors) {
            if ($model->save()) {
                $emails = [$model->toUser->email, Yii::$app->user->identity->email];
                $messages = [];
                foreach ($emails as $email) {
                    $messages[] = Yii::$app->mailer->compose()->setFrom('exchange.book@yandex.ru') //'exchange.book@mail.ru'
//                        ->setTo($model->toUser->email)
                        ->setSubject('Заявка на обмен книг')
                        ->setHtmlBody('Поступила заявка на обмен книги '
                            . '<strong>' . $model->toBook->name . '. ' . $model->toBook->author . '</strong><br>'
                            . 'от пользователя ' . Yii::$app->user->identity->name . '<br>'
                            . Yii::$app->user->identity->phone . '<br>' . Yii::$app->user->identity->email . '<br>'
                            . 'Способ доставки: ' . $model->delivery->name . '<br>'
                            . ($model->delivery_id == Delivery::SELF_TAKE_ID ? $model->toUser->address : Yii::$app->user->identity->address))
                        ->setTo($email);
                }
                $result = Yii::$app->mailer->sendMultiple($messages);
                return ['ok' => 1, 'errors' => $errors, 'mail' => $result];
            }
        }
        return ['errors' => $errors];
    }

    /**
     * Страница объявлений
     *
     * @return string
     */
    public function actionEvents()
    {
        return $this->render('events', [
            'items' => Event::find()->where(['>=', 'date_start', date('Y-m-d')])->orderBy('date_start DESC, date_end DESC')->limit(4)->all()
        ]);
    }

    /**
     * Страница поиска
     *
     * @return string
     */
    public function actionSearch()
    {
        $search = $this->request->get('search');
        $books = Book::find()->where(['like', 'book.name', $search])->orWhere(['like', 'book.author', $search])
            ->with('images')->joinWith('category')->orderBy('book.name')->all();
        return $this->render('search', [
            'items' => $books
        ]);
    }

    /**
     * Получить массив доставок для формы заявки
     *
     * @return array
     */
    public function actionFetchDeliveries(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $deliveries = Delivery::find()->all();
        $items = [];
        $bookUser = User::findOne(['id' => $this->request->post('to_user_id')]);
        if ($bookUser && $deliveries) {
            foreach ($deliveries as $delivery) {
                $item = $delivery->toArray();
                //если самовывоз - то добавляем адрес текущего юзера
                //иначе - адрес хозяина книги
                if ($delivery->id != Delivery::SELF_TAKE_ID) $item['name'] .= ' (' . Yii::$app->user->identity->address . ')';
                else $item['name'] .= ' (' . $bookUser->address . ')';
                $items[] = $item;
            }
        }
        return ['items' => $items, 'ok' => 1];
    }
}
