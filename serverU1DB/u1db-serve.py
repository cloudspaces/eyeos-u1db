__author__ = 'root'

from u1db import (
    __version__ as _u1db_version,
)
from u1db.commandline import (
    serve,
)
import sys, os

def main(args):
    import argparse

    try:
        pid = os.fork()
        if pid > 0:
            createPid(str(pid))
            sys.exit(0)
    except OSError, e:
        print >>sys.stderr, "fork #1 failed: %d (%s)" % (e.errno, e.strerror)
        sys.exit(1)

    p = argparse.ArgumentParser(usage='%(prog)s [options]',
                                description='Run the U1DB server')
    p.add_argument('--version', action='version', version=_u1db_version)
    p.add_argument('--verbose', action='store_true', help='be chatty')
    p.add_argument('--host', '-H', default='127.0.0.1', metavar='HOST',
                   help='Bind on this host when serving.')
    p.add_argument('--port', '-p', default=9000, metavar='PORT', type=int,
                   help='Bind to this port when serving.')
    p.add_argument('--working-dir', default='/var/lib/u1db/', metavar='WORKING_DIR',
                   help='Directory where the databases live.')

    args = p.parse_args(args)

    server = serve.make_server(args.host, args.port, args.working_dir)
    sys.stdout.write('listening on: %s:%s\n' % server.server_address)
    sys.stdout.flush()
    server.serve_forever()

def createPid(pid):
    try:
        file = open('/var/run/serverU1DB.pid', 'w')
        file.write(str(pid))
        file.close()
    except IOError as e:
        print >>sys.stderr, "Error create file pid:%d (%s)" % (e.errno, e.strerror)
        os.kill(int(pid), 9)
        sys.exit(0)
if __name__ == '__main__':
    sys.exit(main(sys.argv[1:]))
