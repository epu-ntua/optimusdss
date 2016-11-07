__author__ = 'gcosta, asicilia'

import ztreamy
import logging
import httplib
from ztreamy import split_url

# If you need to wireshark the packages you can use
# the following filter : ip.dst == 84.88.233.144 || ip.src == 84.88.233.144

class ZtreamyClient(object):
    """Publishes events by sending them to a server. Synchronous.

    Uses a synchronous HTTP client.

    """
    _headers = {'Content-Type': ztreamy.event_media_type}

    def __init__(self, server_url):
        """Creates a new 'SynchronousEventPublisher' object.

        Events are sent in separate HTTP requests to the server given
        by 'server_url'.

        """
        scheme, self.hostname, self.port, self.path = split_url(server_url)
        assert scheme == 'http'
        if not self.path.endswith('/publish'):
            if self.path.endswith('/'):
                self.path = self.path + 'publish'
            else:
                self.path = self.path + '/publish'

    def publish(self, event):
        """Publishes a new event.

        The event is sent to the server in a new HTTP request. Returns
        True if the data is received correctly by the server.

        """
        return self.publish_events([event])

    def publish_events(self, events):
        """Publishes a list of events.

        The events in the list 'events' are sent to the server in a new
        HTTP request.

        """
        body = ztreamy.serialize_events(events)
        logging.info("Connecting to " + self.hostname + " on port " + str(self.port))
        conn = httplib.HTTPConnection(self.hostname, self.port)
        conn.request('POST', self.path, body, ZtreamyClient._headers)
        response = conn.getresponse()
        if response.status == 200:
            logging.info("Got 200 status from " + self.path)
            logging.info("Sent :" + body)
            return True
        else:
            logging.error(str(response.status) + ' ' + response.reason)
            return False

    def close(self):
        """Closes the event publisher.

        It does nothing in this class, but is maintained for
        compatibility with the asynchronous publisher.

        """
        pass

class Publisher():

    # Constructor:
    def __init__(self):

        print ''

    def PublishData(self, stream_path, stream_name, stream_content, frequency):

        # input_ztreamy_url + input_ztreamy_stream + "/publish"

        print "stream path: " + stream_path
        result = True
		
        try:

            # send data to server:
            publisher = ZtreamyClient(stream_path)

            for stream_event in stream_content:

                source_id = stream_name

                try:

                    event = ztreamy.Event(source_id, 'text/plain', stream_event)
                    result = publisher.publish(event)
                    publisher.close()
                    logging.info("Success publishing data to ztreamy")

                except KeyboardInterrupt:
                    # Allow ctrl-c to finish the program
                    logging.exception("Sending data to ztreamy interrupted")
                    return False;

                finally:
                    publisher.close()

        except:
            logging.exception("Failed to publish data to ztreamy")
            return False;

        return result
