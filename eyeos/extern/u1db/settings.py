__author__ = 'root'

settings = {
    "Oauth": {
        "server": "192.168.100.33",
        "port": 8080,
        "urls": {
            "REQUEST_TOKEN_URL": "/request_token",
            "ACCESS_TOKEN_URL": "/access_token",
            "AUTHORIZATION_URL": "/authorize",
            "CALLBACK_URL": "http://192.168.100.33:8080/request_token_ready"
        },
        "consumer": {
            "key": "keySebas",
            "secret":"secretSebas"
        },
        "verifier":"verifier",
        "sync": "http://"
    },
    "Clouds":{
        "Stacksync": {
            "urls": {
                "REQUEST_TOKEN_URL": "http://api.stacksync.com:8080/oauth/request_token",
                "ACCESS_TOKEN_URL": "http://api.stacksync.com:8080/oauth/access_token",
                "CALLBACK_URL": "http://cloudspaces1.eyeosbcn.com",
                "RESOURCE_URL": "http://api.stacksync.com:8080/v1/",
                "OAUTH_URL": "http://api.stacksync.com:8080/oauth/authorize?oauth_token="
            },
            "consumer": {
                "key": "8224c4148302d09e287ba35fe96f214e0ac5b3c5c",
                "secret" : "f88a48225a02542ec720dc18cba36023"
            },
            "interop": {
              "consumer": {
                  "key": "b3af4e669daf880fb16563e6f36051b105188d413",
                  "secret": "c168e65c18d75b35d8999b534a3776cf"
              }
            },
            "version": "v2",
            "controlVersion": "false",
            "comments": "true",
            "calendar": "true",
            "log": "/var/log/Cloudspaces"
        },
        "NEC": {
            "urls": {
                "REQUEST_TOKEN_URL": "http://csdev.neccloudhub.com:1080/oauth/request_token",
                "ACCESS_TOKEN_URL": "http://csdev.neccloudhub.com:1080/oauth/access_token",
                "CALLBACK_URL": "http://cloudspaces1.eyeosbcn.com",
                "RESOURCE_URL": "http://cs.neccloudhub.com:2080/api/cloudspaces/",
                "OAUTH_URL": "http://csdev.neccloudhub.com:1080/oauth/Authorize.aspx?oauth_token="
            },
            "consumer": {
                "key": "8224c4148302d09e287ba35fe96f214e0ac5b3c5c",
                "secret" : "f88a48225a02542ec720dc18cba36023"
            },
            "interop": {
              "consumer": {
                  "key": "b3af4e669daf880fb16563e6f36051b105188d413",
                  "secret": "c168e65c18d75b35d8999b534a3776cf"
              }
            },
            "version": "v2",
            "controlVersion": "false",
            "comments": "true",
            "calendar": "true",
            "log": "/var/log/Cloudspaces"
        }
    },
    "NEW_CODE": "true"
}
