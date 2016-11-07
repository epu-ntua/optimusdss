package eu.optimus.energyprices.datamodel.global;

import eu.optimus.energyprices.explorer.ConfigurationManager;

public abstract class DailyPrices {
	protected String resource ;
	protected String sensor ;
	protected String city ;
	public abstract String createRDF();
	
}
