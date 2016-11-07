
import ztreamy
import ConfigParser

########################################
## Reading the config file
config = ConfigParser.RawConfigParser()
config.read('ztreamy_config.cfg')

input_port = config.get('global', "port")
input_streams = config.items('streams')

########################################
## Create a server with the streams setup in the config file
server = ztreamy.StreamServer(input_port)

for (name, value) in input_streams:
    stream = ztreamy.Stream('/' + value, allow_publish=True)
    server.add_stream(stream)

try:
    print 'Starting the server at ' + input_port + ' with the following streams: '
    for (name, value) in input_streams:
        print '/' + value
    print ''
    server.start(loop=True)
except KeyboardInterrupt:
    # Allow ctrl-c to close the server
    pass
finally:
    server.stop()
