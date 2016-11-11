package Schedule;

import gateway.StreamManager;

import java.io.File;
import java.io.IOException;
import java.util.Calendar;
import java.util.Map;
import java.util.TimeZone;
import java.util.Timer;
import java.util.TimerTask;
import java.util.Vector;

import parser.RawFiles;
import config.Config;

public class Schedule_TimerTask extends TimerTask{
	
	private int _delay=1;
	private int _checkForecast[];
	private int _checkMeasure[];
	
	public Schedule_TimerTask()
	{
		int n=Config.getInstance().size();
		
		_checkMeasure=new int[n];
		_checkForecast=new int[n];
		for(int i=0;i<this._checkForecast.length;i++)
		{
			this._checkMeasure[i]=1;
			this._checkForecast[i]=1;
		}
		Calendar c=Calendar.getInstance();

		if(c.get(Calendar.MINUTE)<10)
			c.set(Calendar.getInstance().get(Calendar.YEAR), Calendar.getInstance().get(Calendar.MONTH), Calendar.getInstance().get(Calendar.DAY_OF_MONTH), Calendar.getInstance().get(Calendar.HOUR_OF_DAY), 10, 0);
		else
			c.set(Calendar.getInstance().get(Calendar.YEAR), Calendar.getInstance().get(Calendar.MONTH), Calendar.getInstance().get(Calendar.DAY_OF_MONTH), Calendar.getInstance().get(Calendar.HOUR_OF_DAY)+1, 10, 0);
		long timestart=c.getTimeInMillis();
		long now=Calendar.getInstance().getTimeInMillis();
		_delay=Integer.parseInt((timestart-now)+"");
	}
	
	
	public void start() {
		System.out.println(Calendar.getInstance().getTime().toString()+" OPTIMUS  DataCapturingModule START");
		this.action();
		Timer timer = new Timer();
		timer.schedule(this,this._delay,60*60*1000);
	}

	
	public void run()
	{
		Calendar c=Calendar.getInstance();
		c.setTimeZone(TimeZone.getTimeZone("UTC"));
		if(c.get(Calendar.HOUR_OF_DAY)==0)
		{
			for(int i=0;i<this._checkForecast.length;i++)
			{
				this._checkMeasure[i]=1;
				this._checkForecast[i]=1;
			}
		}
		this.action();
	}
	
	private void action()
	{
		try
		{
			for(int i=0;i<this._checkForecast.length;i++)
			{
				if(this._checkMeasure[i]==1)
				{
					System.out.println(Calendar.getInstance().getTime().toString()+"	sendMeasure");
					_checkMeasure[i]=sendMeasure(i)==1?0:1;
				}
				if(this._checkForecast[i]==1)
				{
					System.out.println(Calendar.getInstance().getTime().toString()+"	sendForecast");
					_checkForecast[i]=sendForecast(i)==1?0:1;
				}
			}
		}
		catch(IOException ioexec)
		{
			System.out.println("IOException");
		}
		catch(InterruptedException iexec)
		{
			System.out.println("InterruptedException");
		}
		
	}
	
	
	
	
	public static int sendForecast(int index)throws IOException, InterruptedException
	{
		  int code=0;
		  int ok=0;
		  //cerco il file di previsione di oggi
		  Calendar c = Calendar.getInstance();
		  c.setTimeZone(TimeZone.getTimeZone("UTC"));
		  String monthStr=(c.get(Calendar.MONTH)+1)+"";
		  String dayStr=c.get(Calendar.DAY_OF_MONTH)+"";
		  monthStr=(monthStr.length()<2)?("0"+monthStr):monthStr;
		  dayStr=(dayStr.length()<2)?("0"+dayStr):dayStr;
		  String filename=c.get(Calendar.YEAR)+""+monthStr+""+dayStr+".csv";
		  //filename="20150725.csv"; //N.B. DEMO
		  
		  String path=Config.getInstance().get(index).forecast_dir+File.separator+c.get(Calendar.YEAR)+""+monthStr;
		  File fileForecastData=new File(path+File.separator+filename);
		  
		  String path_nosubdir=Config.getInstance().get(index).forecast_dir;
		  File fileForecastDataSub=new File(path_nosubdir+File.separator+filename);
		  
		  String stream_name="";String ztreamy_url="";String resource_uri="";
		  ok=0;
		  if(fileForecastData.exists())
		  {
			  ok=1;
		  }
		  else if(fileForecastDataSub.exists())
		  {
			  path=path_nosubdir;
			  ok=1;
		  }
		  if(ok==1)
		  {
			  System.out.println("Reading file "+Config.getInstance().get(index).forecast_dir+File.separator+filename);
			  Vector<Vector<Map<String,String>>> data = new Vector<Vector<Map<String,String>>>();
			  data=new Vector<Vector<Map<String,String>>>();
			  //parso il contenuto
			  RawFiles row=new RawFiles();
			  row.parser(path+File.separator+filename,data);
			  System.out.println("Sending data to server");
			  //lo invio al server
			  for(int i=0;i<Config.getInstance().get(index).sensorCount;i++)
			  {
				  int numEvents = data.get(i).size();
				  double valueDouble=0;
				  for (int j = 0; j < numEvents; j++) {
					  if(!data.get(i).get(j).get("value").equals("False"))
					  {
						  valueDouble=Double.parseDouble(data.get(i).get(j).get("value").replaceAll(",", "."));
						  StreamManager.sendStream(index,Config.getInstance().get(index).sensor.get(i), "forecast", valueDouble, Long.parseLong(data.get(i).get(j).get("timestamp")));
						  Thread.sleep(3000);
					  }
				  }
				  code=1;
			  }
		  }
		  else
		  {
			  System.out.println("ERROR: file "+path_nosubdir+File.separator+filename+" not found");
			  System.out.println("ERROR: file "+path+File.separator+filename+" not found");
			  code=0;
		  }
		  return code;
	}
	
	
	public static int sendMeasure(int index)throws IOException, InterruptedException
	{
		  int code=0;
		  String filename="";String sensorLabelCurrent="";String monthStr="";String dayStr="";
		  long timestampCurrent=0;long timestampBefore=0; long timestampAfter=0;
		  double valueBefore=0;double valueAfter=0;double valueForecastInterpolato=0;double valueMeasure=0;
		  File fileMeasureData=null;
		  RawFiles row=null;
		  Vector<Vector<Map<String,String>>> measures=null;Vector<Vector<Map<String,String>>> forecast=null;
		  
		  //cerco il file di misure di oggi
		  Calendar c = Calendar.getInstance();
		  c.setTimeZone(TimeZone.getTimeZone("UTC"));
		  //timestampCurrent=1413842400;// "20141021.csv" DEMO,DA COMMENTARE
		  //c.setTimeInMillis(timestampCurrent*1000);// "20141021.csv" DEMO,DA COMMENTARE
		  c.set(Calendar.MINUTE, 0);c.set(Calendar.SECOND, 0);c.set(Calendar.HOUR_OF_DAY, 0);
		  monthStr=(c.get(Calendar.MONTH)+1)+"";
		  dayStr=c.get(Calendar.DAY_OF_MONTH)+"";
		  monthStr=(monthStr.length()<2)?("0"+monthStr):monthStr;
		  dayStr=(dayStr.length()<2)?("0"+dayStr):dayStr;
		  filename=c.get(Calendar.YEAR)+""+monthStr+""+dayStr+".csv";
		  fileMeasureData=new File(Config.getInstance().get(index).measure_dir+File.separator+c.get(Calendar.YEAR)+""+monthStr+File.separator+filename);		  
		  if(fileMeasureData.exists())
		  {
			  System.out.println("Reading file "+Config.getInstance().get(index).measure_dir+File.separator+c.get(Calendar.YEAR)+""+monthStr+File.separator+filename);
			  measures = new Vector<Vector<Map<String,String>>>();
			  //parso il contenuto dei file di dati
			  row=new RawFiles();
			  row.parser(Config.getInstance().get(index).measure_dir+File.separator+c.get(Calendar.YEAR)+""+monthStr+File.separator+filename,measures);
			  //ricavo i dati di previsione del run più fresco
			  File file;int num_giorni=7;String filenamePrev=filename;
			  do
			  {
				  c.set(Calendar.MINUTE, 0);c.set(Calendar.SECOND, 0);c.set(Calendar.HOUR_OF_DAY, 0);
				  monthStr=(c.get(Calendar.MONTH)+1)+"";
				  dayStr=c.get(Calendar.DAY_OF_MONTH)+"";
				  monthStr=(monthStr.length()<2)?("0"+monthStr):monthStr;
				  dayStr=(dayStr.length()<2)?("0"+dayStr):dayStr;
				  filenamePrev=c.get(Calendar.YEAR)+""+monthStr+""+dayStr+".csv";
				  file=new File(Config.getInstance().get(index).forecast_dir+File.separator+c.get(Calendar.YEAR)+""+monthStr+File.separator+filenamePrev);
				  if(!file.exists())
				  {
					  file=new File(Config.getInstance().get(index).forecast_dir+File.separator+filenamePrev);
				  }
				  c.add(Calendar.DAY_OF_MONTH, -1);
				  num_giorni--;
			  }
			  while(!file.exists() && num_giorni>0);
			  if(num_giorni<=0)
			  {
				  System.out.println("Forecast file not found");
				  code=0;
				  return code;
			  }
			  System.out.println("Reading file "+file.getAbsolutePath());
			  forecast=new Vector<Vector<Map<String,String>>>();
			  //parso il contenuto dei file di previsione
			  row.parser(file.getAbsolutePath(),forecast);
			  //per ogni timestamp del dato calcolo la previsione per lo stesso istante di tempo
			  for(int i=0;i<measures.size();i++)
			  {
				  sensorLabelCurrent=measures.get(i).get(0).get("label");
				  for(int j=0;j<measures.get(i).size();j++)
				  {  
					  for(int a=0;a<forecast.size();a++)
					  {
						  if(sensorLabelCurrent.equals(forecast.get(a).get(0).get("label")))
						  {
							  timestampCurrent=Long.parseLong(measures.get(i).get(j).get("timestamp"));
							  valueMeasure=Double.parseDouble(measures.get(i).get(j).get("value").replaceAll(",", "."));
							  //ricavo i due valori di previsione che contengono il timestamp del dato
							  for(int b=1;b<forecast.get(a).size();b++)
							  {
								  timestampBefore=Long.parseLong(forecast.get(a).get(b-1).get("timestamp"));
								  timestampAfter=Long.parseLong(forecast.get(a).get(b).get("timestamp"));
								  if(timestampCurrent>=timestampBefore && timestampCurrent<=timestampAfter)
								  {
									  valueBefore=Double.parseDouble(forecast.get(a).get(b-1).get("value").replaceAll(",", "."));
									  valueAfter=Double.parseDouble(forecast.get(a).get(b).get("value").replaceAll(",", "."));
									  valueForecastInterpolato=(timestampCurrent-timestampBefore)*(valueAfter-valueBefore)/(timestampAfter-timestampBefore)+valueBefore;
									  //invio al server il dato di previsione
									  StreamManager.sendStream(index,Config.getInstance().get(index).sensor.get(a), "forecast", valueForecastInterpolato, timestampCurrent);
									  Thread.sleep(1000);
									  //invio al server il dato originale
									  StreamManager.sendStream(index,Config.getInstance().get(index).sensor.get(a), "measure", valueMeasure, timestampCurrent);
									  Thread.sleep(1000);
									  //invio al server il dato differenza
									  StreamManager.sendStream(index,Config.getInstance().get(index).sensor.get(a), "difference", valueForecastInterpolato-valueMeasure, timestampCurrent);  
									  code=1;
								  }
							  }
						  }
					  }
				  }
			  }
		  }
		  else
		  {
			  System.out.println("ERROR: file "+Config.getInstance().get(index).measure_dir+File.separator+c.get(Calendar.YEAR)+""+monthStr+File.separator+filename+" not found");
			  code=0;
		  }
		  return code;
	  }
}
