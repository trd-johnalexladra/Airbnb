<?php

class cURL
{
    public $response;
    protected $sendHeader;
    protected $PostFields;
    private $query;

    public function __construct($query = '')
    {
        $this->sendHeader = false;
        $this->query = $query;
        if(!empty($this->query)){
            if(!is_array($this->query))
                $this->response =   $this->Connect($this->query);
            else
                $this->encode();
        }
    }

    public function SendPost($array = array())
    {
        $this->PostFields['payload'] = $array;
        $this->PostFields['query'] = http_build_query($array);
        return $this;
    }

    public  function Connect($_url,$deJSON = true)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if(strpos($_url,"https://") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
        }

        if(!empty($this->PostFields['payload'])) {
            curl_setopt($ch, CURLOPT_POST, count($this->PostFields['payload']));
            curl_setopt($ch, CURLOPT_POSTFIELSD, $this->PostFields['query']);
        }

        if(!empty($this->sendHeader))

            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11) AppleWebKit/601.1.56 (KHTML, like Gecko) Version/9.0 Safari/601.1.56');

        $decode = curl_exec($ch);
        $_response = ($deJSON)? json_decode($decode, true) : $decode;
        $error = curl_error($ch);

        curl_close($ch);
        return (empty($error))? $_response: $error;
    }

    public function emulateBrowser()
    {
        $this->sendHeader = true;
        return $this;
    }

    public function encode($_filter = 0)
    {
        foreach($this->query as $key => $value) {
            $string[]   =   urlencode($key).'='.urlencode($value);
        }

        if($_filter == true)
            $string =   array_filter($string);

        return implode("&",$string);
    }
}

$path   =   'https://www.airbnb.com/s/Fukuoka-Prefecture--Japan?guests=&checkin=10%2F28%2F2015&checkout=10%2F31%2F2015&ss_id=xrj64lwm&ss_preload=true&source=bb';
$cURL   =   new cURL();
$html   =   $cURL->emulateBrowser()->connect($path,false);
$dom    =   new DOMDocument;
@$dom->loadHTML($html);

foreach ($dom->getElementsByTagName('div') as $tag) {
    if ($tag->getAttribute('class') === 'col-sm-12 row-space-2 col-md-6') {
        echo $tag->nodeValue;
    }
}
