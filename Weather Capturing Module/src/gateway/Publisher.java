package gateway;

import java.net.HttpURLConnection;
import java.net.URL;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStream;

import org.apache.log4j.Logger;


public class Publisher {

    private URL serverURL;
    private static  org.apache.log4j.Logger log = Logger.getLogger(Publisher.class);
    private String logFileName;
    
    public Publisher(URL serverURL) {
        this.serverURL = serverURL;
    }

    public Publisher(URL serverURL, String logFileName) {
      this.serverURL = serverURL;
      this.logFileName = logFileName;
    }
   
    public int publish(Event[] events) throws IOException {
      HttpURLConnection con = (HttpURLConnection) serverURL.openConnection();
      con.setRequestMethod("POST");
      con.setRequestProperty("Content-Type", "application/ztreamy-event");
      con.setDoOutput(true);
      OutputStream out = con.getOutputStream();
      OutputStream log = null;
      if (logFileName != null) {
          log = new FileOutputStream(logFileName, true);
      }
      for (Event event: events) {
          byte[] data = event.serialize();
          out.write(data);
          if (logFileName != null) {
              log.write(data);
          }
      }
      out.close();
      if (logFileName != null) {
          log.close();
      }
      return con.getResponseCode();
    
  }
    
    public int publish(Event event) throws IOException {
        return publish(new Event[] {event});
    }
}
