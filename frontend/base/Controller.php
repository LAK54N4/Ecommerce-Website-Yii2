<?php 

namespace frontend\base;

use common\models\CartItem;
use Yii;

/**
 * Class Controller
 * @package frontend\base
 */

class Controller extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        if(isGuest()) {
            $cartItems = \Yii::$app->session->get(CartItem::SESSION_KEY, []);
            $sum = 0;
            foreach ($cartItems as $cartItem) {
                $sum += $cartItem['quantity']; 
            } 
        } else {
            $sum = CartItem::findBySql(
                "SELECT SUM(quantity) FROM cart_items WHERE created_by = :userId", ['userId' => Yii::$app->user->id]
            )->scalar();
        }
        $this->view->params['cartItemCount'] = $sum; 
        return parent::beforeAction($action);
    }
}
?>