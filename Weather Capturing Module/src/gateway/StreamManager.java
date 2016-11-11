package gateway;

import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;

import config.Config;

public class StreamManager {
	
	public static void sendStream(int index,String sensor, String stream_type, double value, long timestamp)
	{
		try 
		{
			sensor = sensor.replaceAll("\\[stream_type\\]", Config.getInstance().get(index).stream_type.get(stream_type));
			String stream_name = Config.getInstance().get(index).stream_name.replaceAll("\\[sensor\\]", sensor);
			stream_name = stream_name.replaceAll("\\[stream_type\\]", Config.getInstance().get(index).stream_type.get(stream_type));
			String ztreamy_url = Config.getInstance().get(index).ztreamy_url.replaceAll("\\[sensor\\]", sensor);
			ztreamy_url = ztreamy_url.replaceAll("\\[stream_type\\]", Config.getInstance().get(index).stream_type.get(stream_type));
			String resource_uri = Config.getInstance().get(index).resource_uri.replaceAll("\\[sensor\\]", sensor);
			resource_uri = resource_uri.replaceAll("\\[stream_type\\]", Config.getInstance().get(index).stream_type.get(stream_type));
			Publisher publisher = new Publisher(new URL(ztreamy_url));
			int result = publisher.publish(new DataCapturingModulesEvent(stream_name,resource_uri,stream_name,sensor,timestamp,value));
			if (result == 200) 
			{
				System.out.println(stream_type+" event just been sent to the server: stream_name="+stream_name+",sensor="+sensor);
			} 
			else 
			{
				System.out.println(stream_type+" event: The server responded with error " + result+"(stream_name="+stream_name+",sensor="+sensor+")");
			}
		}
		catch (MalformedURLException e) {
			e.printStackTrace();
		}
		catch (IOException e) {
			e.printStackTrace();
		}
	}

}
