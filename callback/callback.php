<?php
    $doc_root = realpath(dirname(__FILE__).'/../');
	require($doc_root."/bitrix/modules/main/include/prolog_before.php");
?>
<?php
	require_once "amocrm_functions/functions.php";
	// $mail->CharSet = 'utf-8';
	ob_start();
	
	$name = $_POST['name'];
	$phone = $_POST['phone'];
	$email = $_POST['email'];
	$request_goal = $_POST['request_goal'];
	$city = $_POST['city'];
	$contact = $_POST['contact'];
	$comment = $_POST['comment'];

	$ip = @$_SERVER['REMOTE_ADDR'];		
	$url = @$_SERVER['HTTP_REFERER'];
	$time = date("Y-m-d H:i:s");

	// от кого
	$fromMail = 'hiconix@ecoclima.ru';
	#$fromMail = 'info@hiconix.ru';
	$fromName = 'Форма с hiconix.ru';

	//цель обращения

	if ($request_goal == "retail") {
		$subject = 'Форма-РОЗНИЦА-hiconix.ru';
		$emailTo = ["hiconix@ecoclima.ru", "zakaz@logofirm.ru"];
	}


	if ($request_goal == "dealer") {
		$subject = 'Форма-ОПТ-hiconix.ru';
		$emailTo = ["hiconix@ecoclima.ru", "zakaz@logofirm.ru"];
	}

	if ($request_goal == "commerc") {
		$subject = 'Форма-Директору-hiconix.ru';
		$emailTo = ["hiconix@ecoclima.ru", "zakaz@logofirm.ru"];
	}

	$crmSubject = $subject;
	//$subject = '=?utf-8?b?'. base64_encode($subject) .'?=';
	$headers = "Content-type: text/plain; charset = utf-8";
	$headers .= "From: ". $fromName ." <". $fromMail ."> \r\n";

// тело письма
$body = "Получен заказ Звонка или Eмаил с сайта {$_SERVER['SERVER_NAME']}:\n
Имя: $name
Город: $city
Телефон: $phone
E-mail: $email \n";

if ($comment !== "") {
	$body .= "Комментарий: $comment\n\n";
} else {
	$body .= "\n\n";
}

$body .= "IP-адрес посетителя: $ip
Заявка пришла с сайта: $url
Время заказа: $time";

$arEventFields = array(
    'SUBJECT' => $subject,
	'EMAIL_TO' => $emailTo,
	'NAME' => $name,
	'CITY' => $city,
	'PHONE' => $phone,
	'EMAIL' => $email,
	'COMMENT' => $comment,
	'IP' => $ip,
	'URL' => $url,
	'TIME' => $time
);

echo "<pre>arEventFields";
print_r($arEventFields);
echo "</pre>";

CEvent::Send("ZAKAZ_ZVONKA", s1, $arEventFields);

$dealData = [
	"subject" => $crmSubject,
	"name" => $name,
	"phone" => $phone,
	"email" => $email,
	"city" => $city,
	"userNote" => $comment
];

print_r(amoCreateDeal($accessToken, $dealData, $subdomain));

/**foreach ($emailTo as $email_to) {
	$mail = mail($email_to, $subject, $body, $headers, '-f'. $fromMail );
}*/

echo $body;

$test_text = ob_get_contents();
ob_end_clean();
$h = fopen(__DIR__.'/1.txt', 'ab');
fwrite($h, $test_text);
fclose($h);

?>
<?php require($doc_root."/bitrix/modules/main/include/epilog_after.php");?>
