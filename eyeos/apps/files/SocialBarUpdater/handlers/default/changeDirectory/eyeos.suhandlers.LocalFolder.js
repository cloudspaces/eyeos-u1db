/*
*                 eyeos - The Open Source Cloud's Web Desktop
*                               Version 2.0
*                   Copyright (C) 2007 - 2010 eyeos Team
*
* This program is free software; you can redistribute it and/or modify it under
* the terms of the GNU Affero General Public License version 3 as published by the
* Free Software Foundation.
*
* This program is distributed in the hope that it will be useful, but WITHOUT
* ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
* FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
* details.
*
* You should have received a copy of the GNU Affero General Public License
* version 3 along with this program in the file "LICENSE".  If not, see
* <http://www.gnu.org/licenses/agpl-3.0.txt>.
*
* See www.eyeos.org for more details. All requests should be sent to licensing@eyeos.org
*
* The interactive user interfaces in modified source and object code versions
* of this program must display Appropriate Legal Notices, as required under
* Section 5 of the GNU Affero General Public License version 3.
*
* In accordance with Section 7(b) of the GNU Affero General Public License version 3,
* these Appropriate Legal Notices must retain the display of the "Powered by
* eyeos" logo and retain the original copyright notice. If the display of the
* logo is not reasonably feasible for technical reasons, the Appropriate Legal Notices
* must display the words "Powered by eyeos" and retain the original copyright notice.
*/
qx.Class.define('eyeos.suhandlers.LocalFolder', {
	extend: eyeos.files.ASocialBarHandler,
	implement: [eyeos.files.ISocialBarHandler],

	statics: {
		checkHandler: function (params) {
			return eyeos.files.SUPathManager.isLocalPath(params['path']);
		}
	},
	members: {
        _controller: null,
		updateSocialBar: function (controller) {
            this._controller = controller;
            var path = this.getParams().path;
            if(this._controller && this._controller.isRootCloudSpaces(path)) {
                this.getSocialBar().createCloudSpacesTabs();
                this._createContentCloudSpacesTab();
            }else {
                this.getSocialBar().createDefaultTabs();
            }
			this.getSocialBar().removeTab('Share');
		},
        _createContentCloudSpacesTab: function() {
            /*eyeos.callMessage(this.getParams()['checknum'], 'getCloudsList', null, function (clouds) {
                // Update socialbar handlers data struct
                this._cloudsBox = new eyeos.socialbar.CloudsBox(clouds, this._controller);

                //Add to Socialbar
                if(this.getSocialBar().getTab('Clouds')) {
                    this.getSocialBar().getTab('Clouds').removeAll();
                    this.getSocialBar().getTab('Clouds').addBox(this._cloudsBox, 'cloudBox');
                }
            }, this);*/

            this._cloudsBox = new eyeos.socialbar.CloudsBox(this._controller,this.getParams()['checknum']);
            this.getSocialBar().getTab('Clouds').addBox(this._cloudsBox, 'cloudBox');
        }
	}
});