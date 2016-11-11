
package gateway;


public class TestEvent extends Event {

    public TestEvent(String sourceId , int value) {
    	super(sourceId, "application/x-ztreamy-event", "1");
    	/*
         String data = "<http://www.optimus-smartcity.eu/resource/test/observation/2000568991_201409231613544890180> <http://purl.oclc.org/NET/ssnx/ssn#observedBy> <http://www.optimus-smartcity.eu/resource/test/sensor/2000568991>;\n"+ 
      		"<http://purl.oclc.org/NET/ssnx/ssn#observedProperty> <http://www.optimus-smartcity.eu/resource/test/PowerProperty>;\n" +
      		"<http://purl.oclc.org/NET/ssnx/ssn#observationResult> \"" + value +"\""+"<http://www.w3.org/2001/XMLSchema#decimal>;\n"+ 
      		"<http://purl.oclc.org/NET/ssnx/ssn#observationResultTime> <http://www.optimus-smartcity.eu/resource/test/instant/2000568991_201409231613544890180>.\n" + 
      	  "<http://www.optimus-smartcity.eu/resource/test/instant/2000568991_201409231613544890180> <http://www.w3.org/2006/time#inXSDDateTime> \"2014-08-25T00:00:00+00:00 \"" +"<http://www.w3.org/2001/XMLSchema#dateTime>.";
        */
    	
    	String data="<http://www.optimus-smartcity.eu/resources/sant_cugat/observation/sunnyportal_energy_production3> ssn:observedBy <http://www.optimus-smartcity.eu/resources/sant_cugat/sensingdevice/sunnyportal_energy_production>.\n"+
    			"<http://www.optimus-smartcity.eu/resources/sant_cugat/observation/sunnyportal_energy_production3> ssn:observationResult <http://www.optimus-smartcity.eu/resources/sant_cugat/sensoroutput/sunnyportal_energy_production3>.\n"+
    			"<http://www.optimus-smartcity.eu/resources/sant_cugat/observation/sunnyportal_energy_production3> ssn:observationResultTime <http://www.optimus-smartcity.eu/resources/sant_cugat/instant/201410031622>.\n"+
    			"<http://www.optimus-smartcity.eu/resources/sant_cugat/sensoroutput/sunnyportal_energy_production3> ssn:hasValue “11.11”^^xsd:decimal.\n"+
    			"<http://www.optimus-smartcity.eu/resources/sant_cugat/instant/201410031622> time:inXSDDateTime “2014-10-03T23:10:00Z“^^xsd:dateTime.\n";	
    	
    	
    	
        setBody(data + "\r\n");
    }
}
