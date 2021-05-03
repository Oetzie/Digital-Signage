DigitalSignage.combo.Broadcasts = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url         : DigitalSignage.config.connector_url,
        baseParams  : {
            action      : 'mgr/broadcasts/getnodes'
        },
        fields      : ['id', 'name', 'calendar'],
        hiddenName  : 'broadcast_id',
        valueField  : 'id',
        displayField : 'name',
        tpl         : new Ext.XTemplate('<tpl for=".">' +
            '<div class="x-combo-list-item">' +
                '<span class="x-cal-combo x-cal-{calendar}">' +
                    '<span class="ext-cal-picker-icon"></span>' +
                '</span> {name}' +
            '</div>' +
        '</tpl>')
    });

    DigitalSignage.combo.Broadcasts.superclass.constructor.call(this, config);
};

Ext.extend(DigitalSignage.combo.Broadcasts, MODx.combo.ComboBox);

Ext.reg('digitalsignage-combo-broadcasts', DigitalSignage.combo.Broadcasts);

DigitalSignage.combo.Slides = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url         : DigitalSignage.config.connector_url,
        baseParams  : {
            action      : 'mgr/slides/getnodes'
        },
        fields      : ['id', 'name', 'slide_type_name', 'slide_type_name_formatted', 'slide_type_icon'],
        hiddenName  : 'slide_id',
        pageSize    : 15,
        valueField  : 'id',
        displayField : 'name',
        typeAhead   : true,
        editable    : true,
        tpl         : new Ext.XTemplate('<tpl for=".">' +
            '<div class="x-combo-list-item">' +
                '<i class="icon icon-slide-type icon-{slide_type_icon}"></i>' +
                '<span>{name}</span>' +
            '</div>' +
        '</tpl>')
    });

    DigitalSignage.combo.Slides.superclass.constructor.call(this,config);
};

Ext.extend(DigitalSignage.combo.Slides, MODx.combo.ComboBox);

Ext.reg('digitalsignage-combo-slides', DigitalSignage.combo.Slides);

DigitalSignage.combo.SlidesTypes = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url         : DigitalSignage.config.connector_url,
        baseParams  : {
            action      : 'mgr/slides/types/getnodes'
        },
        fields      : ['id', 'key', 'name', 'description', 'icon', 'data', 'name_formatted', 'description_formatted'],
        hiddenName  : 'slide_type_id',
        pageSize    : 15,
        valueField  : 'id',
        displayField : 'name_formatted',
        tpl         : new Ext.XTemplate('<tpl for=".">' +
            '<div class="x-combo-list-item">' +
                '<i class="icon icon-slide-type-large icon-{icon}"></i>' +
                '<span><span style="font-weight: bold;">{name_formatted}</span><br />{description_formatted}</span>' +
            '</div>' +
        '</tpl>')
    });

    DigitalSignage.combo.SlidesTypes.superclass.constructor.call(this,config);
};

Ext.extend(DigitalSignage.combo.SlidesTypes, MODx.combo.ComboBox);

Ext.reg('digitalsignage-combo-slides-types', DigitalSignage.combo.SlidesTypes);

DigitalSignage.combo.Templates = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url         : DigitalSignage.config.connector_url,
        baseParams  : {
            action      : 'mgr/broadcasts/templates/getnodes'
        },
        fields      : ['id', 'templatename', 'description'],
        hiddenName  : 'template',
        valueField  : 'id',
        displayField : 'templatename',
        tpl         : new Ext.XTemplate('<tpl for=".">' +
            '<div class="x-combo-list-item">' +
                '<span style="font-weight: bold">{templatename}</span><br />{description}' +
            '</div>' +
        '</tpl>')
    });

    DigitalSignage.combo.Templates.superclass.constructor.call(this, config);
};

Ext.extend(DigitalSignage.combo.Templates, MODx.combo.ComboBox);

Ext.reg('digitalsignage-combo-templates', DigitalSignage.combo.Templates);

DigitalSignage.combo.Players = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url         : DigitalSignage.config.connector_url,
        baseParams  : {
            action      : 'mgr/players/getnodes',
            broadcast   : config.broadcast || null
        },
        fields      : ['id', 'key', 'name'],
        hiddenName  : 'player',
        pageSize    : 15,
        valueField  : 'id',
        displayField: 'name'
    });

    DigitalSignage.combo.Players.superclass.constructor.call(this, config);
};

Ext.extend(DigitalSignage.combo.Players, MODx.combo.ComboBox);

Ext.reg('digitalsignage-combo-players', DigitalSignage.combo.Players);