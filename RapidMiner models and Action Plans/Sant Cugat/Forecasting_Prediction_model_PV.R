#The "output" matrix includes the produced forecasts  & prediction intervals for each hour of the upcomming week 
#The only input needed for the estimation of the PV production forecasts is the 'santcugat_pv_out' file, which is like the one i attach;

library(MASS)
library(leaps)

from<-min(Totalmodels[[2]])
to<-max(Totalmodels[[2]])
Totalmodels<-Totalmodels[[1]]

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

#data=data.frame(read.table("C:/Users/vangelis spil/Google Drive/OPTIMUS/Final Models/SantCougat Prediction Models/PV production Sant Cougat Town Hall/Inputs/santcugat_pv_out.csv", header = TRUE, sep = ",",stringsAsFactors = FALSE))

#These are the weather data (training)
data$Year <- as.numeric(substring(data$Timestamp, first=1, last=4))
data$Month <- as.numeric(substring(data$Timestamp, first=6, last=7))
data$Day <- as.numeric(substring(data$Timestamp, first=9, last=10))
data$Hour<-as.numeric(substring(data$Timestamp, first=12, last=13))
data <- data[order(data$Year, data$Month, data$Day, data$Hour),]

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


forecastPVE<-function(tempweather,Totalmodels){
  Hours<-NULL
  for (h in from:to){
    Hours[length(Hours)+1]<-list(tempweather[ which(tempweather$Hour==h), !names(tempweather) %in% c("Hour")])
  }
  predictions<-NULL
  
  for (i in 1:length(Totalmodels)){
    
    temp <- as.data.frame(predict.regsubsets(Totalmodels[[i]],newdata=Hours[[i]],1))
    colnames(temp)[1]="Fitted"
    temp$ID=as.numeric(row.names(temp))
    predictions[length(predictions)+1] <- list(temp)
  }
  
  #Combine forecasts 
  output<-predictions[[1]]
  for (i in 2:length(Totalmodels)){
    output=rbind(output,predictions[[i]])
  }
  output<- merge(output,dataDatetime,by="ID",all=TRUE)
  output <- output[ order(output$Year, output$Month, output$Day, output$Hour), ]
  
  output <- output[,!names(output) %in% c("santcugat_weatherforecast_air_temperature_forecast",
                                          "santcugat_weatherforecast_wind_speed_forecast",
                                          "santcugat_weatherforecast_air_pressure_forecast",
                                          "santcugat_weatherforecast_relative_humidity_forecast",
                                          "santcugat_weatherforecast_cloud_cover_forecast",
                                          "santcugat_weatherforecast_wind_direction_forecast", 
                                          "santcugat_weatherforecast_irradiation_forecast",
                                          "Year","Month","Day","Hour","ID","Oldness")]
  
  #### Fill hours with zero production ##############
  for (i in 1:nrow(output)){
    if (is.na(output$Fitted[i])==TRUE){
      output$Fitted[i]=0
    }
  }
  ##############################################################
  ####  Negative predictions are meaningless  ##############
  for (i in 1:nrow(output)){
    if (output$Fitted[i]<0){
      output$Fitted[i]=0
    }
  }
  
  for (i in 2:(nrow(output)-1)){
    if ((output$Fitted[i]==0)&(output$Fitted[i+1]>0)&(output$Fitted[i-1]>0)){
      output$Fitted[i]=(output$Fitted[i-1]+output$Fitted[i+1])/2
    }
  }
  output$Fitted<-round(output$Fitted,2)
  return(output)
}
output<-forecastPVE(tempweather,Totalmodels)
