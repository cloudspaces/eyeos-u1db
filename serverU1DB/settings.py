settings = {
	"MongoDb":{
		"host":"localhost",
		"port":27017,
		"name":"oauth"
	},
	"Server":{
		"host":"192.168.3.118",
		"port":8080
	},
	"Urls":{
		"REQUEST_TOKEN_URL":"/request_token",
		"AUTHORIZATION_URL":"/authorize",
		"ACCESS_TOKEN_URL":"/access_token",
		"CALLBACK_URL":"http://localhost:8080/request_token_ready",
		"RESOURCE_URL":"http://localhost:9000"
	},
	"VERIFIER":"verifier",
	"token": {
		"expires":86400
	},
    "U1DB": {
        "path":"/var/lib/u1db/"
    }
}