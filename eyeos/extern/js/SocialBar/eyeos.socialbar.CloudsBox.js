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


    events: {
        /**
         * Fired when a user change the rating
         */
        changeRating: 'qx.event.type.Data'
    },

    /**
     * Constructor of a CloudsBox
     *
     */
    construct: function (clouds) {
        this.base(arguments);
        this.set({
            marginTop: 20,
            marginLeft: 10,
            marginRight: 10,
            layout: new qx.ui.layout.HBox(),
            decorator: null
        });
        //if (Info instanceof eyeos.socialbar.Info){
            this._buildGui(clouds);
        //}
    },

    members: {
        _layoutImageBox: null,
        _layoutImage: null,
        _layoutCloudsBox: null,
        _layoutRatingBox: null,
        _emptyStar: 'index.php?extern=images/rate_off.png',
        _fullStar: 'index.php?extern=images/rate_on.png',

        /**
         * Create the View of a CloudsBox
         *
         * @param Info {Info} Info Object with all the related information
         */
        _buildGui: function (clouds) {
            //this._buildImageBox(Info);
            this._buildCloudsBox(clouds);

            /*if (Info.getEnableRating()){
                this._buildRatingBox(Info);
            }*/
        },

        /**
         * Create the View for the Image container (just the image)
         *
         * @param Info {Info} Info Object with all the related information
         */
        _buildImageBox: function (Info) {
            this._layoutImageBox = new eyeos.ui.widgets.Image(Info.getImage()).set({
                width: 70,
                height: 70,
                marginRight: 12,
                scale: true,
                forceRatio: 'auto'
            });
            this.add(this._layoutImageBox);
        },

        /**
         * Create the View of the information Container (info and rating system
         * if enabled)
         *
         * @param Info {Info} Info Object with all the related information
         */
        _buildCloudsBox: function (clouds) {
            this._layoutCloudsBox = new qx.ui.container.Composite().set({
                allowGrowX: false,
                allowGrowY: true,
                layout: new qx.ui.layout.VBox()
            });

            this.add(this._layoutCloudsBox, {flex: 1});

            var titleLabel = new qx.ui.basic.Label().set({
                textColor: '#333333',
                value: "Status cloudspaces:",
                font: new qx.bom.Font(14).set({
                    family: ["Helvetica", "Arial", "Lucida Grande"],
                    bold: true
                }),
                margin: 2
            });
            this._layoutCloudsBox.add(titleLabel);
            var blackHtml = '<span style=\'text-align:left; font-family: "Helvetica", "Arial", "Lucida Grande"; font-size: 12px; color: #666666\'; margin: 0; padding: 0\'>';

            for ( var i = 0; i < clouds.length; i++){
                this._layoutCloudsBox.add(new qx.ui.basic.Label().set({
                    value: blackHtml + clouds[i]+'</span>',
                    rich: true,
                    padding: 0,
                    margin: 0
                }));
                this._layoutCloudsBox.addListener('mouseover', function(){
                    this._layoutCloudsBox.setCursor('pointer');
                }, this);
                this._layoutCloudsBox.addListener('mouseout', function(){
                    this._layoutCloudsBox.setCursor('default');
                }, this);
                this._layoutCloudsBox.addListener('click', function(e){
                    console.log(e.getTarget().getValue());
                }, this);
            }

        }
    }
});