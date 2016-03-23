var MagicToolbox = {};
MagicToolbox.Gallery = Class.create();
MagicToolbox.Gallery.prototype = {
    images : [],
    file2id : {
        'no_selection' :0
    },
    idIncrement :1,
    containerId :'',
    container :null,
    uploader :null,
    initialize : function(containerId, uploader) {
        this.containerId = containerId, this.container = $(this.containerId);
        this.uploader = uploader;
        if(this.uploader) {
            this.uploader.onFilesComplete = this.handleUploadComplete.bind(this);
        }
        // this.uploader.onFileProgress = this.handleUploadProgress.bind(this);
        // this.uploader.onFileError = this.handleUploadError.bind(this);
        this.images = this.getElement('save').value.evalJSON();
        if(this.images.length) {
            this.getElement('head').show();
        }
        this.template = new Template('<tr id="__id__" class="preview">' + this.getElement('template').innerHTML + '</tr>',
                                     new RegExp('(^|.|\\r|\\n)(__([a-zA-Z0-9_]+)__)', '')
        );
        this.fixParentTable();
        this.updateImages();
        varienGlobalEvents.attachEventHandler('moveTab', this.onImageTabMove.bind(this));
    },
    onImageTabMove : function(event) {
        var imagesTab = false;
        this.container.ancestors().each( function(parentItem) {
            if(parentItem.tabObject) {
                imagesTab = parentItem.tabObject;
                throw $break;
            }
        }.bind(this));

        if(imagesTab && event.tab && event.tab.name && imagesTab.name == event.tab.name) {
            /*this.container.select('input[type="radio"]').each(function(radio) {
                radio.observe('change', this.onChangeRadio);
            }.bind(this));*/
            this.updateImages();
        }

    },
    fixParentTable : function() {
        this.container.ancestors().each( function(parentItem) {
            if(parentItem.tagName.toLowerCase() == 'td') {
                parentItem.style.width = '100%';
            }
            if(parentItem.tagName.toLowerCase() == 'table') {
                parentItem.style.width = '100%';
                throw $break;
            }
        });
    },
    getElement : function(name) {
        return $(this.containerId + '_' + name);
    },
    handleUploadComplete : function(files) {
        files.sort(function(a, b){
            var aName = a.name.toLowerCase(), bName = b.name.toLowerCase();
            if(aName < bName) return -1;
            if(aName > bName) return 1;
            return 0;
        });
        files.each( function(item) {
            if(!item.response.isJSON()) {
                try {
                    console.log(item.response);
                } catch(e2) {
                    alert(item.response);
                }
                return;
            }
            var response = item.response.evalJSON();
            if(response.error) {
                return;
            }
            var newImage = {};
            newImage.url = response.url;
            newImage.file = response.file;
            newImage.position = this.getNextPosition();
            newImage.disabled = 0;
            newImage.removed = 0;
            this.images.push(newImage);
            this.uploader.removeFile(item.id);
        }.bind(this));
        if(this.images.length) {
            $$('#magic360_columns')[0].value = this.images.length;
            this.getElement('head').show();
        }
        this.container.setHasChanges();
        this.updateImages();
    },
    updateImages : function() {
        this.getElement('save').value = Object.toJSON(this.images);
        this.images.each( function(row) {
            if(!$(this.prepareId(row.file))) {
                this.createImageRow(row);
            }
            this.updateVisualisation(row.file);
        }.bind(this));
        //this.updateUseDefault(false);
    },
    /*onChangeRadio: function(evt) {
        var element = Event.element(evt);
        element.setHasChanges();
    },*/
    createImageRow : function(image) {
        var vars = Object.clone(image);
        vars.id = this.prepareId(image.file);
        var html = this.template.evaluate(vars);
        //console.log(html);
        Element.insert(this.getElement('list'), {
            bottom :html
        });
        /*$(vars.id).select('input[type="radio"]').each(function(radio) {
            radio.observe('change', this.onChangeRadio);
        }.bind(this));*/
    },
    prepareId : function(file) {
        if(typeof this.file2id[file] == 'undefined') {
            this.file2id[file] = this.idIncrement++;
        }
        return this.containerId + '-image-' + this.file2id[file];
    },
    getNextPosition : function() {
        var maxPosition = 0;
        this.images.each( function(item) {
            if(parseInt(item.position) > maxPosition) {
                maxPosition = parseInt(item.position);
            }
        });
        return maxPosition + 1;
    },
    updateImage : function(file) {
        var index = this.getIndexByFile(file);
        this.images[index].position = this.getFileElement(file, 'cell-position input').value;
        this.images[index].removed = (this.getFileElement(file, 'cell-remove input').checked ? 1 : 0);
        this.images[index].disabled = (this.getFileElement(file, 'cell-disable input').checked ? 1 : 0);
        this.getElement('save').value = Object.toJSON(this.images);
        this.updateState(file);
        this.container.setHasChanges();
    },
    loadImage : function(file) {
        var image = this.getImageByFile(file);
        this.getFileElement(file, 'cell-image img').src = image.url;
        this.getFileElement(file, 'cell-image img').show();
        this.getFileElement(file, 'cell-image .place-holder').hide();
    },
    updateVisualisation : function(file) {
        var image = this.getImageByFile(file);
        this.getFileElement(file, 'cell-position input').value = image.position;
        this.getFileElement(file, 'cell-remove input').checked = (image.removed == 1);
        this.getFileElement(file, 'cell-disable input').checked = (image.disabled == 1);
        this.updateState(file);
    },
    updateState : function(file) {
        if(this.getFileElement(file, 'cell-disable input').checked) {
            this.getFileElement(file, 'cell-position input').disabled = true;
        } else {
            this.getFileElement(file, 'cell-position input').disabled = false;
        }
    },
    getFileElement : function(file, element) {
        var selector = '#' + this.prepareId(file) + ' .' + element;
        var elems = $$(selector);
        if(!elems[0]) {
            try {
                console.log(selector);
            } catch(e2) {
                alert(selector);
            }
        }

        return $$('#' + this.prepareId(file) + ' .' + element)[0];
    },
    getImageByFile : function(file) {
        if(this.getIndexByFile(file) === null) {
            return false;
        }

        return this.images[this.getIndexByFile(file)];
    },
    getIndexByFile : function(file) {
        var index;
        this.images.each( function(item, i) {
            if(item.file == file) {
                index = i;
            }
        });
        return index;
    },
    handleUploadProgress : function(file) {

    },
    handleUploadError : function(fileId) {

    }
};
