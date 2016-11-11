package log;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.util.Calendar;

import config.Config;

public class Log {
	
	private static Log log;
	private String _event="optimusEvent"+Calendar.getInstance().getTimeInMillis()+".log";
	private String _packet="optimusPacket"+Calendar.getInstance().getTimeInMillis()+".log";
	
	private Log()
	{
		
	}
	
	public static Log getInstance() {
		if (log == null) {
			log = new Log();
		}
		return log;
	}

	
	public void printEventLog(String testo, boolean newLine)
	{
		try 
		{
			FileWriter  file = new FileWriter (Config.getInstance().get(0).dirLog_locale+File.separator+this._event,true);
			BufferedWriter output = new BufferedWriter(file);
			if(newLine)output.newLine();
			output.write(testo);
			output.flush();
			output.close();
			file.close();
		} 
		catch (IOException ee)
		{
			ee.printStackTrace();
			System.out.println(Config.getInstance().get(0).dirLog_locale+"Errore scrittura file di log "+this._event);
		}
	}
	
	
	public void printPacketLog(String testo)
	{
		try 
		{
			FileWriter  file = new FileWriter (Config.getInstance().get(0).dirLog_locale+File.separator+this._packet,true);
			BufferedWriter output = new BufferedWriter(file);
			output.newLine();
			output.write(testo);
			output.newLine();
			output.newLine();
			output.newLine();
			output.flush();
			output.close();
			file.close();
		} 
		catch (IOException ee)
		{
			ee.printStackTrace();
			System.out.println(Config.getInstance().get(0).dirLog_locale+"Errore scrittura file di log "+this._packet);
		}
	}
	
}
