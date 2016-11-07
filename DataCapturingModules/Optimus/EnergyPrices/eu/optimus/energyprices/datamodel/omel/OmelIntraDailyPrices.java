package eu.optimus.energyprices.datamodel.omel;

import java.util.Date;

public class OmelIntraDailyPrices {	
	private int hour;
	private Date date;
	private float value;
	
	
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
	
}
