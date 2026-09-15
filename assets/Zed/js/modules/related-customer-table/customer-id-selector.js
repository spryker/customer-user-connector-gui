/**
 * Copyright (c) 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

function CustomerIdSelector() {
    var selector = {};
    var selectedIds = {};

    selector.addIdToSelection = function (id, row) {
        selectedIds[id] = row;
    };

    selector.removeIdFromSelection = function (id) {
        delete selectedIds[id];
    };

    selector.isIdSelected = function (id) {
        return selectedIds.hasOwnProperty(id);
    };

    selector.clearAllSelections = function () {
        selectedIds = {};
    };

    selector.getSelected = function () {
        return selectedIds;
    };

    /**
     * @return {Array} Rows of everything selected, the table of the selection is built from them.
     */
    selector.getRows = function () {
        return Object.keys(selectedIds).map(function (id) {
            return selectedIds[id];
        });
    };

    return selector;
}

module.exports = {
    /**
     * @return {CustomerIdSelector}
     */
    create: function () {
        return new CustomerIdSelector();
    },
};
