package eu.optimus.energyprices.datamodel.gme;

import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpressionException;
import javax.xml.xpath.XPathFactory;

import org.apache.log4j.Logger;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.xml.sax.SAXException;

import eu.optimus.energyprices.explorer.ConfigurationManager;


public class GMEDailyPricesModeler {
	
	private String fileName ;
	private static  org.apache.log4j.Logger log = Logger.getLogger(GMEDailyPricesModeler.class);
	
	public GMEDailyPricesModeler (String a)
	{	
		this.fileName = a ;
	}
	
	public ArrayList <GMEDailyPrices> modelDailyPrices (){
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		
		ArrayList <GMEDailyPrices> res = new ArrayList <GMEDailyPrices> ();
		String inputFile = ConfigurationManager.getConfiguration().getValue("outPutPath") +"\\" + fileName; 
		DocumentBuilderFactory builderFactory = DocumentBuilderFactory.newInstance();
		DocumentBuilder builder = null;
		try {
			builder = builderFactory.newDocumentBuilder();
			Document document = builder.parse( new FileInputStream(inputFile));
		
		
			XPath xPath =  XPathFactory.newInstance().newXPath();
			String expression = "//Prezzi/Data | //Prezzi/Ora | //Prezzi/PUN";
			
	    
			NodeList nl = (NodeList) xPath.compile(expression).evaluate(document, XPathConstants.NODESET);
			ArrayList <String> nodeValues = new ArrayList<String>(); 
			if (nl != null && nl.getLength() > 0) {        
				log.debug("Node count: " + nl.getLength());				
				for (int i = 0; i < nl.getLength(); i++) {
            if (nl.item(i).getNodeType() == Node.ELEMENT_NODE) {
                Element el = (Element) nl.item(i);        
                nodeValues.add(el.getFirstChild().getTextContent());
                log.debug("Valor de nodo: " + el.getTextContent());                
            }
            if (nodeValues.size() >=3){
            	res.add(this.createDailyPricesList ( nodeValues.toArray()));
            	nodeValues.clear();
            }
				}				
			}
	  } catch (FileNotFoundException e) {
    	log.error("Modeling daily prices" + e.getMessage());
    } catch (IOException e) {
    	log.error("Modeling daily prices" + e.getMessage());
    } catch (ParseException e) {
    	log.error("Modeling daily prices" + e.getMessage());
    } catch (ParserConfigurationException e) {
    	log.error("Modeling daily prices" + e.getMessage());
    } catch (SAXException e) {
    	log.error("Modeling daily prices" + e.getMessage());	    
    } catch (XPathExpressionException e) {
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
	private GMEDailyPrices createDailyPricesList(Object[] splitString) throws ParseException {
		GMEDailyPrices res  = new GMEDailyPrices ();
		String today = (String)splitString[0];
		DateFormat formatter = new SimpleDateFormat("yyyyMMdd");
		res.setDate(formatter.parse(today));
		res.setHour(Integer.parseInt((String)splitString[1]));
		res.setValue(Float.parseFloat(((String)splitString[2]).replace(',','.'))); //FIXME 
		return res;
  }
	

}
