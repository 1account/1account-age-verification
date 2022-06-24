define([
    'underscore',
    'Magento_Ui/js/grid/columns/select'
], function (_, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'OneAccount_OneAccountAgeVerification/ui/grid/cells/text'
        },
        getStatusColor: function (row) {
            if (row.av_status === "failed") {
                return '#B22222';
            } else {
                return '#6B8E23';
            }
        }
    });
});
