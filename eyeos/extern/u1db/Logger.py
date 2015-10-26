import logging
from settings import settings
import os
import time

class Logger:
    path = ""
    open = False
    def openLog(self,cloud):
        try:
            if settings['Clouds'][cloud].has_key('log') and len(settings['Clouds'][cloud]['log']) > 0:
                path_log = settings['Clouds'][cloud]['log']
                if os.path.isdir(path_log) == True and os.path.exists(path_log) == True:
                    self.path = path_log + '/' + cloud + "_" + time.strftime('%Y%m%d')
                    self.open = True
        except:
            pass

    def info(self,message):
        try:
            if self.open == True and message != None:
                logging.basicConfig(filename=self.path,level=logging.INFO,format='%(asctime)s.%(msecs)d %(levelname)s : %(message)s', datefmt="%Y-%m-%d %H:%M:%S")
                logging.info(message)
        except:
            pass



