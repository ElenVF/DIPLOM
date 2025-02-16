<?php

namespace app\models;

use Codeception\Scenario;
use yii\base\Model;
use yii\web\UploadedFile;
use Yii;

/**
 * This is the model class for table "bid".
 *
 * @property int $id
 * @property string $description
 * @property string $created_at
 * @property string $comment
 * @property int $status_id
 * @property int $from_user_id
 * @property int $from_book_id
 * @property int $to_user_id
 * @property int $to_book_id
 * @property int $delivery_id
 *
 * @property Status $status
 * @property User $fromUser
 * @property Book $fromBook
 * @property User $toUser
 * @property Book $toBook
 * @property Delivery $delivery
 */
class Bid extends \yii\db\ActiveRecord
{

    const SCENARIO_MESSAGE= 'comment';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bid';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_user_id', 'to_user_id', 'to_book_id', 'delivery_id'], 'required'],
            [['created_at'], 'safe'],
            [['status_id', 'from_user_id', 'to_user_id', 'from_book_id', 'to_book_id', 'delivery_id'], 'integer'],
            [['description', 'comment'], 'string', 'max' => 255],
            [['from_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['from_user_id' => 'id']],
            [['to_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['from_user_id' => 'id']],
            [['from_book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Book::class, 'targetAttribute' => ['from_book_id' => 'id']],
            [['to_book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Book::class, 'targetAttribute' => ['to_book_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::class, 'targetAttribute' => ['status_id' => 'id']],
            [[ 'comment'], 'required','on'=>BId::SCENARIO_MESSAGE],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Описание',
            'created_at' => 'Временная метка',
            'comment' => 'Комментарий',
            'status_id' => 'Статус',
            'from_user_id' => 'От кого',
            'to_user_id' => 'Кому',
            'from_book_id' => 'Книга',
            'to_book_id' => 'Книга в обмен',
            'delivery_id' => 'Доставка',
        ];
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::class, ['id' => 'status_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromUser()
    {
        return $this->hasOne(User::class, ['id' => 'from_user_id']);
    }
    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getToUser()
    {
        return $this->hasOne(User::class, ['id' => 'to_user_id']);
    }

    /**
     * Gets query for [[Book]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFromBook()
    {
        return $this->hasOne(Book::class, ['id' => 'from_book_id']);
    }

    /**
     * Gets query for [[Book]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getToBook()
    {
        return $this->hasOne(Book::class, ['id' => 'to_book_id']);
    }

    /**
     * Gets query for [[Book]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDelivery()
    {
        return $this->hasOne(Delivery::class, ['id' => 'delivery_id']);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert) {
            $this->from_user_id = Yii::$app->user->identity->id;
            $this->created_at = date('Y-m-d H:i:s');
            $this->status_id = 1;
        }
        return true;
    }
}
