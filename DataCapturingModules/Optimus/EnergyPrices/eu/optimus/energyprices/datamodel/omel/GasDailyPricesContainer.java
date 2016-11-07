package eu.optimus.energyprices.datamodel.omel;

import java.util.ArrayList;

import org.apache.log4j.Logger;

public class GasDailyPricesContainer extends OmelDailyPricesContainer {
	
	//private ArrayList <GasDailyPrices> data ;
	private static  org.apache.log4j.Logger log = Logger.getLogger(GasDailyPricesContainer.class);
	
	public GasDailyPricesContainer(String outPutFile) {
	  super(outPutFile);
	  // TODO Auto-generated constructor stub
  }
	
	/**
	 * @return the data
	 */

	
	public Boolean LoadData() {
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		String fileName = this.getFileName();
		GasDailyPricesModeler modeler = new GasDailyPricesModeler(fileName);
		ArrayList <Object> pl = modeler.modelDailyPrices();
	  if (pl.size() == 24) this.setData(pl);
	  else pl= null;
	  
	  log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
	  return true;
	}

}
