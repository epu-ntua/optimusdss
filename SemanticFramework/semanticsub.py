from rdfextras import sparql

__author__ = 'asicilia'

from tornado.httpclient import AsyncHTTPClient
from ztreamy import Client
from SPARQLWrapper import SPARQLWrapper, JSON, POST, POSTDIRECTLY, SELECT

import rdflib
from rdflib import URIRef, BNode, Literal
import ConfigParser
import datetime


def event_received(event):
    log("INFO", "Event received from: " + event.source_id)
    #print(event)

    ###############################
    ## Creating the RDF graph with the event body and prefixes
    try:
		g = rdflib.Graph()
		g.parse(data=prefix_block + event.serialize_body(), format='n3')

		###############################
		## Detecting sensor
		qres = g.query(
		"""SELECT DISTINCT ?observation ?sensor ?sensoroutput
		   WHERE {
				?observation <http://purl.oclc.org/NET/ssnx/ssn#observedBy> ?sensor .
				?observation <http://purl.oclc.org/NET/ssnx/ssn#observationResult> ?sensoroutput.
				}""")

		if len(qres) == 1:
			for row in qres:
				sensor = row.sensor
				observation = row.observation
				sensoroutput = row.sensoroutput

				## Looping for the streams obtained from the config file. This can/must be improved!!!
				#for owl_sensingdevice_uri in streams_description:
				#    if sensor.endswith(owl_sensingdevice_uri):
				if event.source_id in streams_description:
					uri = sensor.replace(streams_description[event.source_id]["owl_sensingdevice_uri"], "").replace("sensingdevice/", "")

					triples = "<%s> ssn:featureOfInterest <%sfeatureofinterest/%s> . " % (observation, uri, streams_description[event.source_id]["owl_featureofinterest_uri"])
					triples = "%s <%s> ssn:observedProperty <%sproperty/%s> . " % (triples, observation, uri, streams_description[event.source_id]["owl_observedproperty_uri"])
					triples = "%s <%s> a %s . " % (triples, sensoroutput, streams_description[event.source_id]["owl_sensoroutput_class"])

					triples = "%s <%s> a %s . " % (triples, sensor, streams_description[event.source_id]["owl_sensingdevice_class"])
					triples = "%s <%sfeatureofinterest/%s> a %s . " % (triples, uri, streams_description[event.source_id]["owl_featureofinterest_uri"], streams_description[event.source_id]["owl_featureofinterest_class"])
					triples = "%s <%sfeatureofinterest/%s> ssn:hasProperty <%sproperty/%s> . " % (triples, uri, streams_description[event.source_id]["owl_featureofinterest_uri"], uri, streams_description[event.source_id]["owl_observedproperty_uri"])
					triples = "%s <%sproperty/%s> a %s . " % (triples, uri, streams_description[event.source_id]["owl_observedproperty_uri"], streams_description[event.source_id]["owl_observedproperty_class"])
						
					# the new triples giving context are added to the RDF graph
					g.parse(data=prefix_block + triples, format='n3')
				else :
					log("ERROR", event.source_id + "Not found in the configuration file")

		###############################
		## Serializing the RDF graph in ntriples format for generating the INSERT query
		triples = g.serialize(format='nt')

		query = """INSERT IN GRAPH <""" + input_sparq_endpoint_graph + """>{ """ + triples + """ }"""

		log("INFO", query)

		###############################
		## Sending INSERT query to the triplestore
		sparql = SPARQLWrapper(endpoint=input_sparq_endpoint_url, defaultGraph=input_sparq_endpoint_graph)
		sparql.setQuery(query)
		sparql.setMethod(POST)
		sparql.setReturnFormat(JSON)
		sparql.queryType = SELECT       ## this is needed for Virtuoso servers
		results = sparql.query()
    except :
		log("ERROR", event.serialize_body())
    #print(results)


def error(message, http_error=None):
    if http_error is not None:
        log("ERROR", '[Error] ' + message + ': ' + str(http_error))
    else:
        log("ERROR", '[Error] ' + message)


def log(category, message):
    if input_log_level > 0 and (input_log_level > 1 or category == "ERROR"):
        try:
            fmt = '%Y-%m-%d %H:%M:%S'

            d = datetime.datetime.now()
            d_string = d.strftime(fmt)

            text_file = open("semanticsub_log.txt", "a")

            text_file.write('%s | %s | %s \n' % (d_string, category, message))

            text_file.close()
        except Exception,e:
            print ("Exception...." + str(e))

########################################
## This method read the parameters of the stream
def setupSream (name, streams, streams_description):
    stream_name =               config.get(name, "stream")

    streams_description[stream_name] = dict({
        "owl_observation_uri":          config.get(name, "owl_observation_uri"),
        "owl_sensingdevice_class":      config.get(name, "owl_sensingdevice_class"),
        "owl_sensingdevice_uri":      config.get(name, "owl_sensingdevice_uri"),
        "owl_featureofinterest_uri":    config.get(name, "owl_featureofinterest_uri"),
        "owl_featureofinterest_class":  config.get(name, "owl_featureofinterest_class"),
        "owl_observedproperty_uri":     config.get(name, "owl_observedproperty_uri"),
        "owl_observedproperty_class":   config.get(name, "owl_observedproperty_class"),
        "owl_sensoroutput_class":       config.get(name, "owl_sensoroutput_class")
    })

    log("INFO", " >> " + stream_name)
    log("INFO", streams_description[stream_name])
    streams.append(input_ztreamy_url + stream_name + '/compressed')


###########################################################
# Reading the configuration file
input_log_level = 2
log("INFO", '\n\nReading configuration....')
config = ConfigParser.RawConfigParser()
config.read('semanticsub_config.cfg')

input_ztreamy_url =             config.get('global', "ztreamy_server")
input_sparq_endpoint_url =      config.get('global', "sparq_endpoint_url")
input_sparq_endpoint_graph =    config.get('global', "sparq_endpoint_graph")


###########################################################
# Configuring the prefixes
prefix_block = ""   # string with the prefixes in n3 format
input_prefixes =                config.items('prefixes')\

for (name, value) in input_prefixes:
    prefix_block = prefix_block + """@prefix """+name+""": """ + value + """ . """


log("INFO", "Prefixes")
log("INFO", prefix_block)

###########################################################
# Configuring the streams
epro_number_streams =              config.get('global', "epro_number_streams")
epri_number_streams =              config.get('global', "epri_number_streams")
sm_number_streams =                config.get('global', "sm_number_streams")
wf_number_streams =                config.get('global', "wf_number_streams")
dcd_number_streams =               config.get('global', "dcd_number_streams")

total_streams = int(epro_number_streams)+int(epri_number_streams)+int(sm_number_streams)+int(wf_number_streams)+int(dcd_number_streams)
log("INFO", "Streams description")

streams = []
streams_description = {}

log("INFO", "Reading energy production streams")
for num in range(1, int(epro_number_streams)+1):
    setupSream('stream_EPro'+str(num), streams, streams_description)

log("INFO", "Reading energy prices streams")
for num in range(1, int(epri_number_streams)+1):
    setupSream('stream_EPri'+str(num), streams, streams_description)

log("INFO", "Reading social media streams")
for num in range(1, int(sm_number_streams)+1):
    setupSream('stream_SM'+str(num), streams, streams_description)

log("INFO", "Reading Weather forecast streams")
for num in range(1, int(wf_number_streams)+1):
    setupSream('stream_WF'+str(num), streams, streams_description)

log("INFO", "Reading de-centralized data streams")
for num in range(1, int(dcd_number_streams)+1):
    setupSream('stream_DCD'+str(num), streams, streams_description)


log("INFO", "Streams: " +str(total_streams))
log("INFO", streams)

input_log_level = int(config.get('global', "log_level"))

###########################################################
# Creating the clientes
AsyncHTTPClient.configure("tornado.curl_httpclient.CurlAsyncHTTPClient", max_clients=total_streams+2)
client = Client(streams, event_callback=event_received, error_callback=error)
try:
    # Start receiving events and block on the IOLoop
    client.start(loop=True)
except KeyboardInterrupt:
    # Ctrl-c finishes the program
    pass
finally:
    client.stop()