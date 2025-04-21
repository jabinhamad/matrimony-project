<?php
require_once 'HTTP/Request2.php';
$request = new HTTP_Request2();
$request->setUrl('https://cpaas.messagecentral.com/verification/v3/send?countryCode=91&customerId=C-9FDE479C09AC472&flowType=SMS&mobileNumber=8270923108');
$request->setMethod(HTTP_Request2::METHOD_POST);
$request->setConfig(array(
'follow_redirects' => TRUE
));
$request->setHeader(array(
'authToken' => eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJDLTlGREU0NzlDMDlBQzQ3MiIsImlhdCI6MTc0NDY0NzYwNiwiZXhwIjoxOTAyMzI3NjA2fQ.oBYuXFIzP0s7qWmmG1JKM9o49Mm8BWHKRxXEmd7SmYGqQRVkF2-3ik_ssp6Am1ZEU0WRkjrnv6u6tagjHg7Ipg
));
try {
$response = $request->send();
if ($response->getStatus() == 200) {
echo $response->getBody();
}
else {
echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .
$response->getReasonPhrase();
}
}
catch(HTTP_Request2_Exception $e) {
echo 'Error: ' . $e->getMessage();
}