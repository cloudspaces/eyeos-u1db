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

function calendar_application(checknum, pid, args) {
	var myApp = new eyeos.application.Calendar(checknum, pid, args);
	myApp.drawGUI();
}

qx.Class.define('eyeos.application.Calendar', {
	extend: eyeos.system.EyeApplication,
	
	statics: {
		DEFAULT_WIDTH: 800,
		DEFAULT_HEIGHT: 600,		
		ButtonDecorator_released: new qx.ui.decoration.Beveled(null, null, 100).set({
			backgroundColor: null
		}),		
		ButtonTextColor_released: '#4886ce'
	},
	
	construct: function(checknum, pid, args) {
		arguments.callee.base.call(this, 'calendar', checknum, pid);
	},
	members: {
	
		// GUI components
		__menuBar: null,
		__toolBar: null,
		__window: null,
		
		// Menu / Toolbar actions
		__menuToolbarActions: null,
		
		// Other
		_controller: null,
        _today: null,
        _viewModeSelector: null,
        _imageBack: null,
        _labelBack: null,
        _blockCalendar: null,
        _myCalendars: null,
        _cursor: null,
		
		_buildMenuBar: function() {
			this.__menuBar = new eyeos.ui.menubar.MenuBar();
			this.__menuBar.setIconsPath('index.php?extern=images/calendar/');
			this.__menuBar.setItems(
	            new eyeos.calendar.menu.Items().getItems()
	        );
			this.__menuBar.setActions(this._getMenuToolbarActions());
			this.__menuBar.createMenuBar();
			
			return this.__menuBar;
		},
		
		_buildToolBar: function() {
			this.__toolBar = new qx.ui.toolbar.ToolBar();
			
			this.__toolBar = new eyeos.ui.toolbar.ToolBar();
			this.__toolBar.setHeight(50);
			this.__toolBar.setIconsPath('index.php?extern=images/calendar/');
			this.__toolBar.setItems(
				new eyeos.calendar.toolbar.Items().getItems()
			);
			this.__toolBar.setActions(this._getMenuToolbarActions());
			/*this.__topToolBar.setHeader(
				new eyeos.ui.toolbar.Header(this.__iconsPath + 'application-switch.png')
			);*/
			this.__toolBar.createToolBar();
			return this.__toolBar;
		},
		
		/**
		 * Builds the 'work area' of the window: everything but the menubar & toolbar. 
		 */
		_buildWorkArea: function() {
			var workArea = new qx.ui.splitpane.Pane('horizontal');
			
			workArea.add(this._buildWorkArea_leftPane(), 0);
			workArea.add(this._buildWorkArea_rightPane(), 1);
			
			return workArea;
		},
		
		/**
		 * Build the left pane (mini-grid calendar / my calendars list / shared calendars list)
		 */
		_buildWorkArea_leftPane: function() {
			var leftContainer = new qx.ui.container.Composite(new qx.ui.layout.VBox(5)).set({
				width: 170,
				minWidth: 170,
				padding: 2,
				backgroundColor: '#ffffff'
			});
			
			//
			//	BLOCK CALENDAR (MINI-CALENDAR)
			//
			this._blockCalendar = new eyeos.calendar.view.BlockCalendar().set({
				controller: this._controller
			});
		
			this._blockCalendar.setPeriodMode(eyeos.calendar.Constants.PERIOD_MODE_MONTH);		//TODO default mode will be configurable by the user
			leftContainer.add(this._blockCalendar);
			
			//
			//	MY CALENDARS
			//
			this._myCalendars = new eyeos.calendar.view.MyCalendarsList().set({
				controller: this._controller
			});
			leftContainer.add(this._myCalendars);
			
			//
			//	group CALENDARS
			
			var groupCalendars = new eyeos.calendar.view.GroupCalendarsList().set({
				controller: this._controller
			});			
					
			leftContainer.add(groupCalendars);

            //
			//	remote CALENDARS

			/*var remoteCalendars = new eyeos.calendar.view.RemoteCalendarsList().set({
				controller: this._controller
			});

			leftContainer.add(remoteCalendars);*/

			//
			//	OTHER CALENDARS
			//
			/* TODO */
			
			return leftContainer;
		},
		
		/**
		 * Builds the calendar view.
		 */
		_buildWorkArea_rightPane: function() {
			var rightContainer = new qx.ui.container.Composite(new qx.ui.layout.VBox(2)).set({
				padding: 1,
				backgroundColor: '#ffffff'
			});
			
			//
			//	FIRST ROW: Current period + View mode selector
			//
			var firstContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox()).set({
				height: 28,
				margin: 2
			});
			firstContainer.getLayout().setAlignY('bottom');
			rightContainer.add(firstContainer);
			this._today = new qx.ui.form.Button(tr('Today')).set({
				
				/*allowGrowY: false,
				decorator: btnDecorator,
				cursor: 'pointer',
                backgroundColor:"#e9e9e9",
                marginRight:3*/
			});
			this._today.addListener('execute', function(e) {
				this._controller.setCalendarSelectedDate(new Date());
				//console.log(this._controller);
			}, this);
			firstContainer.add(this._today, {flex: 1, width: '10%'});
			firstContainer.add(new qx.ui.core.Spacer(5))
			var currentPeriodLabel = new eyeos.calendar.view.SimplePeriodDisplay().set({
				controller: this._controller
			});
			firstContainer.add(currentPeriodLabel, {flex: 1, width: '25%'});
			
			this._viewModeSelector = new eyeos.calendar.view.ViewModeSelector().set({
				controller: this._controller
			});
			firstContainer.add(this._viewModeSelector, {flex: 1, width: '40%'});
			
			// Spacer (right)
			firstContainer.add(new qx.ui.core.Spacer(), {flex: 1, width: '15%'});

            this._imageBack = new qx.ui.basic.Image().set({
                width: 16,
                height: 16,
                source: "index.php?extern=images/16x16/actions/go-previous-view.png",
                cursor: 'pointer'
            });

            this._imageBack.addListener('click',this.createSelectCalendars,this);

            firstContainer.add(this._imageBack);

            this._labelBack = new qx.ui.basic.Label(tr('Back')).set({
                font: new qx.bom.Font(12, ['Sans-serif']).set({
                    bold :true
                }),
                textColor: '#4f565c',
                marginLeft: 5,
                marginRight: 10,
                cursor: 'pointer'
            });
            this._labelBack.addListener('click',this.createSelectCalendars,this);
            firstContainer.add(this._labelBack);


			//
			//	SECOND ROW: Ribbon calendar
			//
			/*var secondContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox()).set({
				height: 34,
				margin: 2
			});
			
			var ribbonCalendar = new eyeos.calendar.view.RibbonCalendar().set({
				controller: this._controller
			});
			secondContainer.add(ribbonCalendar, {flex: 1});
			
			rightContainer.add(secondContainer);*/
			//	THIRD ROW: Main grid calendar
			//
			var thirdContainer = new qx.ui.container.Composite(new qx.ui.layout.HBox()).set({
				margin: 2
			});

			var gridCalendar = new eyeos.calendar.view.GridCalendar().set({
				controller: this._controller
			});
            this._controller.setGridCalendar(gridCalendar);
            gridCalendar.showHeader(false);
			thirdContainer.add(gridCalendar, {flex: 1});
			rightContainer.add(thirdContainer, {flex: 1});
			return rightContainer;
		},
		
		_getMenuToolbarActions: function() {
			if (!this._menuToolbarActions) {
				this._menuToolbarActions = new eyeos.calendar.Actions(this.__window, this._checknum, this, this._controller);
			}
			return this._menuToolbarActions;
		},
		
		/**
		 * Builds the Graphical User Interface.
		 */
		drawGUI: function() {
			this.__window = new eyeos.ui.Window(this, tr('Calendar'), 'index.php?extern=images/16x16/apps/office-calendar.png').set({
				width: this.self(arguments).DEFAULT_WIDTH,
				height: this.self(arguments).DEFAULT_HEIGHT,
				contentPadding: 0,
				backgroundColor: '#ffffff',
				destroyOnClose: true
			});
			this.__window.setLayout(new qx.ui.layout.VBox());
			
			// --For debug purposes only--
			this.__window.addListener('keypress', function(e) {
				if (e.isCtrlPressed() && e.getKeyIdentifier() == 'L') {
					var locManager = qx.locale.Manager.getInstance();
					switch (locManager.getLocale()) {
						case 'fr_FR':
							locManager.setLocale('en_US');
							break;
						
						case 'en_US':
							locManager.setLocale('es_ES');
							break;
							
						case 'es_ES':
							locManager.setLocale('fr_FR');
							break;
						
						default:
							locManager.setLocale('en_US');
							break;
					}
				}
				e.preventDefault();
			}, this);

			// --For debug purposes only--
			
			// Initialize controller for view parts
            this._controller = new eyeos.calendar.Controller(this._checknum,this);
            this._controller.setMainWindow(this.__window);

            this.__window.addListener('beforeClose',function() {
                this.__closeTimers();
                this._controller.close = true;
                this.closeCursor();
            },this);

            this.__window.addListener('appear',function() {
                this.createSelectCalendars();
            },this);

            this.__window.addListener('resize',function() {
                this.centerCursor();
            },this);

            this.__window.addListener('move',function() {
                this.centerCursor();
            },this);

			this.__window.open();
		},
        __createCalendars: function(typeCalendar) {
            this.__window.removeAll();
            this.__window.setCaption(tr('Calendar') + '-' + tr(typeCalendar));
            this._controller.setTypeCalendar(typeCalendar);

            //
            //	MENUBAR
            //
            this.__window.add(this._buildMenuBar());
            //
            //	TOOLBAR
            //
            //this.__window.add(this._buildToolBar());
            //
            //	WORKAREA
            //
            this.__window.add(this._buildWorkArea(), {flex: 1});
            this.enabledWindowsComponents(false);
            this.showCursor();
            this._controller.setCalendarPeriodMode(eyeos.calendar.Constants.PERIOD_MODE_WEEK);
            this._controller.init();
        },
        enabledWindowsComponents: function(enabled) {
            var cursor = 'default';
            if(enabled) {
                cursor = 'pointer';
            }

            this._today.set({
                enabled: enabled,
                cursor: cursor
            });

            this._imageBack.set({
                enabled: enabled,
                cursor: cursor
            });

            this._labelBack.set({
                enabled: enabled,
                cursor: cursor
            })

            this._viewModeSelector.showSelector(enabled);
            this._blockCalendar.enabledCalendar(enabled);
            this._myCalendars.enabledCalendarList(enabled);
            this._controller.setEnabled(enabled);
        },
        createSelectCalendars:function() {
            this.__window.setCaption(tr('Calendar'));
            this.__window.removeAll();
            this.__closeTimers();
            this._controller.setTypeCalendar(null);
            this.closeCursor();

            var cursor = new qx.ui.basic.Image().set({
                width: 42,
                height: 42,
                source: "index.php?extern=images/ajax-loader-1.gif"
            });
            this.__centerCursor(cursor);
            this._controller.selectCalendars();
            this.__window.add(cursor);
        },
        __closeTimers: function() {
            this._controller.closeTimer();
            this._controller.closeTimerCalendar();
        },
        __centerCursor:function(cursor) {
            var left = (this.__window.getWidth() - cursor.getWidth())/2;
            var top = ((this.__window.getHeight() - cursor.getHeight())/2) - 40;
            cursor.setMarginTop(top);
            cursor.setMarginLeft(left);
        },
        selectCalendars: function(calendars) {
            this.__window.removeAll();
            var cals = [];
            if(calendars.status == 'OK') {
                for(var i in calendars.clouds) {
                    if(calendars.clouds[i].permission === "true") {
                        cals.push(calendars.clouds[i].cloud);
                    }
                }

            } else {
                eyeos.error(tr('Error load calendars'));
            }
            cals.push(tr('EyeOS'));

            var labelCalendars = new qx.ui.basic.Label().set({
                 value: tr('Calendars'),
                 font: new qx.bom.Font(24, ["Lucida Grande", "Verdana"]).set({bold: true}),
                 alignX: 'center',
                 marginTop: 50
            });

            this.__window.add(labelCalendars);

            var scroll = new qx.ui.container.Scroll().set({
                allowStretchY: false,
                allowStretchX: false,
                width: 170,
                height: 400,
                alignX: 'center',
                marginTop: 60
            });

            var containerCalendars = new qx.ui.container.Composite().set({
                layout: new qx.ui.layout.VBox()
            });
            scroll.add(containerCalendars);
            this.__window.add(scroll);

            for(var i in cals) {
                var containerCal = new qx.ui.container.Composite().set({
                    layout: new qx.ui.layout.HBox(),
                    marginBottom: 40
                });

                var imageCloud = new qx.ui.basic.Image().set({
                    width: 32,
                    height: 32,
                    source: "index.php?extern=images/32x32/actions/go-next-view.png",
                    cursor: 'pointer'
                });

                imageCloud.setUserData('cloud',cals[i]);
                imageCloud.addListener('click',this.__cloudClick,this);

                var labelCloud = new qx.ui.basic.Label().set({
                    value: cals[i],
                    font: new qx.bom.Font(18, ["Lucida Grande", "Verdana"]),
                    marginTop: 4,
                    marginLeft: 10,
                    cursor: 'pointer'
                });
                labelCloud.addListener('mouseover',this.__labelMouseOver,this);
                labelCloud.addListener('mouseout',this.__labelMouseOut,this);
                labelCloud.setUserData('cloud',cals[i]);
                labelCloud.addListener('click',this.__cloudClick,this);
                imageCloud.setUserData('labelCloud',labelCloud);
                imageCloud.addListener('mouseover',this.__imageMouseOver,this);
                imageCloud.addListener('mouseout',this.__imageMouseOut,this);
                containerCal.add(imageCloud);
                containerCal.add(labelCloud);
                containerCalendars.add(containerCal);
            }
        },
        __cloudClick: function(e) {
            var cloud = e.getCurrentTarget().getUserData('cloud');
            this.__createCalendars(cloud);

        },
        showCursor: function() {
            if(this._cursor == null) {
                this._cursor = new eyeos.calendar.view.Cursor(this.__window);
                this._cursor.open();
            }
        },
        closeCursor: function() {
            if(this._cursor != null) {
                this._cursor.close();
                this._cursor = null;
            }
        },
        centerCursor: function() {
            if(this._cursor != null) {
                this._cursor.centerCursor(this.__window);
            }
        },
        __labelMouseOver: function(e) {
            e.getCurrentTarget().setTextColor('blue');
        },
        __labelMouseOut: function(e) {
            e.getCurrentTarget().setTextColor('black');
        },
        __imageMouseOver: function(e) {
            var labelCloud = e.getCurrentTarget().getUserData('labelCloud');
            labelCloud.setTextColor('blue');
        },
        __imageMouseOut: function(e) {
            var labelCloud = e.getCurrentTarget().getUserData('labelCloud');
            labelCloud.setTextColor('black');
        }
	},
	destruct : function() {
		this._disposeObjects('_controller');
	}
});
