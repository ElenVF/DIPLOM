<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $name
 * @property string $login
 * @property string $email
 * @property string $phone
 * @property string $auth_key
 * @property string $password_hash
 * @property int $role_id
 *
 * @property Bid[] $bs
 * @property Role $role
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public $terms;
    public $password_repeat;
    public $password;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['name', 'login', 'email', 'phone', 'password', 'password_repeat'], 'required'],
            [['terms'], 'required', 'requiredValue'=>1, 'message'=>'дайте согласие на обработку персональных данных'],
            
            [['name', 'login', 'email', 'phone', 'password', 'password_repeat'], 'string', 'max' => 255],
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
            ['email', 'email'],
            ['email', 'unique'],
            ['login', 'unique'],
            ['name', 'match', 'pattern' => '/^[А-ЯЁа-яё\-\s]+$/u','message'=>'кирилл,дефис,пробел'],
            ['login', 'match', 'pattern' => '/^[A-Za-z\-\d]+$/u','message'=>'латин,дефис,цифры'],
            ['password', 'match', 'pattern' => '/^[A-Za-z\-\d]+$/u','message'=>'латин,дефис,цифры'],
            ['phone', 'match', 'pattern' => '/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/u','message'=>'латин,дефис,цифры'],
            
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
            'login' => 'Login',
            'email' => 'Email',
            'phone' => 'Phone',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'role_id' => 'Role ID',
        ];
    }

    /**
     * Gets query for [[Bs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBs()
    {
        return $this->hasMany(Bid::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }



    
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id'=>$id])?? null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        
        return null;
    }

    /**
     * Finds user by login
     *
     * @param string $login
     * @return static|null
     */
    public static function findByLogin($login)
    {
        return self::findOne(['login'=>$login])?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }


    public function beforeSave($insert)
{
    if (!parent::beforeSave($insert)) {
        return false;
    }
if($insert){
    $this->role_id=1;
    $this->password_hash=Yii::$app->getSecurity()->generatePasswordHash($this->password);
    $this->auth_key=Yii::$app->getSecurity()->generateRandomString();
}
    // ...custom code here...
    return true;
}
public function getIsAdmin(){
    return $this->role_id==2;
    
}
public static function options(){
    return ArrayHelper::map(self::find()->orderBy('name')->all(), 'id', 'name'); 
}

}
