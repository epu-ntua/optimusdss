# Energy Production Module (2.6)
from Optimus.Common import Publisher

__author__ = 'gcosta, asicilia'

import ConfigParser
import sys, traceback

import data_reader

## CHECK ARGUMENTS -------------------------------------------------------------------------------------------------- ##

configuration_file = ""

try:
    if len(sys.argv) != 2:
        print 'Error: 2 parameters must be specified, (not ' + str(len(sys.argv)) + ')'
        raise SystemExit
    else:
        if str(sys.argv[1]).split(".")[1] != 'cfg':
            print 'Error: parameter 2 must specify a configuration file name (.cfg) (not .' +str(sys.argv[1]).split(".")[1] + ')'
            raise SystemExit
        else:
            configuration_file = str(sys.argv[1])

except Exception,e:
    print 'Error: ' + str(e)
    print 'use -> example: python enpro.py connection_conf.cfg'
    raise SystemExit

config = ConfigParser.RawConfigParser()

## PARSE CONFIGURATION FILE ----------------------------------------------------------------------------------------- ##

# Read the config file
print "configuration file :" + configuration_file
config.read(configuration_file)

# Globals
input_ztreamy_server = config.get('global', "ztreamy_server")

# connection_conf
pilot = config.get('connection_conf', "pilot")
user_name = config.get('connection_conf', "username")
user_pass = config.get('connection_conf', "password")

# Streams
input_ztreamy_stream_power = config.get('streams', "streams_pwr")
input_ztreamy_stream_irradiation = config.get('streams', "streams_irrad")

# variables:
frequency = 10000 # Not used

## GET DATA FROM SOURCES -------------------------------------------------------------------------------------------- ##

try:

    sending_on = 1

    # Init common classes:
    publisher = Publisher.Publisher()

    triples_power = []
    triples_irradiation = []

    # Get some data:
    connector = config.get('connection_conf', "connector")
    days_offset = config.get('connection_conf', "days_offset")
    interval = config.get('connection_conf', "interval")

    if config.has_option('connection_conf', "web"):

        print 'Web connection selected'

        # Init connector class:
        if connector == "scrap_sunnyportal":
            data_reader = data_reader.ScrapSunnyPortal()

        source_url = config.get('connection_conf', "web")

        if interval == "no":
            try:
                # Load and create RDF data content (params-> specific date -None=current date, backward days offset)
                triples_power, triples_irradiation = data_reader.Read(source_url, pilot, None, int(days_offset))
            except Exception,e:
                print 'Error <enpro> : Running data_reader.Read() modul :' + str(e)
                raise SystemExit	

        elif interval == "yes":	
            try:
                # get interval dates:
                date_init = config.get('connection_conf', "interval_date_init")
                date_end = config.get('connection_conf', "interval_date_end")
                # Load and create RDF data content (params-> specific date -None=current date, backward days offset)
                triples_power, triples_irradiation = data_reader.ReadMultiple(source_url, pilot, date_init, date_end)
            except Exception,e:
                print 'Error <enpro> : Running data_reader.Read() modul :' + str(e)
                traceback.print_exc(file=sys.stdout)

                raise SystemExit	

    if config.has_option('connection_conf', "ftp"):

        print 'ftp connection selected'

        # Init connector class:
        if connector == "ftp_cvs":
            data_reader = data_reader.FtpCVSPortal()

        source_ftp = config.get('connection_conf', "ftp")

        if interval == "no":
            try:
                # Load and create RDF data content (params-> specific date -None=current date, backward days offset)
                triples_power, triples_irradiation = data_reader.Read(source_ftp, pilot, None, int(days_offset), user_name, user_pass)
            except Exception,e:
                print 'Error <enpro> : Running data_reader.Read() modul :' + str(e)
                raise SystemExit

        elif interval == "yes":
            try:
                # get interval dates:
                date_init = config.get('connection_conf', "interval_date_init")
                date_end = config.get('connection_conf', "interval_date_end")
                # Load and create RDF data content (params-> specific date -None=current date, backward days offset)
                triples_power, triples_irradiation = data_reader.ReadMultiple(source_ftp, pilot, date_init, date_end, user_name, user_pass)
            except Exception,e:
                print 'Error <enpro> : Running data_reader.Read() modul :' + str(e)
                raise SystemExit

    if data_reader == None:
        print 'there is no data to send.'
        sys.exit()

## SEND RDF DATA TO ZTREAMY SERVER (AS PUBLISHER) ------------------------------------------------------------------- ##

    print 'Starting connection with server at ' + input_ztreamy_server + ' with the following connector: '

    # store last values in a backup copy file:
    try:
        text_file = open("stream_power.txt", "w")
        for triples_event in triples_power:
            text_file.write(triples_event)
        text_file.close()
        print 'stream power copy [saved]'
    except Exception,e:
        print 'stream power copy [not saved]' + str(e)

    try:
        text_file = open("stream_irradiation.txt", "w")
        for triples_event in triples_power:
            text_file.write(triples_event)
        text_file.close()
        print 'stream irradiation copy [saved]'
    except Exception,e:
        print 'stream irradiation copy [not saved]' + str(e)

    # Send data:
    if sending_on == 1:
        if triples_power <> None:
            result1 = publisher.PublishData(input_ztreamy_server+input_ztreamy_stream_power+"/publish", input_ztreamy_stream_power,
                                            triples_power, frequency)
            print 'Result 1 :' + str(result1)

        if triples_irradiation <> None:
            result2 = publisher.PublishData(input_ztreamy_server+input_ztreamy_stream_irradiation+"/publish", input_ztreamy_stream_irradiation,
                                        triples_irradiation, frequency)
            print 'Result 2 :' + str(result2)
    else:
        print 'INFO: sending=OFF'	

except KeyboardInterrupt:
    # Allow ctrl-c to close the server
    pass

finally:
    print 'stopped'
    # server.stop()