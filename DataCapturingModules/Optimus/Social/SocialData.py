__author__ = 'abot'

class SocialData():

     # Constructor:
    def __init__(self, pilot, channelName):
        self.pilot = pilot
        self.sensor_name = channelName

    def GetTriple(self, channelId, messageId, message, original, messageDate):
        resource_uri = "http://www.optimus-smartcity.eu/resource/" + self.pilot.split("_")[0] + "/"
        resource_id = str(self.sensor_name) + str(channelId)
        datetime_formated = '%.4d-%.2d-%.2dT%.2d:%.2d:00Z' % (int(messageDate.year), int(messageDate.month), int(messageDate.day), int(messageDate.hour), int(messageDate.minute))

        prop_observed_by = "ssn:observedBy"
        prop_observation_id = "ssn:observationId"
        uri_observation_id = str(resource_uri) + "sensoroutput/" + str(resource_id) + str("/id")
        prop_observation_message = "ssn:observationMessage"
        uri_observation_message = str(resource_uri) + "sensoroutput/" + str(resource_id) + str("/message")
        prop_observation_original = "ssn:observationOriginal"
        uri_observation_original = str(resource_uri) + "sensoroutput/" + str(resource_id) + str("/original")
        prop_observation_resulttime = "ssn:observationResultTime"
        uri_observation_resulttime = str(resource_uri) + "instant/" + str(channelId)
        prop_has_value = "ssn:hasValue"
        prop_datetime = "time:inXSDDateTime"

        if message == None: message=""
        if original == None: original=""


        triples = "<" + str(resource_uri) + str("observation/") + str(resource_id) + "> " + str(prop_observed_by) + " <" + str(resource_uri) + "sensingdevice/" + str(self.sensor_name) + "> ." + '\n' + \
                  "<" + str(resource_uri) + str("observation/") + str(resource_id) + "> " + str(prop_observation_id) + " <" + uri_observation_id + "> ." + '\n' + \
                  "<" + str(resource_uri) + str("observation/") + str(resource_id) + "> " + str(prop_observation_message) + " <" + uri_observation_message + "> ." + '\n' + \
                  "<" + str(resource_uri) + str("observation/") + str(resource_id) + "> " + str(prop_observation_original) + " <" + uri_observation_original + "> ." + '\n' + \
                  "<" + str(resource_uri) + str("observation/") + str(resource_id) + "> " + str(prop_observation_resulttime) + " <" + uri_observation_resulttime + "> ." + '\n' + \
                  "<" + uri_observation_id + "> " + str(prop_has_value) + " \"" + str(messageId) + str("\"^^xsd:string .") + '\n' + \
                  "<" + uri_observation_message + "> " + str(prop_has_value) + " \"" + str(message) + str("\"^^xsd:string .") + '\n' + \
                  "<" + uri_observation_original + "> " + str(prop_has_value) + " \"" + str(original) + str("\"^^xsd:string .") + '\n' + \
                  "<" + uri_observation_resulttime + "> " + str(prop_datetime) + " \"" + str(datetime_formated) + str("\"^^xsd:dateTime .")  + '\n'

        return triples.replace('\n','')
