package eu.optimus.energyprices.datamodel.omel;

import java.util.Calendar;

import eu.optimus.energyprices.explorer.ConfigurationManager;

public class GasDailyPrices extends OmelDailyPrices {

	public GasDailyPrices(){
		resource 	= ConfigurationManager.getConfiguration().getValue("resourceurl");
		sensor 		= ConfigurationManager.getConfiguration().getValue("sensor_gasprices");
		city 			= ConfigurationManager.getConfiguration().getValue("pilot1_city");
		setDate(Calendar.getInstance().getTime());
		setHour(0);
		setValue(0.0f);
	}
}
