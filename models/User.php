<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $login
 * @property string $name
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property string $phone
 * @property int $role_id
 * @property string $address
 *
 * @property Book[] $books
 * @property Bid[] $bs
 * @property Role $role
 */
class User extends \yii\db\ActiveRecord  implements \yii\web\IdentityInterface
{

    public $terms;
    public $password_repeat;
    public $password;

    const SCENARIO_CHECK_PASSWORDS = 'SCENARIO_CHECK_PASSWORDS';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login', 'name', 'email', 'phone'], 'required'],
            [['password', 'password_repeat'], 'required', 'on' => self::SCENARIO_CHECK_PASSWORDS],
            [[ 'terms'], 'required', 'requiredValue'=>1,'message'=>'поставьте галочку'],
            [['login', 'name', 'email', 'password_repeat', 'phone', 'address'], 'string', 'max' => 255],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::class, 'targetAttribute' => ['role_id' => 'id']],
            
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
            ['login', 'unique', 'when' => [$this, 'whenSelfUnique']],
            ['email', 'unique', 'when' => [$this, 'whenSelfUnique']],
            ['login', 'match', 'pattern' => '/^[A-Za-z\d\-]+$/u','message'=>'Логин должен содержать латинские буквы и цифры'],
            ['name', 'match', 'pattern' => '/^[А-ЯЁа-яё\s\-]+$/u','message'=>'Имя должно содержать кириллицу и пробелы'],
             ['phone', 'match', 'pattern' => '/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/u','message'=>'Телефон должен быть в формате +7(999)999-99-99'],
            ['password', 'match', 'pattern' => '/^[A-Za-z\d\-]{8,}$/u','message'=>'Пароль должен быть длиной от 8 символов и содержать латинские буквы и цифры'],
        ];
    }

    public function whenSelfUnique($model, $attribute): bool
    {
        if (!\Yii::$app->user->isGuest) {
            return \Yii::$app->user->identity->$attribute !== $model->$attribute;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Логин',
            'name' => 'Имя',
            'email' => 'Email',
            'password' => 'Пароль',
            'password_repeat' => 'Повтор пароля',
            'phone' => 'Телефон',
            'terms' => 'Согласие на обработку персональных данных',
            'address' => 'Адрес',
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
     * Gets query for [[Books]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Book::class, ['user_id' => 'id']);
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
        return self::findOne(['id'=>$id]) ?? null;
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
    {  return self::findOne(['login'=>$login]) ?? null;
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
        if ($insert){
            $this->role_id=1;
            $this->auth_key=Yii::$app->getSecurity()->generateRandomString();
            $this->password_hash=Yii::$app->getSecurity()->generatePasswordHash($this->password);
            
        }
        return true;
    }

    public function getIsAdmin()
    {
        return $this->role_id == 2;
    }
}
