<?php
require_once "amocrm_functions/functions.php";

$post = (object) $_POST;

$formValues = array_values(get_object_vars($post));

function sendError()
{
    echo json_encode(["type" => "error"]);
}

function sendSuccess()
{
    echo json_encode(["type" => "success"]);
}

function sendMail($dealData)
{
$to = "fm@hiconix.ru"; 
$from = $dealData["email"];
$subject = "Заявка на получение API токена";

$headers  = "Content-type: text/html; charset=utf-8 \r\n"; 
$headers .= "From: <$from>"; 



$message = str_replace(PHP_EOL, '<br>', $dealData['userNote']);

mail($to, $subject, $message, $headers);
}

foreach ($formValues as $key => $value) {
    if (gettype($value) == "array") continue 1;

    if ($key !== "manager" && is_null($value) || $value === "") return sendError();

    //form validated

    if ($post->request_goal !== "api") return sendError();
}

$time = date("Y-m-d H:i:s");

$name = $post->name;
$companyName = $post->urlic_name;
$city = $post->city;
$siteURL = $post->site_url;
$manager = $post->manager;
$phone = $post->phone;
$email = $post->email;
$brands = $post->brands;

$someMessage = "Заявка на получение API токена.\nИмя: $name\nЮр.лицо: $companyName\nГород: $city\nСайт: $siteURL\nОтветственный менеджер: $manager\nНомер телефона: $phone\nПочта: $email\n";

if (count($brands) > 0) {
    $someMessage .= "\n\nЖелаемые бренды:\n";
    foreach ($brands as $brand) {
        $someMessage .= "\n $brand";
    }
}

$someMessage .= "\n\n Время: $time";

//request to api
$ch = curl_init("https://erc.hiconix.ru/manager/get_by_number?manager_id=$manager");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Accept: application/json'
));
$res = curl_exec($ch);
curl_close($ch);

$managerAmoID = (int)$res;

$dealData = [
	"subject" => "Заявка на получение API токена",
	"name" => $name,
	"phone" => $phone,
	"email" => $email,
	"city" => $city,
	"userNote" => $someMessage
];

$dealData['responsible_id'] = !is_null($managerAmoID) && $managerAmoID !== "" ?  $managerAmoID : 2486962;

amoCreateDeal($accessToken, $dealData, $subdomain);
sendSuccess();
sendMail($dealData);