package parser;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.HashMap;
import java.util.Map;
import java.util.TimeZone;
import java.util.Vector;
import java.util.Scanner;

import config.Config;
import log.Log;

public class RawFiles {
	
	public RawFiles()
	{
		
	}

	
	public void parser(String filename, Vector<Vector<Map<String,String>>> data)
	{
		String[] umis=null;
		String[] labels=null;
		
		FileInputStream scannerFile=null;
		Scanner scanner;
		String nextLine="";int numero_righe_lette=0;
		String lineToParse_datePart="";
		String value="";String valueTmp="";long timestamp=0;
		java.util.Date parsedDate;
		SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy HH:mm");
		sdf.setTimeZone(TimeZone.getTimeZone("UTC"));
		try
		{
			scannerFile=new FileInputStream(filename);
			scanner = new Scanner(scannerFile, "UTF-8");
			numero_righe_lette=0;
			while (scanner.hasNextLine())
	    	{
	    		nextLine=scanner.nextLine();
	    		if(numero_righe_lette==0)
	    		{
	    			String[] intestazione=nextLine.split(";");
	    			umis=new String[intestazione.length-2];//escludo le colonna di data e ora
	    			labels=new String[intestazione.length-2];//escludo le colonna di data e ora
	    			for(int a=0;a<umis.length;a++)
	    			{
	    				value=intestazione[a+2];
	    				if(value.indexOf("(")>-1 && value.indexOf(")")>-1)
	    					umis[a]=value.substring(value.indexOf("(")+1,value.indexOf(")"));
	    				else
	    					umis[a]="";
	    				valueTmp=umis[a].replaceAll("\\^", "\\\\^");
	    				labels[a]=value.replaceAll("\\("+valueTmp+"\\)", "");
	    				valueTmp=labels[a];
	    				labels[a]=value.replaceAll("\\[ \\]+$", "");
	    				data.add(new Vector<Map<String,String>>());
	    			}
	    		}
	    		else
	    		{
	    			String[] lineSplitArrayTmp=null;
	    			lineSplitArrayTmp=nextLine.split(";");
	    			//ricavo la timestamp
	    			lineToParse_datePart = lineSplitArrayTmp[0]+" "+lineSplitArrayTmp[1];
					try 
					{
						parsedDate = sdf.parse(lineToParse_datePart);
						timestamp = parsedDate.getTime()/1000;
					} 
					catch (java.text.ParseException e) 
					{
						e.printStackTrace();
						String log_str=" RawFiles.parser "+e.getMessage();
				    	Log.getInstance().printEventLog(log_str,true);
					}
					for(int a=2;a<lineSplitArrayTmp.length;a++)
					{
						Map<String,String> map = new HashMap<String,String>();
						map.put("timestamp", timestamp+"");
						map.put("value", lineSplitArrayTmp[a]);
						map.put("umis", umis[a-2]);
						map.put("label", labels[a-2]);
						data.get(a-2).add(map);
					}
	    		}
	    		numero_righe_lette++;
	    	}
			scanner.close();
		}
	    catch (FileNotFoundException e)
	    {
			e.printStackTrace();
			Log.getInstance().printEventLog(" RawFiles.parser "+e.toString(),true);
		}
	    finally 
        {
            try 
            {
                if (scannerFile != null) 
                {
                	scannerFile.close();
                }
            } 
            catch (IOException e) 
            {
                e.printStackTrace();
                String log_str=Calendar.getInstance().getTime().toString()+" RawFiles.parser inside finally "+e.getMessage();
    	    	Log.getInstance().printEventLog(log_str,true);
            }
        }
		
	}
	

}
