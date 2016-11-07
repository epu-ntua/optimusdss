package eu.optimus.energyprices.datamodel.gme;

import java.util.ArrayList;
import java.util.Date;

import org.apache.log4j.Logger;

public class GMEDailyPricesContainer {
  /**
   * This class stores the 24 values for the dayahead energy prices
   * The date can be considered the 'id' for the value list. 
   * Stored in a map using the date as key.
   */
	private Date date;
	private String fileName;
	private ArrayList <GMEDailyPrices> data ;
	private static  org.apache.log4j.Logger log = Logger.getLogger(GMEDailyPricesContainer.class);
	/**
	 * 
	 * @param outPutFile
	 */
	public GMEDailyPricesContainer(String outPutFile) {
	  fileName = outPutFile;
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
	 * @return the fileName
	 */
	public String getFileName() {
		return fileName;
	}
	/**
	 * @param fileName the fileName to set
	 */
	public void setFileName(String fileName) {
		this.fileName = fileName;
	}
	/**
	 * @return the data
	 */
	public ArrayList<GMEDailyPrices> getData() {
		return data;
	}
	/**
	 * @param data the data to set
	 */
	public void setData(ArrayList<GMEDailyPrices> data) {
		this.data = data;
	}
	/**
	 * @return the OmelDailyPrices item
	 */
	public GMEDailyPrices getDataItem(int a) {
		return data.get(a);
	}
	/**
	 * @param Add a new OmelDailyPrice data item
	 */
	public void setDataItem(GMEDailyPrices a) {
		this.data.add(a);
	}
	public Boolean LoadData() {
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		Boolean res= false ;
		GMEDailyPricesModeler modeler = new GMEDailyPricesModeler(fileName);
		ArrayList <GMEDailyPrices> pl = modeler.modelDailyPrices();
	  if (pl.size() == 24) {
	  	this.setData(pl);
	  	res = true;
	  }
	  else pl= null;
	  
	  log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
	  return res;
	}
}
