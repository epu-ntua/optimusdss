#The  'final.datatable' data-frame includes the original data (production-weather) and the in-sample (past data) forecasts -together with the
#prediction intervals- of the dataset given for Zaanstad Town Hall. 
#The input needed for the estimation of the energy consumption model is the 'zaanstan_consumption_in' csv file (see attached)
#The regfit.final1...regfit.final24 are the calculated models for the hourly forecast of the energy consumption. These will be used for
#predicting for the upcomming week with the other script.
#We have 48 models (WD and WE since we analyze working and non-working days seperately)

library(MASS)
library(leaps)
library(timeDate)
library(forecast)

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


data<-na.omit(data) #Get rid of NAs (Missing Values)
data$Year <- as.numeric(substring(data$Timestamp, first=1, last=4))
data$Month <- as.numeric(substring(data$Timestamp, first=6, last=7))
data$Day <- as.numeric(substring(data$Timestamp, first=9, last=10))
data$Hour<-as.numeric(substring(data$Timestamp, first=12, last=13))
data <- data[order(data$Year, data$Month, data$Day, data$Hour),]

data$Oldness <- c(1:length(data[,1])) #IDs etc.
row.names(data)=data$ID<-c(1:length(data[,1]))
data$weekend <- isWeekend(data$Timestamp)
data$weekdays <- weekdays(as.Date(substring(data$Timestamp, first=1, last=10)))

for (i in 1:nrow(data)){
  if (data$weekdays[i]=="Monday"){ data$weekdays[i]= 1}
  if (data$weekdays[i]=="Tuesday"){ data$weekdays[i]= 2}
  if (data$weekdays[i]=="Wednesday"){ data$weekdays[i]= 3}
  if (data$weekdays[i]=="Thursday"){ data$weekdays[i]= 4}
  if (data$weekdays[i]=="Friday"){ data$weekdays[i]= 5}
  if (data$weekdays[i]=="Saturday"){ data$weekdays[i]= 6}
  if (data$weekdays[i]=="Sunday"){ data$weekdays[i]= 7}
}
data$weekdays<-as.numeric(data$weekdays)

###################### Non working Days are equal to weekend  #############################
for (i in 1:length(data[,1])){
  if ((data$Day[i]==1)&(data$Month[i]==1)){ data$weekend[i]=1 }
  if ((data$Day[i]==27)&(data$Month[i]==4)){ data$weekend[i]=1 }
  if ((data$Day[i]==5)&(data$Month[i]==12)){ data$weekend[i]=1 }
  if ((data$Day[i]==24)&(data$Month[i]==12)){ data$weekend[i]=1 }
  if ((data$Day[i]==25)&(data$Month[i]==12)){ data$weekend[i]=1 }
  if ((data$Day[i]==26)&(data$Month[i]==12)){ data$weekend[i]=1 }
  if ((data$Day[i]==31)&(data$Month[i]==12)){ data$weekend[i]=1 }
}

#NWD 2016
for (i in 1:length(data[,1])){
  if ((data$Day[i]==25)&(data$Month[i]==3)&(data$Year[i]==2016)){ data$weekend[i]=1 }
  if ((data$Day[i]==27)&(data$Month[i]==3)&(data$Year[i]==2016)){ data$weekend[i]=1 }
  if ((data$Day[i]==28)&(data$Month[i]==3)&(data$Year[i]==2016)){ data$weekend[i]=1 }
  if ((data$Day[i]==5)&(data$Month[i]==5)&(data$Year[i]==2016)){ data$weekend[i]=1 }
  if ((data$Day[i]==15)&(data$Month[i]==5)&(data$Year[i]==2016)){ data$weekend[i]=1 }
  if ((data$Day[i]==16)&(data$Month[i]==5)&(data$Year[i]==2016)){ data$weekend[i]=1 }
}
#NWD 2017
for (i in 1:length(data[,1])){
  if ((data$Day[i]==14)&(data$Month[i]==4)&(data$Year[i]==2017)){ data$weekend[i]=1 }
  if ((data$Day[i]==16)&(data$Month[i]==4)&(data$Year[i]==2017)){ data$weekend[i]=1 }
  if ((data$Day[i]==17)&(data$Month[i]==4)&(data$Year[i]==2017)){ data$weekend[i]=1 }
  if ((data$Day[i]==5)&(data$Month[i]==5)&(data$Year[i]==2017)){ data$weekend[i]=1 }
  if ((data$Day[i]==25)&(data$Month[i]==5)&(data$Year[i]==2017)){ data$weekend[i]=1 }
  if ((data$Day[i]==4)&(data$Month[i]==6)&(data$Year[i]==2017)){ data$weekend[i]=1 }
  if ((data$Day[i]==6)&(data$Month[i]==6)&(data$Year[i]==2017)){ data$weekend[i]=1 }
}
for (i in 1:length(data[,1])){ if ( data$weekend[i]==TRUE ){ data$weekend[i]=1 } }
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
                                               "santcugat_weatherforecast_air_pressure_forecast",
                                               "santcugat_weatherforecast_cloud_cover_forecast",
                                               "santcugat_weatherforecast_air_pressure_forecast",
                                               "santcugat_weatherforecast_irradiation_forecast")]


PredictionEnergy <- function(tempweather,dataDatetime){
  Hours<-NULL
  for (h in 0:23){
    Hours[length(Hours)+1]<-list(tempweather[ which(tempweather$Hour==h), !names(tempweather) %in% c("Hour")])
    outliersH<-boxplot(Hours[[length(Hours)]],plot=FALSE)$stats[5,] 
    outliersL<-boxplot(Hours[[length(Hours)]],plot=FALSE)$stats[1,]#and exclude outliers
    for (j in 1:ncol(Hours[[length(Hours)]])){
      Hours[[length(Hours)]] <- subset(Hours[[length(Hours)]], Hours[[length(Hours)]][,j] <= outliersH[j])
      Hours[[length(Hours)]] <- subset(Hours[[length(Hours)]], Hours[[length(Hours)]][,j] >= outliersL[j])
    }
  }
  ####################################################################################################################################################
  #Calculate models
  Totalmodels<-NULL
  for (i in 1:length(Hours)){
    model<-regsubsets(santcugat_townhall_energea_energymeter_total ~ .  ,intercept=TRUE ,data=Hours[[i]])
    summodel<-summary(model)
    Totalmodels[length(Totalmodels)+1]<-list(regsubsets(santcugat_townhall_energea_energymeter_total ~ . ,intercept=TRUE ,data=Hours[[i]],nvmax=which.min(summodel$bic)))
  }
  #Estimate forecasts
  predictions<-NULL
  Hours<-NULL
  for (h in 0:23){
    Hours[length(Hours)+1]<-list(tempweather[ which(tempweather$Hour==h), !names(tempweather) %in% c("Hour")])
  }
  for (i in 1:length(Totalmodels)){
    
    temp <- as.data.frame(predict.regsubsets(Totalmodels[[i]],newdata=Hours[[i]],1))
    colnames(temp)[1]="Fitted"
    temp$ID=as.numeric(row.names(temp))
    predictions[length(predictions)+1] <- list(temp)
    
  }
  ###############################################################################################################################################
  #Combine forecasts 
  final.datatable<-predictions[[1]]
  for (i in 2:length(Totalmodels)){  final.datatable=rbind(final.datatable,predictions[[i]])}
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
  
  minE<-boxplot(final.datatable$santcugat_townhall_energea_energymeter_total,plot=FALSE)$stats[1]
  #### Fill hours with zero production and NA ##############
  for (i in 1:nrow(final.datatable)){
    if (is.na(final.datatable$Fitted[i])==TRUE){ final.datatable$Fitted[i]=minE}
    #  Negative predictions are meaningless
    if (final.datatable$Fitted[i]<0){ final.datatable$Fitted[i]=minE }
    #Min of last 7 days
  }
  ##############################################################
  for (i in 2:(nrow(final.datatable)-1)){
    if ((final.datatable$Fitted[i]==0)&(final.datatable$Fitted[i+1]>0)&(final.datatable$Fitted[i-1]>0)){final.datatable$Fitted[i]=(final.datatable$Fitted[i-1]+final.datatable$Fitted[i+1])/2 }
  }
  final.datatable$Fitted<-round(final.datatable$Fitted,2)  
  
  return( list(final.datatable,Totalmodels) )
}


################################  For working days   #####################################
fileimport<-tempweather[tempweather$weekend==0,] ;  fileimport$weekend<-NULL
fileimport2<-dataDatetime[dataDatetime$weekend==0,] ;  fileimport2$weekend<-NULL
outputWD<-PredictionEnergy(tempweather=fileimport,dataDatetime=fileimport2)

###########################  For non working days and weekends  ##############################
fileimport<-tempweather[tempweather$weekend==1,] ;  fileimport$weekend<-NULL
fileimport2<-dataDatetime[dataDatetime$weekend==1,] ;  fileimport2$weekend<-NULL
outputNWD<-PredictionEnergy(tempweather=fileimport,dataDatetime=fileimport2)

final.datatable <-rbind(outputWD[[1]],outputNWD[[1]])
final.datatable <- final.datatable[ order(final.datatable$Timestamp), ]
Totalmodels <- list(outputWD[[2]],outputNWD[[2]])

final.datatable$weekdays<-NULL
#write.csv(final.datatable, file=paste("C:/Users/vangelis spil/Desktop/final.datatable.csv"),row.names=FALSE)

################  Validate    ##########################
# from=1
# to=268
# for (i in 1:23){
#   object<-Totalmodels[[2]][[i]]
#   form  <-  as.formula(~.)
#   coefi  <-  coef(object, 1)
#   xvars  <-  names(coefi)
#   #print(xvars)
# }
# plot(final.datatable$santcugat_townhall_energea_energymeter_total[from:to],type="l")
# lines(final.datatable$Fitted[from:to],type="l",col="red")
# ppp<-abs(final.datatable$santcugat_townhall_energea_energymeter_total-final.datatable$Fitted)
# mean(ppp)*100/mean(final.datatable$santcugat_townhall_energea_energymeter_total)
# paste(round(mean(ppp,na.rm = TRUE)*100/mean(final.datatable$santcugat_townhall_energea_energymeter_total),2),"% error")
# ################  End Validation    ##########################