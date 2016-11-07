package eu.optimus.energyprices.datamodel.omel;

import java.util.Date;

public class OmelDemandOfferPrices {
	///TODO COMPLETE REVISION
	private Date date;
	private int hour;
	private float tradeEnergy;
	private float tradePrice;
	private String matchOffer;
	private String country;
	private String unit;
	private String offerType;
	/**
	 * @return the date
	 */
	public Date getDate() {
		return date;
	}
	/**
	 * @param date the date to set
	 */
	public void setDate(Date date) {
		this.date = date;
	}
	/**
	 * @return the hour
	 */
	public int getHour() {
		return hour;
	}
	/**
	 * @param hour the hour to set
	 */
	public void setHour(int hour) {
		this.hour = hour;
	}	
	/**
	 * @return the country
	 */
	public String getCountry() {
		return country;
	}
	/**
	 * @param country the country to set
	 */
	public void setCountry(String country) {
		this.country = country;
	}
	/**
	 * @return the unit
	 */
	public String getUnit() {
		return unit;
	}
	/**
	 * @param unit the unit to set
	 */
	public void setUnit(String unit) {
		this.unit = unit;
	}
	/**
	 * @return the offerType
	 */
	public String getOfferType() {
		return offerType;
	}
	/**
	 * @param offerType the offerType to set
	 */
	public void setOfferType(String offerType) {
		this.offerType = offerType;
	}
	/**
	 * @return the tradeEnergy
	 */
	public float getTradeEnergy() {
		return tradeEnergy;
	}
	/**
	 * @param tradeEnergy the tradeEnergy to set
	 */
	public void setTradeEnergy(float tradeEnergy) {
		this.tradeEnergy = tradeEnergy;
	}
	/**
	 * @return the tradePrice
	 */
	public float getTradePrice() {
		return tradePrice;
	}
	/**
	 * @param tradePrice the tradePrice to set
	 */
	public void setTradePrice(float tradePrice) {
		this.tradePrice = tradePrice;
	}
	/**
	 * @return the matchOffer
	 */
	public String getMatchOffer() {
		return matchOffer;
	}
	/**
	 * @param matchOffer the matchOffer to set
	 */
	public void setMatchOffer(String matchOffer) {
		this.matchOffer = matchOffer;
	}
	
}
