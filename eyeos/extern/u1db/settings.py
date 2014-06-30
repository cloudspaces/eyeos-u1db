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
    "Stacksync": {
        "urls": {
            "REQUEST_TOKEN_URL": "http://api.stacksync.com:8080/oauth/request_token",
            "ACCESS_TOKEN_URL": "http://api.stacksync.com:8080/oauth/access_token",
            "CALLBACK_URL": "http://sebasvm.eyeosbcn.com",
            "RESOURCE_URL": "http://api.stacksync.com:8080/v1/"
        },
        "consumer": {
            "key": "8224c4148302d09e287ba35fe96f214e0ac5b3c5c",
            "secret" : "f88a48225a02542ec720dc18cba36023"
        },
        "version": "v2"
    }
}
