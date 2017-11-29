<?php
header("Access-Control-Allow-Origin: *");
/**
 * Bootstrap the api
 */
require_once 'config.php';
require_once 'bootstrap.php';
date_default_timezone_set('Australia/Sydney');

$route = new Router();




// root
$route->register('/', 'GET', function () {
    exit('Unauthorized access!');
});



// fetch all mapping
$route->register('/getMappings', 'GET', function () {
    $mypengoMappingObj = new MypengoMapping();
    $mypengoMappings = $mypengoMappingObj->fetchAllByParams(['is_ddi_active' => 1]);
    Response::sendJSON($mypengoMappings);
});



// fetch mapping by ddi
$route->register('/getMappingByDDI/(\d+)', 'GET', function ($ddi) {

    $mypengoMappingObj = new MypengoMapping();
    $mypengoMapping = $mypengoMappingObj->fetchAllByParams(['geographic_no' => $ddi, 'is_ddi_active' => 1]);
    Response::sendJSON($mypengoMapping);
});


// fetch mapping by service id (mypengo given only)
$route->register('/getServiceId/(\d+)', 'GET', function ($ddi) {

    $mypengoMappingObj = new MypengoMapping();
    $mypengoMapping = $mypengoMappingObj->fetchServiceID(['geographic_no' => $ddi, 'is_ddi_active' => 1]);
    Response::sendJSON($mypengoMapping);
});



// fetch subscription
$route->register('/getSubscriptions?(.+)', 'GET', function () {

    $ddi = Utility::getValue($_REQUEST, 'req_ddi', '0284482842');
    $source = Utility::getValue($_REQUEST, 'req_source', 'Portal1300');
    $countryPrefix = Utility::getValue($_REQUEST, 'country_prefix');
    $requestNumber = Utility::getValue($_REQUEST, 'req_number');
    $msisdn = Utility::getMsisdn($requestNumber, $countryPrefix);

    $ticketId = Utility::getValue($_REQUEST, 'ticket_id', NULL);

    $result = MyPengoHelper::getSubscriptions($ddi, $msisdn, $ticketId, $source);

    Response::sendHTML('subscriptions', $result);
});


// unsubscrib from service
$route->register('/unsubscribe?(.+)', 'GET', function () {

    // set req_for as unsub
    $_REQUEST['req_for'] = 'unsub';
    $serviceid = Utility::getValue($_REQUEST, 'service_id');
    $requestNumber = Utility::getValue($_REQUEST, 'req_number');
    $countryPrefix = Utility::getValue($_REQUEST, 'country_prefix');
    $msisdn = Utility::getMsisdn($requestNumber, $countryPrefix);

    // make entry log
    $logId = MypengoUnsubStatus::entryLog();


    $postData = json_encode(['msisdn' => $msisdn, "serviceid" => $serviceid]);

    // mypengo actual api call
    $url = MYPENGO_BASE_URL_UNSUBSCRIBE;
    $response = Utility::apiPostRequest($url, $postData);


    // update log
    MypengoUnsubStatus::updateLog($logId, $response);


    $httpResponseMessage = Utility::getValue($response, 'httpResponseMessage');


    if ($httpResponseMessage) {
        $httpMypengoResponseMessage = json_decode($httpResponseMessage, true);
        exit("<p style='color:red'><b>" . Utility::getValue($httpMypengoResponseMessage, 'Message') . "</b></p>");
    }

    exit("OK");
});












/**
 * get uri params and request method such as GET, POST, PUT, DELETE
 */
$uri = Utility::getValue($_REQUEST, 'uri');
$requestMethod = Utility::getValue($_SERVER, 'REQUEST_METHOD');

// route based on the uri and request method
$route->route($uri, $requestMethod);
