/**
 * Copyright (c) 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

var RelatedCustomerTable = require('./related-customer-table/table');

var sourceTabSelector = '#available-tab';
var sourceTableSelector = sourceTabSelector + ' table.table';

var destinationTabSelector = '#to-be-assigned-tab';
var destinationTabLabelSelector = destinationTabSelector + '-label';
var destinationTableSelector = destinationTabSelector + '-table';

var checkboxSelector = '.js-customer-checkbox';
var tableHandler;

/**
 * @return {void}
 */
function initialize() {
    tableHandler = RelatedCustomerTable.create(
        sourceTableSelector,
        destinationTableSelector,
        checkboxSelector,
        $(destinationTabLabelSelector).text(),
        destinationTabLabelSelector,
        'customerUserConnection_idCustomersToAssign',
        onRemove,
    );

    $(sourceTabSelector + ' .js-select-all-button a').on('click', tableHandler.selectAll);
}

/**
 * @return {boolean}
 */
function onRemove() {
    tableHandler.removeSelectedCustomer($(this).data('id'));

    return false;
}

module.exports = {
    initialize: initialize,
};
