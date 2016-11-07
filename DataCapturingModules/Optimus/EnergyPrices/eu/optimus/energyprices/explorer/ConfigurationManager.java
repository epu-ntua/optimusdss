package eu.optimus.energyprices.explorer;

import java.io.IOException;
import java.io.InputStream;
import java.util.InvalidPropertiesFormatException;
import java.util.Properties;

import org.apache.log4j.Logger;

public class ConfigurationManager {
//private static String CONFIG_PATH="D:\\Prj\\GE\\Software\\Release\\ics.cnf";
	private static String CONFIG_PATH="em.cnf";
	private static  org.apache.log4j.Logger log = Logger.getLogger(ConfigurationManager.class);
	private static ConfigurationManager handler = null ;
	private Properties properties = null;
	
	private ConfigurationManager(){			
		ClassLoader cl = null ;
		InputStream is ;
		try {		
			properties = new Properties();
			cl = this.getClass().getClassLoader();    
      is = cl.getResourceAsStream(CONFIG_PATH);
			properties.loadFromXML(is); 
		} catch (Exception e) {
			log.warn ("Using default d:\\ics.cnf");
			is = cl.getResourceAsStream(CONFIG_PATH);
			try {
	      properties.loadFromXML(is);
      } catch (IOException e1) {
      	log.error ("d:\\ics.cnf : " + e1.getMessage());
      } 
		}							
	}
	/**
	 * 
	 * @return
	 */
	public static ConfigurationManager getConfiguration (){
		if (handler == null){			
			handler = new ConfigurationManager ();
		}
		return handler;
	}
	/**
	 * 
	 * @param key
	 * @return
	 */
	public String getValue (String key){
		String value = null ;
		value = properties.getProperty(key);
		return value ;
	}
		
}

