
package gateway;

import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Locale;
import java.util.TimeZone;

import log.Log;


public class DataCapturingModulesEvent extends Event {

    public DataCapturingModulesEvent(String sourceId , String resource_uri, String stream_name, String sensor_name, long timestamp, double value) {
    	super(sourceId, "application/x-ztreamy-event", "1");
    	long id=Calendar.getInstance().getTimeInMillis();
    	Calendar cal=Calendar.getInstance();
    	cal.setTimeZone(TimeZone.getTimeZone("UTC"));
    	cal.setTimeInMillis(timestamp*1000);
    	Calendar calCET=Calendar.getInstance();
    	calCET.setTimeZone(TimeZone.getTimeZone("Europe/Rome"));
    	calCET.setTimeInMillis(timestamp*1000);
    	int year=cal.get(Calendar.YEAR);
    	if(year<2000)
    		cal.add(Calendar.YEAR, 2000);
    	String yearStr=cal.get(Calendar.YEAR)+"";
    	if(yearStr.indexOf("0")==0)
    		yearStr="2"+yearStr.substring(1);
    	String monthStr=(cal.get(Calendar.MONTH)+1)+"";
  	  	String dayStr=cal.get(Calendar.DAY_OF_MONTH)+"";
  	  	String hour=calCET.get(Calendar.HOUR_OF_DAY)+"";
  	  	String min=cal.get(Calendar.MINUTE)+"";
  	  	String sec=cal.get(Calendar.SECOND)+"";
  	  	monthStr=(monthStr.length()<2)?("0"+monthStr):monthStr;
  	  	dayStr=(dayStr.length()<2)?("0"+dayStr):dayStr;
  	  	hour=(hour.length()<2)?("0"+hour):hour;
  	  	min=(min.length()<2)?("0"+min):min;
  	  	sec=(sec.length()<2)?("0"+sec):sec;
    	String timeId=cal.get(Calendar.YEAR)+""+monthStr+""+dayStr+""+hour+min+sec+"000";
    	SimpleDateFormat destFormatter = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss'Z'");    
    	String time_in_xsd = destFormatter.format(cal.getTime());
    	//String time_in_xsd=cal.get(Calendar.YEAR)+"-"+monthStr+"-"+dayStr+"T"+hour+":"+min+":"+sec+"Z"; //{year}-{month}-{day}T{hour}:{miute}:{second}Z 
    	String data="<"+resource_uri+"/observation/"+sensor_name+""+id+"> ssn:observedBy <"+resource_uri+"/sensingdevice/"+sensor_name+">.\n"+
    			"<"+resource_uri+"/observation/"+sensor_name+""+id+"> ssn:observationResult <"+resource_uri+"/sensoroutput/"+sensor_name+""+id+">.\n"+
    			"<"+resource_uri+"/observation/"+sensor_name+""+id+"> ssn:observationResultTime <"+resource_uri+"/instant/"+timeId+">.\n"+
    			"<"+resource_uri+"/sensoroutput/"+sensor_name+""+id+"> ssn:hasValue \""+value+"\"^^xsd:decimal.\n"+
    			"<"+resource_uri+"/instant/"+timeId+"> time:inXSDDateTime \""+time_in_xsd+"\"^^xsd:dateTime.\n";	
    	
    	
    	//Log.getInstance().printPacketLog(data);
        
    	setBody(data + "\r\n");
    }
}
