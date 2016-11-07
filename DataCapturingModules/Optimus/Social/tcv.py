# -*- coding: utf-8 -*-

__author__ = 'dimitris'

import sys
import ConfigParser
from time import sleep
from mysql import connector
from models.tcv import TCVRecord, ADDRESSES
from Optimus.Common.Publisher import Publisher

        
# Poll new TCV records
def poll():
    # read configuration
    config = ConfigParser.ConfigParser()
    config.read("social_monitors_settings.ini")

    # Ztreamy variables
    ztreamy_servers = {}
    for city in ['savona', 'sant_cugat', 'zaanstad']:
        ztreamy_servers[city] = config.get("global", "ztreamy_server_%s" % city)
    #ztreamy_url = config.get("global", "ztreamy_server") + pilot + "/publish"
    
    ztreamy_streams = {}
    for building in ['savona_school', 'zaanstad_town_hall', 'sant_cugat_town_hall', 'sant_cugat_theatre']:
        ztreamy_streams[building] = config.get("streams", "streams_%s" % building)
    
    frequency = 10000

    # Database configuration
    db_config = {
        'host': config.get('tcv_db', 'host'),
        'port': config.get('tcv_db', 'port'),
        'user': config.get('tcv_db', 'user'),
        'password': config.get('tcv_db', 'password'),
        'database': config.get('tcv_db', 'database'),
    }

    # connect to the database
    cnx = connector.connect(**db_config)

    # the documents to send to ztreamy
    rdf_doc = {}
    for address in ADDRESSES:
       rdf_doc[address] = '' 

    # read unpublished records
    query = "SELECT * FROM questionnaire WHERE published=false"

    cursor = cnx.cursor()
    cursor.execute(query)
    tcv_records = []
    for row in cursor:
        record = TCVRecord(row)
        if record.is_valid():
            rdf_doc[record.building_address] += record.to_rdf(base_uri='http://www.optimus-smartcity.eu/resource/')
            tcv_records.append(record)
        else:
            print('Ignoring invalid record #' + str(record.pk) + ':\n\t' + '\n\t'.join(record.errors))

    cursor.close()
    
    # send rdf documents to ztreamy
    for address in ADDRESSES:
        if rdf_doc[address]:
            info = ADDRESSES[address]
            stream = ztreamy_streams[info[1]]
            publish_url = ztreamy_servers[info[0]] + '/' + stream + '/publish'
            doc = TCVRecord.get_prefixes() + rdf_doc[address]

            # try to publish the RDF to ztreamy
            if Publisher().PublishData(publish_url, stream, [doc.encode('utf-8')], frequency):
                # if successfully published
                # mark records in the database as published to ztreamy
                n_of_added = 0
                for record in tcv_records:
                    if record.building_address == address:
                        n_of_added += 1
                        record.mark_as_published(cnx)

	            if n_of_added:
	                print('Record sent to %s' % ztreamy_streams[info[1]])
	            else:
	                print('Record skipped from %s' % ztreamy_streams[info[1]])
            else:
                print('Ztreamy server %s is down: ' % publish_url)

    # close the connection
    cnx.close()


if '-d' in sys.argv:  # deamon mode
    # listen for new TCV records forever
    print('Starting TCV record listener\nPress Ctrl+C to stop')
    
    while True:
        poll()
        sleep(60)  # sleep for 1 minute
else:  # plain mode
    poll()  

