RapidMiner Prediction Models and Action Plans
=======

Each of the .R files includes a script which can be used to calculate one of the Action Plans (APs) or Prediction Models (PMs) developed within the OPTIMUS project for implementing the DSS to the following pilots: Sant Cugat - Savona - Zaanstad.

The scripts refer only to the part of the Action Plans and Prediction Models developed under R and applied using a RapidMiner server. 

In order to fully apply the Action Plans and Prediction Models, one must first create and define the data streams required as input for running the scripts. This is done through RapidMiner.

The name of the script provides a brief explanantion of its content as follows:

Creating_Prediction_model_xxxxx.R
This is a script for estimating the parameters of the forecasting model used for prediction variable xxxxx.

Forecasting_Prediction_model_xxxxxx.R
This is a script for predicting variable xxxxxxxx using the model estimated through Creating_Prediction_model_xxxxx.R.

ActionPlan_xxxxxxxxxxx.R
This is a script for implementing Action Plan xxxxxxxxxxx

Based on the folder placed, each PM or AP refers to a different pilot

License
=======

The scripts provided were built under

RapidMiner 5.3.015
R 3.1

The R packages used for developing are the following

MASS 7.3-45
leaps 2.9
timeDate 3012.100
forecast 7.1
