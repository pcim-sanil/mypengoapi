<?php
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

	$mypengoMappingObj 	= 	new MypengoMapping();
    $mypengoMapping 	= 	$mypengoMappingObj->fetchServiceID(['geographic_no' => $ddi, 'is_ddi_active' => 1]);
    Response::sendJSON($mypengoMapping);
});



// fetch subscription
$route->register('/getSubscriptions?(.+)', 'GET', function () {


    $serviceId 			= Utility::getValue($_REQUEST, 'serviceId');
    $custkey 			= Utility::getValue($_REQUEST, 'custkey');
    $requestNumber 		= Utility::getValue($_REQUEST, 'req_number');
    $countryPrefix	 	= Utility::getValue($_REQUEST, 'country_prefix');
    $msisdn 			= Utility::getMsisdn($requestNumber, $countryPrefix);
   

    // make entry log
    $logId = MypengoUnsubStatus::entryLog();
    //echo $logId;

    // mypengo actual api call
   // $url = MYPENGO_BASE_URL_SUBSCRIPTION . "?serviceid=$serviceId&msisdn=$msisdn";
    $response = Utility::apiGetRequest("url");


    // update log
   // MypengoUnsubStatus::updateLog($logId, $response);

     Utility::r($logId);  
     // Utility::r($response);
    //exit($response);
});


// unsubscrib from service
$route->register('/unsubscribe?(.+)', 'GET', function () {


    // set req_for as unsub
    $_REQUEST['req_for'] = 'unsub';
    $serviceId = Utility::getValue($_REQUEST, 'serviceId');
    $requestNumber = Utility::getValue($_REQUEST, 'req_number');
    $countryPrefix = Utility::getValue($_REQUEST, 'country_prefix');
    $msisdn = Utility::getMsisdn($requestNumber, $countryPrefix);

    // make entry log
    $logId = MypengoUnsubStatus::entryLogUnsub();



    $postData = json_encode(['msisdn' => $msisdn, "ServiceId" => $serviceId]);

    // mypengo actual api call
    $url = MYPENGO_BASE_URL_UNSUBSCRIBE . "?serviceid=$serviceId&msisdn=$msisdn";
    $response = Utility::apiPostRequest($url, $postData);


    // update log
    MypengoUnsubStatus::updateLog($logId, $response);

    
    Utility::r($response);
    //exit($response);
});












  /**
 * get uri params and request method such as GET, POST, PUT, DELETE
 */
$uri = Utility::getValue($_REQUEST, 'uri');
$requestMethod = Utility::getValue($_SERVER, 'REQUEST_METHOD');

// route based on the uri and request method
$route->route($uri, $requestMethod);
