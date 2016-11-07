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

data$Year <- as.numeric(substring(data$Timestamp, first=1, last=4))
data$Month <- as.numeric(substring(data$Timestamp, first=6, last=7))
data$Day <- as.numeric(substring(data$Timestamp, first=9, last=10))
data$Hour<-as.numeric(substring(data$Timestamp, first=12, last=13))
data <- data[order(data$Year, data$Month, data$Day, data$Hour),]

for (i in 1:nrow(data)){
  if (data$pv_power[i]<0){
    data$pv_power[i]<-0
  }
}

#start end of regression
list25<-c()
for (h in 0:23){
check1<-data[data$Hour==h,]$pv_power
check2<-data[data$Hour==h,]$irradiation
stat1<-boxplot(check1,plot=FALSE)$stat[4]
stat2<-boxplot(check2,plot=FALSE)$stat[4]
if ((stat1>0)&(stat2>0)){ list25<-c(list25,h)}
}
starthour<-min(list25)
endhour<-max(list25)

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
tempweather <- weather[,!names(weather) %in% c("Year","ID","Oldness")]

trainmodel<-function(tempweather){
  Hours<-NULL
  lengthwra<-endhour-starthour+1
  for (h in starthour:endhour){
    Hours[length(Hours)+1]<-list(tempweather[ which(tempweather$Hour==h), !names(tempweather) %in% c("Hour")])
    
  }
  # For the rest is equal to zero.....
  ####################################################################################################################################################
  #Calculate models
  Totalmodels<-NULL
  includeInt = FALSE
  for (i in 1:lengthwra){
    model<-regsubsets(pv_power ~ .  ,intercept=includeInt ,data=Hours[[i]])
    summodel<-summary(model)
    Totalmodels[length(Totalmodels)+1]<-list(regsubsets(pv_power ~ . ,intercept=includeInt ,data=Hours[[i]],nvmax=which.min(summodel$bic)))
  }
  #Estimate forecasts
  predictions<-NULL
  Hours<-NULL
  for (h in starthour:endhour){
    Hours[length(Hours)+1]<-list(tempweather[ which(tempweather$Hour==h), !names(tempweather) %in% c("Hour")])
  }
  
  for (i in 1:length(Totalmodels)){
    
    temp <- as.data.frame(predict.regsubsets(Totalmodels[[i]],newdata=Hours[[i]],1))
    colnames(temp)[1]="Fitted"
    conf<-mean((temp$Fitted-Hours[[i]]$pv_power)^2)^0.5
    temp$lwr<-temp$Fitted-2.326*conf
    temp$upr<-temp$Fitted+2.326*conf
    
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
  
  final.datatable <- final.datatable[,!names(final.datatable) %in% c("Year","Month","Day","Hour","ID","Oldness")]
  
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
calculatemodel<-trainmodel(tempweather)
final.datatable<-calculatemodel[[1]]
Totalmodels<-calculatemodel[[2]]

maintenance<-function(final.datatable){
  ###################################  PV MAINTENANCE ######################################################
  final.datatable$Alarm=c(0)
  for (i in 1:length(final.datatable[,1])){
    if (final.datatable$pv_power[i]<final.datatable$lwr[i]){
      final.datatable$Alarm[i]=1
    }
  }
  
  ##############################################################################################################
  final.datatable$Day <- substring(final.datatable$Timestamp, first=1, last=10)
  
  
  ################################## Evaluation ###########################################
  ####################   Construct Day ID  ########################
  how_many_days=1 #calculate stats per day;How many have i?
  names_days=c(final.datatable$Day[1])
  for (i in 2:length(final.datatable[,1])){
    if (final.datatable$Day[i]!=final.datatable$Day[i-1]){
      how_many_days=how_many_days+1 
      names_days=c(names_days,final.datatable$Day[i])
    }
  }
  history=matrix(0,ncol=1,nrow=how_many_days);history=as.data.frame(history) #constract dataframe
  history$Day=names_days; colnames(history)=c("Alarms","Day")
  ########################################################################
  
  ####################   Construct Week ID  ########################
  history$Week=as.numeric(round(abs(difftime(history$Day[1], history$Day[], unit="week")),4))#calculate weeks
  history=format(history, digits=4)
  for (i in 1:length(history$Week)){
    history$Week[i]=substr(history$Week[i], nchar(history$Week[i], type = "chars")-6, nchar(history$Week[i], type = "chars")-5)
  }
  history$Week=as.numeric(history$Week);history$Alarms=as.numeric(history$Alarms)
  ###################### Fill missing Data  from framework ###########################
  history$PVlwr=history$PVpredicted=history$PVproduction=0
  k=1
  for (i in 1:length(final.datatable[,1])){
    if (final.datatable$Day[i]==history$Day[k]){
      history$Alarms[k]=history$Alarms[k]+final.datatable$Alarm[i]
      history$PVproduction[k]= history$PVproduction[k]+final.datatable$pv_power[i]
      history$PVpredicted[k]= history$PVpredicted[k]+final.datatable$Fitted[i]
      history$PVlwr[k]=history$PVlwr[k]+final.datatable$lwr[i]
    }else{
      k=k+1
      history$Alarms[k]=history$Alarms[k]+final.datatable$Alarm[i]
      history$PVproduction[k]= history$PVproduction[k]+final.datatable$pv_power[i]
      history$PVpredicted[k]=history$PVpredicted[k]+final.datatable$Fitted[i]
      history$PVlwr[k]=history$PVpredicted[k]+final.datatable$lwr[i]
    }
  }
  history$HourlyAlarms=history$Alarm  #H mera posa wriaia eixe sto shnolo ths
  history$Alarms<-NULL
  history$DailyAlarm=0  #H mera san suma paragwghs an htan apo katw
  for (i in 1:length(history$PVlwr)){
    if (history$PVproduction[i]<history$PVlwr[i]){
      history$DailyAlarm[i]=1
    }
  }
  ###########################   End of daily alarms  ##########################
  
  ###################     The same is done per week  #############################
  how_many_weeks=1
  names_weeks=c(history$Week[1])
  for (i in 2:length(history[,1])){
    if (history$Week[i]!=history$Week[i-1]){
      how_many_weeks=how_many_weeks+1 
      names_weeks=c(names_weeks,history$Week[i])
    }
  }
  Weeks=matrix(0,ncol=1,nrow=how_many_weeks);Weeks=as.data.frame(Weeks); Weeks$Week=names_weeks; colnames(Weeks)=c("HourlyAlarms","Week")
  Weeks$PVlwr=Weeks$PVpredicted=Weeks$PVproduction=0
  k=1
  for (i in 1:length(history[,1])){
    if (history$Week[i]==Weeks$Week[k]){
      Weeks$HourlyAlarms[k]=Weeks$HourlyAlarms[k]+history$HourlyAlarms[i]
      Weeks$PVproduction[k]= Weeks$PVproduction[k]+history$PVproduction[i]
      Weeks$PVpredicted[k]= Weeks$PVpredicted[k]+history$PVpredicted[i]
      Weeks$PVlwr[k]=Weeks$PVlwr[k]+history$PVlwr[i]
    }else{
      k=k+1
      Weeks$HourlyAlarms[k]=Weeks$HourlyAlarms[k]+history$HourlyAlarms[i]
      Weeks$PVproduction[k]= Weeks$PVproduction[k]+history$PVproduction[i]
      Weeks$PVpredicted[k]= Weeks$PVpredicted[k]+history$PVpredicted[i]
      Weeks$PVlwr[k]=Weeks$PVlwr[k]+history$PVlwr[i]
    }
  }
  Weeks$LastWeeksAlarms[1]=0
  for (i in 2:length(Weeks[,1])){
    Weeks$LastWeeksAlarms[i]=Weeks$HourlyAlarms[i-1]
  }
  
  Weeks$WeeklyAlarm=0
  for (i in 1:length(Weeks$PVlwr)){
    if (Weeks$PVproduction[i]<Weeks$PVlwr[i]){
      Weeks$WeeklyAlarm[i]=1
    }
  }
  ############################   End of weekly alarms ######################################
  ###########  Constract final matrix   ################
  Risk=merge(history,Weeks,by="Week")
  ordr=c("Day","HourlyAlarms.x","PVproduction.x","PVpredicted.x","PVlwr.x","DailyAlarm",
         "Week","HourlyAlarms.y","PVproduction.y","PVpredicted.y","PVlwr.y","WeeklyAlarm","LastWeeksAlarms")
  
  
  Risk=Risk[ordr]
  Risk$WeeklyAlarm.x<-NULL
  
  names=c("Day","Hourly_Alarms_for_the_Day","PVproduction_for_the_Day","PVpredicted_for_the_Day","PVlwr_for_the_Day","Abnormal_Day",
          "Week","HourlyAlarms_for_the_Week","PVproduction_for_the_Week","PVpredicted_for_the_Week","PVlwr_for_the_Week","Abnormal_Week",
          "LastWeeksHourlyAlarms")
  colnames(Risk)=names
  
  
  Risk$WeeklyAlarms=0
  Risk$WeeklyAlarms[1]=Risk$Hourly_Alarms_for_the_Day[1]
  for (i in 2:length(Risk[,1])){
    if (Risk$Week[i]==Risk$Week[i-1]){
      Risk$WeeklyAlarms[i]=Risk$WeeklyAlarms[i-1]+Risk$Hourly_Alarms_for_the_Day[i]
    }else{
      Risk$WeeklyAlarms[i]=Risk$Hourly_Alarms_for_the_Day[i]
    }
  }
  
  Risk$TotalRisk=(Risk$LastWeeksHourlyAlarms+Risk$WeeklyAlarms)/2
  Risk$WeeklyAlarms<-NULL
  Threshold=max(c(as.numeric(boxplot(Risk$TotalRisk,plot=FALSE)$stats[5]),6))
  Risk$Alert=0
  for (i in 1:length(Risk[,1])){
    if (Risk$TotalRisk[i]>Threshold){
      Risk$Alert[i]=1 
    }
  }
  final.datatable$Day <- NULL; history <- NULL ; Weeks <- NULL
  
  # Estimate risk in the upcomming week #
  upcomming_week_Alarms=matrix(data = 0, nrow = 7, ncol = 3);upcomming_week_Alarms=data.frame(upcomming_week_Alarms)
  colnames(upcomming_week_Alarms)=c("Day","Est_Risk","Alert")
  upcomming_week_Alarms$Day[1:7]=c(1:7)
  upcomming_week_Alarms$Est_Risk[1:7]=Risk$HourlyAlarms_for_the_Week[length(Risk[,1])]
  mean_risk=round(sum(Risk$Hourly_Alarms_for_the_Day[(length(Risk[,1])-6):length(Risk[,1])])/7,0)
  for (i in 1:7){
    if (i==1){ 
      upcomming_week_Alarms$Est_Risk[i]=(upcomming_week_Alarms$Est_Risk[i]+mean_risk)/2
    }else{
      upcomming_week_Alarms$Est_Risk[i]=(upcomming_week_Alarms$Est_Risk[i-1]+mean_risk)/2
    }
    if (upcomming_week_Alarms$Est_Risk[i]>Threshold){
      upcomming_week_Alarms$Alert[i]=1 
    }
  }
  ######################################################
  Risk$Hourly_Alarms_for_the_Day <- NULL; Risk$PVlwr_for_the_Day <- NULL 
  Risk$Abnormal_Day      <- NULL; Risk$Week <- NULL 
  Risk$HourlyAlarms_for_the_Week  <- NULL; Risk$PVproduction_for_the_Week <- NULL 
  Risk$PVpredicted_for_the_Week  <- NULL; Risk$PVlwr_for_the_Week  <- NULL 
  Risk$Abnormal_Week <- NULL; Risk$LastWeeksHourlyAlarms <- NULL; Risk$TotalRisk <- NULL 
  Risk$PVproduction_for_the_Day <- NULL ; Risk$PVpredicted_for_the_Day <- NULL 
  return(list(Risk,upcomming_week_Alarms,final.datatable))
}
maintenanceout<-maintenance(final.datatable)
final.datatable<-maintenanceout[[3]] ; final.datatable$irradiation<-NULL
upcomming_week_Alarms<-maintenanceout[[2]]
Risk<-maintenanceout[[1]]

final.datatable<-final.datatable[,c(1,2,3,5,4,6)]