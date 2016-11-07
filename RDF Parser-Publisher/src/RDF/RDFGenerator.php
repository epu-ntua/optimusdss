<?php

namespace Sot\optimus\RDF;

/**
*   This class handles the generation of the RDF triples
*/
class RDFGenerator
{
    public function generate($id, $location, $sensor, $timestamp, $value)
    {
        $value = str_replace(",", ".", $value);
        $datetime = date("Y-m-d H:i:s", strtotime($timestamp));
        $temp_date = explode(" ", $datetime);
        $datetime = $temp_date[0]."T".$temp_date[1]."Z";
        $microtime = date("YmdHis", strtotime($timestamp));
        $location = str_replace(" ", "_", $location);
        $location = strtolower($location);




        return "<http://www.optimus-smartcity.eu/resource/$location/observation/$sensor$id> ssn:observedBy "
        ."<http://www.optimus-smartcity.eu/resource/$location/sensingdevice/$sensor> .\r\n"
        ."<http://www.optimus-smartcity.eu/resource/$location/observation/$sensor$id> ssn:observationResult "
        ."<http://www.optimus-smartcity.eu/resource/$location/sensoroutput/$sensor$id> .\r\n"
        ."<http://www.optimus-smartcity.eu/resource/$location/observation/$sensor$id> ssn:observationResultTime "
        ."<http://www.optimus-smartcity.eu/resource/$location/instant/$microtime> .\r\n"
        ."<http://www.optimus-smartcity.eu/resource/$location/sensoroutput/$sensor$id> "
        ."ssn:hasValue \"$value\"^^xsd:decimal .\r\n"
        ."<http://www.optimus-smartcity.eu/resource/$location/instant/$microtime> time:inXSDDateTime "
        ."\"$datetime\"^^xsd:dateTime .";
    }
}
