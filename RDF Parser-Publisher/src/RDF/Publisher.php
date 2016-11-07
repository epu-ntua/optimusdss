<?php

namespace Sot\optimus\RDF;

class Publisher
{
    public $basicUri;
    public $requestHeader = "Content-Type: application/ztreamy-event\r\n";
    public $applicationId = "Optimus";
    public $useCurl = false;

    public function __construct()
    {
        //$this->basicUri = "http://arcdev.housing.salle.url.edu/optimus/ztreamy/";
    }

    public function getPublishUrl($aStream)
    {
        return $this->basicUri.$aStream."/publish";
    }

    public function getUid()
    {
        return uniqid();
    }

    public function publish($id, $aStream, $aTriple)
    {
        $event_id = $id;
        $source_id = $id;
        $body = $aTriple;
        date_default_timezone_set("Europe/Athens");
        //$serialized_header = "Event-Id: $event_id\r\nSource-Id: $aStream\r\nSyntax: text/plain\r\nTimestamp: "
        //.date("Y-m-d\TH:i:sP",time())."\r\nBody-Length: ".strlen($body)."\r\n";
        $serialized_header = "Event-Id: $event_id\r\nSource-Id: $aStream\r\nSyntax: text/plain\r\nTimestamp: "
            .date("Y-m-d\TH:i:s", time())."+03:00\r\nBody-Length: ".strlen($body)."\r\n\r\n";
        echo $data = $serialized_header.$body;
        if ($this->useCurl) {
            $header = array($this->requestHeader);
            $chandler = curl_init();
            $uri = $this->getPublishUrl($aStream);
            echo "uri= ".$uri."\n";
            curl_setopt($chandler, CURLOPT_URL, $this->getPublishUrl($aStream));
            curl_setopt($chandler, CURLOPT_TIMEOUT, 900);
            curl_setopt($chandler, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($chandler, CURLOPT_HTTPHEADER, $header);
            curl_setopt($chandler, CURLOPT_POST, true);
            curl_setopt($chandler, CURLOPT_POSTFIELDS, $data);
            $response = curl_exec($chandler);
            if (curl_errno($chandler)) {
                echo curl_error($chandler);
            }
            curl_close($chandler);
        } else {
            $params = array(
                'http' => array(
                    'header'  => $this->requestHeader . "Content-Length: " . strlen($data) . "\r\n",
                    'method'  => 'POST',
                    'content' => $data
                )
            );
            $this->getPublishUrl($aStream);

            //echo "basic uri =".$this->basicUri."\n";

            $ctx = stream_context_create($params);
            $fp = fopen($this->getPublishUrl($aStream), 'rb', false, $ctx);
            $response = stream_get_contents($fp);
        }

        //echo "Response: ".$response."\n";
    }
}
