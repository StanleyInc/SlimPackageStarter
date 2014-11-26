<?php

//dependency - app.config - google.php - gmail array
function smtpmailer($to, $from, $from_name, $subject, $body = '', $is_gmail = true, $ishtml = false,$cc = '',$altbody = false, $smtpdebug = 0, $debugoutput = 'html' ) {

	global $error, $config;
	$mail = new PHPMailer();

	//Tell PHPMailer to use SMTP
	$mail->IsSMTP();
	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	if($smtpdebug != ''){$mail->SMTPDebug = $smtpdebug; };

	//Ask for HTML-friendly debug output
	if($debugoutput != ''){$mail->Debugoutput = $debugoutput; };



	$mail->SMTPAuth = true; 
	if ($is_gmail) {
		$mail->Host = 'smtp.gmail.com';
		 //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$mail->Port = 587;
        //Set the encryption system to use - ssl (deprecated) or tls
		$mail->SMTPSecure = 'tls';

		$mail->Username = $config['gmail']['user'];  
		$mail->Password = $config['gmail']['password'];    

	} else {
		$mail->Host = $config['smtp']['server'];
		$mail->Username = $config['smtp']['user'];
		$mail->Password = $config['smtp']['password'];
	}        
	$mail->SetFrom($from, $from_name);
	$mail->Subject = $subject;

	

	//convert HTML into a basic plain-text alternative body
    //$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
    //
	// if($msghtml){

	// 	$mail->msgHTML = $msghtml; 
	// }else{
	// 	$mail->msgHTML(file_get_contents(VENDOR_PATH.'phpmailer/contents.html'), dirname(__FILE__));
	// }
	if($ishtml){
		$mail->IsHTML(true);
	}
	// else{
	// 	$mail->msgHTML(file_get_contents(VENDOR_PATH.'phpmailer/contents.html'), dirname(__FILE__));
	// }

	
	if($body != ''){$mail->Body = $body; };
	if($altbody){$mail->AltBody = $altbody; };

	$mail->AddAddress($to);
	
	if($cc != ''){
	$addresses = explode(',', $cc);
	foreach ($addresses as $address) {
	    $mail->AddCC($address,$address);
	}
}
	if(!$mail->Send()) {
		$error = 'Mail error: '.$mail->ErrorInfo;
		return false;
	} else {
		$error = 'Message sent!';
		return true;
	}
	
}

#################################Array Section#############################################
function serialize_array_values($arr){
	foreach($arr as $key=>$val){
		sort($val);
		$arr[$key]=serialize($val);
	}

	return $arr;
}


//smart merge arrays base on one of the unique key field eg,id is the key field
//only return array 1 side data 

function smartAllArrayMerge($array1,$array2,$keyfield){

		$groupByKey = $keyfield; // this becomes the fixed key
	$merged = array_merge($array1,$array2); // array_merge($a,$b,$c); // cumulative container of all items in every subject array

	$result = array(); // the result will be stored here, e.g. a temporary "table"
	foreach ( $merged as $item ) { // $merged is essentially a table of subjects and $item is each row
	    if ( !isset($result[$item[$groupByKey]]) ) { // if we haven't come across this key yet
	        $result[$item[$groupByKey]] = array(); // initialize it
	    }
	    $result[$item[$groupByKey]] = array_merge($result[$item[$groupByKey]],$item); // consolidate all the cells for this row, later duplicate keys will cause values to be replaced
	}
	$resultmatch = array_values($result); // normalize the result keys, for the view they should increment rather than represent the group-by subjects
		// print_r($result);
		// exit();
	return $resultmatch;


}

//only all field data match will merge to the list if both array have exactly same structure value and key
//dependent - serialize_array_values function
function smartAllArrayMatch($array1,$array2){
	$result = array_map("unserialize", array_intersect(serialize_array_values($array1),serialize_array_values($array2)));
	return $result;
}


//merge common key data for multiple arrays
function smartAllArrayMergeCommonKeys(){
	$arr = func_get_args();
	$num = func_num_args();

	$keys = array();
	$i = 0;
	for($i=0;$i<$num;++$i){
		$keys = array_merge($keys, array_keys($arr[$i]));
	}
	$keys = array_unique($keys);

	$merged = array();

	foreach($keys as $key){
		$merged[$key] = array();
		for($i=0;$i<$num;++$i){
			$merged[$key][] = isset($arr[$i][$key])?$arr[$i][$key]:null;
		}
	}
	return $merged;
}


//only will match if array 1 keys or array 2 keys exist
function smartAllArrayMatchById($array1,$array2){
	
	$result = array_uintersect($array1, $array2, function($x, $y)
	{
		$x = is_array($x)?$x['id']:$x;
		$y = is_array($y)?$y['id']:$y;
		return $x-$y;
	});
	return $result;
}


#################################End Array Section#############################################


//dependent on request object
//usage - controller
function routeLastPath(){
        $routepath = Request::getPathInfo();
        $paths = explode('/', $routepath);
        $lastpath = end($paths);
        // $currentfunction = __FUNCTION__;
        $result = $lastpath;
        return $result;
 }


 ###############################user action#############################

function userIsLogin(){
	if ( ! Sentry::check())
	{
		// User is not logged in, or is not activated
		return false;
	}
	else
	{	
		// User is logged in
		return true;	    
	}
}


?>