__author__ = 'evmorfia, petros, abot'

from facepy import GraphAPI
from facepy import utils
import datetime
import json
import logging
from SocialData import SocialData
from MessageManipulation import replace_url
from MessageManipulation import replace_multichars
from Optimus.Common.Publisher import Publisher

# configure logging
logging.basicConfig(filename='facebookLog.log',level=logging.DEBUG)


#reading file "social_monitors_settings.ini"
import ConfigParser
config=ConfigParser.ConfigParser()
config.read("social_monitors_settings.ini")
# ztreamy variables
pilot = config.get("streams","streams_facebook")
ztreamyUrl =  config.get("global","ztreamy_server") + pilot +"/publish"
# variables:
frequency = 10000 # Not used

# Configure facebook
application_id = config.get('facebook','app_id')
application_secret_key = config.get('facebook','app_secret')
access_token = utils.get_application_access_token(application_id, application_secret_key)
# TODO: If we want to access private pages or groups, a bot must be used
graph = GraphAPI(access_token)
fb_pages = config.get('facebook','fb_pages').split(",")

# The facebook message has the following properties
#     print message['message'].encode('utf-8')
#     print message['from']['name'].encode('utf-8')
#     print message['created_time']
#     print message['id']

def parse_comment(page_name,comment):
    logging.info("Incoming comment from page " + page_name + ". Message: " + json.dumps(comment))
    json_to_keep = fix_json_format("FB_CMT "+comment['message'], comment['created_time'], page_name+":"+comment['from']['name'], comment['id'])
    json_to_keep["originalMessage"] = json.dumps(comment).replace("\"","#quot#")
    return json_to_keep

def parse_post(page_name,message):
    logging.info("Incoming post from page " + page_name + ". Message: " + json.dumps(message))
    msg = ""
    if "message" in message:
        msg = message['message']
    json_to_keep = fix_json_format(msg, message['created_time'], page_name+":"+message['from']['name'], message['id'])
    if 'full_picture' in message:
        #print message['full_picture']
        json_to_keep["entities"]={"media":[{"media_url_https":message['full_picture']}]}
        #print json_to_keep
    json_to_keep["originalMessage"] = json.dumps(message).replace("\"","#quot#")
    return json_to_keep

def fix_json_format(text,date,username,pid):
    text_no_url = text.lower()
    text_no_url = replace_multichars(text_no_url)
    text_no_url = replace_url(text_no_url)
    username = "facebook:"+username
    return {'text_no_url':text_no_url,'id':pid,'created_at':datetime.datetime.strptime(date,'%Y-%m-%dT%H:%M:%S+0000'),'text':text, 'user_screen_name':username,'social_source':'facebook'}

def saveTextInFile(text,filename):
    result_file = open(filename,"w")
    result_file.write(str(text.encode('utf-8')) )
    result_file.close()
    return True

dataGen = SocialData(pilot, "facebook")

def get_triple(fb_page,jsonMessage):
    return dataGen.GetTriple(fb_page, jsonMessage["id"], jsonMessage["text"], jsonMessage["originalMessage"], jsonMessage["created_at"])


publisher = Publisher()

# To test the fb api https://developers.facebook.com/tools/explorer

for fb_page in fb_pages:
    feed = graph.get('/'+fb_page+'?fields=feed.limit(30).fields(message,from,created_time,comments.filter(toplevel).fields(message,parent,from,id,created_time),object_id,full_picture),name&locale="en_US"')
    page_name = feed['name']
    feed = feed['feed']['data']
    triplesToSend = []
    for message in feed:
        try:
            doc_to_store=parse_post(page_name,message)
            triplesToSend.append(get_triple(fb_page,doc_to_store))
            # id = doc_to_store["id"];
            # saveTextInFile(doc_to_store['text_no_url'],id)
            if "comments" in message:
                comments = message['comments']['data']
                for comment in comments:
                    doc_to_store = parse_comment(page_name,comment)
                    triplesToSend.append(get_triple(fb_page,doc_to_store))
                    # id = doc_to_store["id"];
                    # saveTextInFile(doc_to_store['text_no_url'],id)
        except:
            logging.exception("Error parsing message:" + json.dumps(message))
            continue
    publishResult = publisher.PublishData(ztreamyUrl, pilot,triplesToSend, frequency)
    logging.info("Publishing finished with result: " + str(publishResult))
    print triplesToSend
