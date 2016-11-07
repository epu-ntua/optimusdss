package eu.optimus.energyprices.gateway;

import eu.optimus.energyprices.datamodel.global.DailyPrices;
import eu.optimus.energyprices.datamodel.omel.*;

public class DailyPricesEvent extends Event {
  public DailyPricesEvent(String sourceId,  DailyPrices dp) {
  			super(sourceId, "application/x-ztreamy-event", "1");
        setBody(dp.createRDF());
    }
}
