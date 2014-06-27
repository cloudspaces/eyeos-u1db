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

qx.Class.define('eyeos.files.ViewManager', {

	extend: eyeos.ui.Window,

	construct: function (controller, model, application, caption, icon, fakeMinimize, fakeClose) {
		arguments.callee.base.call(this, application, caption, icon, fakeMinimize, fakeClose);
		this.setController(controller);
		this.setModel(model);
		this.setKeyPress(false);
		/*this._buildLayout();
		this._buildMenuBar();
		this._buildToolBar();
		this._buildSideBar();
		this._buildContent();
		this._addListeners();*/
		this.set({
			width: 800,
			height: 450,
            contentPadding: 0,
            layout: new qx.ui.layout.HBox()
		});

		this.open();
                //this.toggleShowStatusbar();

        this.addListener('resize',function() {
             if(this._cursor) {
                 this.centerCursor();
             }
             this.__centerCursorLoad();

        },this);

        this.addListener('move',function(e) {
            this.__centerCursorLoad();
        },this);
	},

	properties: {
		controller: {
			check: 'Object'
		},

		model: {
			check: 'Object'
		},

		keyPress: {
			check: 'Boolean'
		},

        cursorLoad: {
            check: 'Object',
            init: null
        }
	},

	members: {

		_content: null,
		_mainView: null,
		_menuBar: null,
		_searchBox: null,
		_sideBar: null,
		_toolBar: null,
		_toolBarLayout: null,
		_top: null,
        _cursor: null,

		close: function () {
			var dBusListeners = this.getController()._dBusListeners
			var dBus = eyeos.messageBus.getInstance();
			for (var i = 0; i < dBusListeners.length; ++i) {
				dBus.removeListenerById(dBusListeners[i]);
			}
			this.base(arguments);
		},

        initFiles: function() {
            this._buildStacksync();
            this._buildLayout();
            this._buildMenuBar();
            this._buildToolBar();
            this._buildSideBar();
            this._buildContent();
            this._addListeners();
            this.toggleShowStatusbar();
        },

        _buildStacksync: function() {
            if(!this.getController().getToken()) {
                for(var i in this.self(arguments).PLACES) {
                    if(this.self(arguments).PLACES[i].label == 'Stacksync') {
                        this.self(arguments).PLACES.splice(i,1);
                        break;
                    }
                }
            }
        },

		_buildLayout: function () {

			this._socialBar = new eyeos.socialbar.SocialBar('#FFFFFF');

			this._mainWindow = new qx.ui.container.Composite(new qx.ui.layout.VBox());

			var borderBottom = new qx.ui.decoration.Single(2, 'solid', '#D3D3D3').set({
				styleLeft: null,
				styleTop: null,
				styleRight: null
			});

			var borderRight = new qx.ui.decoration.Single(1, 'solid', '#D3D3D3').set({
				styleLeft: null,
				styleTop: null,
				styleBottom: null
			});

			this._top = new qx.ui.container.Composite(new qx.ui.layout.VBox()).set({decorator: borderBottom});
			this._menuBar = new qx.ui.menubar.MenuBar().set({decorator: null, backgroundColor: '#FCFCFC'});
			this._toolBarLayout = new qx.ui.container.Composite(new qx.ui.layout.HBox()).set({
				backgroundColor: '#FCFCFC'
			});
			this._searchBox = new qx.ui.form.TextField(tr('Search')).set({
				marginTop: 8,
				marginRight: 7,
				marginLeft: 20,
				width: 120
			});
			this._searchBox.addListener('focusin', function (e) {
				if (this.getValue() == tr('Search')){
					this.setValue('');
				} 
			});
			this._searchBox.addListener('keypress', function(e) {
				if (e.getKeyIdentifier().toLowerCase() == 'enter') {
                                    this._view.filterBrowse(this._searchBox.getValue());
				}
			}, this);

			this._toolBar = new qx.ui.toolbar.ToolBar().set({decorator: null, backgroundColor: '#FCFCFC'});
			this._content = new qx.ui.container.Composite(new qx.ui.layout.HBox());
			this._sideBar = new qx.ui.container.Composite(new qx.ui.layout.VBox()).set({
				backgroundColor: '#FFFFFF',
				decorator: borderRight
			});

			this._mainView = new qx.ui.container.Composite(new qx.ui.layout.VBox());

			this._toolBarLayout.add(this._toolBar, {flex: 1});
			this._toolBarLayout.add(this._searchBox);
			this._top.add(this._menuBar);
			this._top.add(this._toolBarLayout);
			this._mainWindow.add(this._top);
			this._content.add(this._sideBar);
			this._content.add(this._mainView, {flex: 1});
			this._mainWindow.add(this._content, {flex: 1});
			this.add(this._mainWindow, {flex: 1})
			this.add(this._socialBar);

            this.addListener('beforeClose',function() {
                this.getController().closeTimer();
                this.getController().closeProgress();
                if(this.getCursorLoad() !== null) {
                    this.getCursorLoad().close();
                }
            },this);
		},

		_buildDynamicBar: function (which, buttons, menus) {
			for (var i = 0; i < buttons.length; ++i) {
				var id = buttons[i].id;
				if (menus[id] != undefined) {
					var borderMenu = new qx.ui.decoration.Single(1, 'solid', '#C3C3C3');
					var menu = new qx.ui.menu.Menu().set({
						decorator: borderMenu,
						shadow: null,
						padding: 3,
						backgroundColor: '#F3F3F3'
					});
					for (var f = 0; f < menus[id].length; ++f) {

						if (menus[id] != undefined) {
							var menuItem = null;
							if (menus[id][f] && menus[id][f].id != 'separator') {
								menuItem = new qx.ui.menu.Button(tr(menus[id][f].name), menus[id][f].image);
								menuItem.setUserData('id', menus[id][f].id);
								menuItem._manager = this.getController();
								menuItem.addListener('appear', function (e) {
									this.setBackgroundColor(null);
									this.setDecorator(null);
									this.setTextColor('#4A4A4A');
								});
								menuItem.addListener('mouseover', function (e) {
									this.setBackgroundColor('#D3D3D3');
								});
								menuItem.addListener('mouseout', function (e) {
									if (!qx.ui.core.Widget.contains(menuItem, e.getRelatedTarget())) {
										this.setDecorator(null);
									}
									this.setBackgroundColor(null);
								});
								menuItem.addListener('execute', function (e) {
									//									//console.log('ye')
									eval('this._manager.'+this.getUserData('id'));
								});
							} else {
								menuItem = new qx.ui.menu.Separator();
							}
							menu.add(menuItem);
						}
					}
					if (which == 'menu') {
						var item = new qx.ui.menubar.Button(tr(buttons[i].name), buttons[i].image, menu);
					} else {
						var item = new qx.ui.toolbar.MenuButton(tr(buttons[i].name), buttons[i].image, menu);
						item.setIconPosition('top');
					}

				} else {
					if (which == 'menu') {
						var item = new qx.ui.menubar.Button(tr(buttons[i].name), buttons[i].image);
					} else {
						var item = new qx.ui.toolbar.Button(tr(buttons[i].name), buttons[i].image);
						item.setIconPosition('top');
					}

				}
				item.setUserData('id', buttons[i].id);
				item._manager = this.getController();
				item.addListener('execute', function (e) {
					eval('this._manager.'+this.getUserData('id'));
				});
				if (which == 'menu') {
					this._menuBar.add(item);
				} else {
					this._toolBar.add(item);
				}
			}
		},

		_buildMenuBar: function () {
			this._menuBar.removeAll();
			this._buildDynamicBar('menu', this.self(arguments).MENUBAR_BUTTONS, this.self(arguments).MENUBAR_MENUS);
		},

		_buildToolBar: function () {
			this._toolBar.removeAll();
			this._buildDynamicBar('toolbar', this.self(arguments).TOOLBAR_BUTTONS, this.self(arguments).TOOLBAR_MENUS);
		},

		_buildSideBar: function () {
			this._places = new eyeos.files.Sidebar(tr('Places'), this.self(arguments).PLACES, this.getController(), true);
			this._sideBar.add(this._places);

			this._shared = new eyeos.files.Sidebar(tr('Shared'), this.self(arguments).SHARES, this.getController(), false);
			this._sideBar.add(this._shared);
			
			this._workgroups = new eyeos.files.Sidebar(tr('Groups'), this.self(arguments).WORKGROUPS, this.getController(), false);
			this._sideBar.add(this._workgroups);
		},

		_buildContent: function () {
			this._header = new eyeos.files.HeaderBar(this);
			if (this.getModel().getDefaultView() == 'iconview') {
				this._view = new eyeos.files.IconView(this);
			} else {
				//this._view = new eyeos.files.IconView(this._checknum);
			}
			this._mainView.add(this._header);
			this._mainView.add(this._view, {flex: 1});
		},

		_addListeners: function () {
			this.addListener('keydown', function (e) {
				if (e.getKeyIdentifier() == 'Control') {
					this.setKeyPress(true);
				}
			});

			this.addListener('keyup', function (e) {
				if (e.getKeyIdentifier() == 'Control') {
					this.setKeyPress(false);
				}
			});

			//copy by shortcuts
			this.addListener('keypress', function(e) {
				if(this.isKeyPress()) {
					if(e.getKeyIdentifier() == 'C') {
						this.getController().copyFile();
					} else if(e.getKeyIdentifier() == 'V') {
						this.getController().pasteFile();
					}
				}
			}, this);

			var bus = eyeos.messageBus.getInstance();
			bus.addListener('eyeos_upload_uploadFinished', function(e) {
				if (e.getData() != null && e.getData() != undefined) {
					this.getController()._browsePath(this.getModel().getCurrentPath()[1], true);
				}
			}, this);

		},

		/**
		 * Common View functions that are linked to the member "_view" which can switch between IconView and the future ListView
		 * TODO: Some functions should be common and implemented directly here, not on every view
		 */

		reorder: function (filesToOrder) {
			return this._view.reorder(filesToOrder);
		},

		showBrowse: function () {
			this._header.showBrowse();
			this._view.showBrowse();
		},

		returnSelected: function () { 
			return this._view.returnSelected();
		},

		returnAll: function () { 
			return this._view.returnAll();
		},

		resetAllSelected: function () { 
			this._view.resetAllSelected();
		},

        showCursor: function() {
            this._cursor = new qx.ui.basic.Image().set({
                width: 42,
                height: 42,
                source: "index.php?extern=images/ajax-loader-1.gif"
            });
            this.add(this._cursor);
            this.centerCursor();
        },

        closeCursor: function() {
            this.remove(this._cursor);
            this._cursor = null;
        },

        centerCursor: function() {
            var widthParent = this.getBounds()?this.getBounds().width:this.getWidth();
            var heightParent = this.getBounds()?this.getBounds().height:this.getHeight();
            var widthImage = this._cursor.getWidth();
            var heightImage = this._cursor.getHeight();

            var left = (widthParent / 2) - (widthImage / 2);
            var top = (heightParent / 2) - (heightImage / 2) - 30;

            this._cursor.setMarginTop(top);
            this._cursor.setMarginLeft(left);
        },

        createDialogStacksync:function() {

            var dialog = new qx.ui.container.Composite().set({
                layout: new qx.ui.layout.VBox(),
                width: this.getWidth(),
                allowGrowY: false
            });

            dialog.addListener('appear',function(e) {
                var heightParent = this.getBounds()?this.getBounds().height:this.getHeight();
                var heightDialog = e.getCurrentTarget().getBounds().height;
                var top = (heightParent / 2) - (heightDialog / 2) - 80;
                e.getCurrentTarget().setMarginTop(top);
            },this);

            var logo = new qx.ui.basic.Image().set({
               source:  "index.php?extern=images/stacksync.png",
               width: 100,
               height: 100,
               scale: true,
               alignX: 'center'
            });
            dialog.add(logo);

            var label = new qx.ui.basic.Label().set({
               value: tr("Do you want to log on Stacksync?"),
               font:  new qx.bom.Font(18, ['Arial', 'sans-serif']),
               alignX: 'center',
               marginTop: 20
            });

            dialog.add(label);

            var containerButtons = new qx.ui.container.Composite().set({
                layout: new qx.ui.layout.HBox(),
                allowGrowX: false,
                alignX: 'center',
                marginTop: 20
            });

            var buttonOK = new qx.ui.form.Button().set({
                label: tr('Yes'),
                font:  new qx.bom.Font(12, ['Arial', 'sans-serif']),
                marginRight: 40,
                width: 60,
                height: 30
            });

            buttonOK.addListener('execute',function() {
                this.waitStackSync();
            },this);

            var buttonKO = new qx.ui.form.Button().set({
                label: tr('No'),
                font:  new qx.bom.Font(12, ['Arial', 'sans-serif']),
                width: 60,
                height: 30
            });

            buttonKO.addListener('execute',function() {
                this.removeAll();
                this.getController()._initFiles();
            },this);

            containerButtons.add(buttonOK);
            containerButtons.add(buttonKO);

            dialog.add(containerButtons);

            this.add(dialog);
        },
        waitStackSync: function() {

            this.removeAll();

            var containerWait = new qx.ui.container.Composite().set({
                layout: new qx.ui.layout.VBox(),
                width: this.getWidth(),
                allowGrowY: false
            });

            containerWait.addListener('appear',function(e) {
                var heightParent = this.getBounds()?this.getBounds().height:this.getHeight();
                var heightContainer = e.getCurrentTarget().getBounds().height;
                var top = (heightParent / 2) - (heightContainer / 2) - 80;
                e.getCurrentTarget().setMarginTop(top);
            },this);

            var logo = new qx.ui.basic.Image().set({
                source:  "index.php?extern=images/stacksync.png",
                width: 100,
                height: 100,
                scale: true,
                alignX: 'center'
            });
            containerWait.add(logo);

            var label = new qx.ui.basic.Label().set({
                value: tr("Waiting StackSync"),
                font:  new qx.bom.Font(18, ['Arial', 'sans-serif']),
                alignX: 'center',
                marginTop: 20
            });

            containerWait.add(label);

            var cursor = new qx.ui.basic.Image().set({
                width: 42,
                height: 42,
                source: "index.php?extern=images/ajax-loader-1.gif",
                marginTop: 10
            });

            cursor.addListener('appear',function(e) {
                var widthParent = this.getBounds()?this.getBounds().width:this.getWidth();
                var widthCursor = e.getCurrentTarget().getBounds().width;
                var left = (widthParent / 2) - (widthCursor / 2);
                e.getCurrentTarget().setMarginLeft(left);
            },this);

            containerWait.add(cursor);

            this.add(containerWait);

            this.getController()._getTokenStacksync();

        },
        timeOutStakSync: function(message) {
            this.removeAll();

            var containerTimeout = new qx.ui.container.Composite().set({
                layout: new qx.ui.layout.VBox(),
                width: this.getWidth(),
                allowGrowY: false
            });

            containerTimeout.addListener('appear',function(e) {
                var heightParent = this.getBounds()?this.getBounds().height:this.getHeight();
                var heightContainer = e.getCurrentTarget().getBounds().height;
                var top = (heightParent / 2) - (heightContainer / 2) - 80;
                e.getCurrentTarget().setMarginTop(top);
            },this);

            var logo = new qx.ui.basic.Image().set({
                source:  "index.php?extern=images/stacksync.png",
                width: 100,
                height: 100,
                scale: true,
                alignX: 'center'
            });
            containerTimeout.add(logo);

            var label = new qx.ui.basic.Label().set({
                value: message,
                font:  new qx.bom.Font(18, ['Arial', 'sans-serif']),
                alignX: 'center',
                marginTop: 20
            });

            containerTimeout.add(label);

            var containerButtons = new qx.ui.container.Composite().set({
                layout: new qx.ui.layout.HBox(),
                allowGrowX: false,
                alignX: 'center',
                marginTop: 20
            });

            var buttonReload = new qx.ui.form.Button().set({
                label: tr('Reload'),
                font:  new qx.bom.Font(12, ['Arial', 'sans-serif']),
                marginRight: 40,
                width: 100,
                height: 30
            });

            buttonReload.addListener('execute',function() {
                 this.waitStackSync();
            },this);

            var buttonCancel = new qx.ui.form.Button().set({
                label: tr('Cancel'),
                font:  new qx.bom.Font(12, ['Arial', 'sans-serif']),
                width: 100,
                height: 30
            });

            buttonCancel.addListener('execute',function() {
                this.removeAll();
                this.getController()._initFiles();
            },this);

            containerButtons.add(buttonReload);
            containerButtons.add(buttonCancel);

            containerTimeout.add(containerButtons);

            this.add(containerTimeout);

        },

        _permissionDenied: function() {
            var permissionDialog = new qx.ui.container.Composite().set({
                layout: new qx.ui.layout.VBox(),
                width: this.getWidth(),
                allowGrowY: false
            });

            permissionDialog.addListener('appear',function(e) {
                var heightParent = this.getBounds()?this.getBounds().height:this.getHeight();
                var heightDialog = e.getCurrentTarget().getBounds().height;
                var top = (heightParent / 2) - (heightDialog / 2) - 80;
                e.getCurrentTarget().setMarginTop(top);
            },this);

            var logo = new qx.ui.basic.Image().set({
                source:  "index.php?extern=images/stacksync.png",
                width: 100,
                height: 100,
                scale: true,
                alignX: 'center'
            });
            permissionDialog.add(logo);

            var label = new qx.ui.basic.Label().set({
                value: tr("Access denied."),
                font:  new qx.bom.Font(18, ['Arial', 'sans-serif']),
                alignX: 'center',
                marginTop: 20
            });

            permissionDialog.add(label);

            var containerButtons = new qx.ui.container.Composite().set({
                layout: new qx.ui.layout.HBox(),
                allowGrowX: false,
                alignX: 'center',
                marginTop: 20
            });

            var buttonOK = new qx.ui.form.Button().set({
                label: tr('Continue'),
                font:  new qx.bom.Font(12, ['Arial', 'sans-serif']),
                width: 100,
                height: 30
            });

            buttonOK.addListener('execute',function() {
                this.removeAll();
                this.createDialogStacksync();
            },this);

            containerButtons.add(buttonOK);

            permissionDialog.add(containerButtons);

            this.add(permissionDialog);
        },
        __centerCursorLoad: function() {
            if(this.getCursorLoad() !== null) {
                this.getCursorLoad().centerCursor(this);
            }
        },
        enabledFiles: function(enabled) {
            this._header.enabledItems(enabled);
            this._menuBar.setEnabled(enabled);
            this._toolBar.setEnabled(enabled);
            this._searchBox.setEnabled(enabled);
            this._sideBar.setEnabled(enabled);
            this._socialBar.setEnabled(enabled);
            this._view.enabledFiles(enabled);
        }
	},
	// function tr() to translate menus and toolbar isn't need here, because apply translate when items is constructing.
	statics: {
		MENUBAR_BUTTONS: [
			{
				name: 'File',
				id: 'menuBarFile'
			}, {
				name: 'Edit',
				id: 'menuBarEdit'
			}
//			{
//				name: 'View',
//				id: 'menuBarView'
//			}
		],

		MENUBAR_MENUS: {
			menuBarFile: [
				{
					name: 'Open',
					image: 'index.php?extern=images/22x22/actions/document-open.png',
					id: 'openFile()'
				}, {
					name: 'Rename',
					image: 'index.php?extern=images/22x22/actions/edit-rename.png',
					id: 'editFile()'
				}, {
					name: 'Separator',
					id: 'separator'
				}, {
					name: 'New folder',
					image: 'index.php?extern=images/22x22/places/folder.png',
					id: 'newFolder()'
				}, {
					name: 'New simple document',
					image: 'index.php?extern=images/22x22/mimetypes/text-x-generic.png',
					id: 'newFile(\'txt\')'
				}, {
					name: 'New document',
					image: 'index.php?extern=images/22x22/mimetypes/application-msword.png',
					id: 'newFile(\'edoc\')'
				}, {
					name: 'New link',
					image: 'index.php?extern=images/22x22/mimetypes/application-x-mswinurl.png',
					id: 'newLink()'
				}
			],

			menuBarEdit: [
				{
					name: 'Cut',
					image: 'index.php?extern=images/22x22/actions/edit-cut.png',
					id: 'cutFile()'
				}, {
					name: 'Copy',
					image: 'index.php?extern=images/22x22/actions/edit-copy.png',
					id: 'copyFile()'
				}, {
					name: 'Paste',
					image: 'index.php?extern=images/22x22/actions/edit-paste.png',
					id: 'pasteFile()'
				}
			]
		},

		TOOLBAR_BUTTONS: [
			{
				name: 'Back',
				id: 'toolBarBack()',
				image: 'index.php?extern=images/22x22/actions/go-previous.png'
			}, {
				name: 'Forward',
				id: 'toolBarForward()',
				image: 'index.php?extern=images/22x22/actions/go-next.png'
			}, {
				name: 'New',
				id: 'toolBarNew',
				image: 'index.php?extern=images/22x22/actions/document-new.png'
			}, {
				name: 'Upload',
				id: 'toolBarUpload()',
				image: 'index.php?extern=images/eyefiles/upload.png'
			}
		],

		TOOLBAR_MENUS: {
			toolBarNew: [
				{
					name: 'Folder',
					image: 'index.php?extern=images/eyefiles/folder.png',
					id: 'newFolder()'
				}, {
					name: 'Simple document',
					image: 'index.php?extern=images/eyefiles/doc.png',
					id: 'newFile(\'txt\')'
				}, {
					name: 'Document',
					image: 'index.php?extern=images/eyefiles/document.png',
					id: 'newFile(\'edoc\')'
				}, {
					name: 'Spreadsheet',
					image: 'index.php?extern=images/eyefiles/spreadsheet.png',
					id: 'newFile(\'xls\')'
				}, {
					name: 'New link',
					image: 'index.php?extern=images/22x22/mimetypes/application-x-mswinurl.png',
					id: 'newLink()'
				}
			],
			toolBarView: [
				{
					name: 'Icons',
					image: 'index.php?extern=images/eyefiles/view_icon.png'
				}, {
					name: 'List',
					image: 'index.php?extern=images/eyefiles/view_list.png'
				}, {
					name: 'Icon with types',
					image: 'index.php?extern=images/eyefiles/view_icon.png'
				}, {
					name: 'Hide SocialBar',
					image: 'index.php?extern=images/eyefiles/view_icon.png'
				}
			]
		},

		PLACES: [
			{
				label: 'Home',
				icon: 'index.php?extern=images/16x16/places/user-home.png',
				path: 'home://~'+eyeos.getCurrentUserName()+'/'
			}, {
				label: 'Desktop',
				icon: 'index.php?extern=images/16x16/places/user-desktop.png',
				path: 'home://~'+eyeos.getCurrentUserName()+'/Desktop'
			}, {
                label: 'Stacksync',
                icon: 'index.php?extern=images/16x16/places/folder-txt.png',
                path: 'home://~'+eyeos.getCurrentUserName()+'/Stacksync'
            },{
				label: 'Documents',
				icon: 'index.php?extern=images/16x16/places/folder-txt.png',
				path: 'home://~'+eyeos.getCurrentUserName()+'/Documents'
			}, {
				label: 'Images',
				icon: 'index.php?extern=images/16x16/places/folder-image.png',
				path: 'home://~'+eyeos.getCurrentUserName()+'/Images'
			}, {
				label: 'Music',
				icon: 'index.php?extern=images/16x16/places/folder-sound.png',
				path: 'home://~'+eyeos.getCurrentUserName()+'/Music'
			}
		],

		SHARES: [
			{
				label: 'By me',
				icon: 'index.php?extern=images/16x16/places/user-identity.png',
				path: 'share://~' + eyeos.getCurrentUserName() + '/'
			},
			{
				label: 'By my contacts',
				icon: 'index.php?extern=images/16x16/apps/system-users.png',
				path: 'share:///'
			}
		],

		WORKGROUPS: [
			{
				label: 'All my groups',
				icon: 'index.php?extern=images/16x16/places/folder-development.png',
				path: 'workgroup:///'
			}
		]
	}
});


