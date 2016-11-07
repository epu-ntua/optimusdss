__author__ = 'asicilia'


from SPARQLWrapper import SPARQLWrapper, JSON, POST, POSTDIRECTLY, SELECT

import rdflib


print ("Reading sparql....")


def ExtractDump (filename, query, limit):
    exit = False
    i = 0

    try:
        text_file = open(filename, "a")

        while (exit == False):

            sparql = SPARQLWrapper("http://winarc.housing.salle.url.edu:8080/sparql")
            sparql.setQuery(query + """ offset """ + str(i*limit) + """ limit """+str(limit))
            sparql.setReturnFormat(JSON)
            results = sparql.query().convert()


            print "results: " + str(len(results["results"]["bindings"]))
            if len(results["results"]["bindings"]) == 0:
                exit = True
            else:
                for result in results["results"]["bindings"]:
                    #print(result["power"]["value"] + "|" + result["radiation"]["value"] + "|" + result["datetime"]["value"])

                    for key in result:
                        # print key + ": " + result[key]["value"]
                        text_file.write('%s,' % (result[key]["value"]))

                    text_file.write('\n')
                    #text_file.write('%s,%s,%s\n' % (result["power"]["value"], result["radiation"]["value"], result["datetime"]["value"]))

            print i
            i = i + 1



        text_file.close()

    except Exception,e:
        print ("Reading configuration.... " + e.message)


queryEnergyProduction = """
SELECT  ?power ?radiation ?datetime
WHERE
{
  {
select distinct ?power ?radiation ?datetime

where {

?obs1 <http://purl.oclc.org/NET/ssnx/ssn#observedBy> <http://www.optimus-smartcity.eu/resource/sant_cugat/sensingdevice/sunnyportal_energy_production>;
 <http://purl.oclc.org/NET/ssnx/ssn#observationResult> [<http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?power];
     <http://purl.oclc.org/NET/ssnx/ssn#observationResultTime> [<http://www.w3.org/2006/time#inXSDDateTime> ?datetime].

?obs2 <http://purl.oclc.org/NET/ssnx/ssn#observedBy> <http://www.optimus-smartcity.eu/resource/sant_cugat/sensingdevice/sunnyportal_solar_radiation>;
 <http://purl.oclc.org/NET/ssnx/ssn#observationResult> [<http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?radiation];
     <http://purl.oclc.org/NET/ssnx/ssn#observationResultTime> [<http://www.w3.org/2006/time#inXSDDateTime> ?datetime].

    } order by ?datetime
  }
}
"""


querySpanishEnergyPrices = """
SELECT  ?price ?datetime
WHERE
{
  {
select distinct ?price ?datetime

where {

?obs2 <http://purl.oclc.org/NET/ssnx/ssn#observedBy> <http://www.optimus-smartcity.eu/resources/sant-cugat/sensingdevice/electricity_hourly_prices>;
 <http://purl.oclc.org/NET/ssnx/ssn#observationResult> [<http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?price];
     <http://purl.oclc.org/NET/ssnx/ssn#observationResultTime> [<http://www.w3.org/2006/time#inXSDDateTime> ?datetime].

    } order by ?datetime
  }
}
"""

queryItalianEnergyPrices = """
SELECT  ?price ?datetime
WHERE
{
  {
select distinct ?price ?datetime

where {

?obs2 <http://purl.oclc.org/NET/ssnx/ssn#observedBy> <http://www.optimus-smartcity.eu/resources/savona/sensingdevice/electricity_hourly_prices>;
 <http://purl.oclc.org/NET/ssnx/ssn#observationResult> [<http://purl.oclc.org/NET/ssnx/ssn#hasValue> ?price];
     <http://purl.oclc.org/NET/ssnx/ssn#observationResultTime> [<http://www.w3.org/2006/time#inXSDDateTime> ?datetime].

    } order by ?datetime
  }
}
"""




ExtractDump ("energyproductiondata.csv", queryEnergyProduction, 5000)

#ExtractDump ("energypricesdata_spain.csv", querySpanishEnergyPrices, 5000)
#ExtractDump ("energypricesdata_italy.csv", queryItalianEnergyPrices, 5000)