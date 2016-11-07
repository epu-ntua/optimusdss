library(MASS)
library(leaps)
library(timeDate)
library(forecast)

##Here we calculate the mean price per zone and month based on hourly data
data$Year <- as.numeric(substring(data$Timestamp, first=1, last=4))
data$Month <- as.numeric(substring(data$Timestamp, first=6, last=7))
data$Day <- as.numeric(substring(data$Timestamp, first=9, last=10))
data$Hour<- as.numeric(substring(data$Timestamp, first=12, last=13))
data$weekdays <- weekdays(as.Date(substring(data$Timestamp, first=1, last=10)))
data$Holidays<-FALSE
#These are the offical holidays

if (length(data[(data$Month==1)&(data$Day==1),]$Holidays)>0) {data[(data$Month==1)&(data$Day==1),]$Holidays<-TRUE}
if (length(data[(data$Month==1)&(data$Day==6),]$Holidays)>0) {data[(data$Month==1)&(data$Day==6),]$Holidays<-TRUE}
if (length(data[(data$Month==4)&(data$Day==25),]$Holidays)>0) {data[(data$Month==4)&(data$Day==25),]$Holidays<-TRUE}
if (length(data[(data$Month==5)&(data$Day==1),]$Holidays)>0) {data[(data$Month==5)&(data$Day==1),]$Holidays<-TRUE}
if (length(data[(data$Month==6)&(data$Day==2),]$Holidays)>0) {data[(data$Month==6)&(data$Day==2),]$Holidays<-TRUE}
if (length(data[(data$Month==8)&(data$Day==15),]$Holidays)>0) {data[(data$Month==8)&(data$Day==15),]$Holidays<-TRUE}
if (length(data[(data$Month==11)&(data$Day==1),]$Holidays)>0) {data[(data$Month==11)&(data$Day==1),]$Holidays<-TRUE}
if (length(data[(data$Month==12)&(data$Day==8),]$Holidays)>0) {data[(data$Month==12)&(data$Day==8),]$Holidays<-TRUE}
if (length(data[(data$Month==12)&(data$Day==25),]$Holidays)>0) {data[(data$Month==12)&(data$Day==25),]$Holidays<-TRUE}
if (length(data[(data$Month==12)&(data$Day==26),]$Holidays)>0) {data[(data$Month==12)&(data$Day==26),]$Holidays<-TRUE}
if (length(data[(data$Month==12)&(data$Day==31),]$Holidays)>0) {data[(data$Month==12)&(data$Day==31),]$Holidays<-TRUE}

#Categorize hours to zones
t1<-7 ; t2<-8 ; t3<-19 ; t4<-22 ; t5<-23
data$Zone<-"NA"
data[data$weekdays=="Sunday",]$Holidays<-TRUE
data[data$Holidays==TRUE,]$Zone<-"F3"
data[(data$weekdays=="Saturday")&((data$Hour>=t1)&(data$Hour<t5)),]$Zone<-"F2"
data[(data$weekdays=="Saturday")&((data$Hour<t1)|(data$Hour>t4)),]$Zone<-"F3"
data[(data$weekdays!="Sunday")&(data$weekdays!="Saturday")&(data$Holidays==FALSE),]$weekdays<-"Weekday"
data[(data$weekdays=="Weekday")&((data$Hour>=t2)&(data$Hour<t3)),]$Zone<-"F1"
data[(data$weekdays=="Weekday")&((data$Hour<t1)|(data$Hour>t4)),]$Zone<-"F3"
data[(data$weekdays=="Weekday")&(data$Zone=="NA"),]$Zone<-"F2"


#aggregate per zone and month
data$Holidays=data$Day=data$Hour=data$weekdays=data$Timestamp<-NULL
agg<-aggregate(data,by=list(data$Year,data$Month,data$Zone),FUN=mean)
agg$Year=agg$Month=agg$Zone<-NULL
colnames(agg)<-c("Year","Month","Zone","Price")
agg<-agg[order(agg$Year,agg$Month,agg$Zone),]

#Final aggregated historial data
ref<-agg[agg$Zone=="F1",]
Energyprices<-data.frame(matrix(NA,ncol=5,nrow=nrow(ref))) ; colnames(Energyprices)<-c("Year","Month","F1","F2","F3")
Energyprices$Month<-ref$Month
Energyprices$Year<-ref$Year
for (i in 1:nrow(Energyprices)){
  n<-agg[(agg$Year==Energyprices$Year[i])&(agg$Month==Energyprices$Month[i]),]
  Energyprices$F1[i]<-n[n$Zone=="F1",]$Price
  Energyprices$F2[i]<-n[n$Zone=="F2",]$Price
  Energyprices$F3[i]<-n[n$Zone=="F3",]$Price
}


Energyprices2<-data.frame(matrix(0,ncol=5,nrow=12)) ; colnames(Energyprices2)<-c("Year","Month","F1","F2","F3")
Energyprices2$F1<-forecast(ets(ts(Energyprices$F1,start=c(Energyprices$Year[i],Energyprices$Month[i]),frequency=12)),h=12)$mean
Energyprices2$F2<-forecast(ets(ts(Energyprices$F2,start=c(Energyprices$Year[i],Energyprices$Month[i]),frequency=12)),h=12)$mean
Energyprices2$F3<-forecast(ets(ts(Energyprices$F3,start=c(Energyprices$Year[i],Energyprices$Month[i]),frequency=12)),h=12)$mean

Energyprices<-rbind(Energyprices,Energyprices2)

for (i in (nrow(Energyprices)-12):nrow(Energyprices)){
 if (Energyprices$Month[i-1]<12){
  Energyprices$Month[i]<-Energyprices$Month[i-1]+1
  Energyprices$Year[i]<-Energyprices$Year[i-1]
 }else{
  Energyprices$Month[i]<-1
  Energyprices$Year[i]<-Energyprices$Year[i-1]+1
 }
}