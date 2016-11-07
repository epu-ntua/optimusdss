package eu.optimus.energyprices.datamodel.omel;

import java.io.BufferedReader;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.Reader;
import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

import org.apache.log4j.Logger;

import eu.optimus.energyprices.explorer.ConfigurationManager;


public class OmelDailyEnergyMarketSubtotalsModeler {
		
	private  final int NUMBER_OF_FIELDS_IN_DAILYMARKETSUBTOTALS = 29;
	private  final int NUMBER_OF_FIELDS_IN_DAILYMARKETSUBTOTALSHEADER= 9;	
	private Date releaseDate;
	private Date valueDate;
	private String fileName ;
	private static  org.apache.log4j.Logger log = Logger.getLogger(OmelDailyEnergyMarketSubtotalsModeler.class);
	
	public OmelDailyEnergyMarketSubtotalsModeler (String a)
	{	
		this.fileName = a ;
	}
	/**
	 * 
	 * @return
	 */
	public ArrayList <OmelDailyEnergyMarketSubtotals> modelDailyEnergyMarketSubtotals (){

		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		
		ArrayList <OmelDailyEnergyMarketSubtotals> res = new ArrayList <OmelDailyEnergyMarketSubtotals> ();
		String inputFile = ConfigurationManager.getConfiguration().getValue("outPutPath") +"\\" + fileName; 
		InputStream inputStream;
    try {
	    inputStream = new FileInputStream(inputFile);
	    Reader      reader      = new InputStreamReader(inputStream);
	    BufferedReader in = new BufferedReader(reader);
	    String line = null;	    
	    while((line = in.readLine()) != null) {
	    	String[] splitString = (line.split(";",-1));   
	    	log.debug("Parsing OMEL subtolals item: " + line +" Total items: " + splitString.length);
	    	if (splitString.length == NUMBER_OF_FIELDS_IN_DAILYMARKETSUBTOTALS ){
	    		log.debug("Parsing OMEL subtolals item: " + line);
	    		OmelDailyEnergyMarketSubtotals item = this.createDailyEnergyMarketSubtotalsList (splitString) ; 
	    		if (item != null) res.add(item);
	    	}
	    	else if (splitString.length == NUMBER_OF_FIELDS_IN_DAILYMARKETSUBTOTALSHEADER){
	    		this.createDailyEnergyMarketSubtotalsListHeader(splitString);
	    	}
	    }	    	
	    //Close all buffers
	    in.close();
	    reader.close();    
			inputStream.close();
			
    } catch (FileNotFoundException e) {
    	log.error("Modeling daily prices" + e.getMessage());
    } catch (IOException e) {
    	log.error("Modeling daily prices" + e.getMessage());
    } catch (ParseException e) {
    	log.error("Modeling daily prices" + e.getMessage());
    }
    
    log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
    return res;
	}
		
	/**
	 * 
	 * @param splitString
	 * @return
	 * @throws ParseException 
	 */
	private OmelDailyEnergyMarketSubtotals createDailyEnergyMarketSubtotalsList(String[] splitString) throws ParseException {
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		
		OmelDailyEnergyMarketSubtotals res  = null;
		try {
			if (Integer.valueOf(splitString[0]) == 43 || Integer.valueOf(splitString[0]) == 59){
				res  = new OmelDailyEnergyMarketSubtotals ();
				res.setCode(Integer.valueOf(splitString[0]));
				res.setReleaseDate(releaseDate);
				res.setValuesDate(valueDate);
				for (int i = 0 ; i<25; i++){
					String valor = splitString[i+3].replace(".", ""); //transform from 3.3434,5 to 33434.5
					valor = valor.replace(",",".");
					if (splitString[i+3].length()>0  &&  splitString[i+3].compareTo(" ") != 0)res.setValueItem(Float.valueOf(valor));
					else res.setValueItem(0);
				}
			} 
										
		}	
		catch (Exception e){
			log.error("Exception reading value" + e.getMessage());				
			res = null;
		}
		
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		return res;
  }
	/**
	 * 
	 * @param splitString
	 * @throws ParseException
	 */
	private void createDailyEnergyMarketSubtotalsListHeader(String[] splitString) throws ParseException {
		String relDate = splitString[1];
		DateFormat formatter = new SimpleDateFormat("dd/MM/yyyy - HH");
		releaseDate = formatter.parse(relDate.split(":")[1]);
		formatter = new SimpleDateFormat("dd/MM/yyyy");
		valueDate = formatter.parse(splitString[3]);			
		
  }
	/**
	 * @return the releaseDate
	 */
	public Date getReleaseDate() {
		return releaseDate;
	}
	/**
	 * @param releaseDate the releaseDate to set
	 */
	public void setReleaseDate(Date releaseDate) {
		this.releaseDate = releaseDate;
	}
	/**
	 * @return the valueDate
	 */
	public Date getValueDate() {
		return valueDate;
	}
	/**
	 * @param valueDate the valueDate to set
	 */
	public void setValueDate(Date valueDate) {
		this.valueDate = valueDate;
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
	
}
