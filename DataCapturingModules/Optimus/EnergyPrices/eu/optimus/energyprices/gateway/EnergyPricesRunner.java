package eu.optimus.energyprices.gateway;

import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

import org.apache.log4j.Logger;

import eu.optimus.energyprices.datamodel.global.DailyPrices;
import eu.optimus.energyprices.datamodel.gme.GMEDailyPricesContainer;
import eu.optimus.energyprices.datamodel.omel.BiomassDailyPricesContainer;
import eu.optimus.energyprices.datamodel.omel.GasDailyPrices;
import eu.optimus.energyprices.datamodel.omel.GasDailyPricesContainer;
import eu.optimus.energyprices.datamodel.omel.OmelDailyEnergyMarketSubtotals;
import eu.optimus.energyprices.datamodel.omel.OmelDailyEnergyMarketSubtotalsContainer;
import eu.optimus.energyprices.datamodel.omel.OmelDailyPricesContainer;
import eu.optimus.energyprices.datamodel.omel.OmelIntraDailyPricesModeler;
import eu.optimus.energyprices.explorer.BiomassExplorer;
import eu.optimus.energyprices.explorer.ConfigurationManager;
import eu.optimus.energyprices.explorer.GMEExplorer;
import eu.optimus.energyprices.explorer.GasExplorer;
import eu.optimus.energyprices.explorer.OmelExplorer;

public class EnergyPricesRunner {

	public static String sc_dailypricesstream =  "http://arcdev.housing.salle.url.edu/optimus/ztreamy/" +
																								"santcugat_electricity_energycost/publish" ;
			
	private static  org.apache.log4j.Logger log = Logger.getLogger(EnergyPricesRunner.class);
	
	public static int OmelFormatElectricityRunner (String fecha) throws IOException, InterruptedException, ParseException{
		
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		int res = 0;
		OmelDailyPricesContainer odpc;
		OmelExplorer oe = new OmelExplorer(); //Valido para formatos OMEL
		String stream = ConfigurationManager.getConfiguration().getValue("serversurl") +
										ConfigurationManager.getConfiguration().getValue("stream_pilot1_electricityprices") + "publish";
		log.debug("Publis stream: " + stream);
		String today =  new SimpleDateFormat("yyyyMMdd").format(new Date()); //
		Date fechaOrigen 	=  new SimpleDateFormat("yyyyMMdd").parse(fecha);
		Date fechaFin  		=  new SimpleDateFormat("yyyyMMdd").parse(today);
		while (!fechaOrigen.after(fechaFin)){
			odpc = oe.getDailyPrices(new SimpleDateFormat("yyyyMMdd").format(fechaOrigen));
			//odpc = oe.getDailyPrices("omel_dailyprices_20141003");
			if (odpc != null ){
				Calendar cal = Calendar.getInstance();
				cal.setTime(fechaOrigen);
				for (Object obj : odpc.getData()){
					Date fechaDato = cal.getTime();
					
					DailyPrices dp = (DailyPrices) obj;
					Publisher publisher = new Publisher(new URL(stream) , "C:\\DSS\\tecnalia\\MarketsGathering\\ztreamy.xml");			    
			    //String sourceId = Event.createUUID();
					String sourceId = ConfigurationManager.getConfiguration().getValue("stream_pilot1_electricityprices");
					sourceId= sourceId.replace("/", "");
			    try {
			    	int result = publisher.publish(new DailyPricesEvent(sourceId , dp));
				    if (result == 200) {
				    	log.info("An event just just been sent to the server for date: " + fechaDato);
				    } else {
				    	log.error("The server responded with error " + result);
				    }
			    }catch (IOException e){
			    	log.error("Error: " + e.getMessage() );
			    }
			    Thread.sleep(250);
			    cal.add(Calendar.HOUR, 1);  // number of days to add
				}
			}
			Calendar c = Calendar.getInstance();
			c.setTime(fechaOrigen);
			c.add(Calendar.DATE, 1);  // number of days to add
			fechaOrigen = c.getTime();  // dt is now the new date
		}		
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		return res;
	}
	/**
	 * 
	 * @return
	 * @throws IOException
	 * @throws InterruptedException
	 * @throws ParseException 
	 */
	public static int GMEFormatElectricityRunner (String fecha) throws IOException, InterruptedException, ParseException{
		
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		int res = 0;
		GMEDailyPricesContainer odpc = null;
		GMEExplorer ge = new GMEExplorer(); //Valido para formatos OMEL
		String stream = ConfigurationManager.getConfiguration().getValue("serversurl") +
										ConfigurationManager.getConfiguration().getValue("stream_pilot2_electricityprices") + "publish";
		log.debug("Publis stream: " + stream);
		String today =  new SimpleDateFormat("yyyyMMdd").format(new Date()); //
		Date fechaOrigen 	=  new SimpleDateFormat("yyyyMMdd").parse(fecha);
		Date fechaFin  		=  new SimpleDateFormat("yyyyMMdd").parse(today);
		while (!fechaOrigen.after(fechaFin)){
		//odpc = oe.getDailyPrices("omel_dailyprices_20141003");
			if (ge.getDailyPrices(new SimpleDateFormat("yyyyMMdd").format(fechaOrigen))==true){
				odpc = ge.getLastDailyPrices();
				for (Object obj : odpc.getData()){
					DailyPrices dp = (DailyPrices) obj;
					Publisher publisher = new Publisher(new URL(stream) , "C:\\DSS\\tecnalia\\MarketsGathering\\ztreamy.xml");			    
					String sourceId = ConfigurationManager.getConfiguration().getValue("stream_pilot2_electricityprices");
					sourceId= sourceId.replace("/", "");
					try {
			    	int result = publisher.publish(new DailyPricesEvent(sourceId , dp));
				    if (result == 200) {
				    	log.info("An event just just been sent to the server for date: " + fechaOrigen);
				    } else {
				    	log.error("The server responded with error " + result);
				    }
			    }catch (IOException e){
			    	log.error("Error: " + e.getMessage() );
			    }
					Thread.sleep(150);		    
				}
			}
			Calendar c = Calendar.getInstance();
			c.setTime(fechaOrigen);
			c.add(Calendar.DATE, 1);  // number of days to add
			fechaOrigen = c.getTime();  // dt is now the new date
		}
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		return res;
	}
	/**
	 * 
	 * @return
	 * @throws IOException
	 * @throws InterruptedException
	 */
	public static int OmelFormatGasRunner () throws IOException, InterruptedException{
		
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		int res = 0;
		GasDailyPricesContainer odpc;
		GasExplorer oe = new GasExplorer(); //Valido para formatos OMEL
//		String stream = ConfigurationManager.getConfiguration().getValue("serversurl") +
//										ConfigurationManager.getConfiguration().getValue("stream_pilot1_gasprices") + "publish";
//		
		String stream = ConfigurationManager.getConfiguration().getValue("serversurl") +
				ConfigurationManager.getConfiguration().getValue("stream_pilot1_gasprices") + "publish";

		log.debug("Publish stream: " + stream);
		//odpc = oe.getDailyPrices();
		odpc = oe.getDailyPrices("omel_dailyprices_20141002");
		if (odpc != null){
			for (Object obj : odpc.getData()){
				DailyPrices dp = (DailyPrices) obj;
				Publisher publisher = new Publisher(new URL(stream) , "C:\\DSS\\tecnalia\\MarketsGathering\\ztreamy.xml");			    
		    String sourceId = Event.createUUID();
		    int result = publisher.publish(new DailyPricesEvent(sourceId , dp));
		    if (result == 200) {
		    	log.info("An event just just been sent to the server");
		    } else {
		    	log.error("The server responded with error " + result);
		    }
		    Thread.sleep(5000);
		    
			}
		
		}
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		return res;
	}
	

	public static int OmelFormatBiomassRunner () throws IOException, InterruptedException{
		
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		int res = 0;
		BiomassDailyPricesContainer odpc;
		BiomassExplorer oe = new BiomassExplorer(); //Valido para formatos OMEL
//		String stream = ConfigurationManager.getConfiguration().getValue("serversurl") +
//										ConfigurationManager.getConfiguration().getValue("stream_pilot1_gasprices") + "publish";
//		
		String stream = ConfigurationManager.getConfiguration().getValue("serversurl") +
				ConfigurationManager.getConfiguration().getValue("stream_pilot1_biomassprices") + "publish";

		log.debug("Publish stream: " + stream);
		//odpc = oe.getDailyPrices();
		odpc = oe.getDailyPrices("omel_dailyprices_20141002");
		if (odpc != null){
			for (Object obj : odpc.getData()){
				DailyPrices dp = (DailyPrices) obj;
				Publisher publisher = new Publisher(new URL(stream) , "C:\\DSS\\tecnalia\\MarketsGathering\\ztreamy.xml");			    
		    String sourceId = Event.createUUID();
		    int result = publisher.publish(new DailyPricesEvent(sourceId , dp));
		    if (result == 200) {
		    	log.info("An event just just been sent to the server");
		    } else {
		    	log.error("The server responded with error " + result);
		    }
		    Thread.sleep(5000);
		    
			}
		
		}
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		return res;
	}
	
public static int OmelFormatMarketSubtotalsRunner () throws IOException, InterruptedException{
		
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		int res = 0;
		OmelDailyEnergyMarketSubtotalsContainer odemsList ;
		OmelExplorer oe = new OmelExplorer(); //Valido para formatos OMEL


		//log.debug("Publish stream: " + stream);
		odemsList = oe.getDailyEnergyMarketSubtotals();
		if (odemsList != null){
			for (OmelDailyEnergyMarketSubtotals odems : odemsList.getData()){
				
				String stream = ConfigurationManager.getConfiguration().getValue("serversurl") +
						ConfigurationManager.getConfiguration().getValue("stream_pilot1_marketsubtotals_"+odems.getCode()) + "publish";
				Publisher publisher = new Publisher(new URL(stream) , "C:\\DSS\\tecnalia\\MarketsGathering\\ztreamy.xml");
				for (Float value : odems.getValues()){
				  String sourceId = Event.createUUID();
				  int result = publisher.publish(new MarketSubtotalsEvent(sourceId , odems,value));
				  if (result == 200) {
				  	log.info("An event just just been sent to the server");
				  } else {
				  	log.error("The server responded with error " + result);
				  }
				  Thread.sleep(500);	
				}
			}
		
		}
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		return res;
	}
	
	
	public static void main(String[] args)  {
		Boolean loadOmel 	=	true;//false ;
		Boolean loadGME 	= false;
		
		try {
			log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		
			GMEExplorer ge;
			for (String s: args) {
				if (s.compareToIgnoreCase("omel") == 0) loadOmel =  true;
				if (s.compareToIgnoreCase("gme") == 0) loadGME =  true;
			}
			String today =  new SimpleDateFormat("yyyyMMdd").format(new Date()); //
			if (loadOmel == true) {
				OmelFormatElectricityRunner(today);
				//OmelFormatGasRunner ();
				//OmelFormatBiomassRunner ();
				//OmelFormatMarketSubtotalsRunner();
			}			
			if (loadGME == true) {
				GMEFormatElectricityRunner("20141001");
				//OmelFormatGasRunner ();
				//OmelFormatBiomassRunner () e.printStackTrace();;
				OmelFormatMarketSubtotalsRunner();
			}
			log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		}
		catch (IOException e){
			log.error("Error: " + e.getMessage() );
			e.printStackTrace();
		}
		catch (InterruptedException e){
			log.error("Error: " + e.getMessage() );
			 e.printStackTrace();
		} 
		catch (ParseException e) {
			log.error("Error: " + e.getMessage() );
	    e.printStackTrace();
    }
		catch (Exception e) {
	    e.printStackTrace();
    }
		System.out.println("Fin!! ");
	
	}//main

}//class
