<?php

namespace common\models;

use Exception as GlobalException;
use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "{{%orders}}".
 *
 * @property int $id
 * @property float $total_price
 * @property int $status
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property int $transaction_id
 * @property int|null $created_at
 * @property int|null $created_by
 *
 * @property OrderAddresses[] $orderAddresses
 * @property OrderItems[] $orderItems
 * @property User $createdAt
 * @property User $createdBy
 */
class Order extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_PAID = 1;
    const STATUS_FAILURED = 2;
    const STATUS_COMPLETED = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%orders}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['total_price', 'status', 'firstname', 'lastname', 'email', 'transaction_id'], 'required'],
            [['total_price'], 'number'],
            [['status', 'transaction_id', 'created_at', 'created_by'], 'integer'],
            [['firstName', 'lastName'], 'string', 'max' => 45],
            [['email'], 'string', 'max' => 255],
            [['created_at'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_at' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'total_price' => 'Total Price',
            'status' => 'Status',
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'email' => 'Email',
            'transaction_id' => 'Transaction ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[OrderAddresses]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderAddressesQuery
     */
    public function getOrderAddresses()
    {
        return $this->hasOne(OrderAddress::class, ['order_id' => 'id']);
    }

    /**
     * Gets query for [[OrderItems]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\OrderItemsQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItem::class, ['order_id' => 'id']);
    }

    /**
     * Gets query for [[CreatedAt]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */

    /*
    public function getCreatedAt()
    {
        return $this->hasOne(User::class, ['id' => 'created_at']);
    } 
    */

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\OrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\OrderQuery(get_called_class());
    }

    public function saveAddress($postDate){
        $orderAddress = new OrderAddress();
        $orderAddress->order_id = $this->id;
        if ($orderAddress->load($postDate) && $orderAddress->save()) {
            return true;
        }
        throw new Exception("Could not save order address: ".implode("<br>", $orderAddress-> getFirstErrors()));
    }

    public function saveOrderItems()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $cartItems = CartItem::getItemsForUser(currUserId());
        foreach ($cartItems as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->product_name = $cartItem['name'];
            $orderItem->product_id = $cartItem['id'];
            $orderItem->unit_price = $cartItem['price'];
            $orderItem->order_id = $this->id;
            $orderItem->quantity = $cartItem['quantity'];
            if (!$orderItem->save()) {
                throw new Exception("Order item was not saved: " . implode('<br>', $orderItem->getFirstErrors()));
            }
        }
        $transaction->commit();
        return true;
    }

    public function getItemsQuantity() {
        return $sum = CartItem::findBySql(
            "SELECT SUM(quantity) FROM order_items WHERE order_id = :orderId", ['orderId' => $this->id]
        )->scalar();
    }

    /*
    public function beforeSave($insert)
    {
        // $transaction = Yii::$app->db->beginTransaction();
        $saved = parent::beforeSave($insert); // TODO: Change the autogenerated stub
        
        if (!$saved) return $saved;
        
        $cartItems = CartItem::getItemsForUser(currUserId());
        foreach($cartItems as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->product_name = $cartItem['name'];
            $orderItem->product_id = $cartItem['id'];
            $orderItem->unit_price = $cartItem['price'];
            $orderItem->order_id = $cartItem['name'];
            $orderItem->quantity = $cartItem['quantity'];
            if($orderItem->save()){
                throw new Exception("Order item was not saved".implode('<br>'));
            }
        }

        //$transaction->commit();
        return $saved;

    }
    */

    /*
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

    }
    */
    /*
    public function save($runValidation = true, $attributeNames = null) {
        $saved = parent::save($runValidation, $attributeNames); // TODO: Change the autogenerated stub
    }
    */
}
