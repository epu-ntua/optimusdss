__author__ = 'evmorfia, petros, abot'

import tweepy
import json
import re
import sys
import time
import datetime
import logging
from MessageManipulation import replace_url
from MessageManipulation import replace_multichars
from MessageManipulation import is_swearing
from tweepy.utils import import_simplejson
from Optimus.Common.Publisher import  Publisher
from SocialData import SocialData

# configure logging
logging.basicConfig(filename='twitterLog.log',level=logging.DEBUG)

# read configuration
import ConfigParser
config=ConfigParser.ConfigParser()
config.read("social_monitors_settings.ini")

# ztreamy variables
pilot = config.get("streams","streams_twitter")
ztreamyUrl =  config.get("global","ztreamy_server") + pilot +"/publish"
# variables:
frequency = 10000 # Not used

#Twitter auth stuff
consumer_key= config.get('twitter','consumer_key')
consumer_secret=config.get('twitter','consumer_secret')
access_token_key=config.get('twitter','access_token_key')
access_token_secret=config.get('twitter','access_token_secret')
#accounts to follow
accounts=config.get('twitter','follow_accounts').split(",")
# Define track terms to retrieve more information. We add the account names in order to
# get the mentions
filterTerms = accounts

username_re = re.compile(r"(?:^|\s)(@\w+)")
def replace_username(text):
    withoutUsername = username_re.sub(' _USERNAME',text)
    return withoutUsername

def fix_json(json_tweet,text_no_url):
    fields_wanted = {"created_at","text","lang","retweet_count","id","retweeted","entities"}
    json_to_keep={}
    for k in fields_wanted:
        if k in json_tweet:
            json_to_keep[k]=json_tweet[k]
    json_to_keep["text_no_url"] = replace_username(text_no_url)
    user_name = json_tweet["user"]["name"]
    user_name = 'twitter:' + user_name
    json_to_keep["username"]= user_name
    json_to_keep['social_source']='twitter'
    user_screen_name = json_tweet["user"]["screen_name"]
    user_screen_name = 'twitter:'+user_screen_name
    json_to_keep["user_screen_name"]=user_screen_name
    json_to_keep["originalMessage"] = json.dumps(json_tweet).replace("\"","#quot#")
    return json_to_keep


def fix_text_format(text):
    text_no_url = replace_url(text)
    text_no_url = text_no_url.lower()
    text_no_url = replace_multichars(text_no_url)
    text_no_url = text_no_url.replace('#','')
    return text_no_url

def convertScreenNamesToAccountId(accounts, auth):
    api = tweepy.API(auth)
    results = api.lookup_users(screen_names=accounts)
    output = []
    for result in results:
        output.append(str(result.id))
    return output#zip(accounts, output)

def parseTwitterJsonMessage(json_tweet):
    json_to_keep= None
    try:
        if "text" in json_tweet:
            text = json_tweet["text"]
            text_no_url = fix_text_format(text)

            if "lang" in json_tweet:
                language = json_tweet["lang"]
                if is_swearing(text_no_url,language)==False:
                    json_to_keep = fix_json(json_tweet,text_no_url)
                else:
                    logging.warn("swearing message detected :" + json_tweet)
                    pass
            else:
                json_to_keep = fix_json(json_tweet,text_no_url)
        else:
            pass
    except:
        logging.exception("Error parsing json tweet: "+ json_tweet)
        pass
    return json_to_keep


tweeterAuth = tweepy.OAuthHandler(consumer_key, consumer_secret)
tweeterAuth.set_access_token(access_token_key, access_token_secret)

# Convert screen names to ids
accountIds = convertScreenNamesToAccountId(accounts,tweeterAuth)

# If we need to parse old messages:
# def retrieveOldMessages(accountId, auth):
#     api = tweepy.API(auth)
#     return api.user_timeline(user_id = accountId, count = 300)
#
# for accountId in accountIds:
#   oldMessages = retrieveOldMessages(accountId, tweeterAuth)
#   for mes in oldMessages:
#       # print mes._json
#       parseTwitterJsonMessage(mes._json)

# import json parsing engine
json = import_simplejson()
publisher = Publisher()

dataGen = SocialData(pilot, "twitter")

def get_triple(fb_page,jsonMessage):
    return dataGen.GetTriple(fb_page, jsonMessage["id"], jsonMessage["text"], jsonMessage["originalMessage"], jsonMessage["created_at"])


class StreamListener(tweepy.StreamListener):
    # Constructor:
    def __init__(self, ztreamyUrl, pilot , accounntName, frequency):
        self.accountName = accounntName
        self.ztreamyUrl = ztreamyUrl
        self.pilot = pilot
        self.frequency = frequency
    def on_status(self, tweet):
        print 'Run on_status'
        pass
    def on_timeout(self):
        logging.debug("***timeout:sleeping for a minute***")
        time.sleep(60)
        return True #don't kill the stream
    def on_error(self, status_code):
        logging.warn("-----error---- status_code:" + status_code)
        return True #don't kill the stream
    def on_data(self, data):
        try:
            if data[0].isdigit():
                pass
            else:
                logging.info("Incoming message: " + data)
                json_tweet=json.loads(data)
                #print data
                doc_to_store = parseTwitterJsonMessage(json_tweet)
                if doc_to_store!=None:
                    publisher.PublishData(self.ztreamyUrl, self.pilot, get_triple(self.accountName,doc_to_store), self.frequency)
        except:
            logging.exception("Failed to parse data " + json.dumps(data))


# start following all accounts
steamListener = StreamListener(ztreamyUrl, pilot , ' '.join(accounts), frequency)
streamer = tweepy.Stream(auth=tweeterAuth, listener=steamListener, timeout=3000)
streamer.filter(follow = accountIds, track = filterTerms)


