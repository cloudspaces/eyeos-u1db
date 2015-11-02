__author__ = 'root'

from settings import settings
import sys
import json
from oauthlib.oauth1 import SIGNATURE_PLAINTEXT
from OAuthRequest import OAuthRequest
import urllib
import types
from Logger import Logger


class OauthCredentials:
    def __init__(self, requesttokenurl, accesstokenurl, resourceurl, version,logger=None):
        self.requesturl = requesttokenurl
        self.accessurl = accesstokenurl
        self.resourceurl = resourceurl
        self.version = version
        self.logger = logger

    def getRequestToken(self, oauth):
        token = {}
        try:
            self.createHeader(oauth)
            request_token = oauth.fetch_request_token(self.requesturl)
            if request_token:
                token = json.dumps({"key": "" + request_token['oauth_token'] + "",
                                    "secret": "" + request_token['oauth_token_secret'] + ""})
        except ValueError as e:
            pass

        return token

    def getAccessToken(self, oauth):
        token = {}
        try:
            self.createHeader(oauth)
            access_token = oauth.fetch_access_token(self.accessurl)
            if access_token:
                token = json.dumps({"key": "" + access_token['oauth_token'] + "",
                                    "secret": "" + access_token['oauth_token_secret'] + ""})
        except ValueError as e:
            pass
        return token

    def getMetadata(self, oauth, file, id, contents=None):
        url = self.getUrl(file, id, contents)
        self.writeLog("Function: ---> getMetadata")
        self.writeLog("URL: " + url)
        self.createHeader(oauth)
        result = oauth.get(url)
        return self.createRequest(result)

    def updateMetadata(self, oauth, file, id, name=None, parent=None):
        url = self.getUrl(file, id)
        self.writeLog("Function: ---> updateMetadata")
        self.writeLog("URL: " + url)
        self.createHeader(oauth)
        self.createApplicationJson(oauth)
        data = {}
        if name:
            data['name'] = name
        if parent:
            data['parent'] = parent
        data = json.dumps(data)
        result = oauth.put(url, data)
        return self.createRequest(result)

    def createMetadata(self, oauth, file, name, parent=None, path=None):
        dataFile = None
        url = self.getUrl(file)
        self.writeLog("Function: ---> createMetadata")
        self.writeLog("URL: " + url)
        self.createHeader(oauth)
        self.createApplicationJson(oauth)

        data = {}
        data['name'] = name
        if parent and parent != 'null':
            data['parent'] = str(parent)

        if file:
            self.file = open(path, "r")
            if self.file:
                dataFile = self.file.read()
                self.file.close()
            params = urllib.urlencode(data)
            url += "?" + params
        else:
            dataFile = json.dumps(data)

        result = oauth.post(url, dataFile)
        return self.createRequest(result)

    def uploadFile(self, oauth, id, path):
        metadata = 'false'
        self.file = open(path, "r")
        if self.file:
            self.createHeader(oauth)
            self.createApplicationJson(oauth)
            self.writeLog("Function: ---> uploadFile")
            self.writeLog("URL: " + self.resourceurl + "file/" + str(id) + "/data")
            result = oauth.put(self.resourceurl + "file/" + str(id) + "/data", self.file.read())
            if (not (isinstance(result, dict) and result.has_key('error'))):
                metadata = 'true'
            elif result['error'] == 403:
                metadata = result['error']
            self.file.close()
        return metadata

    def downloadFile(self, oauth, id, path):
        metadata = 'false'
        self.createHeader(oauth)
        self.writeLog("Function: ---> downloadFile")
        self.writeLog("URL: " + self.resourceurl + "file/" + str(id) + "/data")
        result = oauth.get(self.resourceurl + "file/" + str(id) + "/data")
        #if type(result) is str or type(result) is bin:
        if isinstance(result, types.StringTypes):
            file = open(path, 'w')
            if file:
                file.write(result)
                file.close()
            metadata = 'true'
        elif isinstance(result, dict) and result.has_key('error') and result['error'] == 403:
            metadata = result['error']
        return metadata

    def deleteMetadata(self, oauth, file, id):
        url = self.getUrl(file, id)
        self.writeLog("Function: ---> deleteMetadata")
        self.writeLog("URL: " + url)
        self.createHeader(oauth)
        result = oauth.delete(url)
        return self.createRequest(result)

    def getFileVersions(self, oauth, id):
        url = self.getUrl(True, id)
        url += "/versions"
        self.writeLog("Function: ---> getFileVersions")
        self.writeLog("URL: " + url)
        self.createHeader(oauth)
        result = oauth.get(url)
        return self.createRequest(result)

    def getFileVersionData(self, oauth, id, version, path):
        metadata = 'false'
        self.createHeader(oauth)
        self.writeLog("Function: ---> getFileVersionData")
        self.writeLog("URL: " + self.resourceurl + "file/" + str(id) + "/version/" + str(version) + "/data")
        result = oauth.get(self.resourceurl + "file/" + str(id) + "/version/" + str(version) + "/data")
        if isinstance(result, types.StringTypes):
            file = open(path, 'w')
            if file:
                file.write(result)
                file.close()
            metadata = 'true'
        elif isinstance(result, dict) and result.has_key( 'error' ) and result[ 'error' ] == 403:
            metadata = result[ 'error' ]
        return metadata

    def getListUsersShare(self, oauth, id):
        url = self.getUrl(False, id)
        url += "/members"
        self.writeLog("Function: ---> getListUsersShare")
        self.writeLog("URL: " + url)
        self.createHeader(oauth)
        result = oauth.get(url)
        return self.createRequest(result)

    def shareFolder(self, oauth, id, list, isShared):
        url = self.getUrl(False, id)
        if isShared == True:
            url += "/unshare"
            self.writeLog("Function: ---> unShareFolder")
        else:
            url += "/share"
            self.writeLog("Function: ---> shareFolder")

        self.writeLog("URL: " + url)
        self.createHeader(oauth)
        self.createApplicationJson(oauth)
        result = oauth.post(url, json.dumps(list))
        if len(result) == 0:
            return 'true'
        else:
            return self.createRequest(result)

    def insertComment(self,oauth,id,user,text,cloud):
        url = self.resourceurl + 'comment'
        self.writeLog("Function: ---> insertComment")
        self.writeLog("URL: " + url)
        self.createApplicationJson(oauth)
        data = {}
        data['id'] = id;
        data['user'] = user
        data['text'] = text
        data['cloud'] = cloud
        result = oauth.post(url, data)
        return self.createRequest(result)

    def deleteComment(self,oauth,id,user,cloud,time_created):
        url = self.resourceurl + 'comment/' + id + '/' + user + '/' + cloud + '/' + time_created
        self.writeLog("Function: ---> deleteComment")
        self.writeLog("URL: " + url)
        result = oauth.delete(url)
        return self.createRequest(result)

    def getComments(self,oauth,id,cloud,interop=None):
        url = self.resourceurl + 'comment/' + id + '/' + cloud
        if interop != None:
            url += '/' + interop

        self.writeLog("Function: ---> getComments")
        self.writeLog("URL: " + url)
        result = oauth.get(url)
        return self.createRequest(result)

    def insertEvent(self,oauth,user,calendar,cloud,isallday,timestart,timeend,repetition,finaltype,
                    finalvalue,subject,location,description,repeattype):
        url = self.resourceurl + 'event'
        self.writeLog("Function: ---> insertEvent")
        self.writeLog("URL: " + url)
        data = {"user":user,"calendar":calendar,"cloud":cloud,"isallday":isallday,"timestart":timestart,"timeend":timeend,"repetition":repetition,"finaltype":finaltype,"finalvalue":finalvalue,"subject":subject,"location":location,"description":description,"repeattype":repeattype}
        result = oauth.post(url,data)
        return self.createRequest(result)

    def deleteEvent(self,oauth,user,calendar,cloud,timestart,timeend,isallday):
        url = self.resourceurl + 'event/' + user + '/' + calendar + '/' + cloud + '/' + timestart + '/' + timeend + '/' + str(isallday)
        self.writeLog("Function: ---> deleteEvent")
        self.writeLog("URL: " + url)
        result = oauth.delete(url)
        return self.createRequest(result)

    def updateEvent(self,oauth,user,calendar,cloud,isallday,timestart,timeend,repetition,finaltype,
                    finalvalue,subject,location,description,repeattype):
        url = self.resourceurl + 'event'
        self.writeLog("Function: ---> updateEvent")
        self.writeLog("URL: " + url)
        data = {"user":user,"calendar":calendar,"cloud":cloud,"isallday":isallday,"timestart":timestart,"timeend":timeend,"repetition":repetition,"finaltype":finaltype,"finalvalue":finalvalue,"subject":subject,"location":location,"description":description,"repeattype":repeattype}
        result = oauth.put(url,data)
        return self.createRequest(result)

    def getEvents(self,oauth,user,calendar,cloud):
        url = self.resourceurl + 'event/' + user + '/' + calendar + '/' + cloud
        self.writeLog("Function: ---> getEvents")
        self.writeLog("URL: " + url)
        result = oauth.get(url)
        return self.createRequest(result)

    def insertCalendar(self,oauth,user,name,cloud,description,timezone):
        url = self.resourceurl + 'calendar'
        self.writeLog("Function: ---> insertCalendar")
        self.writeLog("URL: " + url)
        data = {"user":user,"name":name,"cloud":cloud,"description":description,"timezone":timezone}
        result = oauth.post(url,data)
        return self.createRequest(result)

    def deleteCalendar(self,oauth,user,name,cloud):
        url = self.resourceurl + 'calendar/' + user + '/' + name + '/' + cloud
        self.writeLog("Function: ---> deleteCalendar")
        self.writeLog("URL: " + url)
        result = oauth.delete(url)
        return self.createRequest(result)

    def updateCalendar(self,oauth,user,name,cloud,description,timezone):
        url = self.resourceurl + 'calendar'
        self.writeLog("Function: ---> updateCalendar")
        self.writeLog("URL: " + url)
        data = {"user":user,"name":name,"cloud":cloud,"description":description,"timezone":timezone}
        result = oauth.put(url,data)
        return self.createRequest(result)

    def getCalendars(self,oauth,user,cloud):
        url = self.resourceurl + 'calendar/' + user + '/' + cloud
        self.writeLog("Function: ---> getCalendars")
        self.writeLog("URL: " + url)
        result = oauth.get(url)
        return self.createRequest(result)

    def getCalendarsAndEvents(self,oauth,user,cloud):
        url = self.resourceurl + 'calEvents/' + user + '/' + cloud
        self.writeLog("Function: ---> getCalendarsAndEvents")
        self.writeLog("URL: " + url)
        result = oauth.get(url)
        return self.createRequest(result)

    def deleteCalendarsUser(self,oauth,user,cloud):
        url = self.resourceurl + 'calUser/' + user + '/' + cloud
        self.writeLog("Function: ---> deleteCalendarsUser")
        self.writeLog("URL: " + url)
        result = oauth.delete(url)
        return self.createRequest(result)

    def lockFile(self,oauth,id,cloud,user,ipserver,datetime,timelimit,interop=None):
        url = self.resourceurl + 'lockFile'
        self.writeLog("Function: ---> lockFile")
        self.writeLog("URL: " + url)
        self.createApplicationJson(oauth)
        data = {"id":id,"cloud":cloud,"user":user,"ipserver":ipserver,"datetime":datetime,"timelimit":timelimit}
        if interop != None:
            data['interop'] = interop
        result = oauth.post(url,data)
        return self.createRequest(result)

    def updateDateTime(self,oauth,id,cloud,user,ipserver,datetime):
        url = self.resourceurl + 'updateTime'
        self.writeLog("Function: ---> updateDateTime")
        self.writeLog("URL: " + url)
        self.createApplicationJson(oauth)
        data = {"id":id,"cloud":cloud,"user":user,"ipserver":ipserver,"datetime":datetime}
        result = oauth.put(url,data)
        return self.createRequest(result)

    def unLockFile(self,oauth,id,cloud,user,ipserver,datetime):
        url = self.resourceurl + 'unLockFile'
        self.writeLog("Function: ---> unLockFile")
        self.writeLog("URL: " + url)
        self.createApplicationJson(oauth)
        data = {"id":id,"cloud":cloud,"user":user,"ipserver":ipserver,"datetime":datetime}
        result = oauth.put(url,data)
        return self.createRequest(result)

    def getMetadataFile(self,oauth,id,cloud,interop=None):
        url = self.resourceurl + 'lockFile/' + id + '/' + cloud
        if interop != None:
            url += '/' + interop
        self.writeLog("Function: ---> getMetadataFile")
        self.writeLog("URL: " + url)
        result = oauth.get(url)
        return self.createRequest(result)

    def createHeader(self, oauth):
        oauth.headers['StackSync-API'] = self.version

    def createApplicationJson(self, oauth):
         oauth.headers['Content-Type'] = 'application/json'

    def getUrl(self, file, id=None, contents=None):
        url = self.resourceurl

        if file == True:
            url += "file"
        else:
            url += "folder"

        if id != None:
            url += "/" + str(id)

        if contents:
            url += "/contents"

        return url

    def replaceNull(self, data):
        for i, j in data.items():
            if j == None:
                data[i] = "null"

        if data.has_key('contents'):
            for file in data['contents']:
                self.replaceNull(file)

        if data.has_key('versions'):
            for file in data['versions']:
                self.replaceNull(file)

        return data

    def replaceNullArray(self, data):
        for version in data:
            for i, j in version.items():
                if j == None:
                    version[i] = "null"
        return data

    def createRequest(self, result):
        if not (isinstance(result, dict)):
            result = json.loads(result)

        metadata = 'false'
        if isinstance(result, dict):
            if not result.has_key("error"):
                metadata = json.dumps(self.replaceNull(result))
            elif result['error'] == 403:
                metadata = result['error']

        if isinstance(result, list):
            metadata = json.dumps(result)

        return metadata

    def writeLog(self,message):
        if self.logger != None:
            logger.info(message)

if __name__ == "__main__":
    if settings[ 'NEW_CODE' ] == "true":
        result = None

        if len(sys.argv) == 2:
            params = json.loads(str(sys.argv[1]))

            if params.has_key( 'config' ) and params[ 'config' ].has_key( 'cloud' ):
                cloud = params[ 'config' ][ 'cloud' ]

                if settings[ 'Clouds' ].has_key( cloud ):
                    settingsCloud = settings[ 'Clouds' ][ cloud ]
                    if params[ 'config' ].has_key( 'type' ):
                        type = params[ 'config' ][ 'type' ]
                        if type == "oauthUrl":
                            result = json.dumps(settingsCloud[ 'urls' ][ 'OAUTH_URL' ])
                        elif type == "controlVersion":
                            result = json.dumps({"controlVersion": "" + settingsCloud['controlVersion'] + ""})
                        elif type == "comments":
                            result = json.dumps({"comments": "" + settingsCloud['comments'] + ""})
                        elif type == "calendar":
                            result = json.dumps({"calendar": "" + settingsCloud['calendar'] + ""})

                    else:
                        key = settingsCloud[ 'consumer' ][ 'key' ]
                        secret = settingsCloud[ 'consumer' ][ 'secret' ]
                        resource_url = settingsCloud[ 'urls' ][ 'RESOURCE_URL' ]
                        callbackUrl = settingsCloud[ 'urls' ][ 'CALLBACK_URL' ]

                        logger = Logger()
                        logger.openLog(cloud)

                        if params[ 'config' ].has_key( 'resource_url' ):
                            resource_url = params['config']['resource_url']
                            if params['config'].has_key('consumer_key') and params['config'].has_key('consumer_secret'):
                                key = params['config']['consumer_key']
                                secret = params['config']['consumer_secret']

                            if settingsCloud.has_key('interop') and settingsCloud['interop'].has_key('consumer'):
                                key = settingsCloud['interop']['consumer']['key']
                                secret = settingsCloud['interop']['consumer']['secret']

                        oauthCredentials = OauthCredentials(settingsCloud[ 'urls' ][ 'REQUEST_TOKEN_URL' ],
                                                            settingsCloud[ 'urls' ][ 'ACCESS_TOKEN_URL' ],
                                                            resource_url,
                                                            settingsCloud[ 'version' ],logger)

                        if params.has_key( 'verifier' ) and params.has_key( 'token' ):
                            token_key = params['token']['key']
                            token_secret = params['token']['secret']
                            verifier = params['verifier']
                            oauth = OAuthRequest(key, client_secret=secret, resource_owner_key=token_key, resource_owner_secret=token_secret, verifier=verifier, signature_method=SIGNATURE_PLAINTEXT)
                            oauth.setLogger(logger)
                            result = oauthCredentials.getAccessToken(oauth)

                        elif params.has_key( 'metadata' ) and params.has_key('token'):
                            token_key = params[ 'token' ][ 'key' ]
                            token_secret = params[ 'token' ][ 'secret' ]
                            metadata = params[ 'metadata' ]
                            type = metadata[ 'type' ]

                            interop = None
                            if metadata.has_key('interop'):
                                interop = metadata['interop']

                            oauth = OAuthRequest(key, client_secret=secret, resource_owner_key=token_key, resource_owner_secret=token_secret)
                            oauth.setLogger(logger)
                            logger.info('Start call')
                            logger.info("access_token_key:" + token_key)
                            logger.info("access_token_secret:" + token_secret)
                            logger.info("consumer_key:" + key)
                            logger.info("consumer_secret:" + secret)

                            if type == 'get':
                                result = oauthCredentials.getMetadata(oauth, metadata[ 'file' ], metadata[ 'id' ], metadata[ 'contents' ])
                            elif type == "create":
                                result = oauthCredentials.createMetadata(oauth, metadata[ 'file' ], metadata[ 'filename' ], metadata[ 'parent_id' ], metadata[ 'path' ])
                            elif type == 'delete':
                                result = oauthCredentials.deleteMetadata(oauth, metadata[ 'file' ], metadata[ 'id' ])
                            elif type == "update":
                                result = oauthCredentials.updateMetadata(oauth, metadata[ 'file' ], metadata[ 'id' ], metadata[ 'filename' ], metadata[ 'parent_id' ])
                            elif type == 'download':
                                result = oauthCredentials.downloadFile(oauth, metadata[ 'id' ], metadata[ 'path' ])
                            elif type == 'upload':
                                result = oauthCredentials.uploadFile(oauth, metadata[ 'id' ], metadata[ 'path' ])
                            elif type == 'listVersions':
                                result = oauthCredentials.getFileVersions(oauth, metadata[ 'id' ])
                            elif type == "getFileVersion":
                                result = oauthCredentials.getFileVersionData(oauth, metadata[ 'id' ], metadata[ 'version' ], metadata[ 'path' ])
                            elif type == "shareFolder":
                                result = oauthCredentials.shareFolder(oauth, metadata[ 'id' ], metadata[ 'list' ], metadata[ 'shared' ])
                            elif type == "listUsersShare":
                                result = oauthCredentials.getListUsersShare(oauth, metadata[ 'id' ])
                            elif type == "insertComment":
                                result = oauthCredentials.insertComment(oauth,metadata['id'],metadata['user'],metadata['text'],cloud)
                            elif type == "deleteComment":
                                result = oauthCredentials.deleteComment(oauth,metadata['id'],metadata['user'],cloud,metadata['time_created'])
                            elif type == "getComments":
                                result = oauthCredentials.getComments(oauth,metadata['id'],cloud,interop)
                            elif type == "insertEvent":
                                result = oauthCredentials.insertEvent(oauth,metadata['user'],metadata['calendar'],cloud,metadata['isallday'],
                                                                      metadata['timestart'],metadata['timeend'],metadata['repetition'],metadata['finaltype'],
                                                                      metadata['finalvalue'],metadata['subject'],metadata['location'],metadata['description'],metadata['repeattype'])
                            elif type == "deleteEvent":
                                result = oauthCredentials.deleteEvent(oauth,metadata['user'],metadata['calendar'],cloud,metadata['timestart'],metadata['timeend'],metadata['isallday'])
                            elif type == "updateEvent":
                                result = oauthCredentials.updateEvent(oauth,metadata['user'],metadata['calendar'],cloud,metadata['isallday'],
                                                                      metadata['timestart'],metadata['timeend'],metadata['repetition'],metadata['finaltype'],
                                                                      metadata['finalvalue'],metadata['subject'],metadata['location'],metadata['description'],metadata['repeattype'])
                            elif type == "getEvents":
                                result = oauthCredentials.getEvents(oauth,metadata['user'],metadata['calendar'],cloud)
                            elif type == "insertCalendar":
                                result = oauthCredentials.insertCalendar(oauth,metadata['user'],metadata['name'],cloud,metadata['description'],metadata['timezone'])
                            elif type == "deleteCalendar":
                                result = oauthCredentials.deleteCalendar(oauth,metadata['user'],metadata['name'],cloud)
                            elif type == "updateCalendar":
                                result = oauthCredentials.updateCalendar(oauth,metadata['user'],metadata['name'],cloud,metadata['description'],metadata['timezone'])
                            elif type == "getCalendars":
                                result = oauthCredentials.getCalendars(oauth,metadata['user'],cloud)
                            elif type == "getCalendarsAndEvents":
                                result = oauthCredentials.getCalendarsAndEvents(oauth,metadata['user'],cloud)
                            elif type == "deleteCalendarsUser":
                                result = oauthCredentials.deleteCalendarsUser(oauth,metadata['user'],cloud)
                            elif type == "lockFile":
                                result = oauthCredentials.lockFile(oauth,metadata['id'],cloud,metadata['user'],metadata['ipserver'],metadata['datetime'],metadata['timelimit'],interop)
                            elif type == "updateDateTime":
                                result = oauthCredentials.updateDateTime(oauth,metadata['id'],cloud,metadata['user'],metadata['ipserver'],metadata['datetime'])
                            elif type == "unLockFile":
                                result = oauthCredentials.unLockFile(oauth,metadata['id'],cloud,metadata['user'],metadata['ipserver'],metadata['datetime'])
                            elif type == "getMetadataFile":
                                result = oauthCredentials.getMetadataFile(oauth,metadata['id'],cloud,interop)

                        elif not(params.has_key( 'metadata' ) or params.has_key( 'verifier' ) or params.has_key( 'token' )):
                            oauth = OAuthRequest(key, client_secret=secret, callback_uri=callbackUrl, signature_method=SIGNATURE_PLAINTEXT)
                            oauth.setLogger(logger)
                            result = oauthCredentials.getRequestToken(oauth)

            if params.has_key( 'config' ) and params[ 'config' ].has_key( 'type' ):
                type = params[ 'config' ][ 'type' ]

                if type == "cloudsList":
                    result = json.dumps(settings[ 'Clouds' ].keys())

    else:
        oauthCredentials = OauthCredentials(settings['Clouds']['Stacksync']['urls']['REQUEST_TOKEN_URL'],
                                            settings['Clouds']['Stacksync']['urls']['ACCESS_TOKEN_URL'],
                                            settings['Clouds']['Stacksync']['urls']['RESOURCE_URL'],
                                            settings['Clouds']['Stacksync']['version'])
        key = settings['Clouds']['Stacksync']['consumer']['key']
        secret = settings['Clouds']['Stacksync']['consumer']['secret']
        callbackurl = settings['Clouds']['Stacksync']['urls']['CALLBACK_URL']
        result = None

        if len(sys.argv) == 2:
            params = json.loads(str(sys.argv[1]))
            if params.has_key('metadata') and params.has_key('token'):
                token_key = params['token']['key']
                token_secret = params['token']['secret']
                metadata = params['metadata']
                type = metadata['type']
                oauth = OAuthRequest(key, client_secret=secret, resource_owner_key=token_key,
                                     resource_owner_secret=token_secret)
                if type == "get":
                    result = oauthCredentials.getMetadata(oauth, metadata['file'], metadata['id'], metadata['contents'])
                elif type == "update":
                    result = oauthCredentials.updateMetadata(oauth, metadata['file'], metadata['id'], metadata['filename'],
                                                             metadata['parent_id'])
                elif type == "create":
                    result = oauthCredentials.createMetadata(oauth, metadata['file'], metadata['filename'],
                                                             metadata['parent_id'], metadata['path'])
                elif type == 'upload':
                    result = oauthCredentials.uploadFile(oauth, metadata['id'], metadata['path'])
                elif type == 'download':
                    result = oauthCredentials.downloadFile(oauth, metadata['id'], metadata['path'])
                elif type == 'delete':
                    result = oauthCredentials.deleteMetadata(oauth, metadata['file'], metadata['id'])
                elif type == 'listVersions':
                    result = oauthCredentials.getFileVersions(oauth, metadata['id'])
                elif type == "getFileVersion":
                    result = oauthCredentials.getFileVersionData(oauth, metadata['id'], metadata['version'],
                                                                 metadata['path'])
                elif type == "listUsersShare":
                    result = oauthCredentials.getListUsersShare(oauth, metadata['id'])
                elif type == "shareFolder":
                    result = oauthCredentials.shareFolder(oauth, metadata['id'], metadata['list'])
            elif params.has_key("verifier") and params.has_key('token'):
                token_key = params['token']['key']
                token_secret = params['token']['secret']
                verifier = params['verifier']
                oauth = OAuthRequest(key, client_secret=secret, resource_owner_key=token_key,
                                     resource_owner_secret=token_secret, verifier=verifier,
                                     signature_method=SIGNATURE_PLAINTEXT)
                result = oauthCredentials.getAccessToken(oauth)
        elif len(sys.argv) == 1:
            oauth = OAuthRequest(key, client_secret=secret, callback_uri=callbackurl, signature_method=SIGNATURE_PLAINTEXT)
            result = oauthCredentials.getRequestToken(oauth)
    if result:
        if type != 'download':
            print(str(result))
        else:
            print(result)
    else:
        print('false')