package eu.optimus.energyprices.gateway;

import eu.optimus.energyprices.datamodel.omel.*;

public class MarketSubtotalsEvent extends Event {
  public MarketSubtotalsEvent(String sourceId,  OmelDailyEnergyMarketSubtotals dp,float value) {
  			super(sourceId, "application/x-ztreamy-event", "1");
        setBody(dp.createRDF(value));
    }
}
