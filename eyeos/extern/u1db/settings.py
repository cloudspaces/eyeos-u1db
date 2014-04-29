__author__ = 'root'

settings = {
    "Oauth": {
        "server": "192.168.3.118",
        "port": 8080,
        "urls": {
            "REQUEST_TOKEN_URL": "/request_token",
            "ACCESS_TOKEN_URL": "/access_token",
            "AUTHORIZATION_URL": "/authorize",
            "CALLBACK_URL": "http://192.168.3.118:8080/request_token_ready"
        },
        "consumer": {
            "key": "keySebas",
            "secret":"secretSebas"
        },
        "verifier":"verifier",
        "sync": "http://"
    }
}
