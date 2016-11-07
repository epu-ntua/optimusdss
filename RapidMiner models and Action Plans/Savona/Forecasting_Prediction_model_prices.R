EnergypricesOR<-Energyprices
weatherOut$Year <- as.numeric(substring(weatherOut$Timestamp, first=1, last=4))
weatherOut$Month <- as.numeric(substring(weatherOut$Timestamp, first=6, last=7))
outmonths<-c(weatherOut$Month[1],weatherOut$Month[168]) ; outyears<-c(weatherOut$Year[1],weatherOut$Year[168])
if (outmonths[1]==outmonths[2]){ outmonths<-c(weatherOut$Month[1]) }
if (outyears[1]==outyears[2]){ outyears<-c(weatherOut$Year[1]) }
forigin<-min(outyears)+outmonths[which.min(outyears)]/100 ; Energyprices$forigin<-0
for (sfo in 1:length(Energyprices$Year)) { Energyprices$forigin[sfo]<-Energyprices$Year[sfo]+Energyprices$Month[sfo]/100 }
Energyprices<-Energyprices[Energyprices$forigin<forigin,] ; Energyprices$forigin<-NULL
horizon<-length(outmonths) ; prices<-matrix(NA, nrow=horizon, ncol=3)
F1p<-ts(Energyprices$F1,frequency=12,start=c(Energyprices$Year[1],Energyprices$Month[1]))
F2p<-ts(Energyprices$F2,frequency=12,start=c(Energyprices$Year[1],Energyprices$Month[1]))
F3p<-ts(Energyprices$F3,frequency=12,start=c(Energyprices$Year[1],Energyprices$Month[1]))
prices[,3]<-forecast(auto.arima(F1p-F2p), h=horizon)$mean ; prices[,1]<-outyears; prices[,2]<-outmonths
prices<-data.frame(prices); colnames(prices)<-c("Year","Month","Difference")

for (i in 1:nrow(prices)){ 
  n<-EnergypricesOR[(EnergypricesOR$Year==prices$Year[i])&(EnergypricesOR$Month==prices$Month[i]),]
  if ( length(n)>0) { 
    diff<-n$F1-n$F2
  }else{
    diff<-1.5
  }
  if (prices$Difference[i]==0){ prices$Difference[i]<-diff } 
}