package eu.optimus.energyprices.datamodel.omel;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

import eu.optimus.energyprices.explorer.ConfigurationManager;

public class OmelDailyEnergyMarketSubtotals {
	private Date releaseDate;
	private Date valuesDate;
	private int hour;
	private int code;
	private String description;
	private ArrayList <Float> values = new ArrayList <Float>();
	
	protected String resource ;
	protected String sensor ;
	protected String city ;
	
	public OmelDailyEnergyMarketSubtotals(){
		resource 	= ConfigurationManager.getConfiguration().getValue("resourceurl");		
		city 			= ConfigurationManager.getConfiguration().getValue("pilot1_city");
		valuesDate = releaseDate = Calendar.getInstance().getTime();
		hour 	= 0;
		code	= 0;
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
	 * @return the releaseDate
	 */
	public Date getReleaseDate() {
		return releaseDate;
	}
	/**
	 * @param releaseDate the releaseDate to set
	 */
	public void setReleaseDate(Date releaseDate) {
		this.releaseDate = releaseDate;
	}
	/**
	 * @return the valuesDate
	 */
	public Date getValuesDate() {
		return valuesDate;
	}
	/**
	 * @param valuesDate the valuesDate to set
	 */
	public void setValuesDate(Date valuesDate) {
		this.valuesDate = valuesDate;
	}
	/**
	 * @return the values
	 */
	public ArrayList<Float> getValues() {
		return values;
	}
	/**
	 * @param values the values to set
	 */
	public void setValues(ArrayList<Float> values) {
		this.values = values;
	}
	
	/**
	 * @return the value item
	 */
	public float getValueItem(int a) {
		return values.get(a).floatValue();
	}
	/**
	 * @param add a new value item 
	 */
	public void setValueItem(float a) {
		this.values.add(a);
	}
	/**
	 * @return the code
	 */
	public int getCode() {
		return code;
	}
	/**
	 * @param code the code to set
	 */
	public void setCode(int code) {
		this.code = code;
	}
	/**
	 * @return the description
	 */
	public String getDescription() {
		return description;
	}
	/**
	 * @param description the description to set
	 */
	public void setDescription(String description) {
		this.description = description;
	}
	
	public String createRDF(float value) {
		DateFormat formatter = new SimpleDateFormat("yyyy-MM-dd");
		DateFormat timestampFormatter = new SimpleDateFormat("yyyyMMddHHmmss.SSS");
		String hhmmss = String.format("%02d:00:00", getHour());		
		String stDate = formatter.format(this.valuesDate);
		stDate =stDate + "T"+hhmmss+"Z";
		String stTimeStamp = timestampFormatter.format(Calendar.getInstance().getTime());
		sensor 		= ConfigurationManager.getConfiguration().getValue("sensor_marketsubtotals_"+code);
		String data = 
			"<"+resource + city +"observation/"+ sensor+stTimeStamp+">"+"ssn:observedBy"+ "<"+resource + city+ "sensingdevice/"+ sensor + ">.\n"+ 
			"<"+resource + city +"observation/"+ sensor+stTimeStamp+">ssn:observationResult"+"<"+resource + city +"sensoroutput/"+ sensor+stTimeStamp+">.\n"+
			"<"+resource + city +"observation/"+ sensor+stTimeStamp+">ssn:observationResultTime"+"<"+resource + city +"instant/"+ stTimeStamp+">.\n"+
			"<"+resource + city +"sensoroutput/"+ sensor+stTimeStamp+">ssn:hasValue" + "\"" +value +"\"^^xsd:decimal.\n"+
			"<"+resource + city +"instant/"+stTimeStamp +">time:inXSDDateTime" + "\"" +stDate +"\"^^xsd:dateTime.";
		return data;

  }
}
