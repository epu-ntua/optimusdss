package eu.optimus.energyprices.datamodel.omel;

import java.util.ArrayList;
import java.util.Date;

import org.apache.log4j.Logger;

public class OmelDailyPricesContainer {

	private Date date;
	private String fileName;
	protected ArrayList <Object> data ;
	private static  org.apache.log4j.Logger log = Logger.getLogger(OmelDailyPricesContainer.class);
	/**
	 * 
	 * @param outPutFile
	 */
	public OmelDailyPricesContainer(String outPutFile) {
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

	public ArrayList<Object> getData() {
		return data;
	}
	/**
	 * @param data the data to set
	 */
	public void setData(ArrayList<Object> data) {
		this.data = data;
	}
	/**
	 * @return the OmelDailyPrices item
	 */
	public Object getDataItem(int a) {
		return data.get(a);
	}
	/**
	 * @param Add a new OmelDailyPrice data item
	 */
	public void setDataItem(OmelDailyPrices a) {
		this.data.add(a);
	}
	public Boolean LoadData() {
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		Boolean res = false ;
		OmelDailyPricesModeler modeler = new OmelDailyPricesModeler(fileName);
		ArrayList <Object> pl = modeler.modelDailyPrices();
	  if (pl.size() == 24) {
	  	this.setData(pl);
	  	res = true ;
	  }
	  else pl= null;
	  log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
	  return res;
  }
}
