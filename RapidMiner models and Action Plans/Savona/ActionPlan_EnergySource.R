library(forecast)
library(MASS)
library(timeDate)
library(leaps)

Pcapacity=50  ; Ncapacity=14

fittingLoad <- function(data,weatherOut){
  
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
  data <- data[order(data$Year, data$Month, data$Day, data$Hour),]
  data$Oldness <- c(1:length(data[,1])) #IDs etc.
  row.names(data)=data$ID<-c(1:length(data[,1]))
  data$weekend <- isWeekend(data$datetime)
  data$weekdays <- weekdays(as.Date(substring(data$datetime, first=1, last=10)))
  
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
  for (i in 1:length(data[,1])){ if ( data$weekend[i]==TRUE ){ data$weekend[i]=1 } }
  if( (data$Month[i]==1)&(data$Day[i]==1) ){ data$weekend[i]=1 }
  if( (data$Month[i]==1)&(data$Day[i]==6)){ data$weekend[i]=1 }
  if( (data$Month[i]==4)&(data$Day[i]==25)){ data$weekend[i]=1 }
  if( (data$Month[i]==5)&(data$Day[i]==1)){ data$weekend[i]=1 }
  if( (data$Month[i]==6)&(data$Day[i]==2)){ data$weekend[i]=1 }
  if( (data$Month[i]==8)&(data$Day[i]==15)){ data$weekend[i]=1 }
  if( (data$Month[i]==11)&(data$Day[i]==1)){ data$weekend[i]=1 }
  if( (data$Month[i]==12)&(data$Day[i]==8)){ data$weekend[i]=1 }
  if( (data$Month[i]==12)&(data$Day[i]==25)){ data$weekend[i]=1 }
  if( (data$Month[i]==12)&(data$Day[i]==26)){ data$weekend[i]=1 }
  if( (data$Month[i]==12)&(data$Day[i]==31)){ data$weekend[i]=1 }
  if( (data$Year[i]==2016)&(data$Month[i]==3)&(data$Day[i]==25)){ data$weekend[i]=1 }
  if( (data$Year[i]==2016)&(data$Month[i]==3)&(data$Day[i]==27)){ data$weekend[i]=1 }
  if( (data$Year[i]==2016)&(data$Month[i]==3)&(data$Day[i]==28)){ data$weekend[i]=1 }
  #Keep datetime for later
  data <- data[order(data$Year, data$Month, data$Day, data$Hour),]
  dataDatetime<-data 
  data$datetime<-NULL
  ########################## Forecasting will be made per hour - Select & Create appropriate data-variables #############################################
  weather=data ; weather$Day<-NULL
  listofmodels<-unique(weather$Hour)
  ######################################         Dataset per hour                   ####################################
  tempweather <- weather[,!names(weather) %in% c("Year","ID","Oldness",
                                                 "PressurehPa","Month",
                                                 "SolarRadiationWatts.m.2")]
  
  
  PredictionEnergy <- function(tempweather,dataDatetime){
    
    tempweather$CHP=tempweather$Storage=tempweather$PV=tempweather$Capacity=tempweather$Grid<-NULL
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
    includeInt = TRUE
    for (i in 1:length(Hours)){
      model<-regsubsets(Load ~ .  ,intercept=includeInt ,data=Hours[[i]])
      summodel<-summary(model)
      Totalmodels[length(Totalmodels)+1]<-list(regsubsets(Load ~ . ,intercept=includeInt ,data=Hours[[i]],nvmax=which.min(summodel$bic)))
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
    
    final.datatable <- final.datatable[,!names(final.datatable) %in% c("TemperatureC","PressurehPa",
                                                                       "Humidity","SolarRadiationWatts.m.2",
                                                                       "CHP","Storage","PV","Capacity","Grid",
                                                                       "Year","Month","Day","Hour","ID","Oldness","weekend","weekdays")]
    
    minE<-boxplot(final.datatable$Load,plot=FALSE)$stats[1]
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
  final.datatable <- final.datatable[ order(final.datatable$datetime), ]
  Totalmodels <- list(outputWD[[2]],outputNWD[[2]])
  final.datatable$weekdays<-NULL
  
  #These are the weather data (training)
  data=weatherOut
  data<-na.omit(data) #Get rid of NAs (Missing Values)
  data$Year <- as.numeric(substring(data$datetime, first=1, last=4))
  data$Month <- as.numeric(substring(data$datetime, first=6, last=7))
  data$Day <- as.numeric(substring(data$datetime, first=9, last=10))
  data$Hour<-as.numeric(substring(data$datetime, first=12, last=13))
  data <- data[order(data$Year, data$Month, data$Day, data$Hour),]
  
  data$Oldness <- c(1:length(data[,1])) #IDs etc.
  row.names(data)=data$ID<-c(1:length(data[,1]))
  data$weekend <- isWeekend(data$datetime)
  data$weekdays <- weekdays(as.Date(substring(data$datetime, first=1, last=10)))
  
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
  if( (data$Month[i]==1)&(data$Day[i]==1) ){ data$weekend[i]=1 }
  if( (data$Month[i]==1)&(data$Day[i]==6)){ data$weekend[i]=1 }
  if( (data$Month[i]==4)&(data$Day[i]==25)){ data$weekend[i]=1 }
  if( (data$Month[i]==5)&(data$Day[i]==1)){ data$weekend[i]=1 }
  if( (data$Month[i]==6)&(data$Day[i]==2)){ data$weekend[i]=1 }
  if( (data$Month[i]==8)&(data$Day[i]==15)){ data$weekend[i]=1 }
  if( (data$Month[i]==11)&(data$Day[i]==1)){ data$weekend[i]=1 }
  if( (data$Month[i]==12)&(data$Day[i]==8)){ data$weekend[i]=1 }
  if( (data$Month[i]==12)&(data$Day[i]==25)){ data$weekend[i]=1 }
  if( (data$Month[i]==12)&(data$Day[i]==26)){ data$weekend[i]=1 }
  if( (data$Month[i]==12)&(data$Day[i]==31)){ data$weekend[i]=1 }
  if( (data$Year[i]==2016)&(data$Month[i]==3)&(data$Day[i]==25)){ data$weekend[i]=1 }
  if( (data$Year[i]==2016)&(data$Month[i]==3)&(data$Day[i]==27)){ data$weekend[i]=1 }
  if( (data$Year[i]==2016)&(data$Month[i]==3)&(data$Day[i]==28)){ data$weekend[i]=1 }
  for (i in 1:length(data[,1])){ if ( data$weekend[i]==TRUE ){ data$weekend[i]=1 } }
  #Keep datetime for later
  data <- data[order(data$Year, data$Month, data$Day, data$Hour),]
  dataDatetime<-data 
  data$datetime<-NULL
  ########################## Forecasting will be made per hour - Select & Create appropriate data-variables #############################################
  weather=data ; weather$Day<-NULL
  listofmodels<-unique(weather$Hour)
  ######################################         Dataset per hour                   ####################################
  tempweather <- weather[,!names(weather) %in% c("Year","ID","Oldness",
                                                 "PressurehPa","Month",
                                                 "SolarRadiationWatts.m.2")]
  
  
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
    
    final.datatable <- final.datatable[,!names(final.datatable) %in% c("TemperatureC","PressurehPa",
                                                                       "Humidity","SolarRadiationWatts.m.2",
                                                                       "CHP","Storage","PV","Capacity","Grid",
                                                                       "Year","Month","Day","Hour","ID","Oldness","weekend","weekdays")]
    
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
  output <- output[ order(output$datetime), ]
  
  output$weekdays<-NULL
  output_f <- list(final.datatable,output)
  
  return(output_f)
}
fittingPV <- function(data,weatherOut){
  
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
  data$Year <- as.numeric(substring(data$datetime, first=1, last=4))
  data$Month <- as.numeric(substring(data$datetime, first=6, last=7))
  data$Day <- as.numeric(substring(data$datetime, first=9, last=10))
  data$Hour<-as.numeric(substring(data$datetime, first=12, last=13))
  data <- data[order(data$Year, data$Month, data$Day, data$Hour),]
  data$Oldness <- c(1:length(data[,1])) #IDs etc.
  row.names(data)=data$ID<-c(1:length(data[,1]))
  #Keep datetime for later
  data <- data[order(data$Year, data$Month, data$Day, data$Hour),]
  dataDatetime<-data 
  data$datetime<-NULL
  ########################## Forecasting will be made per hour - Select & Create appropriate data-variables #############################################
  weather=data ; weather$Day<-NULL
  listofmodels<-unique(weather$Hour)
  ######################################         Dataset per hour                   ####################################
  tempweather <- weather[,!names(weather) %in% c("Year","ID","Oldness",
                                                 "Humidity",
                                                 "PressurehPa" )]
  tempweather$CHP=tempweather$Storage=tempweather$Load=tempweather$Capacity=tempweather$Grid<-NULL
  Hours<-NULL
  for (h in 9:16){
    Hours[length(Hours)+1]<-list(tempweather[ which(tempweather$Hour==h), !names(tempweather) %in% c("Hour")])
    Hours[[length(Hours)]]<- Hours[[length(Hours)]][(Hours[[length(Hours)]]$PV)>0,]
  }
  # For the rest is equal to zero.....
  ####################################################################################################################################################
  #Calculate models
  Totalmodels<-NULL
  includeInt = FALSE
  for (i in 1:8){
    model<-regsubsets(PV ~ .  ,intercept=includeInt ,data=Hours[[i]])
    summodel<-summary(model)
    Totalmodels[length(Totalmodels)+1]<-list(regsubsets(PV ~ . ,intercept=includeInt ,data=Hours[[i]],nvmax=which.min(summodel$bic))) 
  }
  #Estimate forecasts
  predictions<-NULL
  Hours<-NULL
  for (h in 9:19){
    Hours[length(Hours)+1]<-list(tempweather[ which(tempweather$Hour==h), !names(tempweather) %in% c("Hour")])
  }
  
  for (i in 1:length(Totalmodels)){
    
    temp <- as.data.frame(predict.regsubsets(Totalmodels[[i]],newdata=Hours[[i]],1))
    colnames(temp)[1]="Fitted"
    conf<-mean((temp$Fitted-Hours[[i]]$PV)^2)^0.5
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
  
  final.datatable <- final.datatable[,!names(final.datatable) %in% c("TemperatureC","PressurehPa",
                                                                     "Humidity","SolarRadiationWatts.m.2",
                                                                     "Grid","Load","CHP","Storage","Capacity",
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
  
  #   ################  Validate    ##########################
  #   from=100
  #   to=300
  #   plot(final.datatable$PV[from:to],type="l")
  #   lines(final.datatable$Fitted[from:to],type="l",col="red")
  #   ppp<-abs(final.datatable$PV-final.datatable$Fitted)
  #   mean(ppp,na.rm = TRUE)*100/mean(final.datatable$PV)
  #   ################  End Validation    ##########################
  
  # write.csv(final.datatable, file=paste("C:/Users/vangelis spil/Desktop/final.datatable1.csv"),row.names=FALSE)
  data=weatherOut
  #These are the weather data (training)
  data$Year <- as.numeric(substring(data$datetime, first=1, last=4))
  data$Month <- as.numeric(substring(data$datetime, first=6, last=7))
  data$Day <- as.numeric(substring(data$datetime, first=9, last=10))
  data$Hour<-as.numeric(substring(data$datetime, first=12, last=13))
  data <- data[order(data$Year, data$Month, data$Day, data$Hour),]
  
  data$Oldness <- c(1:length(data[,1])) #IDs etc.
  row.names(data)=data$ID<-c(1:length(data[,1]))
  #Keep datetime for later
  data <- data[order(data$Year, data$Month, data$Day, data$Hour),]
  dataDatetime<-data 
  data$datetime<-NULL
  ########################## Forecasting will be made per hour - Select & Create appropriate data-variables #############################################
  weather=data ; weather$Day<-NULL
  listofmodels<-unique(weather$Hour)
  ######################################         Dataset per hour                   ####################################
  tempweather <- weather[,!names(weather) %in% c("Year","ID","Oldness",
                                                 "Humidity",
                                                 "PressurehPa" )]
  Hours<-NULL
  for (h in 9:16){
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
  for (i in 2:length(Totalmodels)){ output=rbind(output,predictions[[i]])  }
  output<- merge(output,dataDatetime,by="ID",all=TRUE)
  output <- output[ order(output$Year, output$Month, output$Day, output$Hour), ]
  output <- output[,!names(output) %in% c("TemperatureC","PressurehPa","Humidity",
                                          "SolarRadiationWatts.m.2",
                                          "Grid","Load","CHP","Storage","Capacity",
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
  output_f <- list(final.datatable,output)
  
  return(output_f)
}
heuristics <- function(dataout,dataoutOR,datain,plott=FALSE){
  
  #   dataout=FinalSuggestions;datain=DataReal ; dataoutOR=dataOUT 
  
  
  ########################## ########################## ########################## 
  ################### Start of stage 1 - Peak shaving ############################ 
  ########################## ########################## ##########################
  startingcapacity<-Ncapacity #Apo pou ksekinhsa (as ksekinhsoume me to elaxisto)
  data2 <- dataout 
  data2$Year <- as.numeric(substring(data2$datetime, first=1, last=4))
  data2$Month <- as.numeric(substring(data2$datetime, first=6, last=7))
  data2$Day <- as.numeric(substring(data2$datetime, first=9, last=10))
  data2$Hour <- as.numeric(substring(data2$datetime, first=12, last=13))
  #data2$RES<-data2$PV-data2$CHP ; data2$PV=data2$CHP<-NULL
  data2$RESoriginal<-dataout$RES
  
  #Charging zones
  data2$ChargingZone<-energycost(dataoutOR,Enprices)$Type
  for (i in 1:length(data2$datetime)){
    if(data2$ChargingZone[i]=="F3"){
      data2$CostH[i]<-0
    }else if ((data2$ChargingZone[i]=="F2")&(prices[prices$Month==data2$Month[i],]$Difference>0)){
      data2$CostH[i]<-1
    }else if ((data2$ChargingZone[i]=="F2")&(prices[prices$Month==data2$Month[i],]$Difference<0)){
      data2$CostH[i]<-2
    }else if ((data2$ChargingZone[i]=="F1")&(prices[prices$Month==data2$Month[i],]$Difference>0)){
      data2$CostH[i]<-2
    }else if ((data2$ChargingZone[i]=="F1")&(prices[prices$Month==data2$Month[i],]$Difference<0)){
      data2$CostH[i]<-1
    }
  }
  
  #NA afta pou thelw na ftiaksw egw
  data2$Storage <- 0;    data2$Grid <- 0
  data2$Capacity <-0 ; data2$Capacity[1]=startingcapacity
  data2$DayID<-paste(data2$Day,data2$Month,data2$Year)
  data2$MonthID<-paste(data2$Month,data2$Year)
  #Analytical presentation of Energy Flows from/to PV and Battery
  data2$MaxOfMonth<-0
  data2$FromGridtoDemand<-0
  data2$FromREStoStorage<-0
  data2$FromREStoGrid<-0
  data2$FromREStoDemand<-0
  data2$FromStoragetoDemand<-0
  
  #Max of historical months
  datain$MonthID<-paste(datain$Month,datain$Year)
  nummonths<-length(unique(datain$MonthID)); monthsIDs<-unique(datain$MonthID)
  PeakOfMonths<-matrix(NA,ncol=2,nrow=nummonths)
  for (i in 1:nummonths){
    PeakOfMonths[i,1]<-monthsIDs[i] ; PeakOfMonths[i,2]<-max(datain[datain$MonthID==monthsIDs[i],]$Grid)
  }
  
  #Max of selected months in data2
  for (j in 1:length(data2$datetime)){
    for (i in 1:nummonths){
      if (PeakOfMonths[i,1]==data2$MonthID[j]){
        data2$MaxOfMonth[j]<-as.numeric(PeakOfMonths[i,2])
      }
    }
    if (is.na(data2$MaxOfMonth[j])==TRUE){
      data2$MaxOfMonth[j]<-as.numeric(PeakOfMonths[nummonths,2])
    }
  }
  
  #FLow from PV to Demand
  data2$ForMax<-0
  for (j in 1:length(data2$datetime)){
    if (data2$Load[j]>data2$MaxOfMonth[j]){ #xreiazomai peak shaving
      difference<-data2$Load[j]-data2$MaxOfMonth[j]
      if (data2$RES[j]>0){ #exw paragwgh
        if (data2$RES[j]>difference){
          data2$FromREStoDemand[j]<-difference
          data2$RES[j]<-data2$RES[j]-difference
        }else{
          data2$FromREStoDemand[j]<-data2$RES[j]
          data2$RES[j]<-0
          data2$ForMax[j]<-difference-data2$FromREStoDemand[j]
        }
      }else{
        data2$ForMax[j]<-difference  #Poso mou leipei gia na mhn exw peak
      }
    }
  }
  
  
  #This is for peak shaving!!!!!!!!!!!
  
  
  #Tables of changes - charge & discharge
  numdays<-length(unique(data2$DayID))
  PV_table<-data.frame(matrix(0,ncol=27,nrow=numdays))
  colnames(PV_table)<-c("DayId","MonthID","MaxM","H0","H1","H2","H3","H4","H5","H6","H7","H8","H9","H10",
                        "H11","H12","H13","H14","H15","H16","H17","H18","H19","H20","H21","H22","H23")
  PV_table$DayId<-unique(data2$DayID)
  for (i in 1:numdays){   PV_table$MonthID[i]<-data2[data2$DayID==unique(data2$DayID)[i],]$MonthID[1] }
  
  for (i in 1:nummonths){
    for (j in 1:numdays){
      if (monthsIDs[i]==PV_table$MonthID[j]){ PV_table$MaxM[j]=PeakOfMonths[i,2]  }
    }
  }
  for (j in 1:numdays){
    if (is.na(PV_table$MaxM[j])==TRUE){ PV_table$MaxM[j]=PeakOfMonths[nummonths,2] }
  }
  NeedCharge_table=Charge=Capacity<-PV_table
  
  #Calculate changes to be made per day and available RES production
  for (i in 1:numdays){
    for (j in 0:23){
      if (length(data2[(data2$DayID==unique(data2$DayID)[i])&(data2$Hour==j),]$ForMax)>0){
        NeedCharge_table[i,j+4]<-data2[(data2$DayID==unique(data2$DayID)[i])&(data2$Hour==j),]$ForMax
        PV_table[i,j+4]<-data2[(data2$DayID==unique(data2$DayID)[i])&(data2$Hour==j),]$RES
      }else{
        NeedCharge_table[i,j+4]<-0
        PV_table[i,j+4]<-0
      }
    }
  }
  
  for (i in 1:length(NeedCharge_table[,1])){
    for (j in 1:length(NeedCharge_table[1,])){
      if (NeedCharge_table[i,j]<=0){ NeedCharge_table[i,j]=0 } #Afinei mono ta provlhmatika
      if (NeedCharge_table[i,j]>0){ PV_table[i,j]=0 } #Ekserei oso PV exei faei hdh
      if (PV_table[i,j]<0){ PV_table[i,j]=0 } #Ekserei kai oso einai arnhtiko
    }
  }
  
  
  for (i in 1:length(NeedCharge_table[,1])){
    for (j in length(NeedCharge_table[1,]):4){
      Capacity[i,j]=Ncapacity
    }
  }
  
  ############Day 1#####################
  NeedCharge_table$DayId=NeedCharge_table$MaxM=NeedCharge_table$MonthID<-NULL
  PV_table$DayId=PV_table$MaxM=PV_table$MonthID<-NULL
  Capacity$DayId=Capacity$MaxM=Capacity$MonthID<-NULL
  Charge$DayId=Charge$MaxM=Charge$MonthID<-NULL
  
  #Capacity einai to ti tha exw available sto telos ths wras
  #Thetiko charge einai oti fortizw - arnitiko oti ksefwrtizw
  cancharge<-Pcapacity-Ncapacity  
  for (i in 1:length(NeedCharge_table[,1])){
    
    if (sum(NeedCharge_table[i,1:length(NeedCharge_table[1,])])>0){
      
      for (j in length(NeedCharge_table[1,]):1){
        
        meaningless<-FALSE
        for (check in j:24){  #is there a max infront
          if (Capacity[i,check]==Pcapacity){
            maxid<-check 
            break
          }else{
            maxid<-14000
          }
        }
        for (check in j:24){ #where is the peak?
          if (NeedCharge_table[i,check]>0){
            peakid<-check 
            break
          }else{
            peakid<-0.0000001
          }
        }
        if (peakid>maxid){meaningless=TRUE} 
        
        
        if ((sum(NeedCharge_table[i,j:length(NeedCharge_table[1,])])>0)&(meaningless==FALSE)){ 
          #xreiazetai na xrhsimopihsw mpataria meta apo afto
          if (PV_table[i,j]>0){ #exw diathesimh paragwgh thn prohgoumenh wra
            howmuchineed<-sum(NeedCharge_table[i,j:length(NeedCharge_table[1,])])
            
            if (PV_table[i,j]>=howmuchineed){
              applied<-howmuchineed  #Ti tha mporousa na traviksw apo afta pou exw
            }else{
              applied<-PV_table[i,j]
            }
            
            cancharge<-Pcapacity-Capacity[i,j+1]
            if (applied<=cancharge){
              Charge[i,j]<-applied
              PV_table[i,j]<-PV_table[i,j]-Charge[i,j]
              Capacity[i,j]<-Capacity[i,j]+Charge[i,j]
            }else{
              Charge[i,j]<-cancharge
              PV_table[i,j]<-PV_table[i,j]-Charge[i,j]
              Capacity[i,j]<-Capacity[i,j]+Charge[i,j]
            }
            for (n in (j+1):length(NeedCharge_table[1,])){ Capacity[i,n]<-Capacity[i,n-1]+Charge[i,n] }
            
            for (k in (j+1):length(NeedCharge_table[1,])){ 
              if (NeedCharge_table[i,k]>0){
                if ((Capacity[i,k]-Ncapacity)>NeedCharge_table[i,k]){
                  Charge[i,k]<-(-1)*NeedCharge_table[i,k]+Charge[i,k]
                  Capacity[i,k]<-Capacity[i,k]-NeedCharge_table[i,k]
                  NeedCharge_table[i,k]<-0
                  for (n in (k+1):length(NeedCharge_table[1,])){ Capacity[i,n]<-Capacity[i,n-1]+Charge[i,n] }
                }else if (Capacity[i,k]>Ncapacity){
                  Charge[i,k]<-(-1)*(Capacity[i,k]-Ncapacity)+Charge[i,k]
                  Capacity[i,k]<-Ncapacity
                  NeedCharge_table[i,k]<-NeedCharge_table[i,k]+Charge[i,k]
                  for (n in (k+1):length(NeedCharge_table[1,])){ Capacity[i,n]<-Capacity[i,n-1]+Charge[i,n] }
                }
              }
            }
            
          }
          
          
        }
        
        
      }
      
    }
    
  }
  
  
  ####Back to data2
  Charge$Day=Capacity$Day=PV_table$Day<-unique(data2$DayID)
  for (nd in 1:length(unique(data2$DayID))){
    search<-unique(data2$DayID)[nd]
    for (i in 1:length(data2$datetime)){
      if (data2$DayID[i]==search){
        for (j in 0:23){
          if (j==data2$Hour[i]){
            data2$Capacity[i]=Capacity[nd,j+1]
            if (Charge[nd,j+1]>0){
              data2$FromREStoStorage[i]=Charge[nd,j+1]
            }else if (Charge[nd,j+1]<0){
              data2$FromStoragetoDemand[i]=(-1)*Charge[nd,j+1]
            }
            data2$RES[i]=PV_table[nd,j+1]
          }
        }
      }
    }
  }
  data2$ForMax=data2$DayID=data2$MaxOfMonth=data2$MonthID<-NULL  
  Capacity=NeedCharge_table=PV_table=Charge<-NULL ; data2$Keepasitis<-0
  for (i in 1:length(data2$Year)){
    if (data2$Capacity[i]>Ncapacity){
      data2$Keepasitis[i]<-1
    }
  }
  data2$LoadOr<-data2$Load
  data2$Load<-data2$LoadOr-data2$FromGridtoDemand-data2$FromREStoDemand-data2$FromStoragetoDemand
  #Stage1 - Peak shaving
  #plot(data2$Load,type="l")
  #lines(data2$Grid,type="l",col="red")
  ########################## ########################## ########################## 
  ################### End of stage 1 - Peak shaving ############################ 
  ########################## ########################## ##########################
  
  
  ########################## ########################## ########################## 
  ################### Start of stage 2 - Charging zones ############################ 
  ########################## ########################## ##########################
  
  new_data2<-NULL ; DaysIDs<-unique(data2$Day)
  for (nod in 1:numdays){
    
    whichday<-DaysIDs[nod]
    temp<-data2[data2$Day==whichday,]
    #temp$LoadOr<-temp$Load-temp$FromStoragetoDemand-temp$FromREStoDemand-temp$FromGridtoDemand
    if (length(temp$Grid)==1){
      new_data2<-rbind(new_data2,temp)
      for (kkk in 2:length(new_data2$Capacity)){
        if (new_data2$Capacity[kkk]<new_data2$Capacity[kkk-1]){
          new_data2$FromStoragetoDemand[kkk]<-new_data2$Capacity[kkk-1]-new_data2$Capacity[kkk]
        }
      }
    }else{
      
      for (i in 1:(length(temp$Hour)-1)){
        
        price<-temp$CostH[i]
        if (temp$Keepasitis[i]==1){
          atleastkeep<-temp$Capacity[i]
        }else{
          atleastkeep<-Ncapacity
        }
        
        if (max(temp$CostH[(i+1):length(temp$Year)],na.rm=TRUE)<=price){  #is the most expensive charging zone
          
          if (temp$RES[i]>=temp$Load[i]){ #PV Production greater or equal with the Energy Demand
            temp$FromREStoDemand[i]<-temp$RES[i]-temp$Load[i]+temp$FromREStoDemand[i]
            temp$RES[i]<-temp$RES[i]-temp$Load[i]
            if (temp$RES[i]>0){ #there is a surplus of RES?
              if (temp$Capacity[i]==Pcapacity){  #maximum capacity
                temp$FromREStoGrid[i]<-temp$RES[i]+temp$FromREStoGrid[i]
                temp$RES[i]<-0
              }else{  #can charge
                xwraeisempataria<-Pcapacity-temp$Capacity[i]
                if (temp$RES[i]>xwraeisempataria){ #more than can charge
                  temp$FromREStoStorage[i]<-xwraeisempataria+temp$FromREStoStorage[i]
                  temp$FromREStoGrid[i]<-temp$RES[i]-xwraeisempataria+temp$FromREStoGrid[i]
                  temp$RES[i]<-0
                  temp$Capacity[i]<-Pcapacity
                }else{ #less than can charge
                  temp$FromREStoStorage[i]<-temp$RES[i]+temp$FromREStoStorage[i]
                  temp$RES[i]<-0
                  temp$Capacity[i]<-temp$Capacity[i]+temp$FromREStoStorage[i]
                }
              }
            }
            temp$Load[i]<-temp$LoadOr[i]-temp$FromGridtoDemand[i]-temp$FromREStoDemand[i]-temp$FromStoragetoDemand[i]
          }else{ #PV Production less than the Energy Demand
            temp$FromREStoDemand[i]<-temp$RES[i]+temp$FromREStoDemand[i]
            temp$RES[i]<-0
            temp$Load[i]<-temp$LoadOr[i]-temp$FromGridtoDemand[i]-temp$FromREStoDemand[i]-temp$FromStoragetoDemand[i]
            if (temp$Capacity[i]>atleastkeep){
              howmuchineed<-temp$Load[i]
              diathesimoC<-temp$Capacity[i]-atleastkeep
              if (diathesimoC>howmuchineed){
                temp$FromStoragetoDemand[i]<-howmuchineed+temp$FromStoragetoDemand[i]
                temp$Capacity[i]<-temp$Capacity[i]-howmuchineed
              }
              temp$Load[i]<-temp$LoadOr[i]-temp$FromGridtoDemand[i]-temp$FromREStoDemand[i]-temp$FromStoragetoDemand[i]
            }
          }#the rest of the charging zones
          for (n in (i+1):length(temp$Year)){ 
            temp$Capacity[n]<-temp$Capacity[n-1]-temp$FromStoragetoDemand[n]+temp$FromREStoStorage[n] 
          }
          
        }else if (max(temp$CostH[(i+1):length(temp$Year)],na.rm = TRUE)>price){
          
          if (temp$Capacity[i]==Pcapacity){
            if (temp$RES[i]>temp$Load[i]){
              temp$FromREStoDemand[i]<-temp$Load[i]+temp$FromREStoDemand[i]
              temp$RES[i]<-temp$RES[i]-temp$Load[i]
              temp$FromREStoGrid[i]<-temp$RES[i]+temp$FromREStoGrid[i]
              temp$RES[i]<-0
              temp$Load[i]<-temp$LoadOr[i]-temp$FromGridtoDemand[i]-temp$FromREStoDemand[i]-temp$FromStoragetoDemand[i]
            }
          }else{
            if (temp$RES[i]>0){
              
              #meletaw poso tha valw xwris na to parakanw
              mexriposo<-Pcapacity-temp$Capacity[i]
              if (temp$RES[i]>=mexriposo){
                vazw<-mexriposo+temp$FromREStoStorage[i]
                newcap<-temp$Capacity[i]+mexriposo
              }else{
                vazw<-temp$RES[i]+temp$FromREStoStorage[i]
                newcap<-temp$Capacity[i]+temp$RES[i]
              }
              cap<-temp$Capacity; cap[i]<-newcap
              for (n in (i+1):length(temp$Year)){ 
                cap[n]<-cap[n-1]-temp$FromStoragetoDemand[n]+temp$FromREStoStorage[n] 
              }
              if (max(temp$Capacity[(i+1):length(temp$Year)]+vazw)>Pcapacity){
                mexriposo<-0
              }
              
              if (temp$RES[i]>=mexriposo){
                temp$FromREStoStorage[i]<-mexriposo+temp$FromREStoStorage[i]
                temp$Capacity[i]<-temp$Capacity[i]+mexriposo
                temp$RES[i]<-temp$RES[i]-mexriposo
                for (n in (i+1):length(temp$Year)){ 
                  temp$Capacity[n]<-temp$Capacity[n-1]-temp$FromStoragetoDemand[n]+temp$FromREStoStorage[n] 
                }
              }else{
                temp$FromREStoStorage[i]<-temp$RES[i]+temp$FromREStoStorage[i]
                temp$Capacity[i]<-temp$Capacity[i]+temp$RES[i]
                temp$RES[i]<-0
                for (n in (i+1):length(temp$Year)){ 
                  temp$Capacity[n]<-temp$Capacity[n-1]-temp$FromStoragetoDemand[n]+temp$FromREStoStorage[n] 
                }
              }
              if (temp$RES[i]>=temp$Load[i]){
                temp$FromREStoDemand[i]<-temp$Load[i]+temp$FromREStoDemand[i]
                temp$RES[i]<-temp$RES[i]-temp$Load[i]
                temp$Load[i]<-0
              }else{
                temp$FromREStoDemand[i]<-temp$RES[i]+temp$FromREStoDemand[i]
                temp$RES[i]<-0
                temp$Load[i]<-temp$LoadOr[i]-temp$FromGridtoDemand[i]-temp$FromREStoDemand[i]-temp$FromStoragetoDemand[i]
              }
              if (temp$RES[i]>0){
                temp$FromREStoGrid[i]<-temp$RES[i]+temp$FromREStoGrid[i]
                temp$RES[i]<-0
              }
            }
            
          }
        }
        
      }
      
      
      
      
      
      
      
      
      for (i in 1:length(temp$Hour)){
        if (temp$RES[i]>0){
          if (temp$Load[i]>0){
            if (temp$RES[i]>temp$Load[i]){
              temp$FromREStoDemand[i]<-temp$FromREStoDemand[i]+temp$Load
              temp$RES[i]<-temp$RES[i]-temp$Load
              temp$Load[i]<-0
              temp$FromREStoGrid[i]<-temp$RES[i]+temp$FromREStoGrid[i]
              temp$RES[i]<-0
            }else{
              temp$FromREStoDemand[i]<-temp$FromREStoDemand[i]+temp$RES[i]
              temp$Load[i]<-temp$Load[i]-temp$RES[i]
              temp$RES[i]<-0
            }
          }else{
            temp$FromREStoGrid[i]<-temp$RES[i]+temp$FromREStoGrid[i]
            temp$RES[i]<-0
          }
        }
      }
      
      new_data2<-rbind(new_data2,temp)
      for (kkk in 2:length(new_data2$Capacity)){
        if (new_data2$Capacity[kkk]<new_data2$Capacity[kkk-1]){
          new_data2$FromStoragetoDemand[kkk]<-new_data2$Capacity[kkk-1]-new_data2$Capacity[kkk]
        }
      }
    }
  }
  
  new_data2$Keepasitis=new_data2$CostH=new_data2$Capacity=new_data2$ChargingZone<-NULL
  new_data2$FromGridtoDemand<-new_data2$Load
  new_data2$RES<-new_data2$RESoriginal  ; new_data2$RESoriginal<-NULL
  new_data2$Storage<-new_data2$FromREStoStorage-new_data2$FromStoragetoDemand
  new_data2$Load<-new_data2$LoadOr ; new_data2$dataLoadOr<-NULL
  new_data2$Grid<-new_data2$Load-new_data2$FromStoragetoDemand-new_data2$FromREStoDemand
  
  if (plott==TRUE){
    MAX<-max(c(new_data2$Load,new_data2$Grid,new_data2$RES,new_data2$Storage))+20  
    MIN<-min(c(new_data2$Load,new_data2$Grid,new_data2$RES,new_data2$Storage))-20  
    plot(new_data2$Load,type="l",ylim=c(MIN,MAX))
    lines(new_data2$Grid,type="l",col="red")
    lines(new_data2$RES,type="l",col="green")
    lines(new_data2$Storage,type="l",col="blue")
  }
  
  ########################## ########################## ########################## 
  ################### End of stage 2 - Charging zones ############################ 
  ########################## ########################## ##########################
  data2<-new_data2
  data2$FromGridtoDemand=data2$FromREStoStorage=data2$FromREStoGrid<-NULL
  data2$FromREStoDemand=data2$FromStoragetoDemand=data2$LoadOr<-NULL
  
  
  return(data2)
}
Kostologish <- function(data1,data2,Enprices){
  
  for (sen in 1:2){
    
    if (sen==1){
      datatemp<-data1
    }else{
      datatemp<-data2
    }
    
    CostCreated<-energycost(datatemp,Enprices)
    test<-CostCreated
    
    howmanymonths<-1 ; test$MY<-test$Year+test$Month/100
    for (i in 1:(length(test$Day)-1)){
      if (test$MY[i]!=test$MY[i+1]) { howmanymonths=howmanymonths+1 }
    }
    
    CompareCosts<-data.frame(matrix(NA,nrow=howmanymonths,ncol=13))
    colnames(CompareCosts)<-c("Year","Month","Peak","PV","CHP","Load","Grid","Storage","F1","F2","F3","TotalCost","FinalCost")
    
    k=1 ; CompareCosts$Year[k]<-test$Year[1] ; CompareCosts$Month[k]<-test$Month[1]
    for (i in 1:(length(test$Day)-1)){
      if (test$MY[i]!=test$MY[i+1]) {
        CompareCosts$Year[k+1]<-test$Year[i+1]
        CompareCosts$Month[k+1]<-test$Month[i+1]
        k=k+1
      }
    }
    
    for (i in 1:length(CompareCosts$Year)){
      sumtemp<-test[(test$Year==CompareCosts$Year[i])&(test$Month==CompareCosts$Month[i]),]
      CompareCosts$PV[i]<-sum(sumtemp$PV) ;    CompareCosts$CHP[i]<-sum(sumtemp$CHP)
      CompareCosts$Load[i]<-sum(sumtemp$Load);     CompareCosts$Grid[i]<-sum(sumtemp$Grid)
      CompareCosts$Storage[i]<-sum(sumtemp$Storage)
      CompareCosts$F1[i]<-sum(sumtemp[sumtemp$Type=="F1",]$Energy_Cost)
      CompareCosts$F2[i]<-sum(sumtemp[sumtemp$Type=="F2",]$Energy_Cost)
      CompareCosts$F3[i]<-sum(sumtemp[sumtemp$Type=="F3",]$Energy_Cost)
      CompareCosts$TotalCost[i]<-sum(CompareCosts$F1[i],CompareCosts$F2[i],CompareCosts$F3[i])
      CompareCosts$Peak[i]<-max(sumtemp$Grid)
      CompareCosts$FinalCost[i]=round((((0.00988004+0.091172)*CompareCosts$Grid[i])+(0.0151*0.0466174797*CompareCosts$Grid[i])+
                                         (2.743633*CompareCosts$Peak[i])+91.647984+(1.04*CompareCosts$TotalCost[i]))*1.22,2)
    }
    
    CompareCostsCreated<-CompareCosts
    
    if (sen==1){
      costsenario1=sum(CompareCostsCreated$FinalCost) 
    }else{
      costsenario2=sum(CompareCostsCreated$FinalCost) 
    }
    
    CompareCosts=CompareCostsCreated<-NULL
    
    
  }
  
  toreturn<-c(costsenario1,costsenario2)
  return(toreturn)
  
}
energycost <- function(data,Energyprices){
  
  #data<-dataoutOR ; Energyprices<-Enprices
  
  t1=7; t2=8; t3=19; t4=23
  data$weekend <-as.numeric(isWeekend(data$datetime)) 
  data$asdate<-as.timeDate(substring(data$datetime, first=1, last=10))
  data$typeofday<-dayOfWeek(data$asdate)
  data$Energy_Cost<-NA; data$Charge<-NA ; data$Type<-NA
  
  for (sm in 1:length(Energyprices[,1])){
    
    pricebuy1=Energyprices[sm,3]/1000 ; pricebuy2=Energyprices[sm,4]/1000 ; pricebuy3=Energyprices[sm,5]/1000 ; pricesell=0.07
    
    ######################
    for (i in 1:length(data$Load)){
      
      if ( (data$Month[i]==Energyprices[sm,2])&((data$Year[i]==Energyprices[sm,1])) ){
        
        if (data$Grid[i]>=0){
          if (data$typeofday[i]=="Sun"){
            data$Energy_Cost[i] <- pricebuy3*data$Grid[i]*(-1)
            data$Charge[i]<-pricebuy3
            data$Type[i]<-"F3"
          }else if (data$typeofday[i]=="Sat"){
            if ((data$Hour[i]>=7)&(data$Hour[i]<23)){
              data$Energy_Cost[i] <- pricebuy2*data$Grid[i]*(-1)
              data$Charge[i]<-pricebuy2
              data$Type[i]<-"F2"
            }else{
              data$Energy_Cost[i] <- pricebuy3*data$Grid[i]*(-1)
              data$Charge[i]<-pricebuy3
              data$Type[i]<-"F3"
            }
          }else{
            if ((data$Hour[i]>=8)&(data$Hour[i]<19)){
              data$Energy_Cost[i] <- pricebuy1*data$Grid[i]*(-1)
              data$Charge[i]<-pricebuy1
              data$Type[i]<-"F1"
            }else if (  (data$Hour[i]==7)|((data$Hour[i]>=19)&(data$Hour[i]<23))){
              data$Energy_Cost[i] <- pricebuy2*data$Grid[i]*(-1)
              data$Charge[i]<-pricebuy2
              data$Type[i]<-"F2"
            }else{
              data$Energy_Cost[i] <- pricebuy3*data$Grid[i]*(-1)
              data$Charge[i]<-pricebuy3
              data$Type[i]<-"F3"
            }
          }
        }else{
          data$Energy_Cost[i] <- pricesell*abs(data$Grid[i])
          data$Charge[i]<-pricesell
          data$Type[i]<-"Sell"
        }
      }
      
      
    }
    
  }
  
  data$weekend <- NULL ; data$asdate <- NULL
  data$typeofday <- NULL 
  
  return(data)
}
simulateData <- function(data){
  
  startingcapacity<-0 #Apo pou ksekinhsa (as ksekinhsoume me 10)
  Pcapacity=50  #estw 50max kai 4 min
  Ncapacity=4
  data2 <- data 
  
  #NA afta pou thelw na ftiaksw egw
  data2$Storage <- 0;    data2$Grid <- 0
  data2$Capacity <-0 ; data2$Capacity[1]=startingcapacity
  data2$Grid[1]=data2$Load[1]-data2$PV[1]+data2$CHP[1]
  
  for (i in 2:length(data2$Load)){
    # Storage - otan travaw apo mpataria
    capacity= data2$Capacity[i-1] #capacity th stigmh i
    renewable <- data2$PV[i]-data2$CHP[i] #res th stigmh i
    
    if ((renewable>data2$Load[i])&(capacity<Pcapacity)) { #Megalhterh paragwgh - Adeia mpataria
      if (  (renewable-data2$Load[i]) >= (Pcapacity-capacity)  ){ #mporw na thn gemisw full
        data2$Storage[i] <- Pcapacity-capacity
        data2$Capacity[i] <- Pcapacity
        data2$Grid[i] <- (-1)*( renewable-data2$Load[i]-(Pcapacity-capacity) )
      }else{                                                     # Th gemizw oso mporw
        data2$Storage[i] <- renewable-data2$Load[i]
        data2$Capacity[i] <- capacity+renewable-data2$Load[i]
        data2$Grid[i] <- 0
      }
    }
    
    if ((renewable>data2$Load[i])&(capacity==Pcapacity)){  #Megalhterh paragwgh - Gemati mpataria
      data2$Storage[i] <- 0
      data2$Capacity[i] <- Pcapacity
      data2$Grid[i] <- (-1)*(renewable-data2$Load[i])
    }
    
    if (renewable<=data2$Load[i]) { #Mikroterh paragwgh
      if ( capacity==Ncapacity  ){ #adeia mpataria
        data2$Storage[i] <- 0
        data2$Capacity[i] <- Ncapacity
        data2$Grid[i] <- data2$Load[i]-renewable
      }else{  
        if (capacity>=(data2$Load[i]-renewable)){ #H mpataria kalhptei
          data2$Storage[i] <- (-1)*(data2$Load[i]-renewable)
          data2$Capacity[i] <- capacity-(data2$Load[i]-renewable)
          data2$Grid[i] <- 0
        }else{                          # H mpataria den kalyptei
          data2$Storage[i] <- (-1)*(capacity-Ncapacity)
          data2$Capacity[i] <- Ncapacity
          data2$Grid[i] <- data2$Load[i]-renewable-(capacity-Ncapacity)
        }
      }
      
    }
    
  }
  return(data2)
}
runningpeaks <- function(data,prices){
  
  #data<-simulated_data_OUT
  
  #Pou eimai panw kai katw apo to peak tou load
  data$canremove<-0
  for (i in 1:length(data$OH)){
    if( data$OH[i]==1 ){ 
      if (data$Load[i]-wherepeak>=0){
        data$canremove[i]=abs(data$Load[i]-wherepeak)
      }
    }
  }
  
  #How many days available?
  energyControl<-data.frame(matrix(NA,ncol=2,nrow=20)) ; colnames(energyControl)<-c("Day","CanRemove")
  data$Year <- as.numeric(substring(data$datetime, first=1, last=4))
  data$Month <- as.numeric(substring(data$datetime, first=6, last=7))
  data$Day <- as.numeric(substring(data$datetime, first=9, last=10))
  data$Hour <- as.numeric(substring(data$datetime, first=12, last=13))
  howmanydays<-1 ; energyControl$Day[1]<-data$Day[1]; k=1
  for (i in 1:(length(data$Day)-1)){
    if (data$Day[i]!=data$Day[i+1]) {
      howmanydays=howmanydays+1
      energyControl$Day[k+1]<-data$Day[i+1]
      k=k+1
    }
  }
  energyControl<-energyControl[1: howmanydays,]
  
  #Energy mix
  for (i in 1:howmanydays){
    temp<-data[data$Day==energyControl$Day[i],]
    energyControl$CanRemove[i]<-sum(temp$canremove)
  }
  
  #Share load within day
  new_data<-NULL
  for (n in 1:howmanydays){
    
    temp<-data[data$Day==energyControl$Day[n],] ; finish=1
    
    ypoloipo<-energyControl$CanRemove[n]
    minmeras=min(temp$Load)
    
    difference<-prices[prices$Month==mean(temp$Month),]$Difference ; symferei=0
    if (difference>=0){ symferei=1} #to F1 einai pio akrivo
    temp$symferei<-0
    if (symferei==1){
      for (ii in 1:length(temp$Hour)){
        if ((temp$Hour[ii]==7)|(temp$Hour[ii]==19)|(temp$Hour[ii]==20)|(temp$Hour[ii]==21)|(temp$Hour[ii]==22)){
          temp$symferei[ii]=1
        }
      }
    }else{
      for (ii in 1:length(temp$Hour)){
        if ((temp$Hour[ii]!=7)|(temp$Hour[ii]!=19)|(temp$Hour[ii]!=20)|(temp$Hour[ii]!=21)|(temp$Hour[ii]!=22)){
          temp$symferei[ii]=1
        }
      }
    }
    
    if ((ypoloipo>0)&(length(temp$Load)>1)) {finish=0}
    reps<-0
    
    while ((finish==0)&(ypoloipo>=0)){
      
      peaktemp<-max(data[(data$Month=temp$Month[1])&(data$Year=temp$Year[1]),]$Grid)
      peakdata<-max(dataRoll[(dataRoll$Month=temp$Month[1])&(dataRoll$Year=temp$Year[1]),]$Grid)
      peakmonth<-max(peaktemp,peakdata,na.rm = TRUE)*0.7
      meiwsh<-5
      
      
      ypoepeksergasia<-temp[(temp$OH==1)&(temp$Load>=minmeras),] ; xwrisepeksergasia<-temp[(temp$OH==0)|(temp$Load<minmeras),]
      ypoepeksergasia$cost<-NA
      for (ki in 1:length(ypoepeksergasia$Grid)){
        if (ypoepeksergasia$symferei[ki]==1){
          ypoepeksergasia$cost[ki]<-ypoepeksergasia$Grid[ki]
        }else{
          ypoepeksergasia$cost[ki]<-ypoepeksergasia$Grid[ki]*(1+abs(difference))
        }
      }
      ypoepeksergasia$Advantage<-1 #to akrivo xwris peak
      for (k in 1:length(ypoepeksergasia$cost)){
        if (ypoepeksergasia$Grid[k]+meiwsh+10>=peakmonth){
          ypoepeksergasia$Advantage[k]=0 #to peak
        }else if (ypoepeksergasia$symferei[k]==1){
          ypoepeksergasia$Advantage[k]=2 #to ftino
        }
      }
      lngth<-length(ypoepeksergasia$Grid)
      
      #######################   Senaria    ########################
      if ( (length(ypoepeksergasia[ypoepeksergasia$Advantage==1,]$Grid)==lngth) | (length(ypoepeksergasia[ypoepeksergasia$Advantage==2,]$Grid)==lngth) ){
        #If 1 or 2 do nothinbg
        finish=1
      }else if (length(ypoepeksergasia[ypoepeksergasia$Advantage==0,]$Grid)==lngth) {
        #If peak share
        max=0 ; theshmax<-NA
        for (i in 1:length(ypoepeksergasia$Advantage)){
          if (ypoepeksergasia$Grid[i]>max){
            max<-ypoepeksergasia$Grid[i] ;        theshmax<-i
          }
        }
        min=100*exp(100) ; theshmin<-NA
        for (i in 1:length(ypoepeksergasia$Advantage)){
          if (ypoepeksergasia$Grid[i]<min){
            min<-ypoepeksergasia$Grid[i];       theshmin<-i
          }
        }
        ypoepeksergasia$Grid[theshmin]=min+meiwsh
        ypoepeksergasia$Grid[theshmax]=max-meiwsh
        ypoloipo<-ypoloipo-meiwsh
        ypoepeksergasia$cost=ypoepeksergasia$Advantage<-NULL
        temp<-rbind(xwrisepeksergasia,ypoepeksergasia)
        temp <- temp[order(temp$Hour),]
        temp$Load <- temp$PV-temp$CHP-temp$Storage+temp$Grid
        reps<-reps+1
      }else if ((length(ypoepeksergasia[ypoepeksergasia$Advantage==2,]$Grid)>0)&(length(ypoepeksergasia[ypoepeksergasia$Advantage==0,]$Grid)>0)){
        max=0 ; theshmax<-NA
        for (i in 1:length(ypoepeksergasia$Advantage)){
          if ((ypoepeksergasia$Advantage[i]==0)&(ypoepeksergasia$Grid[i]>max)){
            max<-ypoepeksergasia$Grid[i] ;        theshmax<-i
          }
        }
        min=100*exp(100) ; theshmin<-NA
        for (i in 1:length(ypoepeksergasia$Advantage)){
          if ((ypoepeksergasia$Advantage[i]==2)&(ypoepeksergasia$Grid[i]<min)){
            min<-ypoepeksergasia$Grid[i];       theshmin<-i
          }
        }
        ypoepeksergasia$Grid[theshmin]=min+meiwsh
        ypoepeksergasia$Grid[theshmax]=max-meiwsh
        ypoloipo<-ypoloipo-meiwsh
        ypoepeksergasia$cost=ypoepeksergasia$Advantage<-NULL
        temp<-rbind(xwrisepeksergasia,ypoepeksergasia)
        temp <- temp[order(temp$Hour),]
        temp$Load <- temp$PV-temp$CHP-temp$Storage+temp$Grid
        reps<-reps+1
      }else if ((length(ypoepeksergasia[ypoepeksergasia$Advantage==2,]$Grid)>0)&(length(ypoepeksergasia[ypoepeksergasia$Advantage==1,]$Grid)>0)){
        max=0 ; theshmax<-NA
        for (i in 1:length(ypoepeksergasia$Advantage)){
          if ((ypoepeksergasia$Advantage[i]==1)&(ypoepeksergasia$Grid[i]>max)){
            max<-ypoepeksergasia$Grid[i] ;        theshmax<-i
          }
        }
        min=100*exp(100) ; theshmin<-NA
        for (i in 1:length(ypoepeksergasia$Advantage)){
          if ((ypoepeksergasia$Advantage[i]==2)&(ypoepeksergasia$Grid[i]<min)){
            min<-ypoepeksergasia$Grid[i];       theshmin<-i
          }
        }
        ypoepeksergasia$Grid[theshmin]=min+meiwsh
        ypoepeksergasia$Grid[theshmax]=max-meiwsh
        ypoloipo<-ypoloipo-meiwsh
        ypoepeksergasia$cost=ypoepeksergasia$Advantage<-NULL
        temp<-rbind(xwrisepeksergasia,ypoepeksergasia)
        temp <- temp[order(temp$Hour),]
        temp$Load <- temp$PV-temp$CHP-temp$Storage+temp$Grid
        reps<-reps+1
      }else {
        max=0 ; theshmax<-NA
        for (i in 1:length(ypoepeksergasia$Advantage)){
          if ((ypoepeksergasia$Advantage[i]==0)&(ypoepeksergasia$Grid[i]>max)){
            max<-ypoepeksergasia$Grid[i] ;        theshmax<-i
          }
        }
        min=100*exp(100) ; theshmin<-NA
        for (i in 1:length(ypoepeksergasia$Advantage)){
          if ((ypoepeksergasia$Advantage[i]!=0)&(ypoepeksergasia$Grid[i]<min)){
            min<-ypoepeksergasia$Grid[i];       theshmin<-i
          }
        }
        ypoepeksergasia$Grid[theshmin]=min+meiwsh
        ypoepeksergasia$Grid[theshmax]=max-meiwsh
        ypoloipo<-ypoloipo-meiwsh
        ypoepeksergasia$cost=ypoepeksergasia$Advantage<-NULL
        temp<-rbind(xwrisepeksergasia,ypoepeksergasia)
        temp <- temp[order(temp$Hour),]
        temp$Load <- temp$PV-temp$CHP-temp$Storage+temp$Grid
        reps<-reps+1
      }
      
    }#END OF CHANGES
    
    #     minlim=min(temp$Load,data[data$Day==energyControl$Day[n],]$Load)
    #     maxlim=max(temp$Load,data[data$Day==energyControl$Day[n],]$Load)
    #     ylimm=c(minlim,maxlim)
    #     plot(temp$Load,type="l",col="red",ylim=ylimm)
    #     lines(data[data$Day==energyControl$Day[n],]$Load,type="l")
    
    new_data[length(new_data)+1] = list(temp)
    
  }#end of day
  
  dataN<-new_data[[length(new_data)]]
  for (i in 1:(length(new_data)-1)){
    dataN<-rbind(dataN,new_data[[i]])
  }
  dataN <- dataN[order(dataN$Year, dataN$Month, dataN$Day, dataN$Hour),]
  
  dataN$canadd=dataN$canremove<-NULL
  
  #   minlim=min(data$Grid,dataN$Grid)
  #   maxlim=max(data$Grid,dataN$Grid)
  #   ylimm=c(minlim,maxlim)
  #   plot(data$Grid,type="l",ylim=ylimm)
  #   lines(dataN$Grid,type="l",col="red")
  
  return (dataN)
}
thermalload<-function(dataforthermal){
  
  for (i in 1:nrow(dataforthermal)){
    if (dataforthermal$MicroA[i]>125){ dataforthermal$MicroA[i]<-125 }
    if (dataforthermal$MicroB[i]>125){ dataforthermal$MicroB[i]<-125 }
    if (dataforthermal$C65[i]<(-35)){ dataforthermal$C65[i]<-35*(-1) }
    if (dataforthermal$C65B[i]<(-60)){ dataforthermal$C65B[i]<-60*(-1) }
    if (dataforthermal$Boilers[i]>1000){ dataforthermal$Boilers[i]<-1000 }
  }
  dataforthermal$Thermal<-dataforthermal$MicroA+dataforthermal$MicroB+dataforthermal$Boilers 
  SI<-c(0.006785155,0.009322496,0.007874386,0.009492284,0.006811254,0.658987747,2.894923619,2.369026382,3.034597685,1.882949597,1.630519503,
        1.525773786,1.464136184,1.366401278,1.253708234,1.139320354,1.022734150,0.900928895,0.781745590,0.663129355,0.542659898,0.422682164,
        0.277235337,0.128254665)/24
  #Calculate daily thermal demand
  DailyThermal<-dataforthermal ; DailyThermal$datetime<-NULL ; DailyThermal<-aggregate(DailyThermal,by=list(DailyThermal$Day,DailyThermal$Month), "sum")
  thermaldays<-c() ; for (i in 1:nrow(DailyThermal)){   thermaldays<-c(thermaldays,SI*DailyThermal$Thermal[i]) }
  DailyThermal$TemperatureC<-DailyThermal$TemperatureC/24
  maximum<-boxplot(DailyThermal$Thermal,plot=FALSE)$stats[5] ; DailyThermal<-DailyThermal[DailyThermal$Thermal<maximum,]
  if (length(DailyThermal$Thermal>0)){
    modelCHP<-auto.arima(DailyThermal$Thermal, seasonal=FALSE,xreg=DailyThermal$TemperatureC)
    #Calculate demand for next week
    regs<-weatherOut; regs$Day<-as.numeric(substring(regs$datetime, first=9, last=10)) ; regs$datetime<-NULL
    regs<-aggregate(regs,by=list(regs$Day), "mean")$TemperatureC
    CHPthermal<-forecast(modelCHP,h=7,xreg=regs)$mean
  }else{
    CHPthermal<-c(1:7)-c(1:7)
  }
  
  thermaldaysout<-c() ; for (i in 1:length(CHPthermal)){   thermaldaysout<-c(thermaldaysout,SI*CHPthermal[i]) }
  
  #If then rules for thermal
  CHPArray<-data.frame(matrix(0,ncol=8,nrow=168)) ; colnames(CHPArray)<-c("ThermalDemand","ElectricityDemand","Aon","Bon","ThA","ThB","ElA","ElB")
  CHPArray$ThermalDemand<-thermaldaysout ; CHPArray$ElectricityDemand<-forecastLoad$Fitted
  
  for (i in 1:168){
    if ((CHPArray$ThermalDemand[i]>125)&(CHPArray$ElectricityDemand[i]>60)){ CHPArray$Aon[i]<-1 }
  }
  for (i in 1:155){
    if ((CHPArray$Aon[i]==1)&(sum(CHPArray$Aon[i:(i+13)])==14)){  CHPArray$ThA[i]<-125  }
  }
  k<-0 ; found<-FALSE
  for (i in 1:168){
    if ((CHPArray$ThA[i]!=125)&(found==TRUE)){
      k<-k+1
      CHPArray$ThA[i]<-125-7.224*k
      if (CHPArray$ThA[i]<0){ CHPArray$ThA[i]<-0 }
    }else{
      k<-0 ; if (CHPArray$ThA[i]==125){found<-TRUE}
    }
  }
  CHPArray$ThermalDemand<-CHPArray$ThermalDemand-CHPArray$ThA
  CHPArray$ElA<-0.48*CHPArray$ThA
  CHPArray$ElectricityDemand<-CHPArray$ElectricityDemand-CHPArray$ElA
  ###### for turbine B
  for (i in 1:168){
    if ((CHPArray$ThermalDemand[i]>125)&(CHPArray$ElectricityDemand[i]>35)){ CHPArray$Bon[i]<-1 }
  }
  for (i in 1:155){
    if ((CHPArray$Bon[i]==1)&(sum(CHPArray$Aon[i:(i+13)])==14)){  CHPArray$ThB[i]<-125  }
  }
  k<-0 ; found<-FALSE
  for (i in 1:168){
    if ((CHPArray$ThB[i]!=125)&(found==TRUE)){
      k<-k+1
      CHPArray$ThB[i]<-125-8.3*k
      if (CHPArray$ThB[i]<0){ CHPArray$ThB[i]<-0 }
    }else{
      k<-0 ; if (CHPArray$ThB[i]==125){found<-TRUE}
    }
  }
  CHPArray$ThermalDemand<-CHPArray$ThermalDemand-CHPArray$ThB
  CHPArray$ElB<-0.28*CHPArray$ThB
  CHPArray$ElectricityDemand<-CHPArray$ElectricityDemand-CHPArray$ElB
  for (i in 1:168){
    if (CHPArray$ThA[i]!=125){ CHPArray$Aon[i]<-0 }
    if (CHPArray$ThB[i]!=125){ CHPArray$Bon[i]<-0 }
  }
  CHPArray$ThermalDemand<-thermaldaysout
  for (i in 1:168){
    if (thermaldaysout[i]-CHPArray$ThA[i]-CHPArray$ThB[i]>30){
      CHPArray$Boilers[i]<-thermaldaysout[i]-CHPArray$ThA[i]-CHPArray$ThB[i]
    }else{
      CHPArray$Boilers[i]<-0
    }
  }
  for (i in 1:nrow(CHPArray)){
    if (CHPArray$Boilers[i]<0){ CHPArray$Boilers[i]<-0 }
    if (CHPArray$ThA[i]<0){ CHPArray$ThA[i]<-0 ; CHPArray$ThA[i]<-0 ; CHPArray$ElA[i]<-0}
    if (CHPArray$ThB[i]<0){ CHPArray$ThB[i]<-0 ; CHPArray$ThB[i]<-0 ; CHPArray$ElB[i]<-0 }
    if (CHPArray$ThermalDemand[i]<0){ CHPArray$ThermalDemand[i]<-0 }
  }
  return(CHPArray)
}


loadshift=FALSE
OHstart=7 ; OHend=19
dataORIGINAL=dataNEW<-NULL;forweeks<-1;cut=17
# #################  Get the weather data files  ################################

dataALL$Year <- as.numeric(substring(dataALL$Timestamp, first=1, last=4))
dataALL$Month <- as.numeric(substring(dataALL$Timestamp, first=6, last=7))
dataALL$Day <- as.numeric(substring(dataALL$Timestamp, first=9, last=10))
dataALL$Hour <- as.numeric(substring(dataALL$Timestamp, first=12, last=13))
Energyprices<-Energyprices_R
replacenames<-function(dataALL){
  for (i in 1:ncol(dataALL)){
    if (colnames(dataALL)[i]=="savona_weatherforecast_air_temperature_forecast"){ colnames(dataALL)[i]<-"TemperatureC" }
    if (colnames(dataALL)[i]=="savona_weatherforecast_relative_humidity_forecast"){ colnames(dataALL)[i]<-"Humidity" }
    if (colnames(dataALL)[i]=="savona_weatherforecast_air_pressure_forecast"){ colnames(dataALL)[i]<-"PressurehPa" }
    if (colnames(dataALL)[i]=="savona_weatherforecast_irradiation_forecast"){ colnames(dataALL)[i]<-"SolarRadiationWatts.m.2" }
    if (colnames(dataALL)[i]=="savona_campus_bms_electricalpower_pv"){ colnames(dataALL)[i]<-"PV" }
    if (colnames(dataALL)[i]=="savona_campus_bms_electricalpower_storage"){ colnames(dataALL)[i]<-"Storage" }
    if (colnames(dataALL)[i]=="savona_campus_bms_electricalpower_network"){ colnames(dataALL)[i]<-"Grid" }
    if (colnames(dataALL)[i]=="savona_campus_bms_electricalpower_grid_chp"){ colnames(dataALL)[i]<-"C65B" }
    if (colnames(dataALL)[i]=="savona_campus_bms_electricalpower_dualmode_chp"){ colnames(dataALL)[i]<-"C65" }
    if (colnames(dataALL)[i]=="Timestamp"){ colnames(dataALL)[i]<-"datetime" }
  }
  return(dataALL)
}
dataALL<-replacenames(dataALL)

for (i in 1:nrow(dataALL)){
if (dataALL$C65[i]>0) { dataALL$C65[i] <- 0 }
if (dataALL$C65B[i]>0) { dataALL$C65B[i] <- 0 }
}
 
if ( mean(dataALL$PV,na.rm = TRUE)<0 ) { dataALL$PV <- (-1)*dataALL$PV }

dataALL$CHP<-dataALL$C65+dataALL$C65B
dataALL$Load <- dataALL$PV-dataALL$CHP-dataALL$Storage+dataALL$Grid
dataALL <- dataALL[order(dataALL$datetime),]
weatherOut<-replacenames(weatherOut)
weatherOut <- weatherOut[order(weatherOut$datetime),]
dataforthermal<-dataALL ; dataALL$MicroA=dataALL$MicroB=dataALL$Boilers<-NULL
dataALL$C65=dataALL$C65B<-NULL

print("OKData")

##############  Kai afto einai to sygrisimo - simulate  #############################################
DataReal<-simulateData(dataALL)  # Apo edw kai pera yparxei mono afto
dataALL=data=energy_data=weather<-NULL
DataReal$Date<-NULL
#Afto exw otan ksekinaw kai tha to ftiaksw stadiaka gemizontas me tis epiloges mou
dataRoll<-DataReal ; TotalSuggestions<-c() 
#################  Get the data for the rolling origin chosen  ################################
data<-dataRoll
weatherOut$WindDirectionDegrees=weatherOut$DewpointC=weatherOut$WindSpeedKMH=weatherOut$WindDirectionDegrees<-NULL
weatherOut$Year=weatherOut$Month=weatherOut$Hour=weatherOut$weatherOut$Grid=weatherOut$C65B<-NULL  
weatherOut$PV=weatherOut$Storage=weatherOut$C65=weatherOut$Grid<-NULL
print("OKRoll")
################### Forecast PV, CHP and Load #########################################
forecastPV<-fittingPV(data,weatherOut)[[2]] #Forecast PV production
print("OKPV")
forecastLoad<-fittingLoad(data,weatherOut)[[2]] #Forecast Energy Consumption
print("OKLoad")
weatherOut$Year <- as.numeric(substring(weatherOut$datetime, first=1, last=4))
weatherOut$Month <- as.numeric(substring(weatherOut$datetime, first=6, last=7))
weatherOut$Hour <- as.numeric(substring(weatherOut$datetime, first=12, last=13))
input<-ts(data$CHP,frequency=168) ; SICHP<-decompose(input,type = "multiplicative")
forecastC<- as.numeric(ses(input/SICHP$seasonal,h=168)$mean*SICHP$seasonal[(length(input)-167):length(input)])
#################################################################################################
print("OKPrethermal")
ThermalTable<-thermalload(dataforthermal)
forecastC<-(ThermalTable$ElA+ThermalTable$ElB)*(-1)
print("OKThermal")
###############################    Energy prices  - forecasts #################################################
EnergypricesOR<-Energyprices
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

lengthEnergyprices<-length(Energyprices$Year)+length(prices$Year)
Enprices<-EnergypricesOR
if (length(prices$Year)>0){
  Enprices<-Enprices[1:lengthEnergyprices,] 
  row.names(Enprices)<-c(1:nrow(Enprices)) ; 
  for (checkpr in 1:length(prices$Year)){
    Enprices[length(Energyprices$Year)+checkpr,5]<-Enprices[length(Energyprices$Year)+checkpr-1,5]
    Enprices[length(Energyprices$Year)+checkpr,3]<-Enprices[length(Energyprices$Year)+checkpr-1,3]
    Enprices[length(Energyprices$Year)+checkpr,4]<-Enprices[length(Energyprices$Year)+checkpr,3]-prices[checkpr,3]
    if (Enprices[length(Energyprices$Year)+checkpr-1,2]==12){
      Enprices[length(Energyprices$Year)+checkpr,2]<-1
    }else{
      Enprices[length(Energyprices$Year)+checkpr,2]<-Enprices[length(Energyprices$Year)+checkpr-1,2]+1
    }
    if (Enprices[length(Energyprices$Year)+checkpr,2]==1){
      Enprices[length(Energyprices$Year)+checkpr,1]<-Enprices[length(Energyprices$Year)+checkpr-1,1]+1
    }else{
      Enprices[length(Energyprices$Year)+checkpr,1]<-Enprices[length(Energyprices$Year)+checkpr-1,1]
    }
  } 
}

####################################################################################################

###############  This is what happens to the in and outsample data    ########################
dataOUT<-weatherOut ; dataOUT$Load<-forecastLoad$Fitted ;   dataOUT$PV<-forecastPV$Fitted ;   dataOUT$CHP <- forecastC 
dataOUT$Day <- as.numeric(substring(weatherOut$datetime, first=9, last=10)) ; dataOUT$Grid=dataOUT$Storage=dataOUT$Capacity<-0
data<-dataOUT
dataInOut<-rbind(dataRoll,dataOUT) ; dataInOut <- dataInOut[order(dataInOut$datetime),]
dataSimulated<-simulateData(dataInOut)
simulated_data_IN<-dataSimulated[1:length(dataRoll$Load),]
simulated_data_OUT<-dataSimulated[(length(dataRoll$Load)+1):(length(dataRoll$Load)+168),]
####################################################################################################

#################################################  Optimize ##############################################
################ Here i find which is the peak load according to the cut chosen  #####################
simulated_data_IN$weekend <-as.numeric(isWeekend(simulated_data_IN$datetime)) ;simulated_data_IN$OH<-0
for (i in 1:length(simulated_data_IN$OH)){
  if( (simulated_data_IN$Hour[i]>=OHstart)&(simulated_data_IN$Hour[i]<=OHend)&(simulated_data_IN$weekend[i]==0) ){ simulated_data_IN$OH[i]=1 }
  
  if( (simulated_data_IN$Month[i]==1)&(simulated_data_IN$Day[i]==1) ){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==1)&(simulated_data_IN$Day[i]==6)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==4)&(simulated_data_IN$Day[i]==25)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==5)&(simulated_data_IN$Day[i]==1)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==6)&(simulated_data_IN$Day[i]==2)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==8)&(simulated_data_IN$Day[i]==15)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==11)&(simulated_data_IN$Day[i]==1)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==12)&(simulated_data_IN$Day[i]==8)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==12)&(simulated_data_IN$Day[i]==25)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==12)&(simulated_data_IN$Day[i]==26)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==12)&(simulated_data_IN$Day[i]==31)){ simulated_data_IN$OH[i]=0 }
  
  if( (simulated_data_IN$Year[i]==2014)&(simulated_data_IN$Month[i]==4)&(simulated_data_IN$Day[i]==3)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Year[i]==2014)&(simulated_data_IN$Month[i]==4)&(simulated_data_IN$Day[i]==5)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Year[i]==2014)&(simulated_data_IN$Month[i]==4)&(simulated_data_IN$Day[i]==6)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Year[i]==2015)&(simulated_data_IN$Month[i]==4)&(simulated_data_IN$Day[i]==20)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Year[i]==2015)&(simulated_data_IN$Month[i]==4)&(simulated_data_IN$Day[i]==21)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Year[i]==2015)&(simulated_data_IN$Month[i]==4)&(simulated_data_IN$Day[i]==25)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Year[i]==2016)&(simulated_data_IN$Month[i]==3)&(simulated_data_IN$Day[i]==25)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Year[i]==2016)&(simulated_data_IN$Month[i]==3)&(simulated_data_IN$Day[i]==27)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Year[i]==2016)&(simulated_data_IN$Month[i]==3)&(simulated_data_IN$Day[i]==28)){ simulated_data_IN$OH[i]=0 }
}
wherepeak<-as.numeric(quantile(simulated_data_IN$Load, probs = seq(0, 1, 0.05))[cut]) # 80% to peak sta working hours
##################################################################################################
simulated_data_OUT$weekend <-as.numeric(isWeekend(simulated_data_OUT$datetime)); simulated_data_OUT$OH<-0
simulated_data_OUT$Day<-as.numeric(substring(simulated_data_OUT$datetime, first=9, last=10))
for (i in 1:length(simulated_data_OUT$OH)){
  if( (simulated_data_OUT$Hour[i]>=OHstart)&(simulated_data_OUT$Hour[i]<=OHend)&(simulated_data_OUT$weekend[i]==0) ){ simulated_data_OUT$OH[i]=1 }
  
  if( (simulated_data_OUT$Month[i]==1)&(simulated_data_OUT$Day[i]==1) ){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==1)&(simulated_data_OUT$Day[i]==6)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==4)&(simulated_data_OUT$Day[i]==25)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==5)&(simulated_data_OUT$Day[i]==1)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==6)&(simulated_data_OUT$Day[i]==2)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==8)&(simulated_data_OUT$Day[i]==15)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==11)&(simulated_data_OUT$Day[i]==1)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==12)&(simulated_data_OUT$Day[i]==8)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==12)&(simulated_data_OUT$Day[i]==25)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==12)&(simulated_data_OUT$Day[i]==26)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==12)&(simulated_data_OUT$Day[i]==31)){ simulated_data_OUT$OH[i]=0 }
  
  if( (simulated_data_OUT$Year[i]==2014)&(simulated_data_OUT$Month[i]==4)&(simulated_data_OUT$Day[i]==3)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Year[i]==2014)&(simulated_data_OUT$Month[i]==4)&(simulated_data_OUT$Day[i]==5)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Year[i]==2014)&(simulated_data_OUT$Month[i]==4)&(simulated_data_OUT$Day[i]==6)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Year[i]==2015)&(simulated_data_OUT$Month[i]==4)&(simulated_data_OUT$Day[i]==20)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Year[i]==2015)&(simulated_data_OUT$Month[i]==4)&(simulated_data_OUT$Day[i]==21)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Year[i]==2015)&(simulated_data_OUT$Month[i]==4)&(simulated_data_OUT$Day[i]==25)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Year[i]==2016)&(simulated_data_OUT$Month[i]==3)&(simulated_data_OUT$Day[i]==25)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Year[i]==2016)&(simulated_data_OUT$Month[i]==3)&(simulated_data_OUT$Day[i]==27)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Year[i]==2016)&(simulated_data_OUT$Month[i]==3)&(simulated_data_OUT$Day[i]==28)){ simulated_data_OUT$OH[i]=0 }
}

if (loadshift==TRUE){
  optimizedOut<-runningpeaks(simulated_data_OUT,prices) #senario 1:to optimized Load vasei tou ti eprepe na kanw (forecast) peak
}else{
  optimizedOut<-simulated_data_OUT
}

optimizedOut <- optimizedOut[order(optimizedOut$datetime),]
SuggestionsHmerwn <- simulated_data_OUT$Load- optimizedOut$Load
TotalSuggestions<-c(TotalSuggestions,SuggestionsHmerwn)

FinalSuggestions<-optimizedOut
FinalSuggestions$FinalSuggestions<-TotalSuggestions
FinalSuggestions$RES<-FinalSuggestions$PV-FinalSuggestions$CHP

FinalSuggestions$Storage=FinalSuggestions$TemperatureC=FinalSuggestions$Humidity<-NULL
FinalSuggestions$PressurehPa=FinalSuggestions$WindDirectionDegrees=FinalSuggestions$SolarRadiationWatts.m.2<-NULL
FinalSuggestions$DewpointC=FinalSuggestions$WindSpeedKMH=FinalSuggestions$Year=FinalSuggestions$Day<-NULL
FinalSuggestions$Month=FinalSuggestions$Hour=FinalSuggestions$Capacity=FinalSuggestions$weekend<-NULL
FinalSuggestions$OH=FinalSuggestions$symferei<-NULL
FinalSuggestions$PV=FinalSuggestions$CHP<-NULL

dataOptimized<-heuristics(FinalSuggestions,dataOUT,DataReal,plott=FALSE)  # Ti kanw me ta heuristics
dataOptimized$FinalSuggestions=dataOptimized$Year=dataOptimized$Month<-NULL
dataOptimized$Day=dataOptimized$Hour<-NULL

#Be sure we use energy to keep batteries alive
k<-runif(168, min=1.25, max=2.25)
dataOptimized$Storage<-dataOptimized$Storage+k
dataOptimized$Grid<-dataOptimized$Grid+k
dataOptimized$Load<-dataOptimized$Load+k


ThermalTable$datetime<-dataOptimized$datetime
#return FinalSuggestions-dataOptimized-ThermalTable