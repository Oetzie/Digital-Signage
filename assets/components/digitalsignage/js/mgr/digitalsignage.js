var DigitalSignage = function(config) {
    config = config || {};

    DigitalSignage.superclass.constructor.call(this, config);
};

Ext.extend(DigitalSignage, Ext.Component, {
    page    : {},
    window  : {},
    grid    : {},
    tree    : {},
    panel   : {},
    combo   : {},
    config  : {},
    loadRTE : function(id) {
        if (!Ext.isEmpty(MODx.config.which_editor)) {
            MODx.loadRTE(id, DigitalSignage.config.rte_config || {});
        }
    }
});

Ext.reg('digitalsignage', DigitalSignage);

DigitalSignage = new DigitalSignage();