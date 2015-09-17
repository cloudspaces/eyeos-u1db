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
qx.Class.define('eyeos.suhandlers.LocalFile', {
	extend: eyeos.files.ASocialBarHandler,
	implement: [eyeos.files.ISocialBarHandler],

	statics: {
		checkHandler: function (params) {
			return eyeos.files.SUPathManager.isLocalPath(params['path']);
		}
	},
	members: {
		_folderPreviewImage: 'index.php?extern=images/64x64/places/folder.png',
		_otherPreviewImage: 'index.php?extern=images/64x64/actions/document-preview.png',
		_multiplePreviewImage: 'index.php?extern=images/64x64/actions/document-multiple-preview.png',

		__shareds: null,		// Array of eyeos.socialbar.Shared

		_infoBox: null,
		_cloudsBox: null,
		_sharedWithBox: null,
		_sharedBox: null,
		_urlBox: null,
		_urlBoxImage: 'index.php?extern=images/22x22/categories/applications-internet.png',
		_activityBox: null,
        _commentsBox: null,
        _headerComments: null,
        _controller: null,
        _file: null,
        _timer: null,
        _timerVersion: null,

		updateSocialBar: function (controller) {
            this._controller = controller;
            this._file = this.getParams()['selected'][0];
			var fileType = this._file.getType();
            var path = this.getParams()['selected'][0].getAbsolutePath();
            var stacksync = false;
            var cloud = this._controller.isCloud(path);
            if(this._controller && this._controller.__isStacksync(path) && path !== 'home://~'+ eyeos.getCurrentUserName()+'/Stacksync') {
                stacksync = true;
                this.getSocialBar().createStackSyncTabs();
                this.__createContentsCommentsTab();
            } else if(this._controller && this._controller.isRootCloudSpaces(path)) {
                this.getSocialBar().createCloudSpacesTabs();
                this._createContentCloudSpacesTab();
            } else {
                this.getSocialBar().createDefaultTabs();
            }

			this._createContentInfoTab();

			if (fileType == 'folder' && this.getParams()['selected'].length == 1) {
				this.getSocialBar().removeTab('Share');
			} else {
				this._createContentShareTab();
			}

            if(cloud.isCloud === true && path !== 'home://~'+ eyeos.getCurrentUserName()+'/Cloudspaces/' + cloud.cloud) {
                if (fileType == 'file') {
                    this._createContenActivityTabCloudSpaces(false, cloud.cloud);
                    //this._controller.enabledCommentsCloud(cloud.cloud);
                    var idParent = this._controller.__getFileIdFolder(this.getParams()['selected'][0].getPath(),cloud.cloud);
                    if(idParent !== null && idParent !== 0) {
                        this.__createCommentsTabSocialBar(cloud.cloud,idParent);
                    }
                } else {
                    this._createContenActivityTabCloudSpaces(true, cloud.cloud);
                    //this._createListUsersActivityTabStacksync();
                }
            } else {
			    this._createContentActivityTab();
            }
			//Show only if the selected file is not a folder
			
			if (fileType != 'folder' && this.getParams()['selected'].length == 1) {
                if(cloud.isCloud === false) {
                    this.getSocialBar().addTab(tr('URL'), this._urlBoxImage, 'white');
                    this._createContentUrlTab();
                }
			}
		},

		_createContentInfoTab: function () {
			this._createInfoBox();
			this._createSharedWithBox();
		},

        _createContentCloudSpacesTab: function () {
            //Contruct the element
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

        },

		_createContentUrlTab: function () {
			var sharedURLS = new eyeos.socialbar.Shared(this.getParams()['selected'][0].getAbsolutePath());
			this._urlBox = new eyeos.socialbar.URLBox(this.getParams()['checknum'], null, [sharedURLS]);
			this.getSocialBar().getTab(tr('URL')).addBox(this._urlBox, 'urlbox');
		},

		_createContentShareTab: function () {
			this._sharedBox = new eyeos.socialbar.SharedBox(this.getParams()['checknum'], this.__shareds);

			this._sharedBox.addListener('deleteShare', function (e) {
				var params = {
					operation: 'Remove',
					userId: e.getData(),
					files: new Array()
				};

				var returnSelected = this.getParams()['selected'];
				for (var j = 0; j < returnSelected.length; ++j) {
					params['files'].push(returnSelected[j].getAbsolutePath());
				}

				this._sharedBox.showLoadingImage(true);

				eyeos.callMessage(this.getParams()['checknum'], '__FileSystem_changePrivilege', params, function (results) {
					var filesArray = new Array();
					var currentPath = this.getParams()['path'];
					var dBus = eyeos.messageBus.getInstance();
					for (var k = 0; k < results.length; ++k) {
						returnSelected[k].setShared(results[k]);
						filesArray.push(returnSelected[k]);
						if (this._sharedBox.getTotalSharesUpdated() >= 1) {
							this._sharedBox.setTotalSharesUpdated(this._sharedBox.getTotalSharesUpdated() - 1);
						}
						if (this._sharedBox.getTotalSharesUpdated() == 0) {
							this._sharedBox.showLoadingImage(false);
//							returnSelected[k].updateImage();
							dBus.send('files', 'update', [currentPath, filesArray]);
							dBus.send('socialbar', 'sharesUpdated', this._sharedBox.getShareds());
						}
					}
				}, this);
			}, this);

			this._sharedBox.addListener('changePrivilege', function (e) {
				var args = e.getData();
				var params = {
					operation: args[1],
					userId: args[0],
					files: new Array()
				};

				var returnSelected = this.getParams()['selected'];
				for (var i = 0; i < returnSelected.length; ++i) {
					var absPath = returnSelected[i].getAbsolutePath();
					if(absPath.substr(0, 12) == 'workgroup://') {
						var optionPane = new eyeos.dialogs.OptionPane(
							"<b>You can't share a file that is inside a wrokgroup. To be able to access this file, the user should be member of the group.</b>",
								eyeos.dialogs.OptionPane.INFORMATION_MESSAGE,
								eyeos.dialogs.OptionPane.DEFAULT_OPTION);
						var dialog = optionPane.createDialog(this, "Unable to share this file", function(result) {
						}, this);
						dialog.open();
						return;
					}
					params['files'].push(absPath);
				}

				this._sharedBox.showLoadingImage(true);

				eyeos.callMessage(this.getParams()['checknum'], '__FileSystem_changePrivilege', params, function (results) {
					var filesArray = new Array();
					var currentPath = this.getParams()['path'];
					var dBus = eyeos.messageBus.getInstance();
					for (var i = 0; i < results.length; ++i) {
						returnSelected[i].setShared(results[i]);
						filesArray.push(returnSelected[i]);
						if (this._sharedBox.getTotalSharesUpdated() >= 1) {
							this._sharedBox.setTotalSharesUpdated(this._sharedBox.getTotalSharesUpdated() - 1);
						}
						if (this._sharedBox.getTotalSharesUpdated() == 0) {
							this._sharedBox.showLoadingImage(false);
//							returnSelected[i].updateImage();
							dBus.send('socialbar', 'sharesUpdated', this._sharedBox.getShareds());
							dBus.send('files', 'update', [currentPath, filesArray]);
						}
					}
				}, this);
			}, this);

			this.getSocialBar().getTab('Share').addBox(this._sharedBox, 'sharebox');
		},


		_createContentActivityTab: function () {
			this._activityBox = new eyeos.socialbar.ActivityBox();
			this.getSocialBar().getTab('Activity').addBox(this._activityBox, 'activityBox');
			var selected = this.getParams()['selected'];
			if (selected.length != 1) {
				return;
			}
			this._activityBox.addListenerOnce('appear', function(e) {
				eyeos.callMessage(this.getParams()['checknum'], '__FileSystem_getFileMetaData', {path: selected[0].getAbsolutePath()}, function(result) {
					if (!result) {
						return;
					}
					if (result.activity && result.activity.length > 0) {
						for(var a = 0; a < result.activity.length; a++) {
							var currentActivity = result.activity[a];
							var currentActivityBox;

							switch(currentActivity.type) {
								case 'creation':
									currentActivityBox = new eyeos.socialbar.Activity(
										'Created',
										currentActivity.by,
										new Date(currentActivity.time * 1000),
										this.getParams()['checknum']
										);
									break;

								case 'edition':
									currentActivityBox = new eyeos.socialbar.Activity(
										'Modification',
										currentActivity.by,
										new Date(currentActivity.time * 1000),
										this.getParams()['checknum'],
										0
										);
									break;

								case 'startsharing':
									currentActivityBox = new eyeos.socialbar.Activity(
										'StartSharing',
										currentActivity['with'],
										new Date(currentActivity.time * 1000),
										this.getParams()['checknum']
										);
									break;

								case 'stopsharing':
									currentActivityBox = new eyeos.socialbar.Activity(
										'StopSharing',
										currentActivity['with'],
										new Date(currentActivity.time * 1000),
										this.getParams()['checknum']
										);
									break;

								default:
									currentActivityBox = new eyeos.socialbar.Activity(
										'',
										currentActivity.by,
										new Date(currentActivity.time * 1000),
										this.getParams()['checknum'],
										'(unknown activity)'
										);
							}
							this._activityBox.add(currentActivityBox);
						}
					}
				}, this);
			}, this);
		},

		_createInfoBox: function () {
			//Contruct the element
			var infoItem = eyeos.socialbar.InfoFactory.getInfoInstance(this.getParams()['selected'], {checknum: this.getParams()['checknum']});
			this._infoBox = new eyeos.socialbar.InfoBox(infoItem);

			//Add listeners
			this._infoBox.addListener('changeRating', function (e) {
				var returnSelected = this.getParams()['selected'];
				var rating = e.getData();
				var files = new Array();
				for (var i = 0; i < returnSelected.length; ++i) {
					files.push(returnSelected[i].getAbsolutePath());
				}
				files.unshift(rating);

				eyeos.callMessage(this.getParams()['checknum'], '__SocialBar_setRating', files, function (results) {
					// Update socialbar handlers data struct
					for(var i = 0; i < returnSelected.length; ++i) {
						returnSelected[i].setRating(rating);
					}

					//Send notification to files
					eyeos.messageBus.getInstance().send('socialbar', 'ratingChanged', {path: this.getParams()['path'], files: returnSelected});

				}, this);
			}, this);
			//Add to Socialbar
			this.getSocialBar().getTab('Info').addBox(this._infoBox, 'infoBox');
		},

		_createSharedWithBox: function () {
			this.__shareds  = eyeos.socialbar.SharedFactory.getSharedInstance(this.getParams()['selected']);
			this._sharedWithBox = new eyeos.socialbar.SharedWithBox(this.getParams()['checknum'], this.__shareds);

			this._sharedWithBox.addListener('deleteShare', function (e) {
				var params = {
					operation: 'Remove',
					userId: e.getData(),
					files: new Array()
				};
				var returnSelected = this.getParams()['selected'];
				for (var j = 0; j < returnSelected.length; ++j) {
					params['files'].push(returnSelected[j].getAbsolutePath());
				}

				eyeos.callMessage(this.getParams()['checknum'], '__FileSystem_changePrivilege', params, function (results) {
					var filesArray = new Array();
					var dBus = eyeos.messageBus.getInstance();
					for (var k = 0; k < results.length; ++k) {
						returnSelected[k].setShared(results[k]);
						filesArray.push(returnSelected[k]);
					}
					dBus.send('files', 'update', [this.getParams()['path'], filesArray]);
				}, this);
			}, this);

			this._sharedWithBox.addListener('changePrivilege', function (e) {
				var args = e.getData();
				var params = {
					operation: args[1],
					userId: args[0],
					files: new Array()
				};
				var returnSelected = this.getParams()['selected'];
				for (var i = 0; i < returnSelected.length; ++i) {
					params['files'].push(returnSelected[i].getAbsolutePath());
				}
				eyeos.callMessage(this.getParams()['checknum'], '__FileSystem_changePrivilege', params, function (results) {
					var filesArray = new Array();
					for (var i = 0; i < results.length; ++i) {
						returnSelected[i].setShared(results[i]);
						filesArray.push(returnSelected[i]);
					}
					var dBus = eyeos.messageBus.getInstance();
					dBus.send('files', 'update', [this.getParams()['path'], filesArray]);
				}, this);
			}, this);


			this.getSocialBar().getTab('Info').addBox(this._sharedWithBox, 'sharedwith');
		},

        __createContentsCommentsTab:function(cloud,idParent) {
            var comments = this.getSocialBar().getTab('Comments');
            comments.removeAll();
            comments.set({
                layout: new qx.ui.layout.VBox()
            });
            this._headerComments = new qx.ui.container.Composite().set({
               layout:  new qx.ui.layout.HBox(),
               paddingTop: 12,
               paddingBottom: 8,
               decorator: new qx.ui.decoration.Single(1).set({
                   colorBottom: '#d1d1d1',
                   widthTop: 0,
                   widthLeft: 0,
                   widthRight: 0
               }),
               marginBottom: 8
            });

            var labelNew = new qx.ui.basic.Label(tr('New')).set({
                paddingRight: 8,
                textColor: '#CEECF5',
                font: new qx.bom.Font(11).set({
                    bold: true
                }),
                enabled: false
            });
            var image = new qx.ui.basic.Image('eyeos/extern/images/add4.png').set({
                enabled: false
            });

            this._headerComments.add(new qx.ui.core.Spacer(165));
            this._headerComments.add(labelNew);
            this._headerComments.add(image);
            comments.add(this._headerComments);

            var scroll = new qx.ui.container.Scroll().set({
                allowStretchY: true,
                allowStretchX: true
            });

            this._commentsBox = new qx.ui.container.Composite().set({
               layout: new qx.ui.layout.VBox()
            });

            scroll.add(this._commentsBox);

            comments.add(scroll, {flex: 1});
            this._controller._comments = [];

            var load = false;

            if(this.getParams()['selected'].length == 1) {
                this._commentsBox.addListener('appear',function() {
                    if(load === false) {
                        this._controller.closeTimerComments();
                        this.closeTimer();
                        this.showCursorLoad(this._commentsBox);
                        var metadata = this._controller.__getFileId(this._file.getPath(), this._file.getName(), true, cloud);
                        if (metadata !== null) {
                            load = true;
                            this._controller.enabledCommentsCloud(metadata, comments, this._file, cloud,idParent);
                        }
                    }
                },this);
            }

        },
        createComments: function(comments,commentsBox,controller,file,metadata,cloud,shared) {
            var headerComments = commentsBox.getChildren()[0];
            headerComments.setVisibility('visible');
            if(shared === true) {
                headerComments.getChildren()[1].setEnabled(true);
                headerComments.getChildren()[1].setTextColor('#3d9af2');
                headerComments.getChildren()[1].setCursor('pointer');
                headerComments.getChildren()[2].setEnabled(true);
                headerComments.getChildren()[2].setCursor('pointer');
                var data = new Object();
                data.commentsBox = commentsBox;
                data.controller = controller;
                data.file = file;
                data.metadata = metadata;
                data.cloud = cloud;
                data.shared = shared;
                data.headerComments = headerComments;
                headerComments.getChildren()[1].setUserData('params',data);
                headerComments.getChildren()[2].setUserData('params',data);
                headerComments.getChildren()[1].addListener('click',this.__createNewComment,this);
                headerComments.getChildren()[2].addListener('click',this.__createNewComment,this);
            }
            this.closeTimer();

            var commentsContainer = commentsBox.getChildren()[1].getChildren()[0];
            commentsContainer.removeAll();

            var that = this;
            var a = function() {that.__createRow(comments,commentsContainer,controller,file,0,metadata,cloud,shared,that);};
            this._timer = setTimeout(a,0);

        },
        __createNewComment: function(e) {
            var params = e.getCurrentTarget().getUserData('params');
            params.controller.closeTimerComments();
            this.closeTimer();
            params.headerComments.setVisibility('excluded');

            var commentsBox = params.commentsBox.getChildren()[1].getChildren()[0];
            commentsBox.removeAll();

            var labelBack = new qx.ui.basic.Label(tr("< Back")).set({
                marginTop: 10,
                paddingLeft: 8,
                textColor: '#3d9af2',
                font: new qx.bom.Font(11).set({
                    bold: true
                }),
                cursor: 'pointer'
            });

            labelBack.addListener('click',function() {
                /*this._controller._comments = [];
                var id = this._controller.__getFileId(this._file.getPath(),this._file.getName());
                if(id !== null) {
                    this._controller.loadComments(id,this.getSocialBar().getTab('Comments'));
                }*/
                params.controller.closeTimerComments();
                this.showCursorLoad(commentsBox);
                params.controller._comments = [];
                params.controller.loadCommentsCloud(params.metadata,params.commentsBox,params.file,params.cloud,params.shared,true);
            },this);

            commentsBox.add(labelBack);

            var textComments = new qx.ui.form.TextArea().set({
                height: 160,
                margin:[10,10,10,10]
            });

            textComments.getContentElement().setStyle("resize", "none");
            commentsBox.add(textComments);

            var containerBottoms = new qx.ui.container.Composite().set({
               layout: new qx.ui.layout.HBox()
            });

            var eraseButton = new qx.ui.form.Button().set({
                label: tr('Delete'),
                width: 60,
                marginLeft: 10,
                cursor: 'pointer'
            });

            eraseButton.addListener('execute',function() {
                textComments.setValue('');
            },this);

            containerBottoms.add(eraseButton);

            var addButton = new qx.ui.form.Button().set({
                label: tr('Add'),
                width: 60,
                marginLeft: 10,
                cursor: 'pointer'
            });

            addButton.addListener('execute',function(){
                if(textComments.getValue() !== null && textComments.getValue().trim().length > 0) {
                    this.showCursorLoad(commentsBox);
                    params.controller._comments = [];
                    params.controller.createComment(params.metadata,params.commentsBox,params.file,params.cloud,params.shared,textComments.getValue().trim());
                }
            },this);

            containerBottoms.add(new qx.ui.core.Spacer(76));
            containerBottoms.add(addButton);
            commentsBox.add(containerBottoms);
        },

        __createRow: function(comments,commentsContainer,controller,file,contador,metadata,cloud,shared,form) {
            if(comments[contador]) {
                var containerRow = new qx.ui.container.Composite().set({
                    layout: new qx.ui.layout.VBox()
                });

                var containerText = new qx.ui.container.Composite().set({
                    layout: new qx.ui.layout.VBox(),
                    decorator: new qx.ui.decoration.Single(1),
                    margin: [0,0,5,10],
                    allowGrowX: false,
                    width: 190
                });

                var lbUser = new qx.ui.basic.Label().set({
                    font: new qx.bom.Font(10).set({
                        bold: true
                    }),
                    value: comments[contador].user,
                    margin: [3,3,5,5],
                    rich: true
                });
                containerText.add(lbUser);

                var lbDate = new qx.ui.basic.Label().set({
                    font: new qx.bom.Font(10).set({
                        italic: true
                    }),
                    value: this.__formatDate(comments[contador].time_created),
                    rich: true,
                    margin: [3,3,5,5]
                });
                containerText.add(lbDate);

                var lbComments = new qx.ui.basic.Label().set({
                    font: new qx.bom.Font(10),
                    margin: [3,3,5,5],
                    rich: true,
                    value: comments[contador].text.replace(/(?:\r\n|\r|\n)/g, '<br />')
                });
                containerText.add(lbComments);
                containerRow.add(containerText);

                var action = new qx.ui.container.Composite().set({
                   layout: new qx.ui.layout.HBox(),
                   marginBottom: 10
                });

                var lbDelete = new qx.ui.basic.Label(tr('Delete')).set({
                    paddingRight: 8,
                    textColor: '#3d9af2',
                    font: new qx.bom.Font(10).set({
                        bold: true
                    }),
                    cursor: 'pointer'
                });
                var imgDelete = new qx.ui.basic.Image('eyeos/extern/images/less3.png').set({
                    cursor: 'pointer'
                });

                lbDelete.addListener('click',function(e) {
                    form.closeTimer();
                    var pos = e.getCurrentTarget().getLayoutParent().getLayoutParent().getLayoutParent().indexOf( e.getCurrentTarget().getLayoutParent().getLayoutParent());
                    if(pos != -1) {
                        controller.closeTimerComments();
                        form.showCursorLoad(commentsContainer);
                        form.getSocialBar().getTab('Comments').getChildren()[0].setVisibility('excluded');
                        //form.__deleteComment(comments[pos].time_created,controller,file);
                        controller.deleteComment(metadata,form.getSocialBar().getTab('Comments'),file,cloud,shared,comments[pos].time_created);
                    }
                },form);

                imgDelete.addListener('click',function(e) {
                    form.closeTimer();
                    var pos = e.getCurrentTarget().getLayoutParent().getLayoutParent().getLayoutParent().indexOf( e.getCurrentTarget().getLayoutParent().getLayoutParent());
                    if(pos != -1) {
                        controller.closeTimerComments();
                        form.showCursorLoad(commentsContainer);
                        form.getSocialBar().getTab('Comments').getChildren()[0].setVisibility('excluded');
                        //form.__deleteComment(comments[pos].time_created,controller,file);
                        controller.deleteComment(metadata,form.getSocialBar().getTab('Comments'),file,cloud,shared,comments[pos].time_created);
                    }
                },form);

                if(eyeos.getCurrentUserName() != comments[contador].user || shared === false) {
                    lbDelete.setEnabled(false);
                    lbDelete.setTextColor('#CEECF5')
                    lbDelete.setCursor('default');
                    imgDelete.setEnabled(false);
                    imgDelete.setCursor('default');

                }

                action.add(new qx.ui.core.Spacer(140));
                action.add(lbDelete);
                action.add(imgDelete);
                containerRow.add(action);
                commentsContainer.add(containerRow);

                //console.log(contador + " ::" + comments.length);
                if((contador + 1) < comments.length) {
                    contador ++;
                    var that = form;
                    var a = function() {that.__createRow(comments,commentsContainer,controller,file,contador,metadata,cloud,shared,that);};
                    form._timer = setTimeout(a,0);
                } else {
                    form.closeTimer();

                }
            }
        },

        __deleteComment: function(time_created,controller,file) {
            this.closeTimer();
            var metadata = controller.__getFileId(file.getPath(),file.getName(),true);
            if(metadata !== null) {
                controller.deleteComment(metadata.id,eyeos.getCurrentUserName(),time_created,this.getSocialBar().getTab('Comments'),file);
            }
        },

        __formatDate: function(date) {
            var dateAux = date.substring(6,8) + "/" + date.substring(4,6) + "/" + date.substring(0,4) + " " + date.substring(8,10) + ":" + date.substring(10,12);
            return dateAux;
        },
        closeTimer: function() {
            if(this._timer) {
                clearTimeout(this._timer);
            }

        },

        closeTimerVersion: function() {
            if(this._timerVersion) {
                clearTimeout(this._timerVersion);
            }
        },

        _createContenActivityTabCloudSpaces: function(folder, cloud) {
            var activity = this.getSocialBar().getTab('Activity');
            activity.removeAll();
            activity.set({
                layout: new qx.ui.layout.VBox()
            });

            var header = new qx.ui.container.Composite().set({
                layout: new qx.ui.layout.HBox(),
                decorator: new qx.ui.decoration.Single(1).set({widthTop:0,widthLeft:0,widthRight:0,colorBottom:'#d1d1d1'}),
                margin: [10,0,5,10],
                paddingBottom: 5,
                allowGrowX: false,
                width: 190
            });


            var labelDate = new qx.ui.basic.Label().set({
               width: 100,
               value: folder?tr('User'):tr('Date'),
               font: new qx.bom.Font(11).set({bold:true})
            });

            header.add(labelDate);

            var labelVersion = new qx.ui.basic.Label().set({
                width: 70,
                value: folder?tr('Owner'):tr('Version'),
                marginLeft: 20,
                font: new qx.bom.Font(11).set({bold:true})
            });

            header.add(labelVersion);
            activity.add(header);

            var scroll = new qx.ui.container.Scroll().set({
                allowStretchY: true,
                allowStretchX: true
            });

            var versions = new qx.ui.container.Composite().set({
                layout: new qx.ui.layout.VBox()
            });

            scroll.add(versions);

            activity.add(scroll,{flex: 1});

            if(this.getParams()['selected'].length == 1) {
                activity.addListener('appear',function() {
                    versions.removeAll();
                    var metadata = this._controller.__getFileId(this._file.getPath(),this._file.getName(),true,cloud);
                    if(metadata !== null) {
                        this._controller.loadActivity(metadata, activity, this._file, folder, cloud);
                    }
                },this);
            }
        },

        /*_createContenActivityTabCloudspaces: function(folder) {
            var activity = this.getSocialBar().getTab('Cloudspaces');
            activity.removeAll();
            activity.set({
                layout: new qx.ui.layout.VBox()
            });

            var header = new qx.ui.container.Composite().set({
                layout: new qx.ui.layout.HBox(),
                decorator: new qx.ui.decoration.Single(1).set({widthTop:0,widthLeft:0,widthRight:0,colorBottom:'#d1d1d1'}),
                margin: [10,0,5,10],
                paddingBottom: 5,
                allowGrowX: false,
                width: 190
            });


            var labelDate = new qx.ui.basic.Label().set({
               width: 100,
               value: folder?tr('User'):tr('Date'),
               font: new qx.bom.Font(11).set({bold:true})
            });

            header.add(labelDate);

            var labelVersion = new qx.ui.basic.Label().set({
                width: 70,
                value: folder?tr('Owner'):tr('Version'),
                marginLeft: 20,
                font: new qx.bom.Font(11).set({bold:true})
            });

            header.add(labelVersion);
            activity.add(header);

            var scroll = new qx.ui.container.Scroll().set({
                allowStretchY: true,
                allowStretchX: true
            });

            var versions = new qx.ui.container.Composite().set({
                layout: new qx.ui.layout.VBox()
            });

            scroll.add(versions);

            activity.add(scroll,{flex: 1});

            if(this.getParams()['selected'].length == 1) {
                activity.addListener('appear',function() {
                    var id = this._controller.__getFileId(this._file.getPath(),this._file.getName());
                    if(id !== null) {
                        this._controller.loadActivity(id,activity,this._file,folder);
                    }
                },this);
            }
        },*/

        createListActivity: function(cloud, list, listBox, controller, file, type) {
            this.closeTimerVersion();
            if(listBox && listBox.getChildren().length > 0 && listBox.getChildren()[1].getChildren().length > 0) {
                var listContainer = listBox.getChildren()[1].getChildren()[0];
                listContainer.removeAll();

                var that = this;
                var a = function () {
                    that.__createRowActivity(cloud, list, listContainer, controller, 0, file, type, that);
                };
                this._timerVersion = setTimeout(a, 0);
            }
        },

        __createRowActivity: function(cloud, list, listContainer, controller, contador, file, type, form) {
            if(list[contador]) {
                var containerRow = new qx.ui.container.Composite().set({
                    layout: new qx.ui.layout.HBox()
                });

                var labelDate = new qx.ui.basic.Label().set({
                   value: type?list[contador].name:form.__formatDateVersion(list[contador].modified_at),
                   marginLeft: 10,
                   width: 100,
                   paddingTop: 1
                });

                containerRow.add(labelDate);

                if (!type){
                    var labelVersion = new qx.ui.basic.Label().set({
                       value: list[contador].version,
                        marginLeft: 20,
                        width: 50,
                        textAlign: 'center',
                        paddingTop: 1
                    });

                    containerRow.add(labelVersion);
                }

                listContainer.add(containerRow);

                if((!type && list[contador].enabled === true) || (type && list[contador].is_owner === true)) {
                    var imageCheck = new qx.ui.basic.Image("eyeos/extern/images/16x16/actions/dialog-ok.png").set({
                        marginLeft: type ? 35 : 2
                    });
                    containerRow.add(imageCheck);
                } else {
                    if (!type){
                        containerRow.addListener('mouseover', form.__mouseOver, form);
                        containerRow.addListener('mouseout', form.__mouseOut, form);

                        var pos = containerRow.getLayoutParent().indexOf(containerRow);
                        if(pos !== -1) {
                            var params = new Object();
                            params.file = file;
                            params.version = list[pos].version;
                            params.controller = controller;
                            params.activity = this.getSocialBar().getTab('Activity');
                            params.cloud = cloud;

                            containerRow.setUserData("params", params);
                            containerRow.addListener('click', form.__getVersion, form);
                        }
                    }
                }

                if((contador + 1) < list.length) {
                    contador ++;
                    var that = form;
                    var a = function() {that.__createRowActivity(cloud, list, listContainer, controller, contador, file, type, that);};
                    form._timerVersion = setTimeout(a, 0);
                } else {
                    form.closeTimerVersion();
                }
            }
        },

        __formatDateVersion: function(date) {
            var aux = date.substring(8,10) + "/" + date.substring(5,7) + "/" + date.substring(0,4) + " " + date.substring(11,13) + ":" + date.substring(14,16);
            return aux;
        },

        __mouseOver: function(e) {
            e.getCurrentTarget().setBackgroundColor('#edf3fa');
            e.getCurrentTarget().setCursor('pointer');
        },

        __mouseOut: function(e) {
            e.getCurrentTarget().resetBackgroundColor();
            e.getCurrentTarget().setCursor('default');
        },

        __getVersion: function(e) {
            var params = e.getCurrentTarget().getUserData('params');
            var metadata = params.controller.__getFileId(params.file.getPath(), params.file.getName(), true, params.cloud);
            if(metadata !== null) {
                params.activity.getChildren()[1].getChildren()[0].setEnabled(false);
                this.closeTimerVersion();
                params.controller.getVersion(metadata.id, params.version, params.activity, params.file, params.cloud,metadata.resource_url,metadata.access_token_key,metadata.access_token_secret);
            }
        },

        showCursorLoad: function(container) {
            container.removeAll();

            var cursor = new qx.ui.basic.Image().set({
                width: 42,
                height: 42,
                source: "index.php?extern=images/loading.gif",
                marginTop: 130,
                marginLeft: 80
            });

            container.add(cursor);
        },

        closeCursorLoad: function(container) {
            container.removeAll();
        },
        __createCommentsTabSocialBar: function(cloud,idParent) {
            this.getSocialBar().createCommentsTab();
            this.__createContentsCommentsTab(cloud,idParent);
        }
    }
});