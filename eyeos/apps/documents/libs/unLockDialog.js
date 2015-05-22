qx.Class.define('eyeos.application.documents.UnLockDialog', {

    extend: qx.ui.window.Window,

    construct : function(parent)
    {
        this.base(arguments,"eyeDocs");
        this.set({
            width: 300,
            height: 160,
            contentPadding: 0,
            layout: new qx.ui.layout.VBox(),
            showMaximize: false,
            showMinimize: false
        });

        this.addListener('close',function() {
            parent.enableDisableEditor(true);
        },this);

        this.setParent(parent);
        this.__createDialog();
    },

    properties: {
        image: {
            check: 'Object',
            init: "index.php?extern=images/ajax-loader-1.gif"
        },
        parent: {
            check: 'Object',
            init: null
        }
    },

    members: {
        __createDialog: function() {
            var containerDialog = new qx.ui.container.Composite().set({
                layout: new qx.ui.layout.VBox()
            });

            var labelText = new qx.ui.basic.Label().set({
                value: "<b>"+tr('Unlocked file. You can edit') + "</b>",
                rich: true,
                alignX: 'center',
                marginTop: 30
            });

            containerDialog.add(labelText);

            var buttonContinue = new qx.ui.form.Button('No').set({
                minWidth: 60,
                label: tr('Continue'),
                alignX: 'center',
                allowGrowX: false,
                marginTop: 20
            });

            buttonContinue.addListener('execute',function(){
                this.close();
            },this);

            containerDialog.add(buttonContinue);
            this.add(containerDialog);
        },

        open: function() {
            this.centerCursor(this.getParent());
            this.show();
        },
        centerCursor: function(parent) {
            var size = parent.getWindow().getBounds();
            var left = (size.width - this.getWidth())/2;
            var top = (size.height - this.getHeight())/2;
            left += size.left;
            top += size.top;
            this.moveTo(left,top);
        }
    }
});
