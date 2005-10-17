<?php
class ICQ_Status{
    var $status;
    var $fp;
    var $ICQServer = 'status.icq.com';

    function ICQ_Status(){
        $this->status = array();
    }

    function Connect2ICQServer(){
        $this->fp = @fsockopen($this->ICQServer, 80, $errno, $errstr, 90);
		
		if(!$this->fp){
			return;
		}
		
        socket_set_blocking($this->fp, 1);
    }

    /* Return  0 if uin's status is offline,
               1 if uin's status is online,
               2 if uin's status is unknown */
    function GetStatus($uin){
        $this->Connect2ICQServer();

		if(!$this->fp){
			return 2;
		}
		
        if( isset($this->status[$uin]) ){
            return $this->status[$uin];
        }

        $data = '';
        fputs($this->fp,
              'GET /online.gif?icq=' . $uin . '&img=1 HTTP/1.1' . "\r\n" .
              'Host: ' . $this->ICQServer . "\r\n\r\n");
        while( !feof($this->fp) ){
            $data = fgets($this->fp, 2048);
            if( ereg('Location: +(.*)', $data, $parts) ){
                break;
            }
        }

        $this->status[$uin] = 2;
        if( isset($parts[1]) ){
            $file = trim($parts[1]);
            if( $file == '/1/online1.gif' ){
                $this->status[$uin] = 1;
            }
            elseif( $file == '/1/online0.gif' ){
                $this->status[$uin] = 0;
            }
        }

        return $this->status[$uin];
    }

    function CloseICQConnection(){
        fclose($this->fp);
    }
}
?>