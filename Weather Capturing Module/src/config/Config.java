package config;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;
import java.util.Map;
import java.util.Properties;
import java.util.Vector;
import java.lang.reflect.*;

import config.Config;

import log.Log;

public class Config 
{
	
	private static Vector<Config> config;
	
	public String forecast_dir;
	public String measure_dir;
	public String stream_name;
	public String ztreamy_url; 
	public String resource_uri;
	public String city;
	public String dirConf="";
	public String dirLog_locale="";
	public String stream_name_type="";
	public Map<String,String> stream_type;

	public int sensorCount=0;			               
	public ArrayList<String> sensor;
	
	private Config(int index)
	{
		Properties confFile = new Properties();
		InputStream filePro=null;

		this.dirConf = "GlobalVariables"+(index+1)+".conf";
		
		try
		{
			filePro=new FileInputStream(this.dirConf);
			confFile.load(filePro);	
			
			forecast_dir=confFile.getProperty("forecast_dir");
			measure_dir=confFile.getProperty("measure_dir");
			dirLog_locale=confFile.getProperty("log_dir");
			
			stream_name=confFile.getProperty("stream_name");
			stream_name_type=confFile.getProperty("stream_name_type");
			
			ztreamy_url=confFile.getProperty("ztreamy_url");
			
			resource_uri=confFile.getProperty("resource_uri");
			
			city=confFile.getProperty("city");
		
			sensorCount=Integer.parseInt(confFile.getProperty("sensor_count"));
			
			sensor=new ArrayList<String>(sensorCount);
			for (int i = 1;i <= sensorCount;i++) 
			{
				sensor.add(confFile.getProperty("sensor_name_"+i));
			}
			stream_type=new HashMap<String,String>();
			stream_type.put("forecast",confFile.getProperty("stream_type_forecast"));
			stream_type.put("measure",confFile.getProperty("stream_type_measure"));
			stream_type.put("difference",confFile.getProperty("stream_type_difference"));
		}
		catch (FileNotFoundException e)
		{
			e.printStackTrace();
		} 
		catch (IOException e)
		{
			e.printStackTrace();
		}
		catch(NumberFormatException e)
		{
			e.printStackTrace();
		}
		finally
		{
			if(filePro!=null)
			{
				try
				{
					filePro.close();
				}
				catch(IOException e)
				{
					String log_str=Calendar.getInstance().getTime().toString()+"  Config " + e.getMessage();
					Log.getInstance().printEventLog(log_str,true);
				}
			}
		}
	}
	

	
	private void replaceMarker()
	{
		boolean loop=true;
		while(loop)
		{
			loop=false;
			Field[] fields = this.getClass().getDeclaredFields();
			String value="";
			String type="";
			for (Field field : fields)
			{
				try 
				{
					type=field.getType()+"";
					if(type.lastIndexOf("String")>=0)
					{
						value = replaceMarkerSingleItem(this.getClass().getField(field.getName()).get(this).toString());
						field.setAccessible(true);
						field.set(this, new String(value));
						if(value.lastIndexOf("{")>=0)
							loop=true;
						
					}
				} catch (IllegalArgumentException e) {
					e.printStackTrace();
				} catch (IllegalAccessException e) {
					e.printStackTrace();
				} catch (NoSuchFieldException e) {
					e.printStackTrace();
				} catch (SecurityException e) {
					e.printStackTrace();
				}
			}
		}
		
	}
	
	
	private String replaceMarkerSingleItem(String item)
	{
		String property=item;
		int index_start=0;int index_end=0;
		String marker="";
		while(item.indexOf("{",index_start)>=0)
		{
			index_end=item.indexOf("}",index_start);
			marker=item.substring(item.indexOf("{",index_start)+1,item.indexOf("}",index_end));
			try
			{
				property = property.replaceAll("\\{"+marker+"\\}",this.getClass().getField(marker).get(this).toString());
			}
			catch (IllegalArgumentException e) {
				e.printStackTrace();
			} catch (IllegalAccessException e) {
				e.printStackTrace();
			} catch (NoSuchFieldException e) {
				e.printStackTrace();
			} catch (SecurityException e) {
				e.printStackTrace();
			}
			
			index_start=index_end+1;
		}
		return property;
		
	}
	
	
	
	public static Vector<Config> getInstance() {
		if (config == null) {
			int i=0;
			config=new Vector<Config>();
			while(i>=0)
			{
				File f=new File("GlobalVariables"+(i+1)+".conf");
				if(f.exists())
				{
					config.add(new Config(i));
					config.get(i).replaceMarker();
					i++;
				}
				else
					i=-1;
			}
			
		}
		return config;
	}
	
}

