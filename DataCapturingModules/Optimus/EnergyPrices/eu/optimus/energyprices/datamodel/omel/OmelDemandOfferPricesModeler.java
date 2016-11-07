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


public class OmelDemandOfferPricesModeler {
	///TODO COMPLETE REVISION
	private  final int NUMBER_OF_FIELDS_IN_DEMANDOFFERPRICES = 8;

	private String fileName ;
	private static  org.apache.log4j.Logger log = Logger.getLogger(OmelDemandOfferPricesModeler.class);
	
	public OmelDemandOfferPricesModeler (String a)
	{	
		this.fileName = a ;
	}
	
	public ArrayList <OmelDemandOfferPrices> modelIntraDailyPrices (){
		ArrayList <OmelDemandOfferPrices> res = new ArrayList <OmelDemandOfferPrices> ();
		String inputFile = ConfigurationManager.getConfiguration().getValue("outPutPath") +"\\" + fileName; 
		InputStream inputStream;
    try {
	    inputStream = new FileInputStream(inputFile);
	    Reader      reader      = new InputStreamReader(inputStream);
	    BufferedReader in = new BufferedReader(reader);
	    String line = null;	    
	    while((line = in.readLine()) != null) {
	    	String[] splitString = (line.split(";"));   
	    	if (splitString.length == NUMBER_OF_FIELDS_IN_DEMANDOFFERPRICES ){
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
	private OmelDemandOfferPrices createIntraDailyPricesList(String[] splitString) throws ParseException {
		 OmelDemandOfferPrices res  = new  OmelDemandOfferPrices ();
		String today = splitString[1];
		DateFormat formatter = new SimpleDateFormat("dd/MM/yyyyMMdd");
		res.setDate(formatter.parse(today));
		res.setHour(Integer.parseInt(splitString[0]));
		
		res.setCountry(splitString[2]);
		res.setMatchOffer(splitString[7]);
		res.setOfferType(splitString[7]);
		res.setTradeEnergy(Float.parseFloat(splitString[5]));
		res.setTradePrice(Float.parseFloat(splitString[6]));
		res.setUnit(splitString[3]);
		//res.setValue(Float.parseFloat(splitString[4]));
		return res;
  }
	

}
