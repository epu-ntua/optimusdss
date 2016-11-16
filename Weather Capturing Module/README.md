Weather Capturing Module
=======

General Information
_________

The weather forecasting module has been designed to provide meteorological information to the DSS based both on weather forecasts and on locally measured data. Therefore, it will actually be ready to receive two sets of information, relying on input data coming from:

* an external weather forecasting service, which is the main input of the module;
* the locally installed weather parameters transducers (i.e. anemometers, natural radiation sensor, thermometers, atmospheric pressure sensors, humidity sensors etc.), where available.

The forecast parameters are:

* Temperature
* Humidity
* Wind speed
* Wind direction
* Pressure
* Total solar radiation (W/m2 on horizontal surface)
* Cloud cover
* Expected conditions (sunny, cloudy, rainy, etc.)
* Expected rain/snowfall (mm/cm)
* Forecast reliability self-assessment (if available by the service)

### Input

Input data for the forecasts will be a .CSV file received by the module and further elaborated as described above.
A seven days weather forecast will be provided by the defined weather center. The timestep of the provided data will be 1 hour.
The CSV file should be arranged by the provider as follows:

|Timestamp day | Timestamp hour | Variable 1 (unit1) | ... | Variable N (unit n)|
|-----------|-----------|-----------|-----------|-----------|
|DD/MM/YYYY | HH:MM | Xxx,xx | ... | Xxx,xx |
|DD/MM/YYYY | HH+1:MM | Xxx,xx | ... | Xxx,xx |
|DD/MM/YYYY | HH+2:MM | Xxx,xx | ... | Xxx,xx |
|... | ... | ... | ... | ... |

The CSV file will be updated daily by the provider before 9.00AM in the morning.
The input data for the measures will come from the distributed sensors and, as anticipated, will either be retrieved by the DSS and be sent to the module or directly connected to the module itself. 
At this stage, it is assumed that they will be arranged locally and sent to the data capturing module as the CSV from the forecast, but with one file sent periodically with only one row of data:

|Timestamp day | Timestamp hour | Variable 1 (unit1) | ... | Variable N (unit n)|
|-----------|-----------|-----------|-----------|-----------|
|DD/MM/YYYY | HH:MM | Xxx,xx | ... | Xxx,xx |

### Output

Output of the process will be a data stream containing two different sets of information:
* Once per day a complete 7-day forecast;
* A set of triplets (forecast – measurement – difference) for every event of measurement that might occur.

Installation
------
For the installation and correct operation of the weather module, it is necessary to install on a recipient server two main parts:

* One contains the file to generate the data streams (a java script named Optimus_DataCapturingModules.jar)
* One folder contains the source codes.

The two items shall be installed in different recipient folders in the server (in our case C:\DSS\dapp\bin and C:\DSS\dapp\src)
Moreover, for any target city, specific repository folders shall be created on the server, where the forecasts are delivered by the selected provider.
At the end of the operations, it is necessary to carry out a thorough check of the consistency of the several folders retrieved by the module, to make sure that the script is updated with the real names of the target folders.
In case of any adjustments to the codes, to make the new version of the module operational, it is necessary to overwrite/substitute the file on the server and to reboot the server.



















