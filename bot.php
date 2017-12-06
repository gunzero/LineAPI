<?php
$access_token = 'Zj4y6teEQK9gTvw5bHru2CF7EeM2TSnMY7zK5vmmSzVhyh9lOTmnUaqTeqq5oXHF1PKWxKMU3ibEhB9J1yZtwHNEtiIiNI6Es45SIT2lfep+7QDYBXLIDgRLuHSOiKQ5XjBAiPlR18ZJA4BTxi4HngdB04t89/1O/w1cDnyilFU=';
$Token_anto = 'N0GmjIPv6l1tJY7RovJW86yg393g3udc8w3CGtSw';
$keys = 'NodeMCU';
$Chanel = 'Status';
$_Status = -1;

// Get POST body content
$content = file_get_contents('php://input');
$events = json_decode($content, true);
$Url = "https://api.anto.io/channel/get/".$Token_anto."/".$keys."/".$Chanel;
$Url_Update = "https://api.anto.io/channel/set/".$Token_anto."/".$keys."/".$Chanel."/";

// Parse JSON



// Validate parsed JSON data
function Check_Status($contents){
$event_Status = json_decode($contents, true);
	if ($event_Status['result'] == "true"){
		if( $event_Status['value'] == "0") {		
			return "ปิด";	
		} else {				
			return "เปิด";
		}	
	}
	return "error";		
}

		// Loop through each event	
		if(!is_null($events['events']) ){
		foreach ($events['events'] as $event) {
			// Reply only when message sent is in 'text' format
			if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
				// Get text sent
				$text = $event['message']['text'];
				// Get replyToken
				$replyToken = $event['replyToken'];
				// Build message to reply back
				if(strtoupper($text) == "GETSTATUS" || $text == "สถานะ"){				
					$messages = [
					'type' => 'text',
					'text' => 'สถานะ '.Check_Status(file_get_contents($Url))
					];	
				} elseif (strtoupper($text) == "ON" || $text == "เปิด" ) {
					$_Status = 1;
					$messages = [
					'type' => 'text',
					'text' => 'เปิด เรียบร้อย'
					];
				} elseif (strtoupper($text)  == "OFF" || $text == "ปิด" ) {
					$_Status = 0;		
					$messages = [
					'type' => 'text',
					'text' => 'ปิด เรียบร้อย'
					];					
				}else {				
					$messages = [
					'type' => 'text',
					'text' => 'กรุณากรอกใหม่...'
					];				
				} 

				if ($_Status > -1) {
					Check_Status(file_get_contents($Url_Update.$_Status));
				}
				// Make a POST Request to Messaging API to reply to sender
				
				$url = 'https://api.line.me/v2/bot/message/reply';
				
				$data = [
					'replyToken' => $replyToken,				
					'messages' => [$messages],									
				];
				$post = json_encode($data);
				$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				$result = curl_exec($ch);
				curl_close($ch);
				echo $result . "\r\n";

			}
		}
	}

echo "OK";
echo "<br>".$Url;
