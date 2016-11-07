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

import org.apache.log4j.Logger;

import eu.optimus.energyprices.explorer.ConfigurationManager;


public class OmelDailyPricesModeler {
	
	private  final int NUMBER_OF_FIELDS_IN_DAILYPRICES = 6;


	private String fileName ;
	private static  org.apache.log4j.Logger log = Logger.getLogger(OmelDailyPricesModeler.class);
	
	public OmelDailyPricesModeler (String a)
	{	
		this.fileName = a ;
	}
	
	protected ArrayList <Object> modelDailyPrices (){
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		
		ArrayList <Object> res = new ArrayList <Object> ();
		String inputFile = ConfigurationManager.getConfiguration().getValue("outPutPath") +"\\" + fileName; 
		InputStream inputStream;
    try {
	    inputStream = new FileInputStream(inputFile);
	    Reader      reader      = new InputStreamReader(inputStream);
	    BufferedReader in = new BufferedReader(reader);
	    String line = null;	    
	    while((line = in.readLine()) != null) {
	    	String[] splitString = (line.split(";"));   
	    	if (splitString.length == NUMBER_OF_FIELDS_IN_DAILYPRICES ){
	    		res.add(this.createDailyPricesList (splitString));
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
		return ((ArrayList <Object>) res);
	}
		
	/**
	 * 
	 * @param splitString
	 * @return
	 * @throws ParseException 
	 */
	private OmelDailyPrices createDailyPricesList(String[] splitString) throws ParseException {
		OmelDailyPrices res  = new OmelDailyPrices ();
		String today = splitString[0]+splitString[1]+splitString[2];
		DateFormat formatter = new SimpleDateFormat("yyyyMMdd");
		res.setDate(formatter.parse(today));
		res.setHour(Integer.parseInt(splitString[3])-1);
		res.setValue(Float.parseFloat(splitString[4]));
		return res;
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
