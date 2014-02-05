#!/usr/bin/env python

logdir = '/mnt/openarena/logs/gameslogs'
phpstats = 'php /mnt/openarena/www/stats/include/stats.php'

import sys
import os
import logging
log = logging.Logger(__file__)

gamefile = False

for line in sys.stdin:
    if '0:00 InitGame:' in line:
        gamefile = open(os.path.join(logdir, str(hash(line))), 'w')
        print 'initgame',


    if gamefile:
        print '.',
        gamefile.write(line)

    if 'ShutdownGame' in line:
        print 'shutdowngame'
        if not gamefile:
            log.error('game stopped without start???')
        else:
            gamefile.close()
            os.system("%s %s &" % (phpstats, gamefile.name))
        gamefile = None
        
