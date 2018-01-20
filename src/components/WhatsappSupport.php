<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 1/19/18
 * Time: 10:11 PM
 */

namespace app\modules\InquidSupport\components;

use app\modules\InquidSupport\models\Whatsapp;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Yii;
use yii\base\Component;


class WhatsappSupport extends Component
{
    const TAG = "/^\+[1-9]{1}[0-9]{3,14}$/";
    public $jsonCodes;

    public function getCountryCodes()
    {
        $data = file_get_contents('/countrycode.json');
        $this->jsonCodes = json_decode($data);
    }

    /**
     * @param Whatsapp $whatsapp
     * @return array|bool
     * @throws \Exception
     */
    public function sendWhatsapp($whatsapp)
    {
        if ($whatsapp->validate()) {
            return $whatsapp->errors;
        } else {
            $phoneUtil = PhoneNumberUtil::getInstance();
            try {
                $rawPhone = $phoneUtil->parse($whatsapp->phone, explode(":", $whatsapp->country)[1]);
            } catch (\Exception $e) {
                throw($e);
            }
            $rawPhone = $phoneUtil->format($rawPhone, PhoneNumberFormat::E164);
            $this->proceedSend($rawPhone, $whatsapp->message);
            return true;
        }
    }

    /**
     * @param $phonenumber
     * @param string $text
     * @return boolean
     */
    public function send($phonenumber, $text = "Hello There")
    {
        if ($phonenumber == null) {
            return false;
        }
        if ($phonenumber[0] != '+') {
            $phonenumber = '+' . $phonenumber;
        }
        if (preg_match(self::TAG, $phonenumber)) {
            $this->proceedSend($phonenumber, $text);
            return true;
        }
        return false;
    }

    /**
     * @param $phtoneNumber
     * @param $text
     * @return void
     */
    public function proceedSend($phtoneNumber, $text)
    {
        $url = !\Yii::$app->devicedetect->isDesktop() ? "https://web.whatsapp.com/send?text=" . $text . "&phone=" . $phtoneNumber : "whatsapp://send?text=" . $text . "&phone=" . $phtoneNumber;
        Yii::$app->getResponse()->redirect($url);
    }
}