<?php 

if(isset($_POST))
{
    $type                      = $_POST['type'];
    $merchantTransactionID     = $_POST['merchantTransactionID'];
	$consumerTransactionID     = $_POST['consumerTransactionID'];

	$date                      = $_POST['transactionDate'];
	$newDate                   = date("d-m-Y", strtotime($date));

	//echo $identifier.' | '.$newDate.'<br><br>';

    $admin_data = file_get_contents("http://localhost/checkout_js/ingenico_AdminData.json");
    $mer_array = json_decode($admin_data, true);

  	$arr_req = array(
  	    "merchant" => [
  	        "identifier" => $mer_array['merchantCode']
  	    ],
        "payment" => ["instruction" => [ "" => "" ]
        ],
  	    "transaction" => [ "deviceIdentifier" => "S", "type" => $type, "currency" => $mer_array['currency'], "identifier" => $merchantTransactionID, "dateTime" => $newDate, "subType" => "002", "requestType" => "TSI"],        
        "consumer" => [ "identifier" => $consumerTransactionID
        ]
  	);

	$finalJsonReq = json_encode($arr_req);

	//echo $finalJsonReq; die();

    function callAPI($method, $url, $finalJsonReq)
    {
        $curl = curl_init();
        switch ($method){
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($finalJsonReq)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $finalJsonReq);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($finalJsonReq)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $finalJsonReq);                              
                break;
            default:
                if ($finalJsonReq)
                    $url = sprintf("%s?%s", $url, http_build_query($finalJsonReq));
        }
        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        // EXECUTE:
        $result = curl_exec($curl);
        if(!$result)
        {
            die("Connection Failure");
        }
        curl_close($curl);
        return $result;
    }

    $method = 'POST';
    $url = "https://www.paynimo.com/api/paynimoV2.req";
    $res_result = callAPI($method, $url, $finalJsonReq);
    $verifyData = json_decode($res_result, true);

    //echo "<pre>";print_r($verifyData);die();

	echo '<table class="table" border = "1" align="center" cellpadding="2" cellspacing="0" style="width: 50%;text-align: center;">
        <thead>
          <tr class="info">
            <th>Field Name</th>
            <th>Value</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Status Code</td>
            <td>'.$verifyData["paymentMethod"]["paymentTransaction"]["statusCode"].'</td>
          </tr>
          <tr>
            <td>Merchant Transaction Reference No.</td>
            <td>'.$verifyData["paymentMethod"]["token"].'</td>
          </tr>
          <tr>
            <td>Ingenico Merchant Transaction ID</td>
            <td>'.$verifyData["paymentMethod"]["paymentTransaction"]["identifier"].'</td>
          </tr>
          <tr>
            <td>Status Message</td>
            <td>'.$verifyData["paymentMethod"]["paymentTransaction"]["statusMessage"].'</td>
          </tr>
        </tbody>
      </table>
      <br>
      <a href=' . $host. "mandate-verification.php" . '>Go Back To Mandate Verification</a>';
}

?>