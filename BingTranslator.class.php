<?php

class BingTranslator {
    const API = 'http://api.microsofttranslator.com/v2/Http.svc/';
    //const AUTH = 'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13';
    private $LNGS = array("mww","tr","da","ja","ca","lt","hu","id","hi","es","he","el","lv","fa","pl","fr","fi","ar","bg","ru","en","no","th","ht","uk","cs","nl","sk","sl","vi","et","sv","it","pt","de","zh-CHT","ko","zh-CHS","ro");
    //const API = 'http://api.microsofttranslator.com/v2/Ajax.svc/';    
    private $access_token = '';
    private $params = array();
    /**
     *@var int $max_len 最大字數 超過則截斷
    */
    private $max_len = 5000;
    public function __construct($appid='',$apppws=''){
        $this->access_token = $this->getToken();
     
    }
    
    private function getToken()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.cognitive.microsoft.com/sts/v1.0/issueToken');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Ocp-Apim-Subscription-Key: ' . BING_PWS, 'Content-length: 0']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt( $ch, CURLOPT_VERBOSE, true ); // Uncomment this line to enable some very useful debugging output

        $strResponse = curl_exec($ch);
        $curlErrno = curl_errno($ch);

        if ($curlErrno)
        {
            $curlError = curl_error($ch);
            curl_close($ch);
            throw new Exception($curlError);
        }

        curl_close($ch);

        $objResponse = json_decode($strResponse);        

        if ( isset($objResponse->message))
        {
            mail('tung@ready-market.com','inquiry translate exception',$objResponse->statusCode . ': ' . $objResponse->message); // FYI - message is HTML formatted
            return '';
        }

        return $strResponse;
    }    
    /**
     * @param string $str Detect content
     * @return string A string containing a two-character Language code for the given text.
     *
     */
    public function Detect($str){
        
        if(empty($this->access_token)){
            return 'en';
        }
        $uri = self::API.__FUNCTION__.'?';
        $params = $this->params;
        $params['text'] = mb_substr($str,0,$this->max_len);
        //$params['appId'] = $this->access_token;
        $authHeader = "Authorization: Bearer ".$this->access_token;
        CurlTool::$options = array(
            CURLOPT_HTTPHEADER => array($authHeader,"Content-Type: text/xml"),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
                                   );
        //CurlTool::$options[CURLOPT_HTTPHEADER] = array($authHeader,"Content-Type: text/xml");
        
       // CurlTool::$options[CURLOPT_SSL_VERIFYPEER] = false;
        
        $rs = CurlTool::fetchContent($uri.http_build_query($params),false);
        $lng = strip_tags($rs);
        if(in_array($lng,$this->LNGS)){
            return $lng;    
        }else{
            return 'en';
        }
        
    }
    /**
     * @param string $str A string representing the text to translate.
     * @param string $from_lng A string representing the language code of the translation text.
     * @param string $to_lng A string representing the language code to translate the text into.
     * @param string $The format of the text being translated. The supported formats are "text/plain" and "text/html". Any HTML needs to be well-formed. .
     * @return string A string representing the translated text
     *
     */
    public function Translate($str,$from_lng=null,$to_lng='en',$contentType='text/plain'){
        
        if(empty($this->access_token)){
            return $str;
        }
        $uri = self::API.__FUNCTION__.'?';
        if(empty($str)){
            return '';
        }        
        if(NULL == $from_lng){
            $from_lng = $this->Detect($str);            
        }
        if($from_lng == $to_lng){
            return $str;
        }
        $params = $this->params;
        $params['text'] = mb_substr($str,0,$this->max_len);
        $params['from'] = $from_lng;
        $params['to'] = $to_lng;
        $params['contentType'] = $contentType;
        //$authHeader = "Authorization: Bearer ".$this->access_token;
        //$params = $this->params;
        //$params['text'] = mb_substr($str,0,$this->max_len);
        //$params['appId'] = $this->access_token;
        $authHeader = "Authorization: Bearer ".$this->access_token;
        CurlTool::$options = array(
            CURLOPT_HTTPHEADER => array($authHeader,"Content-Type: text/xml"),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        );        
        
        $rs = CurlTool::fetchContent($uri.http_build_query($params),false);
        return strip_tags($rs);
    }    
}

?>