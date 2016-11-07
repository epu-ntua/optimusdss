package eu.optimus.energyprices.explorer;

import java.io.BufferedOutputStream;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.io.Writer;
import java.net.SocketException;

import org.apache.commons.net.ftp.FTP;
import org.apache.commons.net.ftp.FTPClient;
import org.apache.log4j.Logger;

public class Util {
	private static  org.apache.log4j.Logger log = Logger.getLogger(Util.class);

	public static Boolean exCommand (String cmd, String outFile){
		log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
		Boolean res = false ;
		try {
			//Open the output buffer
			OutputStream outputStream = new FileOutputStream(ConfigurationManager.getConfiguration().getValue("outPutPath")+outFile);
			//OutputStream outputStream = new FileOutputStream(outFile);
			Writer       writer       = new OutputStreamWriter(outputStream);
			
			//Open input buffer and execute command
			Runtime rt = Runtime.getRuntime();
      Process p = rt.exec(cmd);
      InputStream is = p.getInputStream();
      InputStreamReader isr = new InputStreamReader(is);
      BufferedReader br = new BufferedReader(isr);
      String line ="";
      String fileContent ="";
      while ((line = br.readLine()) != null) {      
      	log.debug("Writting to file line: " + line);
      	writer.write(line + "\n");
      	fileContent = fileContent + line ;
      }        
      p.waitFor();
      isr.close();
      is.close();
      br.close();
      writer.close();
      if (fileContent.contains("html")) res = false; else  res = true ;
    	//Runtime.getRuntime().exec(cmd);      
    } catch (Exception e) {
    	log.error("Error in class: EPSystemEngine, Method: sysCall; Error=" + e.toString());    	
    }
		log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
  	return res;  	
  }
	/**
	 * 
	 * @param server
	 * @param port
	 * @param user
	 * @param pass
	 * @param remoteFile
	 * @param localFile
	 * @return
	 * @throws SocketException
	 * @throws IOException
	 */
	
	static public Boolean getRemoteFiles (String server, int port, String user, String pass, String remoteFile , String localFile) throws SocketException, IOException
  {
  	log.info("Starting function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
  	Boolean res = false;
  	
  	FTPClient ftpClient = new FTPClient();
  	log.info("Opening FTP connetion to: " + server + " using: " +user + ":"+ pass);
  	ftpClient.connect(server, port);
  	
  	log.debug("Connected...");
	  ftpClient.login(user, pass);
  	
	  	  
	  log.debug("Loggin OK...");
	  ftpClient.enterLocalPassiveMode();
	  ftpClient.setFileType(FTP.ASCII_FILE_TYPE);
		  
	  
	  File downloadFile = new File(ConfigurationManager.getConfiguration().getValue("outPutPath")+localFile);
	  OutputStream outputStream = new BufferedOutputStream(new FileOutputStream(downloadFile));
	  log.debug("Downloading file...");
	 
	  if (ftpClient.retrieveFile(remoteFile, outputStream)== true){
	  	log.info("FTP download succesful");
	  	res = true;
	  }	  	
	  else
	  	log.error("FTP download finished with ERROR");
	  
	  outputStream.close();
	  log.info("Ending function: " + new Object(){}.getClass().getEnclosingMethod().getName() );
	  return res ;
  }
}
