package eu.optimus.energyprices.datamodel.omel;

import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.Map;

public class OmelDemandOfferPricesContainer {
	///TODO COMPLETE REVISION
	private Date date;
	private String fileName;
	private Map<Integer, OmelDemandOfferPrices> dataMap = new HashMap<Integer,OmelDemandOfferPrices>();
	private ArrayList <OmelDemandOfferPrices> data ; //OmelDailyPrices and OmelIntraDailyPrices type are equal
	/**
	 * 
	 * @param outPutFile
	 */
	public OmelDemandOfferPricesContainer(String outPutFile) {
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
	public ArrayList<OmelDemandOfferPrices> getData() {
		return data;
	}
	/**
	 * @param data the data to set
	 */
	public void setData(ArrayList<OmelDemandOfferPrices> data) {
		this.data = data;
	}
	/**
	 * @return the OmelDailyPrices item
	 */
	public OmelDemandOfferPrices getDataItem(int a) {
		return data.get(a);
	}
	/**
	 * @param Add a new OmelDailyPrice data item
	 */
	public void setDataItem(OmelDemandOfferPrices a) {
		this.data.add(a);
	}
	/**
	 * 
	 */
	public void LoadData() {
		OmelDailyEnergyMarketSubtotalsModeler modeler = new OmelDailyEnergyMarketSubtotalsModeler(fileName);
		ArrayList <OmelDailyEnergyMarketSubtotals> pl = modeler.modelDailyEnergyMarketSubtotals();
	  ///TODO  todo
		if (pl.size() > 0) {
	  	//for (OmelDemandOfferPrices item :pl)
	  	//	this.dataMap.put(item.getCode(), item);
	  }	  
  }
}
