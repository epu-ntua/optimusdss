# -*- coding: utf-8 -*-

__author__ = 'dimitris'

import re
from datetime import datetime
from dateutil.parser import parse

THERMAL_SENSATIONS = (
    'Cold',
    'Cool',
    'Slightly Cool',
    'Neutral',
    'Slightly Warm',
    'Warm',
    'Hot',
)

PERCEIVED_TEMPERATURES = (
    '',
    'Clearly Acceptable',
    'Just Acceptable',
    'Just Unacceptable',
    'Clearly Unacceptable',
)

PROPOSED_TEMPERATURES = (
    '',
    'Higher',
    'Lower',
    'No Change',
)

PROPOSED_AIR_MOVEMENTS = (
    '',
    'More Air Movement',
    'Less air movement',
    'No Change',
)

PROPOSED_LIGHTINGS = (
    '',
    'More Sun',
    'More Shade',
    'No Change',
)

CLOTHING_TYPES = (
    '',
    'Shorts OR Short Skirt',
    'Jumper AND/OR Jacket',
    'Short Sleeved Shirt',
    'Shoes AND/OR Socks',
    'Jeans OR Other Long Pants OR Long Skirt',
    'Vest OR Single Top',
    'Long Sleeved Shirt',
    'Sandals OR Thongs',
)

CLOTHING_COLORS = (
    '',
    'Light',
    'Dark'
)

ACTIVITY_TYPES = (
    '',
    'Sleeping',
    'Standing',
    'Sitting',
    'Walking',
)

ADDRESSES = {
    u'Savona, Colombo-Pertini School': ('savona', 'savona_school'),
    u'Zaanstad, Town Hall': ('zaanstad', 'zaanstad_town_hall'),
    u'Zaanstad, Zaanstad Town Hall': ('zaanstad', 'zaanstad_town_hall'),
    u'Sant Cugat, Town Hall': ('sant_cugat', 'sant_cugat_town_hall'),
    u'Sant Cugat, Sant Cugat Town Hall': ('sant_cugat', 'sant_cugat_town_hall'),
    u'Sant Cugat, Theatre': ('sant_cugat', 'sant_cugat_theatre'),
}
        
        
class TCVRecord:
    def __init__(self, db_row):
        pos = 0
        # primary key to identify later
        self.pk = db_row[pos]; pos += 1
        # general info
        self.timestamp = parse(db_row[pos]); pos += 1
        self.building_address = db_row[pos]; pos += 1
        self.building_type = db_row[pos]; pos += 1
        self.user_email = db_row[pos]; pos += 1
        # temperature
        self.thermal_sensation = db_row[pos]; pos += 1
        self.perceived_temperature = db_row[pos]; pos += 1
        if self.perceived_temperature is None:
            self.perceived_temperature = ''
        self.proposed_temperature = db_row[pos]; pos += 1
        if self.proposed_temperature is None:
            self.proposed_temperature = ''
        # wind
        self.proposed_air_movement = db_row[pos]; pos += 1
        if self.proposed_air_movement is None:
            self.proposed_air_movement = ''
        # sun
        self.proposed_lighting = db_row[pos]; pos += 1
        if self.proposed_lighting is None:
            self.proposed_lighting = ''
        # clothing
        if db_row[pos] is None: 
            self.clothing = []
        else:    
            self.clothing = db_row[pos].split(','); pos += 1
        self.clothing_color = db_row[pos]; pos += 1
        if self.clothing_color is None:
            self.clothing_color = ''
        # activity
        self.activity = db_row[pos]; pos += 1
        if self.activity is None:
            self.activity = ''

        # validation errors
        self.errors = []

    # validate record
    def is_valid(self):
        self.errors = []

        # validate general information
        if not self.building_address:
            self.errors.append('Building address is required')

        if self.building_address in ADDRESSES:
            address_info = ADDRESSES[self.building_address]
            self.city = address_info[0]
            self.building = address_info[1]
        else:
            self.errors.append('Invalid address: %s' % self.building_address)
            
        if not self.building_type:
            self.errors.append('Building type is required')
                        
        if self.user_email and (not re.match(r'[^@]+@[^@]+\.[^@]+', self.user_email)):
            self.errors.append('Invalid user e-mail address: ' + self.user_email)

        # validate temperature
        if self.thermal_sensation not in THERMAL_SENSATIONS:
            self.errors.append('Invalid thermal sensation: ' + self.thermal_sensation)

        if self.perceived_temperature not in PERCEIVED_TEMPERATURES:
            self.errors.append('Invalid perceived temperature: ' + self.perceived_temperature)

        if self.proposed_temperature not in PROPOSED_TEMPERATURES:
            self.errors.append('Invalid proposed temperature: ' + self.proposed_temperature)

        # validate wind
        if self.proposed_air_movement not in PROPOSED_AIR_MOVEMENTS:
            self.errors.append('Invalid proposed air movement: ' + self.proposed_air_movement)

        # validate sun
        if self.proposed_lighting not in PROPOSED_LIGHTINGS:
            self.errors.append('Invalid proposed lighting: ' + self.proposed_lighting)

        # validate clothing
        for cloth in self.clothing:
            if cloth not in CLOTHING_TYPES:
                self.errors.append('Invalid clothing: ' + cloth)

        if self.clothing_color not in CLOTHING_COLORS:
            self.errors.append('Invalid clothing color: ' + self.clothing_color)

        # validate activity
        if self.activity not in ACTIVITY_TYPES:
            self.errors.append('Invalid activity: ' + self.activity)

        return len(self.errors) == 0

    # mark the record as published in the database
    def mark_as_published(self, cnx):
        query = 'UPDATE questionnaire SET published=true WHERE id=' + str(self.pk)
        cur = cnx.cursor()
        cur.execute(query)
        result = cur.rowcount==1
        cnx.commit()
        cur.close()
        
        return result
        
    # turn record to RDF triples
    def to_rdf(self, base_uri):
        # add specific building to base URI
        base_uri += self.building + '/'
        
        # an array to include all optional data in the query
        optional_data = []
        
        # URIs and timestamps
        timestamp_utc = self.timestamp.strftime("%Y%m%d%H%M%S")
        timestamp_str = self.timestamp.strftime("%Y-%m-%dT%H:%M:%SZ")
        
        gen_uri = '<http://www.optimus-smartcity.eu/resource/sant_cugat/observation/tcv_measurements' + timestamp_utc + '>'
        uri = '<' + base_uri + str(self.pk) + '/>'
        
        
        data = gen_uri + 'ssn:observedBy <http://www.optimus-smartcity.eu/resource/sant_cugat/sensingdevice/tcv_web_application>;\n'
        data += 'ssn:observationResult ' + gen_uri + ';\n'
        data += 'ssn:observationResultTime  <http://www.optimus-smartcity.eu/resource/sant_cugat/instant/' + timestamp_utc + '>.\n'
        data += '<http://www.optimus-smartcity.eu/resource/sant_cugat/instant/' + timestamp_utc + '>  time:inXSDDateTime  "' + timestamp_str + '"^^xsd:dateTime.\n'
        data += gen_uri + 'ssn:hasValue ' + uri + '.\n\n'

        # general information
        data += uri + ' a optimus:tcv_record;\n'
        
        data += 'sem:hasTimeStamp "' + timestamp_str + '"^^xsd:datetime;\n'
        if self.user_email:
            optional_data.append('schema:email "' + self.user_email + '"^^xsd:string')
        data += 'schema:address "' + self.building_address + '"^^xsd:string;\n'
        data += 'optimus:building_type "' + self.building_type + '"^^xsd:string;\n'
        
        # temperature
        data += 'optimus:thermal_sensation "' + self.thermal_sensation + '"^^xsd:string'
        if self.perceived_temperature:
            optional_data.append('optimus:perceived_temperature "' + self.perceived_temperature + '"^^xsd:string')
        if self.proposed_temperature:
            optional_data.append('optimus:proposed_temperature "' + self.proposed_temperature + '"^^xsd:string')

        # wind
        if self.proposed_air_movement:
            optional_data.append('optimus:proposed_air_movement "' + self.proposed_air_movement + '"^^xsd:string')

        # sun
        if self.proposed_lighting:
            optional_data.append('optimus:proposed_lighting "' + self.proposed_lighting + '"^^xsd:string')

        # clothing
        if self.clothing:
            optional_data.append('optimus:clothing "' + ','.join(self.clothing) + '"^^xsd:string')

        # clothing color
        if self.clothing_color:
            optional_data.append('optimus:clothing_color "' + self.clothing_color + '"^^xsd:string')

        # activity
        if self.activity:
            optional_data.append('optimus:activity "' + self.activity + '"^^xsd:string')

        # add optional attributes to RDF
        if not optional_data:
            data += '.\n'
        else:
            data += ';\n'
            
            i = 0
            while i < len(optional_data):
                data += optional_data[i]
                if i+1 < len(optional_data):
                    data += ';'
                else:
                    data += '.'
                
                data += '\n'
                i += 1
				
        return data
        
    @classmethod
    def get_prefixes(cls):
        data = '@prefix xsd: <http://www.w3.org/2001/XMLSchema#>.\n'
        data += '@prefix sem: <http://semanticweb.cs.vu.nl/2009/11/sem/>.\n'
        data += '@prefix schema: <http://schema.org/>.\n'
        data += '@prefix optimus: <http://www.optimus-smartcity.eu/ontology/>.\n\n'
        
        return data

