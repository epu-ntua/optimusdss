package eu.optimus.energyprices.explorer;

import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.net.SocketException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

import org.apache.commons.net.ftp.FTP;
import org.apache.commons.net.ftp.FTPClient;
import org.apache.log4j.Logger;

import eu.optimus.energyprices.datamodel.gme.GMEDailyPricesContainer;
import eu.optimus.energyprices.datamodel.omel.OmelDailyEnergyMarketSubtotalsContainer;
import eu.optimus.energyprices.datamodel.omel.OmelDailyPricesContainer;

public class GMEExplorer {
	
	private String server = "download.mercatoelettrico.org";
  private int port = 21;
  private String user = "BORJATELLADO";
  private String pass = "O16A13L1";
  private static  org.apache.log4j.Logger log = Logger.getLogger(GMEExplorer.class);
  
  /// TODO Create appropiate classes
  private ArrayList<GMEDailyPricesContainer> odpList = new ArrayList<GMEDailyPricesContainer>(); //Omel daily prices
	//private ArrayList<OmelDailyPricesContainer> oidpList = new ArrayList<OmelDailyPricesContainer>(); // Omel intra daily prices
	//private ArrayList<OmelDailyEnergyMarketSubtotalsContainer> odemsList = new ArrayList<OmelDailyEnergyMarketSubtotalsContainer>(); //Omel energy market subtotals
  private FTPClient ftpClient = new FTPClient();
  
  
  public GMEDailyPricesContainer searchDailyPrices (String date){
  	log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		
  	GMEDailyPricesContainer res = null ;
		try {			
			for (GMEDailyPricesContainer item : odpList){
				String today =  new SimpleDateFormat("yyyyMMdd").format(new Date());
				if (today.compareTo(date)== 0) res = item;
			}
			
    } catch (Exception e) {
	    log.error("Error executing function: " + e.getMessage());
	    res = null;
    }
		
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		return res;
  	
  }
  /**
   * 
   * @return
   */
  public GMEDailyPricesContainer getLastDailyPrices (){
  	log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		
  	GMEDailyPricesContainer res = null ;
		try {	
			if (odpList.size()>0) res = odpList.get(odpList.size()-1);			
    } catch (Exception e) {
	    log.error("Error executing function: " + e.getMessage());
	    res = null;
    }
		
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		return res;
  	
  }
  
  public Boolean getDailyPrices (String fecha){
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		
		Boolean res = true ;
		try {			
			//String today =  new SimpleDateFormat("yyyyMMdd").format(new Date()); //
			String today =  fecha; //
			String dpFile = getDailyPricesFileURL(today);
			String outputFile =  "gme_dailyprices_" + today ;
		
			GMEDailyPricesContainer odpcItem = null;
		
	    if (Util.getRemoteFiles(server, port, user, pass, dpFile, outputFile)== true){
	    	odpcItem= new GMEDailyPricesContainer(outputFile);
				if (odpcItem.LoadData() == true)
					this.odpList.add(odpcItem);
	    }
    } catch (SocketException e) {
	    log.error("Error executing function: " + e.getMessage());
	    res = false;
    } catch (IOException e) {
	    log.error("Error executing function: " + e.getMessage());
	    res = false;
    }
		catch (Exception e) {
	    log.error("Error executing function: " + e.getMessage());
	    res = false;
    }
		
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		return res;
	}
  
  
  private String getDailyPricesFileURL(String today) 
  {
		String url = "";
		try {			
			String baseUrl = ConfigurationManager.getConfiguration().getValue("baseurl_italy");
			String fileUrl = ConfigurationManager.getConfiguration().getValue("dailyprices_italy");
			url = baseUrl +"/" + fileUrl+"/" + today + "MGPPrezzi.xml";
			log.debug("Daily prices url to use: " + url);
		}
		catch (Exception  e){
			log.error("Error creating the URL" + e.getMessage());
		}
	  return url;
  }
  /**
   * 
   * @param remoteFile
   * @param localFile
   * @return true if ok finished else throws exception or error
   * @throws SocketException
   * @throws IOException
   */
  /*
  public Boolean getRemoteFiles (String remoteFile , String localFile) throws SocketException, IOException
  {
  	log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
  	Boolean res = false;
  	
  	log.info("Opening FTP connetion to: " + server + " using: " +user + ":"+ pass);
  	ftpClient.connect(server, port);
  	
  	log.debug("Connected...");
	  ftpClient.login(user, pass);
  	
	  	  
	  log.debug("Loggin OK...");
	  ftpClient.enterLocalPassiveMode();
	  ftpClient.setFileType(FTP.ASCII_FILE_TYPE);
		  
	  String remoteFile1 = remoteFile;
	  File downloadFile1 = new File(localFile);
	  OutputStream outputStream1 = new BufferedOutputStream(new FileOutputStream(downloadFile1));
	  log.debug("Downloading file...");
	 
	  if (ftpClient.retrieveFile(remoteFile1, outputStream1)== true){
	  	log.info("FTP download succesful");
	  	res = true;
	  }	  	
	  else 
	  	log.error("FTP download finished with ERROR");
	  
	  outputStream1.close();
	  log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
	  return res ;
  }
  */
}
