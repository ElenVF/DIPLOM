<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "image_admin".
 *
 * @property int $id
 * @property string $name
 * @property int $bid_id
 *
 * @property Bid $bid
 */
class ImageAdmin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'image_admin';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'bid_id'], 'required'],
            [['bid_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['bid_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bid::class, 'targetAttribute' => ['bid_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'bid_id' => 'Bid ID',
        ];
    }

    /**
     * Gets query for [[Bid]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBid()
    {
        return $this->hasOne(Bid::class, ['id' => 'bid_id']);
    }
}
