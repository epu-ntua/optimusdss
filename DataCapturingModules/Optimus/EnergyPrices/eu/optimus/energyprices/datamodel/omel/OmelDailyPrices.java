package eu.optimus.energyprices.datamodel.omel;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;

import eu.optimus.energyprices.datamodel.global.DailyPrices;
import eu.optimus.energyprices.explorer.ConfigurationManager;

public class OmelDailyPrices extends DailyPrices {
	private Date date;
	private int hour;
	private float value;
	
	public OmelDailyPrices(){
		resource 	= ConfigurationManager.getConfiguration().getValue("resourceurl");
		sensor 		= ConfigurationManager.getConfiguration().getValue("sensor_electricityprices");
		city 			= ConfigurationManager.getConfiguration().getValue("pilot1_city");
		date = Calendar.getInstance().getTime();
		hour = 0;
		value= 0.0f;
		
	}
	/**
	 * @return the date
	 */
	public Date getDate() {
		return date;
	}
	/**
	 * @param date the date to set
	 */
	public void setDate(Date date) {
		this.date = date;
	}
	/**
	 * @return the hour
	 */
	public int getHour() {
		return hour;
	}
	/**
	 * @param hour the hour to set
	 */
	public void setHour(int hour) {
		this.hour = hour;
	}
	/**
	 * @return the value
	 */
	public float getValue() {
		return value;
	}
	/**
	 * @param value the value to set
	 */
	public void setValue(float value) {
		this.value = value;
	}
	@Override
	public String createRDF() {
		DateFormat formatter = new SimpleDateFormat("yyyy-MM-dd");
		DateFormat timestampFormatter = new SimpleDateFormat("yyyyMMddHHmmss.SSS");
		String hhmmss = String.format("%02d:00:00", getHour());		
		String stDate = formatter.format(this.getDate());
		stDate =stDate + "T"+hhmmss+"Z";
		String stTimeStamp = timestampFormatter.format(Calendar.getInstance().getTime());

	String data = 
"<"+resource + city +"observation/"+ sensor+stTimeStamp+">"+"ssn:observedBy"+ "<"+resource + city+ "sensingdevice/"+ sensor + ">.\n"+ 
"<"+resource + city +"observation/"+ sensor+stTimeStamp+">ssn:observationResult"+"<"+resource + city +"sensoroutput/"+ sensor+stTimeStamp+">.\n"+
"<"+resource + city +"observation/"+ sensor+stTimeStamp+">ssn:observationResultTime"+"<"+resource + city +"instant/"+ stTimeStamp+">.\n"+
"<"+resource + city +"sensoroutput/"+ sensor+stTimeStamp+">ssn:hasValue" + "\"" + value +"\"^^xsd:decimal.\n"+
"<"+resource + city +"instant/"+stTimeStamp +">time:inXSDDateTime" + "\"" +stDate +"\"^^xsd:dateTime.";

	
	//	"<"+resource + city +"observation/"+ sensor+stTimeStamp+">ssn#observationResult"+"\"" + value +"\""+"<"+resource + city +"sensoroutput/"+ sensor+stTimeStamp+">";
//+"^^<http://www.w3.org/2001/XMLSchema#decimal>;\n"+ 
//"<http://purl.oclc.org/NET/ssnx/ssn#observationResultTime> <"+ resource + city + "instant/"+sensor+stTimeStamp+">.\n" + 
//"<"+ resource + city + "instant/"+stTimeStamp+"> <http://www.w3.org/2006/time#inXSDDateTime> \"" + stDate +"\"^^<http://www.w3.org/2001/XMLSchema#dateTime>.";

		
		
//	Este esta ok
//		String data = "<"+resource + city +"observation/"+ sensor+stTimeStamp+">"+
//	"<http://purl.oclc.org/NET/ssnx/ssn#observedBy>"+ "<"+resource + city+ "sensingdevice/"+ sensor + ">;\n"+ 
//	"<http://purl.oclc.org/NET/ssnx/ssn#observedProperty>"+ "<"+resource + city +""+ sensor+">;\n" +
//	"<http://purl.oclc.org/NET/ssnx/ssn#observationResult> \"" + value +"\""+"^^<http://www.w3.org/2001/XMLSchema#decimal>;\n"+ 
//	"<http://purl.oclc.org/NET/ssnx/ssn#observationResultTime> <"+ resource + city + "instant/"+sensor+stTimeStamp+">.\n" + 
//  "<"+ resource + city + "instant/"+stTimeStamp+"> <http://www.w3.org/2006/time#inXSDDateTime> \"" + stDate +"\"^^<http://www.w3.org/2001/XMLSchema#dateTime>.";

//		String data = "<http://www.optimus-smartcity.eu/resource/test/observation/"+resource+"_"+stDate+">"+
//				"<http://purl.oclc.org/NET/ssnx/ssn#observedBy>+ <http://www.optimus-smartcity.eu/resource/test/sensor/"+resource+">;\n"+ 
//    		"<http://purl.oclc.org/NET/ssnx/ssn#observedProperty> <http://www.optimus-smartcity.eu/resource/test/PowerProperty>;\n" +
//    		"<http://purl.oclc.org/NET/ssnx/ssn#observationResult> \"" + value +"\""+"^^<http://www.w3.org/2001/XMLSchema#decimal>;\n"+ 
//    		"<http://purl.oclc.org/NET/ssnx/ssn#observationResultTime> <http://www.optimus-smartcity.eu/resource/test/instant/"+resource+"_"+stDate+">.\n" + 
//    	  "<http://www.optimus-smartcity.eu/resource/test/instant/"+resource+"_"+stDate+"> <http://www.w3.org/2006/time#inXSDDateTime> \"2014-08-25T00:00:00+00:00 \"" +"^^<http://www.w3.org/2001/XMLSchema#dateTime>.";
		return data;
	}	
}
