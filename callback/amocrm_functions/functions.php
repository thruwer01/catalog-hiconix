<?php
declare(strict_types=1);

// require_once "../logger/logger.php";
require_once "db.php";

/*
* Variables
*/
$subdomain = 'hiconix';
$dbName = "u1501272_amocrm-hiconix";
$dbUser = "u1501272_amocrm";
$dbPass = "amocrmEcoclima";
$dbTable = "amocrm_tokens";


$tokens = getTokensFromDB($db);
$accessToken = $tokens['access_token'];
$refreshToken = $tokens['refresh_token'];
/*
* End variables
*/


/**
 * Create POST request to amoCRM
 *
 * @param string $link
 * @param array $data
 * @param array $headers
 * 
 * @return array Return response for request
 */ 
function amoCreateRequest(string $link, array $data, array $headers = ['Content-Type:application/json']): array {
    $curl = curl_init();
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
    curl_setopt($curl,CURLOPT_URL, $link);
    curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl,CURLOPT_HEADER, false);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
    $out = curl_exec($curl);
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    $code = (int)$code;
    $errors = [
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable',
    ];

    $response = json_decode($out, true);
    $responseData = [
        "response" => $response
    ];

    if ($code < 200 || $code > 204) {
		$responseData['errorCode'] = isset($errors[$code]) ? $errors[$code] : 'Undefined error - '.$code;
	}

    return $responseData;
}

/**
 * Update access token via refresh token
 *
 * @param string $refreshToken
 * @param string $subdomain
 * 
 * @return array Return new access and refresh tokens from api
 */ 
function amoUpdateToken(string $refreshToken, string $subdomain): array {
    $link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //create url
    $data = [
        'client_id' => '7ddca1e1-50d8-48b3-a974-0005ed8d296d',
        'client_secret' => 'OayGLFoe8KfBtSTJcxl7Ps8MkUmkJjfTEJBnApXwGgN4WVhGCovCwQKWKOnIv1VD',
        'grant_type' => 'refresh_token',
        'refresh_token' => $refreshToken,
        'redirect_uri' => 'https://hiconix.ru',
    ];

    $getResponse = amoCreateRequest($link, $data)['response'];

    if (isset($getResponse['errorCode']) && $getResponse['errorCode'] !== "") {
        die('Error: ' . $getResponse['errorCode']);
    }

    return [
        "access_token" => $getResponse['access_token'],
        "refresh_token" => $getResponse['refresh_token']
    ];
}

/**
 * Create deal in amoCRM
 *
 * @param string $accessToken
 * @param array $dealData Deal information with client phone or email
 * 
 * @return array Return information about status of creating deal
 */ 
function amoCreateDeal(string $accessToken, array $dealData, string $subdomain): array {
    $link = 'https://' . $subdomain . '.amocrm.ru/api/v4/leads/complex'; //create url
    
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ];

    if (!isset($dealData['responsible_id'])) $dealData['responsible_id'] = 7315264;

    $data = [
        [
            "name" => $dealData['subject'],
            "pipeline_id" => 801979,
            "status_id" => 16640374,
            "responsible_user_id" => $dealData['responsible_id'],
            "_embedded" => [
                "contacts" => [
                    [
                        "first_name" => $dealData['name'] ? $dealData['name'] : 'Нет имени',
                        "custom_fields_values" => [
                            [
                                "field_code" => "PHONE",
                                "values" => [
                                    [
                                        "value" => $dealData['phone'] ? $dealData['phone'] : null,
                                    ]
                                ]
                            ],
                            [
                                "field_code" => "EMAIL",
                                "values" => [
                                    [
                                        "enum_code" => "WORK",
                                        "value" => $dealData['email'] ? $dealData['email'] : null,
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ],
            "custom_fields_values" => [
                [
                    "field_id" => 359819,
                    "values" => [
                        [
                            "value" => $dealData['userNote'] ? "Комментарий клиента: \n".$dealData['userNote'] : null
                        ]
                    ]
                ],
                [
                    "field_id" => 575413,
                    "values" => [
                        [
                            "value" => $dealData['city'] ? $dealData['city'] : 'Не указан'
                        ]
                    ]
                ]
            ]
        ]
    ];

    $getResponse = amoCreateRequest($link, $data, $headers);

    return $getResponse;
}

// получили токен из бд -> обновили токен -> записали в бд новые токены -> отправили в амо 

/*
    Get new amoCRM tokens
*/
$newTokens = amoUpdateToken($refreshToken, $subdomain);
$accessToken = $newTokens['access_token'];
$refreshToken = $newTokens['refresh_token'];

/*
    Update tokens in database
*/
updateTokensDB($db, $accessToken, $refreshToken);


