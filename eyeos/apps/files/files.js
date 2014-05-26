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

function files_application(checknum, pid, args) {
	var filesApplication = new eyeos.system.EyeApplication('files', checknum, pid);
	var filesController = new eyeos.files.Controller(filesApplication, args, 'iconview', checknum);
}

qx.Class.define('eyeos.files.Controller', {

	extend: qx.core.Object,
	
	construct: function (application, args, defaultView, checknum) {
		
		arguments.callee.base.call(this);
		
		// SETTERS
		this.setApplication(application);
        this.setChecknum(checknum);

		/*
		 * We fill with a default path in case none is given
		 * Init of the model and the view manager
		 */
		
		var defArgs;
		if (args[0] == undefined) {
			defArgs = ['path', 'home:///'];
		} else {
			defArgs = ['path', args[0]];
		}
		
		this.setModel(new eyeos.files.Model(defaultView, defArgs));

		/*
		 * Init of the application depending on what we have to show
		 */
		this._addListeners();
		this.setView(new eyeos.files.ViewManager(
				this,
				this.getModel(),
				this.getApplication(),
				'Files',
				'index.php?extern=/images/16x16/apps/system-file-manager.png',
				false,
				false
		));

        this._getToken();

        /*eyeos.callMessage(this.getApplication().getChecknum(), 'getToken', null, function (results) {

        },this);*/

		// Init SocialBarUpdater
		/*var socialBarUpdater = new eyeos.files.SUManager(this.getView()._socialBar, checknum);
		this.setSocialBarUpdater(socialBarUpdater);
		this._addSocialBarUpdaterListeners();
		
		this._browse(true);*/
		//this.getView().maximize();
	},


	properties: {
		application: {
			check: 'Object'
		},

		model: {
			check: 'Object'
		},
		
		view: {
			check: 'Object'
		},

		socialBarUpdater: {
			check: 'eyeos.files.SocialBarUpdater',
			init: null
		},
        token: {
            check: 'Boolean',
            init: false
        },
        checknum: {
            init: null,
            check: 'Integer'
        }
	},

	members: {

		_dBusListeners: new Array(),
		
		_filesQueue: eyeos.filesQueue.getInstance(),
		_dBus: eyeos.messageBus.getInstance(),
        _metadatas: new Array(),
        _timer: null,
        __progress: null,
        __size: 0,
        __stacksync: false,

		_addListeners: function () {
//			this.addListener('selectedFile', function (e) {
//				var selected = e.getData();
//				var temp = new Array();
//				var callToUpdateContacts = true;
//				if (selected.length == 1) {
//					temp.push(selected[0].getFile());
//				} else if (selected.length > 1) {
//					for (var i = 0; i < selected.length; ++i) {
//						temp.push(selected[i].getFile());
//						callToUpdateContacts = false;
//					}
//				}
//				this.getView().updateSocialBar(temp, callToUpdateContacts);
//			});
//
//			this.addListener('cleanSocialBar', function () {
//				this.getView().cleanSocialBar();
//			});


			// DBUS Messages for syncing all "Files"

			/*
			 * eyeos_files_delete - Deletes a file from the Model in case our current path is the same as the source one and updates the view
			 *
			 * @receives {Array} [sourcePath: string, files: Array] 
			 */
			this._dBusListeners.push(this._dBus.addListener('eyeos_files_delete', function (e) {
				var sourcePath = e.getData()[0];
				var filesToDelete = e.getData()[1];
				var currentPath = this.getModel().getCurrentPath()[1];
				var currentFiles = this.getModel().getCurrentFiles();

				if(sourcePath.charAt(sourcePath.length-1) != '/') {
					sourcePath = sourcePath + '/';
				}
				if(currentPath.charAt(sourcePath.length-1) != '/') {
					currentPath = currentPath + '/';
				}
				if (sourcePath == currentPath) {
					for (var i = currentFiles.length - 1; i >= 0; --i) {
						if (filesToDelete.indexOf(currentFiles[i].getAbsolutePath()) != -1) {
							this.getModel().getCurrentFiles().splice(i, 1);
						}
					}
					this.getView().showBrowse();
				} else if (filesToDelete.indexOf(currentPath) != -1) {
					this._browsePath('home://~'+eyeos.getCurrentUserName()+'/');
				}

				//Update SocialBar
				var params = {
					path: currentPath,
					checknum: this.getApplication().getChecknum()
				}
				this.getSocialBarUpdater().directoryChanged(params);
			}, this));

			/*
			 * eyeos_files_new - Adds a file to the Model in case our current path is the same as the source one and updated the view
			 *
			 * @receives {Array} [sourcePath: string, file: Object]
			 */
			this._dBusListeners.push(this._dBus.addListener('eyeos_files_new', function (e) {

				var sourcePath = e.getData()[0];
				var fileToCreate = e.getData()[1];
				var currentPath = this.getModel().getCurrentPath()[1];

				if (sourcePath == currentPath) {
					var file = new eyeos.files.File(fileToCreate);
					this.getModel().getCurrentFiles().push(file);
					this.getView()._view.showBrowse();

                    var items = this.getView()._view.returnAll();
                    //en items tengo un array de IconViewItems
                    var i = 0;
                    var size = items.length;
                    for(i=0;i<size;i++) {
                        if(items[i].getFile().getName() == e.getData()[1].name) {
                            items[i].select();
                            this.editFile();
                            i = size;
                        }

                    }
				}
			}, this));

			/*
			 * eyeos_files_cut - Directly updates the view if the source folder is our current one
			 *
			 * @receives {Array} [sourcePath: string, files: Array]
			 */
			this._dBusListeners.push(this._dBus.addListener('eyeos_files_cut', function (e) {

				var sourcePath = e.getData()[0];
				var currentPath = this.getModel().getCurrentPath()[1];

				var filesToCut = e.getData()[1];
				var filesToCutPath = new Array();
				for (var i = 0; i < filesToCut.length; ++i) {
					filesToCutPath[i] = filesToCut[i].getAbsolutePath();
				}

				if (sourcePath == currentPath) {
					var currentFiles = this.getModel().getCurrentFiles();
					var currentFilesPaths = new Array();
					for (var i = 0; i < currentFiles.length; ++i) {
						currentFilesPaths[i] = currentFiles[i].getAbsolutePath();
					}

					for (var i = 0; i < filesToCut.length; ++i) {
						var index = currentFilesPaths.indexOf(filesToCutPath[i]);
						if (index != -1) {
							currentFiles[index].setCutted(true);
						}
					}

					this.getView().showBrowse();
				} else if (filesToCutPath.indexOf(currentPath) != -1) {
					this._browsePath('home://~'+eyeos.getCurrentUserName()+'/');
				}
			}, this));

			/*
			 * eyeos_files_update - Adds/Remove information to the Files objects and updates the view
			 *
			 * @receives {Array} [sourcePath: string, files: Array]
			 */

			this._dBusListeners.push(this._dBus.addListener('eyeos_files_update', function (e) {
				var sourcePath = e.getData()[0];
				var filesToUpdate = e.getData()[1];
				var currentPath = this.getModel().getCurrentPath()[1];

				if (currentPath.substr(currentPath.length - 1) != '/') {
					currentPath += '/';
				}

				if (sourcePath.substr(sourcePath.length - 1) != '/') {
					sourcePath += '/';
				}
			
				if (sourcePath == currentPath) {

					var currentFiles = this.getModel().getCurrentFiles();
					var currentFilesPath = new Array();
					var filesToUpdatePath = new Array();

					for (var i = 0; i < filesToUpdate.length; ++i) {
						filesToUpdatePath.push(filesToUpdate[i].getAbsolutePath());
					}

					for (var i = 0; i < currentFiles.length; ++i) {
						currentFilesPath.push(currentFiles[i].getAbsolutePath());
					}

					for (var i = 0; i < filesToUpdate.length; ++i) {
						var index = currentFilesPath.indexOf(filesToUpdatePath[i]);
						if (index != -1) {
							currentFiles[index].setShared(filesToUpdate[i].getShared());
							currentFiles[index].setRating(filesToUpdate[i].getRating());
							
						}
					}

					var returnSelected = this.getView().returnSelected();
					for (var i = 0; i < returnSelected.length; ++i) {
						returnSelected[i].updateImage();
					}

//					this.getView().showBrowse();
				}
			}, this));

			/*
			 * eyeos_files_paste - Adds/Remove files to/from the Model in case our current path is the source or the target one and updates the view
			 *
			 * @receives {Array} [files: Array, action: string, sourcePath: string, targetPath: string, results: Array]
			 *
			 * (results is just used when our action is copy, it's an array containing the new names of the files in case they have been renamed
			 */

			this._dBusListeners.push(this._dBus.addListener('eyeos_files_paste', function (e) {
				var files = e.getData()[0];
				var action = e.getData()[1];
				var source = e.getData()[2];
				var target = e.getData()[3];
				var results = e.getData()[4];
				var currentPath = this.getModel().getCurrentPath()[1];
				var currentFiles = this.getModel().getCurrentFiles();

				var filesPath = new Array();
				var currentFilesPath = new Array();

				for (var i = 0; i < currentFiles.length; ++i) {
					currentFilesPath.push(currentFiles[i].getAbsolutePath());
				}

				for (var i = 0; i < files.length; ++i) {
					filesPath.push(files[i].getAbsolutePath());
				}

				if (action == 'move') {

					var toSplice = new Array();

					for (var i = files.length - 1; i >= 0; --i) {
						var index = currentFilesPath.indexOf(filesPath[i]);
						if (index != -1) {
							if (target == currentPath) {
								currentFiles[index].setCutted(false);
							} else {
								toSplice.push(index);
							}
						} else {
							if (target == currentPath) {
								var destination = target + '/' + files[i].getName();
								var index = currentFilesPath.indexOf(destination);
								if (index == -1) {
									var newFile = {
										type: files[i].getType(),
										size: files[i].getSize(),
										name: files[i].getName(),
										extension: files[i].getExtension(),
										permissions: files[i].getPermissions(),
										owner: files[i].getOwner(),
										path: target,
										absolutepath: destination,
										shared: files[i].getShared(),
										rating: files[i].getRating(),
										created: files[i].getCreated(),
										modified: files[i].getModified()
									};
									var nFile = new eyeos.files.File(newFile);
									this.getModel().getCurrentFiles().push(nFile);
								} else {
									currentFiles[index].set({
										type: files[i].getType(),
										size: files[i].getSize(),
										name: files[i].getName(),
										extension: files[i].getExtension(),
										permissions: files[i].getPermissions(),
										owner: files[i].getOwner(),
										shared: files[i].getShared(),
										rating: files[i].getRating(),
										created: files[i].getCreated(),
										modified: files[i].getModified()
									});
								}
							}
						}
					}

					if (toSplice.length >= 1) {
						for (var i = 0; i < toSplice.length; ++i) {
							this.getModel().getCurrentFiles().splice(toSplice[i], 1);
						}
					}

				} else if (action == 'copy') {
					if (target == currentPath) {
						for (var i = 0; i < results.length; ++i) {
							if (currentFilesPath.indexOf(results[i].absolutepath) == -1) {
								var futureFile = currentPath  + results[i].name;
								if (currentFilesPath.indexOf(futureFile) == -1) {
									var file = new eyeos.files.File(results[i]);
									file.setShared('0');
									this.getModel().getCurrentFiles().push(file);
								}
							}
						}
					}
				}

				this.getView().showBrowse();
				
			}, this));

			/*
			 * eyeos_files_paste - Adds/Remove files to/from the Model in case our current path is the source or the target one and updates the view
			 *
			 * @receives {Array} [files: Array, action: string, sourcePath: string, targetPath: string, results: Array]
			 *
			 * (results is just used when our action is copy, it's an array containing the new names of the files in case they have been renamed
			 */

			this._dBusListeners.push(this._dBus.addListener('eyeos_files_drop', function (e) {
				var files = e.getData()[0];
				var source = e.getData()[1];
				var target = e.getData()[2];

				if(target.charAt(target.length-1) != '/') {
					target = target + '/';
				}

				var currentPath = this.getModel().getCurrentPath()[1];

				if(currentPath.charAt(currentPath.length-1) != '/') {
					currentPath = currentPath + '/';
				}
				var currentFiles = this.getModel().getCurrentFiles();

				var filesPath = new Array();
				var currentFilesPath = new Array();

				for (var i = 0; i < currentFiles.length; ++i) {
					currentFilesPath.push(currentFiles[i].getAbsolutePath());
				}

				for (var i = 0; i < files.length; ++i) {
					filesPath.push(files[i].getAbsolutePath());
				}

				var toSplice = new Array();

				for (var i = files.length - 1; i >= 0; --i) {
					var index = currentFilesPath.indexOf(filesPath[i]);
					if (index != -1) {
						if (target != currentPath) {
							toSplice.push(index);
						}
					} else {
						if (target == currentPath) {
							var destination = target + files[i].getName();
							var index = currentFilesPath.indexOf(destination);
							if (index == -1) {
								var newFile = {
									type: files[i].getType(),
									size: files[i].getSize(),
									name: files[i].getName(),
									extension: files[i].getExtension(),
									permissions: files[i].getPermissions(),
									owner: files[i].getOwner(),
									path: target,
									absolutepath: destination,
									shared: files[i].getShared(),
									rating: files[i].getRating(),
									created: files[i].getCreated(),
									modified: files[i].getModified()
								};
								if(newFile.extension == 'LNK') {
									newFile.content = files[i].getContent();
								}
								var nFile = new eyeos.files.File(newFile);
								this.getModel().getCurrentFiles().push(nFile);
							} else {
								currentFiles[index].set({
									type: files[i].getType(),
									size: files[i].getSize(),
									name: files[i].getName(),
									extension: files[i].getExtension(),
									permissions: files[i].getPermissions(),
									owner: files[i].getOwner(),
									shared: files[i].getShared(),
									rating: files[i].getRating(),
									created: files[i].getCreated(),
									modified: files[i].getModified()
								});
							}
						}
					}
				}

				if (toSplice.length >= 1) {
					for (var i = 0; i < toSplice.length; ++i) {
						this.getModel().getCurrentFiles().splice(toSplice[i], 1);
					}
				}

				this.getView().showBrowse();

			}, this));

			/*
			 * eyeos_files_rename - Adds/Remove files to/from the Model in case our current path is the source or the target one
			 *
			 * @receives {Array} [oldName: string, sourcePath: string, results: Object containing the data of the file]
			 */

			this._dBusListeners.push(this._dBus.addListener('eyeos_files_rename', function (e) {
				var sourcePath = e.getData()[1];
				var currentPath = this.getModel().getCurrentPath()[1];
				if (sourcePath == currentPath) {
					var oldName = e.getData()[0];
					var currentFiles = this.getModel().getCurrentFiles();
					var results = e.getData()[2];
					for (var i = 0; i < currentFiles.length; ++i) {
						if (currentFiles[i].getAbsolutePath() == oldName) {
							currentFiles[i].setName(results.name);
							currentFiles[i].setAbsolutePath(results.absolutepath);
						}
					}
				}
				this.getView().showBrowse();
			}, this));

			this._dBusListeners.push(this._dBus.addListener('eyeos_file_uploadComplete', function (e) {
				var currentPath = this.getModel().getCurrentPath()[1];
				var splitted = e.getData().absolutepath.split('/');
				var path = '';
				for (var i = 0; i < splitted.length - 1; ++i) {
					if (splitted[i] != '') {
						if (i == 0) {
							path += splitted[i] + '//';
						} else {
							path += splitted[i] + '/';
						}
					}
				}

				if (currentPath.substring(currentPath.length - 1) != '/') {
					currentPath += '/';
				}

				if (path == currentPath) {
					var file = new eyeos.files.File(e.getData());
					this.getModel().getCurrentFiles().push(file);
					this.getView().showBrowse();
				}
			}
			, this));

			this._dBusListeners.push(this._dBus.addListener('eyeos_socialbar_ratingChanged', function (e) {
				var eventPath = e.getData()['path'];
				var eventFiles = e.getData()['files'];
				var currentPath = this.getModel().getCurrentPath()[1];
				
				if (eventPath == currentPath) {
					var modelFiles = this.getModel().getCurrentFiles();
					for (var i = 0; i < modelFiles.length; ++i) {
						for (var j=0; j < eventFiles.length; ++j) {
							if (modelFiles[i].getAbsolutePath() == eventFiles[j].getAbsolutePath()) {
								modelFiles[i].setRating(eventFiles[j].getRating());
							}
						}
					}
				}
			}
			, this));


            this._dBusListeners.push(this._dBus.addListener('eyeos_file_refreshStackSync',function(e) {
                if(e.getData().length > 0) {
                    this._browsePath(e.getData()[0]);
                }
            },this));

		},

		_addSocialBarUpdaterListeners: function () {
			this.addListener('selectedFile', function (e) {
				var params = {
					path: this.getModel().getCurrentPath()[1],
					selected: this._getFilesFromIconViews(e.getData()),
					checknum: this.getApplication().getChecknum()
				};
                if (this.__isStacksync(params.path)) {
                    params.selected = this.__getFilesFromIconViewsStackSync(params.path,params.selected);
                }
				this.getSocialBarUpdater().selectionChanged(params);
			}, this);

			this.addListener('directoryChanged', function (e) {
				var params = {
					path: e.getData(),
					checknum: this.getApplication().getChecknum()
				}
				this.getSocialBarUpdater().directoryChanged(params);
			}, this);
		},

        __getFilesFromIconViewsStackSync: function(path,list)
        {
            for(var i=0; i<list.length; i++) {
                var metadata = this.__getFileId(path,list[i].getName(),true);
                if (metadata) {
                    console.log(metadata)
                    list[i].setSize(metadata.size);
                }
            }
            return list;
        },

		_getFilesFromIconViews: function (iconViews) {
			var filesArray = [];
			for (var i = 0; i < iconViews.length; ++i) {
				filesArray.push(iconViews[i].getFile());
			}
			return filesArray;
		},

		_browse: function (addToHistory) {
			var currentPath = this.getModel().getCurrentPath();
			this._browsePath(currentPath[1], addToHistory);
		},

		_browsePath: function(path, addToHistory,refresh) {
            this.closeTimer();
            if (this.__isStacksync(path)) {
                var params = new Object();
                params.path = path;
                params.fileId = this.__getFileIdFolder(path);

                if(params.fileId !== null) {
                    eyeos.callMessage(this.getApplication().getChecknum(), 'getMetadata', params, function (results) {
                        if(results) {
                            if(this.__insertMetadata(JSON.parse(results),path) || !refresh) {
                                this.__callBrowsePath(path,addToHistory,true);
                            } else {
                                this.__refreshFolder(path,addToHistory,true);
                            }
                        } else {
                            this.__refreshFolder(path,addToHistory,true);
                        }
                    }, this, null, 12000);
                } else {
                    this.__callBrowsePath(path,addToHistory,false);
                }
            } else {
                this.__callBrowsePath(path,addToHistory,false);
            }
		},

        __callBrowsePath: function(path,addToHistory, refresh) {
            eyeos.callMessage(this.getApplication().getChecknum(), 'browsePath', [path, null, null], function (results) {
                this._browsePath_callback(results, path, addToHistory);
                if(refresh) {
                    this.__refreshFolder(path,addToHistory,refresh);
                }
            }, this, null, 12000);
        } ,

		_browsePath_callback: function(results, path, addToHistory) {
			// Send data to the model
			this.getModel().setCurrentPath(['path', results.absolutepath]);

			if (addToHistory) {
				this._addToHistory('path');
			}

			// Empty the array with all the previous files
			this.getModel().getCurrentFiles().splice(0, this.getModel().getCurrentFiles().length);
			
			// The Cut/Copy/Paste queue
			var filesQueue = this._filesQueue.getMoveQueue();
			var action = this._filesQueue.getAction();
			var filesQueuePath = new Array();
			for (var i = 0; i < filesQueue.length; ++i) {
				filesQueuePath.push(filesQueue[i].getAbsolutePath());
			}

			// Foreach file we will create a "file object" that will contain all the data of the file
			for (var i = 0; i < results.files.length; ++i) {
				if(path == 'share:///') {
					results.files[i].sharedByContacts = true;
				}

				var item = new eyeos.files.File(results.files[i]);

				var index = filesQueuePath.indexOf(results.files[i].absolutepath);
				if (index != -1 && action == 'move') {
					item.setCutted(true);
				}

				this.getModel().getCurrentFiles().push(item);
			}

			// We call to the view controller to show the browse
			var currentPath = this.getModel().getCurrentPath()[1];
			if (currentPath.substr(0, 8) == 'share://' || currentPath == 'workgroup:///') {
				this.getView()._view.setContextMenu(null);
			} else {
				this.getView()._view.setContextMenu(this.getView()._view._menu);
			}
			this.getView().showBrowse();

			this.fireDataEvent('directoryChanged', currentPath);
		},

		_addToHistory: function (input) {
			// If we have to add this path to the history ...
			var history = this.getModel().getHistory();
			var historyIndex = this.getModel().getHistoryIndex();
			// A new position is added on the array pointing to our current path
			history[historyIndex] = [input, this.getModel().getCurrentPath()[1]];
			if (historyIndex > 0) {
				this.getModel().getHistory().splice(parseInt(historyIndex + 1), parseInt(history.length - parseInt(historyIndex + 1)));
			}
			this.getModel().setHistoryIndex(historyIndex + 1);
		},

		specialMove: function (path, selection) {
			if(selection) {
				var filesToMove = [];
				var files = [path];
				for(var i = 0; i < selection.length; i++) {
					var info = selection[i].getUserData('info');
					var pathFromFile = info.absolutepath;
					var source = pathFromFile.replace(/\\/g, '/').replace(/\/[^\/]*\/?$/, '');
					var target = path;
					var content = selection[i].getUserData('content');
					files.push(pathFromFile);
					filesToMove.push({
							mPathFromFile: pathFromFile,
							mInfo: info,
							content: content,
							getAbsolutePath: function() {
								return this.mPathFromFile;
							},

							getName: function() {
								return this.mPathFromFile.replace(/^.*[\/\\]/g, '');
							},

							getType: function() {
								return this.mInfo.type;
							},

							getSize: function() {
								return this.mInfo.size;
							},

							getExtension: function() {
								return this.mInfo.extension;
							},

							getPermissions: function() {
								return this.mInfo.permissions;
							},

							getOwner: function() {
								return this.mInfo.owner;
							},

							getShared: function() {
								if(!this.mInfo.shared) {
									return "0";
								}
								return this.mInfo.shared;
							},

							getRating: function() {
								if(!this.mInfo.rating) {
									return "0";
								}
								return this.mInfo.rating;
							},

							getCreated: function() {
								try {
									var ret = this.mInfo.meta.creationTime;
								} catch (e) {
									var foo = new Date;
									var unixtime_ms = foo.getTime();
									var unixtime = parseInt(unixtime_ms / 1000);
									return unixtime;
								}
								return ret;
							},

							getModified: function() {
								try {
									var ret = this.mInfo.meta.modificationTime;
								} catch (e) {
									var foo = new Date;
									var unixtime_ms = foo.getTime();
									var unixtime = parseInt(unixtime_ms / 1000);
									return unixtime;
								}
								return ret;
							},

							getContent: function() {
								if(!this.content) {
									return "";
								} else {
									return this.content;
								}
							}
					});
				}
				eyeos.callMessage(this.getApplication().getChecknum(), 'move', files, function (results) {
						this._dBus.send('files', 'drop', [filesToMove, source, target]);
						this._browsePath(path);
						this._filesQueue.setDragQueue([]);
				}, this);
			} else {
				var filesToMove = this._filesQueue.getDragQueue();
				if (filesToMove.length >= 1) {
						var files = new Array();
						var action = this._filesQueue.getAction();
						var source = this._filesQueue.getDragSource();
						var target = path;
						for (var i = 0; i < filesToMove.length; ++i) {
								files.push(filesToMove[i].getAbsolutePath());
						}

						files.unshift(path);
						eyeos.callMessage(this.getApplication().getChecknum(), 'move', files, function (results) {
								this._dBus.send('files', 'drop', [filesToMove, source, target]);
								this._browsePath(path);
								this._filesQueue.setDragQueue([]);
						}, this);
				}
			}

		},
		
		openFile: function () {
			var filesToOpen = this.getView().returnSelected();
			var filesForViewer = new Array();
			var filesForDocuments = new Array();
			var filesForFemto = new Array();
			var foldersToOpen = new Array();
			var filesForImageViewer = new Array();
			var filesForDocPreview = new Array();
			var filesForPDFPreview = new Array();
			var filesForOpenLink = new Array();

			var extensionsForViewer = ['MP3','FLV','HTM','HTML','M4A','WAV','WMA','MOV', '3GP', '3GPP', '3G2', 'MP4', 'MPG', 'MPV', 'AVI', 'OGG', 'OGV', 'WEBM'];
			var extensionsForDocuments = ['EDOC'];
			var extensionsDocPreview = ['DOC', 'DOCX', 'ODT', 'ODS', 'OTS', 'SXC', 'XLS', 'XLT', 'XLS', 'XLSX', 'ODP', 'OTP', 'SXI', 'STI', 'PPT', 'POT', 'SXD', 'PPTX', 'PPSX', 'POTM', 'PPS', 'FODP', 'UOP'];
			var extensionsForFemto = ['TXT'];
			var extensionsForImageViewer = ['JPG', 'JPEG', 'BMP', 'GIF', 'PNG'];
			var extensionsForPDFViewer = ['PDF'];
			var extensionsForLink = ['LNK'];

            var parent = null;

            if(filesToOpen.length > 0 && this.__isStacksync(filesToOpen[0].getFile().getPath())) {
                parent = filesToOpen[0].getFile().getPath();
            }

			for (var i = 0; i < filesToOpen.length; ++i) {
				var type = filesToOpen[i].getFile().getType();
				var extension = filesToOpen[i].getFile().getExtension();
				if (type == 'folder') {
					foldersToOpen.push(filesToOpen[i].getFile().getAbsolutePath());
				} else {
                    console.log(filesToOpen[i].getFile().getName());
					if (extensionsForViewer.indexOf(extension) != -1) {
                        if(parent) {
                            filesForViewer.push(this.__getObjectDownloadStacksync(parent,filesToOpen[i].getFile().getAbsolutePath(),filesToOpen[i].getFile().getName()));
                        } else {
						    filesForViewer.push(filesToOpen[i].getFile().getAbsolutePath());
                        }
					}
					if (extensionsForImageViewer.indexOf(extension) != -1) {
                        if(parent) {
                            filesForImageViewer.push(this.__getObjectDownloadStacksync(parent,filesToOpen[i].getFile().getAbsolutePath(),filesToOpen[i].getFile().getName()));
                        } else {
						    filesForImageViewer.push(filesToOpen[i].getFile().getAbsolutePath());
                        }
					}
					if (extensionsForDocuments.indexOf(extension) != -1) {
                        if(parent) {
                            filesForDocuments.push(this.__getObjectDownloadStacksync(parent,filesToOpen[i].getFile().getAbsolutePath(),filesToOpen[i].getFile().getName()));
                        } else {
						    filesForDocuments.push(filesToOpen[i].getFile().getAbsolutePath());
                        }
					}
					if (extensionsForFemto.indexOf(extension) != -1) {
                        if(parent) {
                            filesForFemto.push(this.__getObjectDownloadStacksync(parent,filesToOpen[i].getFile().getAbsolutePath(),filesToOpen[i].getFile().getName()));
                        } else {
						    filesForFemto.push(filesToOpen[i].getFile().getAbsolutePath());
                        }

					}
					if (extensionsDocPreview.indexOf(extension) != -1) {
                        if(parent) {
                            filesForDocPreview.push(this.__getObjectDownloadStacksync(parent,filesToOpen[i].getFile().getAbsolutePath(),filesToOpen[i].getFile().getName()));
                        } else {
						    filesForDocPreview.push(filesToOpen[i].getFile().getAbsolutePath());
                        }
					}
					if (extensionsForPDFViewer.indexOf(extension) != -1) {
                        if(parent) {
                            filesForPDFPreview.push(this.__getObjectDownloadStacksync(parent,filesToOpen[i].getFile().getAbsolutePath(),filesToOpen[i].getFile().getName()));
                        } else {
						    filesForPDFPreview.push(filesToOpen[i].getFile().getAbsolutePath());
                        }
					}
					if (extensionsForLink.indexOf(extension) != -1) {
                        if(parent) {
                            filesForPDFPreview.push(this.__getObjectDownloadStacksync(parent,filesToOpen[i].getFile().getAbsolutePath(),filesToOpen[i].getFile().getName()));
                        } else {
                            filesForOpenLink.push(filesToOpen[i].getFile().getAbsolutePath());
                        }
					}
				}
			}
			if (filesForViewer.length >= 1) {
                if(parent) {
                    this.__openFileStacksync('viewer',filesForViewer);
                } else {
				    eyeos.execute('viewer', this.getApplication().getChecknum(), filesForViewer);
                }
			}

			if (filesForImageViewer.length >= 1) {
                if(parent) {
                    this.__openFileStacksync('imageviewer',filesForImageViewer);
                } else {
				    eyeos.execute('imageviewer', this.getApplication().getChecknum(), filesForImageViewer);
                }
			}
			
			if (filesForDocuments.length >= 1) {
                if(parent) {
                    this.__openFileStacksync('documents',filesForDocuments);
                } else {
				    eyeos.execute('documents', this.getApplication().getChecknum(), filesForDocuments);
                }
			}

			if (filesForDocPreview.length >= 1) {
                if(parent) {
                    this.__openFileStacksync('docviewer',filesForDocuments);
                } else {
				    eyeos.execute('docviewer', this.getApplication().getChecknum(), filesForDocPreview);
                }
			}

			if (filesForPDFPreview.length >= 1) {
                if(parent) {
                    this.__openFileStacksync('pdfviewer',filesForPDFPreview);
                } else {
				    eyeos.execute('pdfviewer', this.getApplication().getChecknum(), filesForPDFPreview);
                }
			}

			if (filesForFemto.length >= 1) {
                if(parent) {
                    this.__openFileStacksync('notepad',filesForFemto);
                } else {
				    eyeos.execute('notepad', this.getApplication().getChecknum(), filesForFemto);
                }
			}
			if (filesForOpenLink.length >= 1) {
                if(parent) {
                    this.__openFileStacksync('openLink',filesForOpenLink);
                } else {
				    eyeos.execute('openLink', this.getApplication().getChecknum(), filesForOpenLink);
                }
			}

			for (var i = 0; i < foldersToOpen.length; ++i) {
				eyeos.execute('files', this.getApplication().getChecknum(), [foldersToOpen[i]]);
			}
		},

		newFile: function (extension) {
			var currentPath = this.getModel().getCurrentPath()[1];
			if (currentPath.substr(0, 8) != 'share://' && currentPath != 'workgroup://') {
				var name = null;
				switch (extension) {
					case 'txt': {
						name = tr('New File');
						break;
					}
					case 'edoc': {
						name = tr('New Document');
						break;
					}
					case 'xls': {
						name = tr('New Spreadsheet');
						break;
					}
				}

				eyeos.callMessage(this.getApplication().getChecknum(), 'createNewFile', [currentPath + '/' + name + '.' + extension], function (results) {
					this._dBus.send('files', 'new', [currentPath, results]);
				},this);
			}
		},
		newLink: function() {
		   eyeos.execute('newLink', this.getApplication().getChecknum(), [this.getModel().getCurrentPath()[1]]);
		},

		uploadFile: function() {
		   eyeos.execute('upload', this.getApplication().getChecknum(), [this.getModel().getCurrentPath()[1]]);
		},
		newFolder: function () {
			var currentPath = this.getModel().getCurrentPath()[1];
			if (currentPath.substr(0, 8) != 'share://' && currentPath != 'workgroup://') {
				var name = tr('New Folder');

                var params = new Array(currentPath, name);

                if(this.__isStacksync(currentPath)) {
                    var fileId = this.__getFileIdFolder(currentPath);
                    if(fileId !== null) {
                        params.splice(params.length,0,fileId);
                    }
                }

                this.closeTimer();

				eyeos.callMessage(this.getApplication().getChecknum(), 'mkdir',params, function (results) {
                    if(this.__isStacksync(currentPath)) {
                        this._browsePath(currentPath);
                    } else {
					    this._dBus.send('files', 'new', [currentPath, results]);
                    }
				}, this);
			}
		},
		
		deleteFile: function () {
			var currentPath = this.getModel().getCurrentPath()[1];
			if (currentPath.substr(0, 8) != 'share://' && currentPath != 'workgroup://') {
				var filesToDelete = this.getView().returnSelected();
				var files = new Array();
				for (var i = 0; i < filesToDelete.length; ++i) {
					if(filesToDelete[i].getFile().getAbsolutePath() != 'home://~' + eyeos.getCurrentUserName() + '/Desktop') {
                        var params = new Object();
                        params.file = filesToDelete[i].getFile().getAbsolutePath();
                        if (this.__isStacksync(currentPath)) {
                            var id = this.__getFileId(currentPath,filesToDelete[i].getFile().getName());
                            if (id !== null) {
                                params.id =  id;
                            }
                        }
						files.push(params);
					}
				}
				if(files.length == 0) {
					alert('You can not deleted this folder');
					return;
				}

                this.closeTimer();

				eyeos.callMessage(this.getApplication().getChecknum(), 'delete', files, function (results) {
                    if(this.__isStacksync(currentPath)) {
                        this._browsePath(currentPath);
                    } else {
					    this._dBus.send('files', 'delete', [currentPath, results]);
                    }
				}, this);
			}
		},

		cutFile: function () {
			var currentPath = this.getModel().getCurrentPath()[1];
			if (currentPath.substr(0, 8) != 'share://' && currentPath != 'workgroup://') {
				var filesToCut = this.getView().returnSelected();

				var filesToCut_files = new Array();
				for (var i = 0; i < filesToCut.length; ++i) {
					filesToCut_files.push(filesToCut[i].getFile());
				}
				this._dBus.send('files', 'cut', [currentPath, filesToCut_files]);
				this._filesQueue.fillMoveQueue('move', filesToCut, currentPath);
			}
		},

		copyFile: function () {
			var filesToCopy = this.getView().returnSelected();
			var currentPath = this.getModel().getCurrentPath()[1];
			this._filesQueue.fillMoveQueue('copy', filesToCopy, currentPath);
		},

		pasteFile: function () {
			var filesToPaste = this._filesQueue.getMoveQueue();
            this.__checkFilesToPaste(filesToPaste);
			if (filesToPaste.length >= 1) {
				var source = this._filesQueue.getMoveSource();
				var target = this.getModel().getCurrentPath()[1];
				var action = this._filesQueue.getAction();
				var files = new Array();

				for (var i = 0; i < filesToPaste.length; ++i) {
					if (action == 'move') {
						if (target != filesToPaste[i].getPath()) {
							files.push(filesToPaste[i].getAbsolutePath());
						}
					} else {
						files.push(filesToPaste[i].getAbsolutePath());
					}
				}

				if (files.length >= 1) {
					files.unshift(target);

                    var stacksync = false;

                    if(action == 'copy' || action == 'move') {
                        var params = new Object();
                        params.folder = files[0];
                        files.splice(0,1);
                        params.files =files;

                        if(this.__isStacksync(params.folder) || this.__isStacksync(files[0])) {
                            stacksync = true;
                            var idParent = this.__getFileIdFolder(params.folder);
                            var filesAux = [];

                            if(!idParent) {
                                idParent = this.__getFileIdFolder(files[0]);
                            }

                            if(idParent !== null) {
                                params.idParent = idParent;
                                for(var i in filesToPaste) {
                                    var idFile = this.__getFileId(source,filesToPaste[i].getName());
                                    if(idFile !== null) {
                                        var file = new Object();
                                        file.id = idFile;
                                        file.path = filesToPaste[i].getAbsolutePath();
                                        filesAux.splice(filesAux.length,0,file);
                                    } else {
                                        filesAux.splice(filesAux.length,0,filesToPaste[i].getAbsolutePath());
                                    }
                                }

                                params.files = filesAux;
                            }
                        }

                    } else {
                        params = files;
                    }

                    if(action == 'copy' || action == 'move') {
                        this.__createWindowProgress(params,action);
                    } else {
                        eyeos.callMessage(this.getApplication().getChecknum(), action, params, function (results) {
                            if((action == "copy" || action == "move") && stacksync === true) {
                                this._browsePath(target);
                            } else {
                                this._dBus.send('files', 'paste', [filesToPaste, action, source, target, results]);
                            }
                            if (action == 'move') {
                                this._filesQueue.setMoveQueue([]);
                                this._filesQueue.setAction('');
                            }
                        }, this, {"dontAutoCatchExceptions": true});
					}
				} else {
					this._dBus.send('files', 'paste', [filesToPaste, action, source, target]);
						if (action == 'move') {
							this._filesQueue.setMoveQueue([]);
							this._filesQueue.setAction('');
						}
				}
			}
		},

		editFile: function () {
			var currentPath = this.getModel().getCurrentPath()[1];
			if (currentPath.substr(0, 8) != 'share://' && currentPath != 'workgroup://') {
				var selected = this.getView().returnSelected();
				if (selected.length == 1) {
					selected[0].edit();
				}
			}
		},

		renameFile: function (rename, object, file) {
			if(!file) {
				var selected = this.getView().returnSelected()[0];
			} else {
				var selected = file;
			}
		
			var absPath = selected.getFile().getAbsolutePath();
			var currentPath = this.getModel().getCurrentPath()[1];
			if (selected.getFile().getName() != rename) {

                var params =  [absPath, currentPath, rename];

                if(this.__isStacksync(currentPath)) {
                    var idFile = this.__getFileId(currentPath,selected.getFile().getName());
                    var idParent = this.__getFileIdFolder(currentPath);
                    if(idFile !== null && idParent !== null) {
                       params.splice(params.length,0,idFile);
                       params.splice(params.length,0,idParent);
                    }
                }

                this.closeTimer();

				eyeos.callMessage(this.getApplication().getChecknum(), 'rename',params, function (results) {
                    if(this.__isStacksync(currentPath)) {
                        this._browsePath(currentPath);
                    } else {
                        object.setValue(rename);
                        this._dBus.send('files', 'rename', [absPath, currentPath, results]);
                    }
				}, this);
			}
		},

		downloadFile: function (rename) {
			var selected = this.getView().returnSelected();
            var path = selected[0].getFile().getAbsolutePath();
            var stacksync = false;
            if (this.__isStacksync(path)) {
                var pathFather = selected[0].getFile().getPath();
                var filename = selected[0].getFile().getName();
                var params = new Object();
                params.file_id = this.__getFileId(pathFather,filename);
                params.path = path;
                if (params.file_id) {
                    stacksync = true;
                    eyeos.callMessage(this.getApplication().getChecknum(),'downloadFileStacksync',params,function(){
                        eyeos.execute('download',this.getApplication().getChecknum(), [path]);
                    },this);
                }
            }
            if (!stacksync) {
			    eyeos.execute('download',this.getApplication().getChecknum(), [path]);
            }
		},
		
		toolBarBack: function () {
			if (parseInt(this.getModel().getHistoryIndex() - 1) >= 0) {
				if (parseInt(this.getModel().getHistoryIndex() - 1) == 0) {
					this.getModel().setCurrentPath(this.getModel().getHistory()[0]);
				} else {
					this.getModel().setHistoryIndex(this.getModel().getHistoryIndex() - 1);
					this.getModel().setCurrentPath(this.getModel().getHistory()[parseInt(this.getModel().getHistoryIndex() - 1)]);
				}
				this._browse(false);
			} else {
				this.getModel().setHistoryIndex(0);
			}
		},

		toolBarForward: function () {
			if (parseInt(this.getModel().getHistoryIndex()+1) <= this.getModel().getHistory().length) {
				this.getModel().setHistoryIndex(this.getModel().getHistoryIndex() + 1);
			} else {
				this.getModel().setHistoryIndex(this.getModel().getHistory().length);
			}
			this.getModel().setCurrentPath(this.getModel().getHistory()[this.getModel().getHistoryIndex()]);
			this._browse(false);
		},

		toolBarUpload: function () {
                    eyeos.execute('upload', this.getApplication().getChecknum(), [this.getModel().getCurrentPath()[1]]);
		},

        shareURLFile: function () {
            var selected = this.getView().returnSelected();
            eyeos.execute('urlshare', this.getApplication().getChecknum(), [selected[0].getFile().getAbsolutePath(), true]);
        },

        __isStacksync: function(path) {
            if(path.indexOf('home://~'+ eyeos.getCurrentUserName()+'/Stacksync') !== -1) {
                return true;
            }
            return false;
        },

        __getFileIdFolder: function(path) {
            var name = path.substring(path.lastIndexOf('/')+1);
            var father = path === 'home://~'+ eyeos.getCurrentUserName()+'/Stacksync'? path:path.substring(0,path.lastIndexOf('/'));
            var fileIdFolder = null;

            if (path !== 'home://~'+ eyeos.getCurrentUserName()+'/Stacksync') {
                if(this._metadatas.length >0) {
                    for(var i in this._metadatas) {
                        if(this._metadatas[i].path === father) {
                            if(this._metadatas[i].metadata.contents && this._metadatas[i].metadata.contents.length > 0) {
                                for(var j in this._metadatas[i].metadata.contents) {
                                    if(this._metadatas[i].metadata.contents[j].filename === name) {
                                        fileIdFolder = this._metadatas[i].metadata.contents[j].file_id;
                                    }
                                }
                            }
                            break;
                        }
                    }
                }
            } else {
                fileIdFolder = 'null';
            }

            return fileIdFolder;
        },

        __insertMetadata: function(metadata,path)
        {
            var encontrado = false;
            var change = false;

            for(var i in this._metadatas) {
                if(this._metadatas[i].path === path) {
                    encontrado = true;
                    if(this.__changeMetadata(this._metadatas[i].metadata,metadata)) {
                        this._metadatas[i].metadata = metadata;
                        change = true;
                    }
                    break;
                }
            }

            if(!encontrado) {
                var folder = new Object();
                folder.path = path;
                folder.metadata = metadata;
                this._metadatas.splice(this._metadatas.length,0,folder);
                change = true;
            }

            return change;

        },
        closeTimer: function()
        {
            if(this._timer) {
                clearTimeout(this._timer);
                this._timer = null;
            }
        },

        __changeMetadata: function(metadataOld,metadataNew) {
            var change = false;
            var encontrado = false;

            if(metadataOld.contents.length == metadataNew.contents.length) {
                  for(var i in metadataOld.contents) {
                      encontrado = false;
                      for(var j in metadataNew.contents) {
                          if(metadataOld.contents[i].file_id == metadataNew.contents[j].file_id) {
                              encontrado =  true;
                              if(metadataOld.contents[i].filename != metadataNew.contents[j].filename) {
                                change = true;
                              }
                              break;
                          }
                      }

                      if (!encontrado) {
                          change = true;
                          break;
                      }
                      if(change) {
                         break;
                      }
                  }
            } else {
                change = true;
            }

            return change;
        },

        __refreshFolder: function(path,addToHistory,refresh) {
            var that = this;
            var reffunction = function(){that._browsePath(path,addToHistory,refresh)};
            this._timer = setTimeout(reffunction,10000);
        },

        __getFileId: function(path,filename,socialBar) {
            var fileId = null;
            var metadata = null;

            if(this._metadatas.length >0) {
                for(var i in this._metadatas) {
                    if(this._metadatas[i].path === path) {
                        if(this._metadatas[i].metadata.contents && this._metadatas[i].metadata.contents.length > 0) {
                            for(var j in this._metadatas[i].metadata.contents) {
                                if(this._metadatas[i].metadata.contents[j].filename === filename) {
                                    fileId = this._metadatas[i].metadata.contents[j].file_id;
                                    metadata = this._metadatas[i].metadata.contents[j];
                                    break;
                                }
                            }
                        }
                        break;
                    }
                }
            }

            if (socialBar) {
                return metadata;
            } else {
                return fileId;
            }
        },

        __getObjectDownloadStacksync: function(parent,path,name) {
            console.log(parent + "::" + path + "::" + name);
            var file = new Object();
            file.file_id = this.__getFileId(parent, name);
            file.path = path;
            return file;
        },

        __openFileStacksync: function(type,files) {
            var listFiles = new Array();
            for(var i in files) {
                if(files[i].file_id) {
                    eyeos.callMessage(this.getApplication().getChecknum(),'downloadFileStacksync',files[i],function(path) {
                        listFiles.push(path);
                        if(listFiles.length == files.length) {
                            eyeos.execute(type, this.getApplication().getChecknum(), listFiles);
                        }
                    },this);
                } else {
                    listFiles.push(files[i].path);
                    if(listFiles.length == files.length) {
                        eyeos.execute(type, this.getApplication().getChecknum(), listFiles);
                    }
                }
            }
        },

        closeProgress: function()  {
            if(this.__progress) {
                this.__progress.close();
            }
        },

        __createWindowProgress: function(params,action)  {
            if(!this.__progress) {
                this.__size = 0;
                this.closeTimer();
                this.__progress = new qx.ui.window.Window(tr('Progress'));
                this.__progress.set({
                    layout: new qx.ui.layout.VBox(),
                    width: 300,
                    height: 100,
                    resizable: false,
                    showMaximize: false,
                    showMinimize: false
                });

                var progress = new qx.ui.container.Composite().set({
                    layout:  new qx.ui.layout.VBox(),
                    width: 240,
                    height: 18,
                    marginTop: 15,
                    decorator: new qx.ui.decoration.Single(1)
                });

                var imageProgress = new qx.ui.basic.Image('eyeos/extern/images/bg_progress.png').set({
                    width: 0,
                    zIndex: 1,
                    scale: true
                });

                progress.add(imageProgress);

                var labelProgress = new qx.ui.basic.Label().set({
                   marginTop: -17,
                   alignX: 'center',
                   value: "0%",
                   zIndex: 2
                });

                progress.add(labelProgress);

                this.__progress.add(progress);

                this.__progress.center();
                this.__progress.open();

                this.__progress.addListener('beforeClose',function() {
                    this.__progress = null;
                    this._browsePath(params['folder'], true,true);
                },this);

                this.__progress.getChildren()[0].getChildren()[0].setWidth(3);
                this.__progress.getChildren()[0].getChildren()[1].setValue("1%");

                var pathOrig = '';

                if(params.files[0].id) {
                    pathOrig = params.files[0].path.substring(0,params.files[0].path.lastIndexOf('/'));
                } else {
                    pathOrig = params.files[0].substring(0,params.files[0].lastIndexOf('/'));
                }

                var deleteFiles = [];

                eyeos.callMessage(this.getApplication().getChecknum(),'startProgress',params,function(result) {
                    if(result && result.metadatas && result.metadatas.length > 0) {
                        if(action == 'move') {
                            deleteFiles = this.__getFilesDelete(result.metadatas,pathOrig);
                        }

                        this.__copyFile(result,result.metadatas.length - 1,params.folder,pathOrig,deleteFiles);
                    }
                },this);

            }
        },

        __copyFile: function(data,pos,dest,pathOrig,deleteFiles)
        {
            if(pos > -1) {
                var params = new Object();
                params.file = data.metadatas[pos];
                params.dest = dest;
                params.orig = pathOrig;
                eyeos.callMessage(this.getApplication().getChecknum(),'copyFile',params,function(result) {

                    if(result.filenameChange) {
                        this.__replacePath(params.file.filename,result.filenameChange,data.metadatas,pos-1,result.pathChange);
                    }
                    /*if(data.size > 0) {
                        if(!data.metadatas[pos].is_folder) {
                            this.__size += data.metadatas[pos].size;
                            this.__updateProgress(data.size);
                        }
                    } else {
                        this.__size += 1;
                        this.__updateProgress(data.metadatas.length);
                    }*/

                    this.__size += 1;
                    this.__updateProgress(data.metadatas.length + deleteFiles.length);

                    pos--;
                    this.__copyFile(data,pos,dest,pathOrig,deleteFiles);
                },this);
            } else {
                if(deleteFiles.length == 0) {
                    this.__closeProgress();
                } else {
                    this.__deleteComponent(deleteFiles,0,data.metadatas.length + deleteFiles.length);
                }
            }
        },

        __closeProgress: function() {
            var that = this;
            var reffunction = function(){that.__closeProgressUser()};
            this._timer = setTimeout(reffunction,2000);
        },

        __updateProgress: function(sizeTotal) {
            var percent = parseInt((this.__size * 100) / sizeTotal,10);
            var newWidth = parseInt(percent * 2.76);

            this.__progress.getChildren()[0].getChildren()[0].setWidth(newWidth);
            this.__progress.getChildren()[0].getChildren()[1].setValue(percent + "%");
        },

        __replacePath: function(filenameOld,filenameNew,data,pos,dirChange) {
            filenameOld = '/' + filenameOld;
            filenameNew = '/' + filenameNew;

            if(dirChange) {
                filenameOld = dirChange + filenameOld;
                filenameNew = dirChange + filenameNew;
            }

            for(var i = pos;i > -1;i--) {
                if(data[i].hasOwnProperty("parent")) {
                    if(data[i].parent.length >= filenameOld.length) {
                        var file = data[i].parent.substring(0,filenameOld.length);
                        if(file === filenameOld) {
                            var path = filenameNew + data[i].parent.substring(filenameOld.length);
                            data[i].parent = path;
                        }
                    }
                } else {
                    if(data[i].path.length >= filenameOld.length) {
                        var file = data[i].path.substring(0,filenameOld.length);
                        console.log(file + "==" + filenameOld);
                        if(file === filenameOld) {
                            var path = filenameNew + data[i].path.substring(filenameOld.length);
                            data[i].path = path;
                        }
                    }
                }
            }
        },

        __closeProgressUser: function() {
            if(this.__progress) {
                this.__progress.close();
            }
        },

        __checkFilesToPaste: function(files) {
            for(var i in files) {
                if(files[i].getAbsolutePath() === 'home://~'+ eyeos.getCurrentUserName()+'/Stacksync') {
                    files.splice(i,1);
                }
            }
        },

        __getFilesDelete: function(metadatas,pathOrig) {
            console.log(metadatas);
            console.log(pathOrig);
            var files = [];

            for(var i in metadatas) {
                if(metadatas[i].file_id) {
                    var path = pathOrig + "/";
                    var stacksync = 'home://~'+ eyeos.getCurrentUserName()+'/Stacksync';
                    path = path.substring(stacksync.length);

                    if(path == metadatas[i].path) {
                        var file = new Object();
                        file.file = pathOrig + "/" + metadatas[i].filename;
                        file.id = metadatas[i].file_id;
                        files.push(file);
                    }
                } else {
                    var path = metadatas[i].path.replace(/\\/g, '/').replace(/\/[^\/]*\/?$/, '');
                    if(path == pathOrig) {
                        var file = new Object();
                        file.file = metadatas[i].path;
                        files.push(file);
                    }
                }
            }

            return files;

        },

        __deleteComponent: function(deleteFiles,pos,sizeTotal) {
            if(pos < deleteFiles.length) {
                eyeos.callMessage(this.getApplication().getChecknum(),'delete',[deleteFiles[pos]],function() {
                    this.__size += 1;
                    this.__updateProgress(sizeTotal);

                    pos++;

                    this.__deleteComponent(deleteFiles,pos,sizeTotal);

                },this);
            } else {
                this.__closeProgress();
            }
        },
        _getToken: function() {
            this.getView().showCursor();
            eyeos.callMessage(this.getApplication().getChecknum(), 'getToken', null, function (result) {
                this.getView().closeCursor();
                if(result === true) {
                    this.setToken(true);
                    this._initFiles();
                } else {
                    this.getView().createDialogStacksync();
                }
            },this);
        },
        _initFiles: function() {
             this.getView().initFiles();
             var socialBarUpdater = new eyeos.files.SUManager(this.getView()._socialBar, this.getChecknum());
             this.setSocialBarUpdater(socialBarUpdater);
             this._addSocialBarUpdaterListeners();
             this._browse(true);
        },
        _getTokenStacksync: function() {

            this._dBus.removeListener('eyeos_stacksync_token',this.__authorizeUser,this);
            this._dBus.addListener('eyeos_stacksync_token',this.__authorizeUser,this);

            eyeos.callMessage(this.getApplication().getChecknum(), 'getTokenStacksync', null, function (result) {
                if(result.status === true && result.url) {
                    //window.open(result.url, '_new');
                    var that = this;
                    var reffunction = function(){that.__cancelStacksync()};
                    setTimeout(reffunction,60000);
                } else {
                    this.getView().timeOutStakSync(tr("An error has occurred when processing request to Stacksync"));
                    this._dBus.removeListener('eyeos_stacksync_token',this.__authorizeUser,this);
                }
            },this);
        },

        __cancelStacksync: function() {
            if(!this.__stacksync) {
                this.getView().timeOutStakSync(tr("Time out"));
                this._dBus.removeListener('eyeos_stacksync_token',this.__authorizeUser,this);
            }
        },
        __authorizeUser: function(e){
            this.__stacksync = true;
            var verifier = e.getData();
            this._dBus.removeListener('eyeos_stacksync_token',this.__authorizeUser,this);

            eyeos.callMessage(this.getApplication().getChecknum(), 'getAccessStacksync', verifier, function (result) {
                if(result === true) {
                    this.getView().removeAll();
                    this.setToken(true);
                    this._initFiles();
                } else {
                    this.getView().timeOutStakSync(tr("An error has occurred when processing request to Stacksync"));

                }
            },this);
        }
	}
});
