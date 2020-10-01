define([
    'underscore',
    'Magento_Ui/js/grid/columns/select'
], function (_, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'OneAccount_OneAccountAgeVerification/ui/grid/cells/avValid'
        },
        getValidAgeColor: function (row) {
            switch (row.order_av) {
                case 'User Age Is Valid':
                    return '#6B8E23';
                case 'User Age Is Not Valid':
                    return '#B22222';
                default:
                    return '#1A9BF9';
            }
        },

        getValidationStatus: function (row) {
            return row.order_av;
        }
    });
});
