package eu.optimus.energyprices.explorer;

import java.util.ArrayList;

import org.apache.log4j.Logger;




import eu.optimus.energyprices.datamodel.omel.BiomassDailyPricesContainer;
import eu.optimus.energyprices.datamodel.omel.GasDailyPricesContainer;

public class BiomassExplorer {

	private static  org.apache.log4j.Logger log = Logger.getLogger(BiomassExplorer.class);
	private ArrayList<BiomassDailyPricesContainer> odpList = new ArrayList<BiomassDailyPricesContainer>(); //Omel daily prices
	//private ArrayList<GasDailyPricesContainer> oidpList = new ArrayList<GasDailyPricesContainer>(); // Omel intra daily prices
	
	
	public BiomassExplorer(){
	}
	
	/**
	 * 
	 * @return
	 */

	public  BiomassDailyPricesContainer getDailyPrices (String filename){
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		
		String outPutFile =  filename ;
		BiomassDailyPricesContainer odpcItem = null;
		log.debug("Reading data from file: " + outPutFile);
		odpcItem= new BiomassDailyPricesContainer(outPutFile);
		odpcItem.LoadData();
		this.odpList.add(odpcItem);
		
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		return odpcItem;
	}
	
}
