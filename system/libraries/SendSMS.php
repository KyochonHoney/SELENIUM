<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CI_SendSMS {
    var $smsid;
    var $smskey;


    public function __construct(){
        $this->ci =& get_instance();
        $this->smsid=$this->ci->config->item('smsid');
        $this->smskey=$this->ci->config->item('smskey');		
    }

    function GetSMSCount(){
        $data = "";
        $sms_url = "https://sslsms.cafe24.com/sms_remain.php"; // SMS 잔여건수 요청 URL
        $sms['user_id'] = base64_encode($this->smsid); // SMS 아이디
        $sms['secure'] = base64_encode($this->smskey) ;//인증키

        $sms['mode'] = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.

        $host_info = explode("/", $sms_url);
        $host = $host_info[2];
        $path = $host_info[3];
        srand((double)microtime()*1000000);
        $boundary = "---------------------".substr(md5(rand(0,32000)),0,10);

        // 헤더 생성
        $header = "POST /".$path ." HTTP/1.0\r\n";
        $header .= "Host: ".$host."\r\n";
        $header .= "Content-type: multipart/form-data, boundary=".$boundary."\r\n";

        // 본문 생성
        foreach($sms AS $index => $value){
            $data .="--$boundary\r\n";
            $data .= "Content-Disposition: form-data; name=\"".$index."\"\r\n";
            $data .= "\r\n".$value."\r\n";
            $data .="--$boundary\r\n";
        }
        $header .= "Content-length: " . strlen($data) . "\r\n\r\n";

        $fp = fsockopen($host, 80);

        if ($fp) {
            fputs($fp, $header.$data);
            $rsp = '';
            while(!feof($fp)) {
                $rsp .= fgets($fp,8192);
            }
            fclose($fp);
            $msg = explode("\r\n\r\n",trim($rsp));
            $this->Count = $msg[1]; //잔여건수
        }
    }

    function SendMessage($smsdata){


		$message = $smsdata['message'];
		$sendNumber = $smsdata['sendNumber'];
        $smstype = $smsdata['smstype'];
        $testflag = $smsdata['testflag'];
        $sPhone = $smsdata['sPhone'];
        $rDate = $smsdata['rDate'];
        $rTime = $smsdata['rTime'];
        $subject = $smsdata['subject'];

        $sPhoneArray = explode("-", $sPhone);
        $sPhone1 = $sPhoneArray[0];
        $sPhone2 = $sPhoneArray[1];
        $sPhone3 = $sPhoneArray[2];


       /******************** 인증정보 ********************/
        //$sms_url = "https://sslsms.cafe24.com/sms_sender.php"; // 전송요청 URL
		$sms_url = "http://sslsms.cafe24.com/sms_sender.php"; // 전송요청 URL

        $sms['user_id'] = base64_encode($this->smsid); //SMS 아이디.
        $sms['secure'] = base64_encode($this->smskey) ;//인증키
        $sms['msg'] = base64_encode(stripslashes($message));
        if($smstype == "L"){
              $sms['subject'] =  base64_encode($subject);
        }
        $sms['rphone'] = base64_encode($sendNumber);
        $sms['sphone1'] = base64_encode($sPhone1);
        $sms['sphone2'] = base64_encode($sPhone2);
        $sms['sphone3'] = base64_encode($sPhone3);
        $sms['rdate'] = base64_encode($rDate);
        $sms['rtime'] = base64_encode($rTime);
        $sms['mode'] = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.
        //$sms['returnurl'] = base64_encode($_POST['returnurl']);
        $sms['testflag'] = base64_encode($testflag);
        //$sms['destination'] = strtr(base64_encode($POST['destination']), '+/=', '-,');
        //$returnurl = $_POST['returnurl'];
        //$sms['repeatFlag'] = base64_encode($_POST['repeatFlag']);
        //$sms['repeatNum'] = base64_encode($_POST['repeatNum']);
        //$sms['repeatTime'] = base64_encode($_POST['repeatTime']);
        $sms['smsType'] = base64_encode($smstype); // LMS일경우 L
        //$nointeractive = $_POST['nointeractive']; //사용할 경우 : 1, 성공시 대화상자(alert)를 생략

        $host_info = explode("/", $sms_url);
        $host = $host_info[2];
        $path = $host_info[3];

		
        //$path = $host_info[3]."/".$host_info[4];

        srand((double)microtime()*1000000);
        $boundary = "---------------------".substr(md5(rand(0,32000)),0,10);
        //print_r($sms);

        // 헤더 생성
        $header = "POST /".$path ." HTTP/1.0\r\n";
        $header .= "Host: ".$host."\r\n";
        $header .= "Content-type: multipart/form-data, boundary=".$boundary."\r\n";

        $data = "";
        // 본문 생성
        foreach($sms AS $index => $value){
            $data .="--$boundary\r\n";
            $data .= "Content-Disposition: form-data; name=\"".$index."\"\r\n";
            $data .= "\r\n".$value."\r\n";
            $data .="--$boundary\r\n";
        }
        $header .= "Content-length: " . strlen($data) . "\r\n\r\n";

        $fp = fsockopen($host, 80);

	

	

        if ($fp) {
				
            fputs($fp, $header.$data);
            $rsp = '';
            while(!feof($fp)) {
                $rsp .= fgets($fp,8192);
            }
            fclose($fp);
            $msg = explode("\r\n\r\n",trim($rsp));
            $rMsg = explode(",", $msg[1]);
            $Result= $rMsg[0]; //발송결과
            $this->Count= $rMsg[1]; //잔여건수
            //print_r($rsp);



            //발송결과 알림
            /*
            if($Result=="success") {
                $this->ResultMsg= "성공";
                $this->ResultCode = 1;
            } else if($Result=="reserved") {
                $this->ResultMsg = "성공적으로 예약되었습니다.";
            } else if($Result=="3205") {
                $this->ResultMsg = "잘못된 번호형식입니다.";
            } else if($Result=="0044") {
                $this->ResultMsg = "스팸문자는발송되지 않습니다.";
            } else {
                $this->ResultMsg = "[Error]".$Result.":";
            }
            */
        } else {
            $this->ResultMsg = "Connection Failed";
        }

        return $rMsg;
    }
}
?>