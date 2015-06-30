qx.Class.define('eyeos.calendar.view.Cursor', {

    extend: qx.ui.window.Window,

    construct : function(parent)
    {
        this.base(arguments, "Cursor");
        this.set({
            width: 42,
            height: 42,
            contentPadding: 0,
            layout: new qx.ui.layout.VBox()
        });
        this.setParent(parent);
        this.getChildControl('captionbar').setVisibility('hidden');
        this.setAppearance(null);
        this.setOpacity(1);
        this.__createCursor();
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
        __createCursor: function() {
            var loading = new qx.ui.basic.Image(this.getImage()).set({
                alignX: 'center',
                paddingTop: 2
            });
            this.add(loading);
        },

        open: function() {
            this.centerCursor(this.getParent());
            this.show();
        },
        centerCursor: function(parent) {
            var size = parent.getBounds();
            var left = (size.width - this.getWidth())/2;
            var top = (size.height - this.getHeight())/2;
            left += size.left;
            top += size.top;
            this.moveTo(left,top);
        }
    }
});