<?php

namespace app\models;

use Codeception\Scenario;
use yii\base\Model;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use Yii;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $user_id
 * @property int $category_id
 * @property int $year
 * @property string $author
 * @property int $status
 *
 * @property Category $category
 * @property BookImage[] $images
 * @property User $user
 */
class Book extends \yii\db\ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    const STATUSES = [
        0 => "Новая",
        1 => "Одобрена",
        2 => "Отклонена",
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description', 'category_id', 'year', 'author'], 'required'],
            [['user_id', 'category_id', 'year', 'status'], 'integer'],
//            ['date_str', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['name', 'author'], 'string', 'max' => 255],
            ['description', 'string', 'max' => 500],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg,jpeg',/*'maxSize'=>'2*1024*1024'*/],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'description' => 'Краткое описание книги',
            'user_id' => 'Пользователь',
            'category_id' => 'Жанр',
            'year' => 'Год',
            'author' => 'Автор',
            'imageFile' => 'Добавьте картинку',
            'status' => 'Статус книги',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Images]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(BookImage::class, ['book_id' => 'id'])->orderBy('id DESC');
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert) {
            $this->user_id = Yii::$app->user->identity->id;
        }

        if ($insert && Yii::$app->user->identity->isAdmin) {
            $this->status = 1; //одобрена
        } elseif (!Yii::$app->user->identity->isAdmin) {
            $this->status = 0; //если юзер не админ создает или обновляет книгу, то она всегда новая (на модерацию)
        }
        return true;
    }

    public function upload()
    {
        if ($this->validate()&& $this->imageFile) {
            if ($this->imageFile->saveAs('uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension)) {
                $image = new BookImage();
                $image->book_id = $this->id;
                $image->name = 'uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension;
                return $image->save();
            }
            return true;
        } else {
            return false;
        }
    }

    public function  getPreview(): string
    {
        return $this->images ? $this->images[0]->name : 'images/default.png';

    }

    public static function findById($id): ?Book
    {
        if (($model = Book::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public static function statusOptions() {
        return self::STATUSES;
    }
}
