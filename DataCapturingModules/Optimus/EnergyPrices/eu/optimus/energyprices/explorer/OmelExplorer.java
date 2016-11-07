package eu.optimus.energyprices.explorer;

import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

import org.apache.log4j.Logger;

import eu.optimus.energyprices.datamodel.omel.OmelDailyEnergyMarketSubtotalsContainer;
import eu.optimus.energyprices.datamodel.omel.OmelDailyPricesContainer;

public class OmelExplorer {

	private static  org.apache.log4j.Logger log = Logger.getLogger(OmelExplorer.class);
	private ArrayList<OmelDailyPricesContainer> odpList = new ArrayList<OmelDailyPricesContainer>(); //Omel daily prices
	private ArrayList<OmelDailyPricesContainer> oidpList = new ArrayList<OmelDailyPricesContainer>(); // Omel intra daily prices
	private ArrayList<OmelDailyEnergyMarketSubtotalsContainer> odemsList = new ArrayList<OmelDailyEnergyMarketSubtotalsContainer>(); //Omel energy market subtotals
	
	
	
	/**
	 * 
	 * @return
	 */
	public OmelDailyPricesContainer getDailyPrices (String fecha){
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		
		String isProxy = ConfigurationManager.getConfiguration().getValue("proxy");
		String cmd = "";
		String today = "";
		if (fecha == null)
			today =  new SimpleDateFormat("yyyyMMdd").format(new Date()); //
		else 
			today = fecha;
		String dpURL = getDailyPricesFileURL(today);
		if (isProxy.compareTo("true")== 0) 
			cmd = "curl -x " + ConfigurationManager.getConfiguration().getValue("proxyHost") + " " +dpURL ;
		else
			cmd = "curl " + dpURL;
		log.debug("Command to execute: " + cmd );
		String outPutFile =  "omel_dailyprices_" + today ;
		OmelDailyPricesContainer odpcItem = null;
		if (Util.exCommand(cmd, outPutFile)== true){
			odpcItem= new OmelDailyPricesContainer(outPutFile);
			if (odpcItem.LoadData()== true){
				this.odpList.add(odpcItem);
			}
			else odpcItem =  null;
		}
		
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		return odpcItem;
	}
	
	public OmelDailyPricesContainer getDailyPricesFromFile (String filename){
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		
		String outPutFile =  filename ;
		OmelDailyPricesContainer odpcItem = null;
		log.debug("Reading data from file: " + outPutFile);
		odpcItem= new OmelDailyPricesContainer(outPutFile);
		if (odpcItem.LoadData() == true){
			this.odpList.add(odpcItem);
		}
		else odpcItem = null;
		
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		return odpcItem;
	}
	
	/**
	 * 
	 * @return
	 */
	public Boolean getIntraDailyPrices (){
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		
		Boolean res = true ;
		String isProxy = ConfigurationManager.getConfiguration().getValue("proxy");
		String cmd = "";
		String today =  new SimpleDateFormat("yyyyMMdd").format(new Date()); //
		String dpURL = getIntraDailyPricesFileURL(today);
		
		if (isProxy.compareTo("true")== 0) 
			cmd = "curl -x " + ConfigurationManager.getConfiguration().getValue("proxyHost") + " " +dpURL ;
		else
			cmd = "curl " + dpURL;
		
		//String outPutFile = ConfigurationManager.getConfiguration().getValue("outPutPath") +"\\"+ "intradailyprices_" + today ;
		String outPutFile =  "omel_intradailyprices_" + today ;
		OmelDailyPricesContainer odpcItem = null;
		if (Util.exCommand(cmd, outPutFile)== true){
			odpcItem= new OmelDailyPricesContainer(outPutFile);
			odpcItem.LoadData();
			this.oidpList.add(odpcItem);
		} 
		
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );	 
		return res;
	}
	/**
	 * 
	 * @return true correct , false error
	 */
	public  OmelDailyEnergyMarketSubtotalsContainer getDailyEnergyMarketSubtotals (){
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		
		Boolean res = false ;
		SimpleDateFormat formatter = new SimpleDateFormat("yyyyMMdd"); 
		String isProxy = ConfigurationManager.getConfiguration().getValue("proxy");
		String cmd = "";
		OmelDailyEnergyMarketSubtotalsContainer odpcItem = null;
		try {
			Calendar calendar = Calendar.getInstance();
			//Truncated date
			String stToday 	=  formatter.format(calendar.getTime()); //
			Date today 			= formatter.parse(stToday);
			String outPutFile =  "omel_dailyenergymaketsubtotal_" + stToday ;
			int iter = 0 ; //Set the limit of days in 10
			while ((res != true) && (iter++<10)){			
				stToday =  new SimpleDateFormat("yyyyMMdd").format(today); //
				outPutFile =  "dailyenergymaketsubtotal_" + stToday ;
				String dpURL = getDailyEnergyMarketSubtotalsFileURL(stToday);
				if (isProxy.compareTo("true")== 0) 
					cmd = "curl -x " + ConfigurationManager.getConfiguration().getValue("proxyHost") + " " +dpURL ;
				else
					cmd = "curl " + dpURL;
				res = Util.exCommand(cmd, outPutFile);
				calendar.add(Calendar.DAY_OF_YEAR, -1);
				today = calendar.getTime();
			}
			odpcItem= new OmelDailyEnergyMarketSubtotalsContainer(outPutFile);
			odpcItem.LoadData();
			this.odemsList.add(odpcItem);
		}
		catch (ParseException e){
			res = false ;
		}
					
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );	 
		return odpcItem;
	}
	/**
	 * 
	 * @return
	 */
	public Boolean getDailyDemandOfferCurve (){
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		
		Boolean res = true ;
		String isProxy = ConfigurationManager.getConfiguration().getValue("proxy");
		String cmd = "";
		String today =  new SimpleDateFormat("yyyyMMdd").format(new Date()); //
		String dpURL = this.getDemandOfferFileURL(today);
		if (isProxy.compareTo("true")== 0) 
			cmd = "curl -x " + ConfigurationManager.getConfiguration().getValue("proxyHost") + " " +dpURL ;
		else
			cmd = "curl " + dpURL;
		String outPutFile =  "omel_demandoffercurve_" + today ;
		//OmelDailyEnergyMarketSubtotalsContainer odpcItem = null;
		if (Util.exCommand(cmd, outPutFile)== true){
			//odpcItem= new OmelDailyEnergyMarketSubtotalsContainer(outPutFile);
			//odpcItem.LoadData();
			//this.odemsList.add(odpcItem);
		} 
		
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );	 
		return res;
	}
	
	/**
	 * This functions get the energy prices daily file name
	 * @return
	 */
	private String getDailyPricesFileURL(String today) {
		String url = "";
		try {			
			String baseUrl = ConfigurationManager.getConfiguration().getValue("baseurl_spain");
			String fileUrl = ConfigurationManager.getConfiguration().getValue("dailyprices_spain");
			url = baseUrl +"/" + fileUrl+"/" + fileUrl +"_" + today + ".1";
			log.debug("Daily prices url to use: " + url);
		}
		catch (Exception  e){
			log.error("Error creating the URL" + e.getMessage());
		}
	  return url;
  }
	/**
	 * 
	 * @param today
	 * @return
	 */
	private String getIntraDailyPricesFileURL(String today) {
		String url = "";
		try {			
			String baseUrl = ConfigurationManager.getConfiguration().getValue("baseurl_spain");
			String fileUrl = ConfigurationManager.getConfiguration().getValue("intradailyprices_spain");
			url = baseUrl +"/" + fileUrl+"/" + fileUrl +"_" + today + "01.1";
			log.debug("Intra daily prices url to use: " + url);
		}
		catch (Exception  e){
			log.error("Error creating the URL" + e.getMessage());
		}
	  return url;
  }
	/***
	 * 
	 * @param today
	 * @return
	 */
	private String getDailyEnergyMarketSubtotalsFileURL(String today) {
		String url = "";
		try {
			//TODO Verificar URL
			String baseUrl = ConfigurationManager.getConfiguration().getValue("baseurl_spain");
			String fileUrl = ConfigurationManager.getConfiguration().getValue("dailymarketsubtotals_spain");
			url = baseUrl +"/" + fileUrl+"/" + fileUrl +"_" + today + ".1";
			log.debug ("Daily market subtotals url to use:" + url);
		}
		catch (Exception  e){
			log.error("Error creating the URL" + e.getMessage());
		}
	  return url;
  }
	/***
	 * 
	 * @param today
	 * @return
	 */
	private String getDemandOfferFileURL(String today) {
		String url = "";
		try {
			//TODO Verificar URL
			String baseUrl = ConfigurationManager.getConfiguration().getValue("baseurl_spain");
			String fileUrl = ConfigurationManager.getConfiguration().getValue("demandoffer_spain");
			url = baseUrl +"/" + fileUrl+"/" + fileUrl +"_" + today + ".1";
			log.debug ("Daily market subtotals url to use:" + url);
		}
		catch (Exception  e){
			log.error("Error creating the URL" + e.getMessage());
		}
	  return url;
  }
	
}
