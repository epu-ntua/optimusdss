package eu.optimus.energyprices.explorer;

import java.util.ArrayList;

import org.apache.log4j.Logger;



import eu.optimus.energyprices.datamodel.omel.GasDailyPricesContainer;

public class GasExplorer {

	private static  org.apache.log4j.Logger log = Logger.getLogger(GasExplorer.class);
	private ArrayList<GasDailyPricesContainer> odpList = new ArrayList<GasDailyPricesContainer>(); //Omel daily prices
	//private ArrayList<GasDailyPricesContainer> oidpList = new ArrayList<GasDailyPricesContainer>(); // Omel intra daily prices
	
	
	public GasExplorer(){
	}
	
	/**
	 * 
	 * @return
	 */

	public GasDailyPricesContainer getDailyPrices (String filename){
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		
		String outPutFile =  filename ;
		GasDailyPricesContainer odpcItem = null;
		log.debug("Reading data from file: " + outPutFile);
		odpcItem= new GasDailyPricesContainer(outPutFile);
		odpcItem.LoadData();
		this.odpList.add(odpcItem);
		
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		return odpcItem;
	}
	
}
