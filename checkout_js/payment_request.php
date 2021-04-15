<?php

//print_r($_POST);die();

$val = $_POST;

$datastring=$val['mrctCode']."|".$val['txn_id']."|".$val['amount']."|".$val['accNo']."|".$val['custID']."|".$val['mobNo']."|".$val['email']."|".$val['debitStartDate']."|".$val['debitEndDate']."|".$val['maxAmount']."|".$val['amountType']."|".$val['frequency']."|".$val['cardNumber']."|".$val['expMonth']."|".$val['expYear']."|".$val['cvvCode']."|".$val['SALT'];

$hashed = hash('sha512',$datastring);

$data=array("hash"=>$hashed,"data"=>array($val['mrctCode'],$val['txn_id'],$val['amount'],$val['debitStartDate'],$val['debitEndDate'],$val['maxAmount'],$val['amountType'],$val['frequency'],$val['custID'],$val['mobNo'],$val['email'],$val['accNo'],$val['returnUrl'],$val['name'],$val['scheme'],$val['currency'],$val['accountName'],$val['ifscCode'],$val['accountType']));

echo json_encode($data);

?>



