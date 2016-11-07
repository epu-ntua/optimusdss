__author__ = 'gcosta, asicilia'

import datetime
import urllib2
import csv
import string
import datetime

from abc import ABCMeta, abstractmethod
from bs4 import BeautifulSoup
from datetime import timedelta
from ftplib import FTP
from StringIO import StringIO
#from random import randint
#from time import strftime
#import os

# Variables
selected_IDpage = "7ab527fa-0501-4040-af3a-b7c4790dcc1e"

class DataReader:
    __metaclass__ = ABCMeta

    @abstractmethod
    def Read(self): pass

    def GetDate(self, selected_date, days_offset):

        if selected_date == None:
            selected_date = datetime.datetime.now()

        print 'current date: '+ str(selected_date.day) + '/' + str(selected_date.month) + '/' + str(selected_date.year)

        # Offset date        
        if days_offset > 0:
            selected_date = selected_date - timedelta(days=days_offset)
        #print 'date: '+ str(selected_date.day) + '/' + str(selected_date.month) + '/' + str(selected_date.year)

        if selected_date == None:
            selected_date = datetime.datetime.now()

        print 'selected date: '+ str(selected_date.day) + '/' + str(selected_date.month) + '/' + str(selected_date.year)

        # IMPORTANT: this date can contain this form: 1/1/2001 (not 01/01/2001)
        return selected_date

    def GenerateTriples(self, pilot, sensor_name, sensor_id, value, datetime_formated):

        resource_uri = "http://www.optimus-smartcity.eu/resource/" + pilot + "/"

        prop_observed_by = "ssn:observedBy"
        prop_observation_result = "ssn:observationResult"
        prop_observation_resulttime = "ssn:observationResultTime"
        prop_has_value = "ssn:hasValue"
        prop_datetime = "time:inXSDDateTime"

        if value == '': value = 0
        if value == None: value = 0
        resource_id = str(sensor_name) + str(sensor_id)

        triples = "<" + str(resource_uri) + str("observation/") + str(resource_id) + "> " + str(prop_observed_by) + " <" + str(resource_uri) + "sensingdevice/" + str(sensor_name) + "> ." + '\n' + \
                  "<" + str(resource_uri) + str("observation/") + str(resource_id) + "> " + str(prop_observation_result) + " <" + str(resource_uri) + "sensoroutput/" + str(resource_id) + "> ." + '\n' + \
                  "<" + str(resource_uri) + str("observation/") + str(resource_id) + "> " + str(prop_observation_resulttime) + " <" + str(resource_uri) + "instant/" + str(sensor_id) + "> ." + '\n' + \
                  "<" + str(resource_uri) + str("sensoroutput/") + str(resource_id) + "> " + str(prop_has_value) + " \"" + str(value) + str("\"^^xsd:decimal .") + '\n' + \
                  "<" + str(resource_uri) + str("instant/") + str(sensor_id) + "> " + str(prop_datetime) + " \"" + str(datetime_formated) + str("\"^^xsd:dateTime .")  + '\n'

        return triples		


class ScrapSunnyPortal(DataReader):

    def Read(self, source_url, pilot, date, days_offset):

        # Common operations:
        s = super(ScrapSunnyPortal, self).Read()

        # Variables
        selected_date = date
        triples_power = []
        triples_irradiation = []
        n = 0

		# Date: this is specified by user (init date ore days ago)
		#       the URI is acceded according the date
        selected_date = super(ScrapSunnyPortal, self).GetDate(date, days_offset)

        # Sunny portal URL to get the data:
        sunnyportalreport_url = source_url + '?ID=' + selected_IDpage + '&endTime=' + str(selected_date.month) + '/' + str(selected_date.day) + '/' + str(selected_date.year) + '%2011:59:59%20PM&splang=en-US&plantTimezoneBias=120&name='

        # URL example http://www.sunnyportal.com/Templates/PublicChartValues.aspx?ID=7ab527fa-0501-4040-af3a-b7c4790dcc1e&endTime=08/23/2014%2011:59:59%20PM&splang=en-US&plantTimezoneBias=120&name=
        print 'URL: ' + sunnyportalreport_url

	try:
            dom = BeautifulSoup(urllib2.urlopen(sunnyportalreport_url).read())
            print sunnyportalreport_url

        except Exception,e:
            # Usually occurs when we lost the connection with the remote host
            print 'ERROR <Read>: ' + str(e)
            return triples_power, triples_irradiation

        if(len(dom('table', {'class': 'base-grid'})) > 0):
            for row in dom('table', {'class': 'base-grid'})[0].tbody():
                #print '>>> :' + row.prettify()
                cols = row('td')

                if cols != []:
                    if cols[0].string != None:
                        time = ''
                        device_2000568991 = '' # PV Panel
                        device_2000569037 = '' # PV Panel
                        device_2000595567 = '' # PV Panel
                        device_6575 = ''       # Insolation
                        try:
                            # Get values of the time, 3 PV pannels and 1 insolation sensor:
                            time = cols[0].string
                            device_2000568991 = cols[1].string
                            device_2000569037 = cols[2].string
                            device_2000595567 = cols[3].string
                            device_6575 = cols[4].string

                            # if we have all the 5 values (TODO: if not?):
                            if time != '' and device_2000568991 != '' and device_2000569037 != '' and device_2000595567 != '' and device_6575 != '':

                                # Generate ID
                                hours = str(time).split(":")[0]
                                minutes = str(time).split(":")[1]
                                minutes = minutes.split(" ")[0]
                                am_pm = str(time).split(" ")[1]
                                if am_pm == 'PM' and int(hours) < 12:
                                    hours = str(int(hours) + 12)
                                elif am_pm == 'AM'and hours == "12":
                                    hours = "0"

                                #device_id = str(selected_date.year) + str(selected_date.month) + str(selected_date.day) + str(hours) + str(minutes)
                                device_id = '%04d%02d%02d%02d%02d' % (int(selected_date.year), int(selected_date.month), int(selected_date.day), int(hours), int(minutes))

                                # Generating the time stamp from the time column of the table (e.g. time > 2014-09-25T10:00:00Z)

                                datetime_formated = '%04d-%02d-%02dT%02d:%02d:00Z' % (int(selected_date.year), int(selected_date.month), int(selected_date.day), int(hours), int(minutes))

                                if device_2000568991 == None or device_2000568991 == '': device_2000568991 = 0
                                if device_2000569037 == None or device_2000569037 == '': device_2000569037 = 0
                                if device_2000595567 == None or device_2000595567 == '': device_2000595567 = 0
                                if device_6575 == None: device_6575 = ''

                                # Generating triples for power and irradiation measurement:
                                triples_power.append(super(ScrapSunnyPortal, self).GenerateTriples(
                                                                            pilot,
                                                                            "sunnyportal_energy_production",
                                                                            device_id,
                                                                            str(float(device_2000568991) + float(device_2000569037) + float(device_2000595567)),
                                                                            datetime_formated))
                                triples_irradiation.append(super(ScrapSunnyPortal, self).GenerateTriples(
                                                                            pilot,
                                                                            "sunnyportal_solar_radiation",
                                                                            device_id,
                                                                            str(device_6575),
                                                                            datetime_formated))

                                n = n + 1

                        except Exception,e:
                            print 'ERROR <Read>: ' + str(e)
                            pass

        return triples_power, triples_irradiation
   
    def ReadMultiple(self, source_url, pilot, date_init, date_end):

        # Common operations:
        s = super(ScrapSunnyPortal, self).Read()

        # Variables
        triples_power = []
        triples_irradiation = []
        print "> " + date_init
        print "> " + date_end
        selected_date_init = datetime.datetime.strptime(date_init.translate(None, '-'), "%d%m%Y").date()
        selected_date_end = datetime.datetime.strptime(date_end.translate(None, '-'), "%d%m%Y").date()
        n = 0

        # check dates
        if selected_date_init > selected_date_end:
            print 'Error: the starting date must be older than the end -> ( ' + str(selected_date_init) + ' < ' + str(selected_date_end) + ') '
            return triples_power, triples_irradiation

        # Generate dates
        selected_date_tmp = selected_date_init
        dates = []
        while selected_date_tmp < selected_date_end:

            dates.append(selected_date_tmp)

            # increase a day
            selected_date_tmp = selected_date_tmp + timedelta(days=1)

            try:
                triples_power_tmp, triples_irradiation_tmp = self.Read(source_url, pilot, selected_date_tmp, 0)
                triples_power.extend(triples_power_tmp)
                triples_irradiation.extend(triples_irradiation_tmp)

            except Exception,e:
                print 'Error <enpro> : Running data_reader.ReadMultiple() modul -> probably there is no data for a particular day or we lost the connection with the remote host: ' + str(e)
                pass

        return triples_power, triples_irradiation

        
class FtpCVSPortal(DataReader):

    def Read(self, source_url, pilot, date, days_offset, username, password):

        # Common operations:
        s = super(FtpCVSPortal, self).Read()
		
        # Variables
        selected_date = date
        triples_power = []
        triples_irradiation = []
        n = 0
		
        # Date: this is specified by user (init date ore days ago)
		#       the URI is acceded according the date
        selected_date = super(FtpCVSPortal, self).GetDate(date, days_offset)
        
		# Sunny portal URL to get the data:
        selected_file = 'PVdata%.4d%.2d%.2d' % (selected_date.year, selected_date.month, selected_date.day) + '.csv'
		
        try:
		
            result = self.GetDataFromFTP(source_url, selected_file, username, password)
			
            if result == 1:
                # You should see a number of .csv files (one per day) containing four fields: 
				# - date.......................... 12-09-2014;
				# - time.......................... 09:45:00;
				# - PV solar radiation............ 561,24;
				# - PV active power power......... 38,42
				# (Time sample is 15 minutes)
				# (There are also some other files needed to generate the .csv files. ??)

                mycsv = csv.reader(open('data_temp.csv'), delimiter=';')
                n=0

                for row in mycsv:

                    if n > 0:

                        # Generate ID:
                        date = str(row[0])
                        time = str(row[1])

                        id_day = str(date).split("-")[0]
                        id_month = str(date).split("-")[1]
                        id_year = str(date).split("-")[2]

                        device_id = id_year + id_month + id_day + time.translate(None, ':')

                        #print str(device_id)

                        hours = str(time).split(":")[0]
                        minutes = str(time).split(":")[1]
                        minutes = minutes.split(" ")[0]

                        tbl1 = string.maketrans(",", '.')

                        # values:
                        radiation_value = str(row[2])
                        radiation_value = radiation_value.translate(tbl1)

                        production_value = str(row[3])
                        production_value = production_value.translate(tbl1)

                        # Date
                        datetime_formated = '%.4d-%.2d-%.2dT%.2d:%.2d:00Z' % (int(selected_date.year), int(selected_date.month), int(selected_date.day), int(hours), int(minutes))

                        # Generating triples for power and irradiation measurement:
                        triples_power.append(super(FtpCVSPortal, self).GenerateTriples(
                                                                            pilot,
                                                                            "savonaftp_energy_production",
                                                                            device_id,
                                                                            radiation_value,
                                                                            datetime_formated))
                        triples_irradiation.append(super(FtpCVSPortal, self).GenerateTriples(
                                                                            pilot,
                                                                            "savonaftp_solar_radiation",
                                                                            device_id,
                                                                            production_value,
                                                                            datetime_formated))
                    n =  n + 1
                    print 'info :' + str(row[0]) + " " + str(row[1]) + " " + str(row[2])+ " " + str(row[3])

        except Exception,e:
            print 'ERROR <FtpCVSPortal>: ' + str(e)
					
        return triples_power, triples_irradiation


    def ReadMultiple(self, source_url, pilot, date_init, date_end, username, password):

        # Common operations:
        s = super(FtpCVSPortal, self).Read()

        # Variables
        triples_power = ''
        triples_irradiation = ''
        print "> " + date_init
        print "> " + date_end
        selected_date_init = datetime.datetime.strptime(date_init.translate(None, '-'), "%d%m%Y").date()
        selected_date_end = datetime.datetime.strptime(date_end.translate(None, '-'), "%d%m%Y").date()
        n = 0

        # check dates
        if selected_date_init > selected_date_end:
            print 'Error: the starting date must be older than the end -> ( ' + str(selected_date_init) + ' < ' + str(selected_date_end) + ') '
            return triples_power, triples_irradiation

        # Generate dates
        selected_date_tmp = selected_date_init
        dates = []
        while selected_date_tmp < selected_date_end:

            dates.append(selected_date_tmp)

            # increase a day
            selected_date_tmp = selected_date_tmp + timedelta(days=1)

            triples_power_tmp, triples_irradiation_tmp = self.Read(source_url, pilot, selected_date_tmp, 0, username, password)
            triples_power.extend(triples_power_tmp)
            triples_irradiation.extend(triples_irradiation_tmp)


        return triples_power, triples_irradiation


    def GetDataFromFTP(self, source_url, selected_file, username, password):

        # Retrieve the webpage as a string (eg ftp://130.251.145.7/PVdata20140912.csv)

        try:
            ftp1 = FTP(source_url)
            result = ftp1.login(username, password)
            if '230' in result:
                file = open('data_temp.csv', "wb")
                ftp1.set_pasv(False)
                ftp1.retrbinary('RETR ' + selected_file, file.write)
                ftp1.quit()
                file.close()
                return 1
            else:
                return 0

        except Exception,e:
            print 'ERROR <GetDataFromFTP>: [selected file: '+ selected_file + ']' + str(e)
            return 0
