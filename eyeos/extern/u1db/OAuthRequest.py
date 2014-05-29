__author__ = 'root'

from requests_oauthlib import OAuth1Session
from oauthlib.common import urldecode

class OAuthRequest(OAuth1Session):

    def _fetch_token(self, url):
        token = dict(urldecode(self.get(url).text))
        self._populate_attributes(token)
        return token
