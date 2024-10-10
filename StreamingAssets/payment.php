<?php
class Payment {
    const APP_PUBLIC_KEY = "CQABLLLGDIHBABABA";
    const APP_SECRET_KEY = "169078564D00FAC60CA3F20F";

    const ERROR_TYPE_CALLBACK_INVALID_PYMENT = 3;
    const ERROR_TYPE_PARAM_SIGNATURE = 104;

    private static $errors = array(
        1 => "UNKNOWN: please, try again later. If error repeats, contact application support team.",
        2 => "SERVICE: service temporary unavailible. Please try again later",
        3 => "CALLBACK_INVALID_PAYMENT: invalid payment data. Please try again later. If error repeats, contact application support team. ",
        9999 => "SYSTEM: critical system error. Please contact application support team.",
        104 => "PARAM_SIGNATURE: invalid signature. Please contact application support team."
    );

    private static $catalog = array(
        "ShopData_energy_2" => 49,
        "ShopData_energy_3" => 99,
        "ShopData_energy_4" => 199,
        "ShopData_energy_5" => 499,

        "ShopData_money_2" => 49,
        "ShopData_money_3" => 99,
        "ShopData_money_4" => 199,
        "ShopData_money_5" => 499,
        
        "SpecialOffer_1" => 799,
        "SpecialOffer_2" => 799,
        "SpecialOffer_3" => 799,
        "SpecialOffer_4" => 799,
        "SpecialOffer_5" => 799
    );

    public static function calcSignature($request){
        $tmp = $request;
        unset($tmp["sig"]);
        ksort($tmp);
        $resstr = "";
        foreach($tmp as $key=>$value){
            $resstr = $resstr.$key."=".$value;
        }
        $resstr = $resstr.self::APP_SECRET_KEY;
        return md5($resstr); 
    }

    public static function checkPayment($productCode, $amount) {
        return array_key_exists($productCode, self::$catalog) && (self::$catalog[$productCode] == $amount);
    }
    
    public static function returnPaymentOK(){
        $rootElement = 'callbacks_payment_response';

        $dom = self::createXMLWithRoot($rootElement);
        $root = $dom->getElementsByTagName($rootElement)->item(0);
        
        // добавление текста "true" в тег <callbacks_payment_response> 
        $root->appendChild($dom->createTextNode('true')); 
        
        // генерация xml 
        $dom->formatOutput = true;
        $rezString = $dom->saveXML();
        
        // установка заголовка
        header('Content-Type: application/xml');
        // вывод xml
        print $rezString;
    }
    
    public static function returnPaymentError($errorCode){
        $rootElement = 'ns2:error_response';

        $dom = self::createXMLWithRoot($rootElement);
        $root = $dom->getElementsByTagName($rootElement)->item(0);
        // добавление кода ошибки и описания ошибки
        $el = $dom->createElement('error_code');
        $el->appendChild($dom->createTextNode($errorCode));
        $root->appendChild($el);
        if (array_key_exists($errorCode, self::$errors)){
            $el = $dom->createElement('error_msg');
            $el->appendChild($dom->createTextNode(self::$errors[$errorCode]));
            $root->appendChild($el);
        } 
            
        // генерация xml 
        $dom->formatOutput = true;
        $rezString = $dom->saveXML();
        
        // добавление необходимых заголовков
        header('Content-Type: application/xml');
        // ВАЖНО: если не добавить этот заголовок, система может некорректно обработать ответ
        header('invocation-error:'.$errorCode);
        // вывод xml
        print $rezString;
    }

    public static function saveTransaction() {
        // Реализация сохранения данных транзакции
    }
    
    private static function createXMLWithRoot($root){
        // создание xml документа
        $dom = new DomDocument('1.0'); 
        // добавление корневого тега
        $root = $dom->appendChild($dom->createElement($root));
        $attr = $dom->createAttribute("xmlns:ns2");
        $attr->value = "http://api.forticom.com/1.0/";
        $root->appendChild($attr);
        return $dom;
    }
}

if (array_key_exists("product_code", $_GET) && array_key_exists("amount", $_GET) && array_key_exists("sig", $_GET)){
    if (Payment::checkPayment($_GET["product_code"], $_GET["amount"])){
        $calculatedSignature = Payment::calcSignature($_GET);
        
        if ($_GET["sig"] == $calculatedSignature){
            Payment::saveTransaction();
            Payment::returnPaymentOK();
        } else {
            // здесь можно что-нибудь сделать, если подпись неверная
            Payment::returnPaymentError(Payment::ERROR_TYPE_PARAM_SIGNATURE);
            // print("Неверная сигнатура. Рассчитанная: " . $calculatedSignature . ", переданная: " . $_GET["sig"]);
            // print_r($_GET);
        }
    } else {
        // здесь можно что-нибудь сделать, если информация о покупке некорректна
        Payment::returnPaymentError(Payment::ERROR_TYPE_CALLBACK_INVALID_PYMENT);
        // print("Некорректный товар или сумма: product_code = " . $_GET["product_code"] . ", amount = " . $_GET["amount"]);
        // print_r($_GET);
    }
} else {
    // здесь можно что-нибудь сделать, если информация о покупке или подпись отсутствуют в запросе
    Payment::returnPaymentError(Payment::ERROR_TYPE_CALLBACK_INVALID_PYMENT);
   
    // print("Отсутствуют необходимые параметры: ");
    // print_r($_GET);
}
?>