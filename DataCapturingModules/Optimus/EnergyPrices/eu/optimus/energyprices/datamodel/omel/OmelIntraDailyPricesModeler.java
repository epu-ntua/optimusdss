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


public class OmelIntraDailyPricesModeler {
		
	private  final int NUMBER_OF_FIELDS_IN_INTRADAILYPRICES = 6;

	private String fileName ;
	private static  org.apache.log4j.Logger log = Logger.getLogger(OmelIntraDailyPricesModeler.class);
	
	public OmelIntraDailyPricesModeler (String a)
	{	
		this.fileName = a ;
	}
	
	public ArrayList <OmelDailyPrices> modelIntraDailyPrices (){
		ArrayList <OmelDailyPrices> res = new ArrayList <OmelDailyPrices> ();
		String inputFile = ConfigurationManager.getConfiguration().getValue("outPutPath") +"\\" + fileName; 
		InputStream inputStream;
    try {
	    inputStream = new FileInputStream(inputFile);
	    Reader      reader      = new InputStreamReader(inputStream);
	    BufferedReader in = new BufferedReader(reader);
	    String line = null;	    
	    while((line = in.readLine()) != null) {
	    	String[] splitString = (line.split(";"));   
	    	if (splitString.length == NUMBER_OF_FIELDS_IN_INTRADAILYPRICES ){
	    		res.add(this.createIntraDailyPricesList (splitString));
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
		return res;
	}
		
	/**
	 * 
	 * @param splitString
	 * @return
	 * @throws ParseException 
	 */
	private OmelDailyPrices createIntraDailyPricesList(String[] splitString) throws ParseException {
		OmelDailyPrices res  = new OmelDailyPrices ();
		String today = splitString[0]+splitString[1]+splitString[2];
		DateFormat formatter = new SimpleDateFormat("yyyyMMdd");
		res.setDate(formatter.parse(today));
		res.setHour(Integer.parseInt(splitString[3]));
		res.setValue(Float.parseFloat(splitString[4]));
		return res;
  }
	

}
