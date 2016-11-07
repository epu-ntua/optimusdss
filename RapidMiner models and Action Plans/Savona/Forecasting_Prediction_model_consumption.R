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
                                               "pressure","relative_humidity","irradiation")]


PredictionEnergy <- function(tempweather,dataDatetime,Totalmodels){
  Hours<-NULL
  for (h in 0:23){
    Hours[length(Hours)+1]<-list(tempweather[ which(tempweather$Hour==h), !names(tempweather) %in% c("Hour")])
  }
  ####################################################################################################################################################
  #Estimate forecasts
  predictions<-NULL
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
  
  final.datatable <- final.datatable[,!names(final.datatable) %in% c("air_temperature","pressure","relative_humidity","irradiation",
                                                                     "Year","Month","Day","Hour","ID","Oldness")]
  
  #### Fill hours with zero production and NA ##############
  for (i in 1:nrow(final.datatable)){
    if (is.na(final.datatable$Fitted[i])==TRUE){ final.datatable$Fitted[i]=70}
    #  Negative predictions are meaningless
    if (final.datatable$Fitted[i]<0){ final.datatable$Fitted[i]=70 }
    #Min of last 7 days
  }
  ##############################################################
  for (i in 2:(nrow(final.datatable)-1)){
    if ((final.datatable$Fitted[i]==0)&(final.datatable$Fitted[i+1]>0)&(final.datatable$Fitted[i-1]>0)){final.datatable$Fitted[i]=(final.datatable$Fitted[i-1]+final.datatable$Fitted[i+1])/2 }
  }
  final.datatable$Fitted<-round(final.datatable$Fitted,2)  
  
  return( final.datatable )
}
################################  For working days   #####################################
fileimport<-tempweather[tempweather$weekend==0,] ;  fileimport$weekend<-NULL
fileimport2<-dataDatetime[dataDatetime$weekend==0,] ;  fileimport2$weekend<-NULL
outputWD<-PredictionEnergy(tempweather=fileimport,dataDatetime=fileimport2,Totalmodels=Totalmodels[[1]])
###########################  For non working days and weekends  ##############################
fileimport<-tempweather[tempweather$weekend==1,] ;  fileimport$weekend<-NULL
fileimport2<-dataDatetime[dataDatetime$weekend==1,] ;  fileimport2$weekend<-NULL
outputNWD<-PredictionEnergy(tempweather=fileimport,dataDatetime=fileimport2,Totalmodels=Totalmodels[[2]])
output <-rbind(outputWD,outputNWD)
output$weekdays<-NULL
output <- output[ order(output$Timestamp), ]
colnames(output)<-c("prediction","datetime")