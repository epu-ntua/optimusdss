Optimus
=======

The Semantic Framework of the Optimus DSS is a Pub/Sub system based on Ztreamy framework (https://github.com/jfisteus/ztreamy). Currently it is using the version 0.2 of Ztreamy. It is composed of two files:

## server.py

This script instantiates a Ztreamy (https://github.com/jfisteus/ztreamy) server which listening some streams. The streams are setup in the configuration file (ztreamy_config.cfg)

## semanticsub.py

The SemanticSub is a script that adds contextual data to the RDF triples sent by the data capturing modules. In terms of Ztreamy, this script is a subscriber which read data from the streams which are configured in the configuration file (semanticsub_config.cfg).

The main contextual data added are the type of sensor are properties observed. The contextual triples are generated according to the OPTIMUS ontology (under development). For each stream the following parameters have to be configured:

 - **stream**: Name of the stream. (e.g. *santcugat_townhall_pv_irradiation*)
 - **owl_sensingdevice_class**: Class name of the sensor. (e.g. *optimus:SunnyPortal_SolarRadiation*)
 - **owl_sensingdevice_uri**: URI for identifying the sensor triples. (e.g. *sunnyportal_solar_radiation*)
 - **owl_observation_uri**: URI for identifying the observation triples. (e.g. *sunnyportal_solar_radiation*)
 - **owl_featureofinterest_uri**: URI for identifying the feature of interest triples. (e.g. *solar_irradiation*)
 - **owl_featureofinterest_class**: Class name of the Feature of Interest. It is usually the name of the observed property with 'Feature' string concatenated at the end.(e.g. *optimus:Solar_IrradiationFeature*)
 - **owl_observedproperty_uri**: URI for identifying the property observed triples. (e.g. *solar_irradiation*)
 - **owl_observedproperty_class**: Class name of the property observed. (e.g. *semanco:Solar_Irradiation*)
 - **owl_sensoroutput_class**: Class name of the Sensor Output. It is usually the name of the observed property with 'SensorOutput' string concatenated at the end. (e.g. *optimus:Solar_IrradiationSensorOutput*)
