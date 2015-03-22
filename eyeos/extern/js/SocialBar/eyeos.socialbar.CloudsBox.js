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

qx.Class.define('eyeos.socialbar.CloudsBox', {
    extend: qx.ui.container.Composite,
    implement: eyeos.socialbar.ISocialBox,

    properties: {
        name: {
            check: 'String',
            init: null
        },
        checknum: {
            check: 'Integer'
        }
    },

    /**
     * Constructor of a CloudsBox
     *
     */
    construct: function (clouds, controller) {
        this._controller = controller;
        this.base(arguments);
        this.set({
            marginTop: 20,
            marginLeft: 10,
            marginRight: 10,
            layout: new qx.ui.layout.HBox(),
            decorator: null
        });
        this._buildGui(clouds);
    },

    members: {
        _layoutCloudsBox: null,
        _imageCheck: 'index.php?extern=images/eyefiles/checkmark.png',
        _controller: null,

        /**
         * Create the View of a CloudsBox
         *
         * @param clouds
         */
        _buildGui: function (clouds) {
            this._buildCloudsBox(clouds);
        },

        /**
         * Create the View of the information Container
         *
         * @param clouds
         */
        _buildCloudsBox: function (clouds) {
            this._layoutCloudsBox = new qx.ui.container.Composite().set({
                allowGrowX: false,
                allowGrowY: true,
                layout: new qx.ui.layout.VBox()
            });
            self = this;

            this.add(this._layoutCloudsBox, {flex: 1});

            var titleLabel = new qx.ui.basic.Label().set({
                textColor: '#333333',
                value: "Status cloudspaces:",
                font: new qx.bom.Font(14).set({
                    family: ["Helvetica", "Arial", "Lucida Grande"],
                    bold: true
                })
            });
            this._layoutCloudsBox.add(titleLabel);

            for (var i = 0; i < clouds.length; i++) {

                var layoutCloud = new qx.ui.container.Composite().set({
                    allowGrowX: false,
                    allowGrowY: true,
                    layout: new qx.ui.layout.HBox()
                });

                var linkCloud = new qx.ui.basic.Label().set({
                    value: clouds[i],
                    rich: true,
                    padding: 0,
                    margin: 0,
                    maxWidth: 190,
                    width: 190,
                    minWidth: 180
                });
                linkCloud.addListener('mouseover', function () {
                    this._layoutCloudsBox.setCursor('pointer');
                }, this);
                linkCloud.addListener('mouseout', function () {
                    this._layoutCloudsBox.setCursor('default');
                }, this);
                linkCloud.addListener('click', function (e) {
                    self._controller.createDialogueCloud(e.getTarget().getValue());
                }, this);
                layoutCloud.add(linkCloud);

                //TODO Validate if cloud is configured
                if (true) {
                    var image = new qx.ui.basic.Image(this._imageCheck).set({
                        allowGrowX: false,
                        allowGrowY: false,
                        alignX: 'right',
                        alignY: 'middle'
                    });
                    layoutCloud.add(image);
                }

                this._layoutCloudsBox.add(layoutCloud);
            }

        }
    }
});