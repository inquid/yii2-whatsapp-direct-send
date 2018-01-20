<?php

namespace app\modules\InquidSupport\models;
use borales\extensions\phoneInput\PhoneInputValidator;

/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 1/19/18
 * Time: 11:07 PM
 */

class Whatsapp extends \yii\base\Model
{
    public $phone;
    public $message;
    public $country;

    public function rules()
    {
        return [
            [['phone'], 'string'],
            [['phone'], PhoneInputValidator::className()],
        ];
    }
}