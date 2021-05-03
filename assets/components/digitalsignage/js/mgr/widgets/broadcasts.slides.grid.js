DigitalSignage.grid.BroadcastSlides = function(config) {
    config = config || {};

    config.tbar = [{
        xtype       : 'digitalsignage-combo-slides',
        name        : 'digitalsignage-filter-broadcasts-slides',
        id          : 'digitalsignage-filter-broadcasts-slides',
        emptyText   : _('digitalsignage.select_a_slide'),
        width       : 200
    }, {
        text        : _('digitalsignage.broadcast_slide_create'),
        cls         : 'primary-button',
        handler     : this.createSlide,
        scope       : this
    }];

    var columns = new Ext.grid.ColumnModel({
        columns     : [{
            header      : _('digitalsignage.label_slide_name'),
            dataIndex   : 'name',
            sortable    : true,
            editable    : false,
            width       : 125,
            renderer    : this.renderName
        }, {
            header      : _('digitalsignage.label_slide_time'),
            dataIndex   : 'time_formatted',
            sortable    : true,
            editable    : false,
            width       : 100,
            fixed       : true
        }, {
            header      : _('digitalsignage.label_slide_active'),
            dataIndex   : 'active',
            sortable    : true,
            editable    : true,
            width       : 100,
            fixed       : true,
            renderer    : this.renderBoolean
        }]
    });
    
    Ext.applyIf(config, {
        cm          : columns,
        id          : 'digitalsignage-grid-broadcast-slides',
        url         : DigitalSignage.config.connector_url,
        baseParams  : {
            action      : 'mgr/broadcasts/slides/getlist',
            broadcast_id : config.record.id
        },
        fields      : ['id', 'slide_type_id', 'name', 'time', 'protected', 'data', 'active', 'editedon', 'slide_type_key', 'slide_type_name', 'slide_type_name_formatted', 'slide_type_icon', 'time_formatted'],
        paging      : false,
        sortBy      : 'sortindex',
        enableDragDrop : true,
        ddGroup     : 'digitalsignage-grid-broadcast-slides'
    });
    
    DigitalSignage.grid.BroadcastSlides.superclass.constructor.call(this, config);

    this.on('afterrender', this.sortSlides, this);
};

Ext.extend(DigitalSignage.grid.BroadcastSlides, MODx.grid.Grid, {
    sortSlides: function() {
        new Ext.dd.DropTarget(this.getView().mainBody, {
            ddGroup     : this.config.ddGroup,
            notifyDrop  : function(dd, e, data) {
                var index = dd.getDragData(e).rowIndex;

                if (undefined !== index) {
                    for (var i = 0; i < data.selections.length; i++) {
                        data.grid.getStore().remove(data.grid.getStore().getById(data.selections[i].id));
                        data.grid.getStore().insert(index, data.selections[i]);
                    }

                    var order = [];

                    Ext.each(data.grid.getStore().data.items, (function(record) {
                        order.push(record.id);
                    }).bind(this));

                    MODx.Ajax.request({
                        url         : DigitalSignage.config.connector_url,
                        params      : {
                            action      : 'mgr/broadcasts/slides/sort',
                            sort        : order.join(',')
                        },
                        listeners   : {
                            'success'   : {
                                fn          : function() {

                                },
                                scope       : this
                            }
                        }
                    });
                }
            }
        });
    },
    getMenu: function() {
        return [{
            text    : '<i class="x-menu-item-icon icon icon-times"></i>' + _('digitalsignage.broadcast_slide_remove'),
            handler : this.removeFeed,
            scope   : this
        }];
    },
    createSlide: function(btn, e) {
        var slide = Ext.getCmp('digitalsignage-filter-broadcasts-slides');

        if (slide) {
            if (!Ext.isEmpty(slide.getValue())) {
                MODx.Ajax.request({
                    url         : DigitalSignage.config.connector_url,
                    params      : {
                        action      : 'mgr/broadcasts/slides/create',
                        broadcast_id : this.config.record.id,
                        slide_id    : slide.getValue()
                    },
                    listeners   : {
                        'success'   : {
                            fn          : this.refresh,
                            scope       : this
                        }
                    }
                });
            }
        }
    },
    removeFeed: function() {
        MODx.msg.confirm({
            title       : _('digitalsignage.broadcast_slide_remove'),
            text        : _('digitalsignage.broadcast_slide_remove_confirm'),
            url         : DigitalSignage.config.connector_url,
            params      : {
                action      : 'mgr/broadcasts/slides/remove',
                id          : this.menu.record.id
            },
            listeners   : {
                'success'   : {
                    fn          : this.refresh,
                    scope       : this
                }
            }
        });
    },
    renderName: function(d, c, e) {
        return String.format('<i class="icon icon-slide-type icon-{0}"></i> {1}', e.json.slide_type_icon, d);
    },
    renderBoolean: function(d, c) {
        c.css = parseInt(d) === 1 ? 'green' : 'red';

        return parseInt(d) === 1 ? _('yes') : _('no');
    }
});

Ext.reg('digitalsignage-grid-broadcast-slides', DigitalSignage.grid.BroadcastSlides);