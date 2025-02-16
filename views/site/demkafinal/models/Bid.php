<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bid".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $comment
 * @property string $created_at
 * @property string $date_start
 * @property string $date_end
 * @property int $status_id
 * @property int $user_id
 * @property int $category_id
 *
 * @property Category $category
 * @property ImageAdmin[] $imageAdmins
 * @property Image[] $images
 * @property Status $status
 * @property User $user
 */
class Bid extends \yii\db\ActiveRecord
{

    const SCENARIO_IMAGE='imageFile';
    const SCENARIO_COMMENT='comment';
    public $imageFile;
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
            [['name', 'description', 'date_start', 'date_end', 'category_id'], 'required'],
            [['created_at', 'date_start', 'date_end'], 'safe'],
            [['date_start', 'date_end'], 'datetime','format'=>'php:Y-m-d H:i:s'],
            [['status_id', 'user_id', 'category_id'], 'integer'],
            [['name', 'description', 'comment'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::class, 'targetAttribute' => ['status_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['comment'], 'required','on'=>Bid::SCENARIO_COMMENT],
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg','on'=>Bid::SCENARIO_IMAGE],
            ['date_end', 'compare', 'compareAttribute' => 'date_start', 'operator' => '>=', 'type' => 'datetime'],
       

        
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
            'description' => 'Description',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'date_start' => 'Date Start',
            'date_end' => 'Date End',
            'status_id' => 'Status ID',
            'user_id' => 'User ID',
            'category_id' => 'Category ID',
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
     * Gets query for [[ImageAdmins]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImageAdmins()
    {
        return $this->hasMany(ImageAdmin::class, ['bid_id' => 'id']);
    }

    /**
     * Gets query for [[Images]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(Image::class, ['bid_id' => 'id']);
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
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
    if($insert){
        $this->user_id=Yii::$app->user->identity->id;
        $this->created_at=date('Y-m-d H:i:s');
        $this->status_id=1;
    }
        // ...custom code here...
        return true;
    }

    public function upload()
    {
        if ($this->validate() && $this->imageFile) {
            if($this->imageFile->saveAs('uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension))
            if( !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin) $image= new ImageAdmin();
            else  $image= new Image();
            $image->bid_id=$this->id;
            $image->name='uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension;
            $image->save();
            return true;
        } else {
            return false;
        }
    }

    public function preview(){
        return $this->images ? $this->images[0]->name :'';
    }
}
