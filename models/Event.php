<?php

namespace app\models;
/**
 * This is the model class for table "delivery".
 *
 * @property int $id
 * @property string $name
 * @property string $date_start
 * @property string $date_end
 * @property string $description
 * @property string $address
 *
 */
class Event extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'events';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'date_start', 'date_end'], 'required'],
            [['name'], 'string', 'max' => 255],
            ['date_start', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['date_end', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Заголовок',
            'date_start' => 'Дата начала',
            'date_end' => 'Дата конца',
            'description' => 'Описание',
            'address' => 'Адрес',
        ];
    }
}
