library(MASS)
library(leaps)

predict.regsubsets = function(object, newdata, id, ...) {
  form  <-  as.formula(~.)
  mat  <-  model.matrix(form, newdata)
  coefi  <-  coef(object, id)
  xvars  <-  names(coefi)
  if (length(coefi)==1){
    predictions<-mat[, xvars] * coefi
  }else{
    predictions<-mat[, xvars] %*% coefi
  }
  return(predictions)
}

data<-na.omit(data) 
data$Year <- as.numeric(substring(data$Timestamp, first=1, last=4))
data$Month <- as.numeric(substring(data$Timestamp, first=6, last=7))
data$Day <- as.numeric(substring(data$Timestamp, first=9, last=10))
data$Hour<-as.numeric(substring(data$Timestamp, first=12, last=13))
data <- data[order(data$Year, data$Month, data$Day, data$Hour),]

for (i in 1:nrow(data)){
  if (data$santcugat_townhall_pv_power[i]<0){
    data$santcugat_townhall_pv_power[i]<-0
  }
}

list25<-c()
for (h in 0:23){
  check1<-data[data$Hour==h,]$santcugat_townhall_pv_power
  check2<-data[data$Hour==h,]$santcugat_weatherforecast_irradiation_forecast
  stat1<-boxplot(check1,plot=FALSE)$stat[4]
  stat2<-boxplot(check2,plot=FALSE)$stat[4]
  if ((stat1>0)&(stat2>0)){ list25<-c(list25,h)}
}
from<-min(list25)
to<-max(list25)


data$Oldness <- c(1:length(data[,1])) #IDs etc.
row.names(data)=data$ID<-c(1:length(data[,1]))
#Keep datetime for later
data <- data[order(data$Year, data$Month, data$Day, data$Hour),]
dataDatetime<-data 
data$Timestamp<-NULL
########################## Forecasting will be made per hour - Select & Create appropriate data-variables #############################################
weather=data ; weather$Day<-NULL
listofmodels<-unique(weather$Hour)
######################################         Dataset per hour                   ####################################
tempweather <- weather[,!names(weather) %in% c("Year","ID","Oldness",
                                               "santcugat_weatherforecast_wind_speed_forecast",
                                               "santcugat_weatherforecast_wind_direction_forecast",
                                               "santcugat_weatherforecast_air_pressure_forecast")]


predictPV<-function(tempweather){
  Hours<-NULL
  for (h in from:to){
    Hours[length(Hours)+1]<-list(tempweather[ which(tempweather$Hour==h), !names(tempweather) %in% c("Hour")])
    outliersH<-boxplot(Hours[[length(Hours)]],plot=FALSE)$stats[5,] 
    outliersL<-boxplot(Hours[[length(Hours)]],plot=FALSE)$stats[1,]#and exclude outliers
    for (j in 1:ncol(Hours[[length(Hours)]])){
      Hours[[length(Hours)]] <- subset(Hours[[length(Hours)]], Hours[[length(Hours)]][,j] <= outliersH[j])
      Hours[[length(Hours)]] <- subset(Hours[[length(Hours)]], Hours[[length(Hours)]][,j] >= outliersL[j])
    }
    Hours[[length(Hours)]]<- Hours[[length(Hours)]][Hours[[length(Hours)]]$santcugat_townhall_pv_power>10,]
  }
  # For the rest is equal to zero.....
  ####################################################################################################################################################
  #Calculate models
  Totalmodels<-NULL
  includeInt = FALSE
  for (i in 1:11){
    model<-regsubsets(santcugat_townhall_pv_power ~ .  ,intercept=includeInt ,data=Hours[[i]])
    summodel<-summary(model)
    Totalmodels[length(Totalmodels)+1]<-list(regsubsets(santcugat_townhall_pv_power ~ . ,intercept=includeInt ,data=Hours[[i]],nvmax=which.min(summodel$bic)))
  }
  #Estimate forecasts
  predictions<-NULL
  Hours<-NULL
  for (h in from:to){
    Hours[length(Hours)+1]<-list(tempweather[ which(tempweather$Hour==h), !names(tempweather) %in% c("Hour")])
  }
  
  sterros<-c()
  for (i in 1:length(Totalmodels)){
    
    temp <- as.data.frame(predict.regsubsets(Totalmodels[[i]],newdata=Hours[[i]],1))
    colnames(temp)[1]="Fitted"
    conf<-mean((temp$Fitted-Hours[[i]]$santcugat_townhall_pv_power)^2)^0.5
    sterros<-c(sterros,conf)
    temp$lwr<-temp$Fitted-1.28*conf
    temp$upr<-temp$Fitted+1.28*conf
    
    temp$ID=as.numeric(row.names(temp))
    predictions[length(predictions)+1] <- list(temp)
  }
  ###############################################################################################################################################
  
  #Combine forecasts 
  final.datatable<-predictions[[1]]
  for (i in 2:length(Totalmodels)){
    final.datatable=rbind(final.datatable,predictions[[i]])
  }
  final.datatable<- merge(final.datatable,dataDatetime,by="ID",all=TRUE)
  final.datatable <- final.datatable[ order(final.datatable$Year, final.datatable$Month, final.datatable$Day, final.datatable$Hour), ]
  
  final.datatable <- final.datatable[,!names(final.datatable) %in% c("santcugat_weatherforecast_air_temperature_forecast",
                                                                     "santcugat_weatherforecast_wind_speed_forecast",
                                                                     "santcugat_weatherforecast_air_pressure_forecast",
                                                                     "santcugat_weatherforecast_relative_humidity_forecast",
                                                                     "santcugat_weatherforecast_cloud_cover_forecast",
                                                                     "santcugat_weatherforecast_wind_direction_forecast", 
                                                                     "santcugat_weatherforecast_irradiation_forecast",
                                                                     "Year","Month","Day","Hour","ID","Oldness")]
  
  #### Fill hours with zero production ##############
  for (i in 1:nrow(final.datatable)){
    if (is.na(final.datatable$Fitted[i])==TRUE){
      final.datatable$Fitted[i]=0
    }
    if (is.na(final.datatable$lwr[i])==TRUE){
      final.datatable$lwr[i]=0
    }
    if (is.na(final.datatable$upr[i])==TRUE){
      final.datatable$upr[i]=0
    }
  }
  ##############################################################
  ####  Negative predictions are meaningless  ##############
  for (i in 1:nrow(final.datatable)){
    if (final.datatable$Fitted[i]<0){
      final.datatable$Fitted[i]=0
    }
    if (final.datatable$lwr[i]<0){
      final.datatable$lwr[i]=0
    }
    if (final.datatable$upr[i]<0){
      final.datatable$upr[i]=0
    }
  }
  
  for (i in 2:(nrow(final.datatable)-1)){
    if ((final.datatable$Fitted[i]==0)&(final.datatable$Fitted[i+1]>0)&(final.datatable$Fitted[i-1]>0)){
      final.datatable$Fitted[i]=(final.datatable$Fitted[i-1]+final.datatable$Fitted[i+1])/2
      final.datatable$lwr[i]=(final.datatable$lwr[i-1]+final.datatable$lwr[i+1])/2
      final.datatable$upr[i]=(final.datatable$upr[i-1]+final.datatable$upr[i+1])/2
    }
  }
  final.datatable$upr<-round(final.datatable$upr,2)
  final.datatable$lwr<-round(final.datatable$lwr,2)
  final.datatable$Fitted<-round(final.datatable$Fitted,2)
  return(list(final.datatable,Totalmodels))
}

prmdls<-predictPV(tempweather)
final.datatable<-prmdls[[1]]
Totalmodels<-list(prmdls[[2]],c(from,to))