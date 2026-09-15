/**
 * Copyright (c) 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

var CustomerIdSelector = require('./customer-id-selector');
var tableAccess = require('ZedGuiModules/libs/table/table-access');

var CHECKBOX_CHECKED_STATE_CHECKED = 'checked';
var CHECKBOX_CHECKED_STATE_UN_CHECKED = 'un_checked';

/**
 * @param {string} sourceTable
 * @param {string} destinationTable
 * @param {string} labelCaption
 * @param {string} labelId
 * @param {string} formFieldId
 * @param {function} onRemoveCallback
 *
 * @return {object}
 */
function TableHandler(sourceTable, destinationTable, labelCaption, labelId, formFieldId, onRemoveCallback) {
    var tableHandler = {
        labelId: labelId,
        labelCaption: labelCaption,
        formFieldId: formFieldId,
        sourceTable: sourceTable,
        destinationTable: destinationTable,
    };

    var destinationTableIdSelector = CustomerIdSelector.create();
    var destinationHandle = null;

    tableHandler.toggleSelection = function () {
        $('input[type="checkbox"]', sourceTable).each(function (index, checkboxNode) {
            var $checkbox = $(checkboxNode);
            $checkbox.prop('checked', !$checkbox.prop('checked'));
            $checkbox.trigger('change');
        });

        return false;
    };

    tableHandler.selectAll = function () {
        $('input[type="checkbox"]', sourceTable).each(function (index, checkboxNode) {
            var $checkbox = $(checkboxNode);
            $checkbox.prop('checked', true);
            $checkbox.trigger('change');
        });

        return false;
    };

    tableHandler.deSelectAll = function () {
        $('input[type="checkbox"]', sourceTable).each(function (index, checkboxNode) {
            var $checkbox = $(checkboxNode);
            $checkbox.prop('checked', false);
            $checkbox.trigger('change');
        });

        return false;
    };

    tableHandler.addSelectedCustomer = function (idCustomer, firstname, lastname, gender) {
        idCustomer = parseInt(idCustomer, 10);
        if (destinationTableIdSelector.isIdSelected(idCustomer)) {
            return;
        }

        var $removeLinkTextInput = $('#remove-link-text');

        destinationTableIdSelector.addIdToSelection(idCustomer, [
            idCustomer,
            decodeURIComponent((firstname + '').replace(/\+/g, '%20')),
            decodeURIComponent((lastname + '').replace(/\+/g, '%20')),
            decodeURIComponent((gender + '').replace(/\+/g, '%20')),
            '<div><a data-id="' +
                idCustomer +
                '" href="#" class="btn btn-xs remove-item">' +
                ($removeLinkTextInput ? $removeLinkTextInput.val() : 'Remove') +
                '</a></div>',
        ]);

        tableHandler.renderSelection();

        tableHandler.updateSelectedCustomersLabelCount();
    };

    tableHandler.renderSelection = function () {
        if (!destinationHandle) {
            return;
        }

        destinationHandle.raw().clear().rows.add(destinationTableIdSelector.getRows()).draw();
    };

    tableHandler.removeSelectedCustomer = function (idCustomer) {
        idCustomer = parseInt(idCustomer, 10);

        if (destinationTableIdSelector.isIdSelected(idCustomer)) {
            destinationTableIdSelector.removeIdFromSelection(idCustomer);
            tableHandler.renderSelection();
            tableHandler.unCheckCheckbox($('input[value="' + idCustomer + '"]', sourceTable));
        }

        tableHandler.updateSelectedCustomersLabelCount();
    };

    tableHandler.getSelector = function () {
        return destinationTableIdSelector;
    };

    tableHandler.updateSelectedCustomersLabelCount = function () {
        $(tableHandler.getLabelId()).text(
            labelCaption + ' (' + Object.keys(this.getSelector().getSelected()).length + ')',
        );
        var customerIds = Object.keys(this.getSelector().getSelected());
        var s = customerIds.join(',');
        var field = $('#' + tableHandler.getFormFieldId());
        field.attr('value', s);
    };

    tableHandler.getLabelId = function () {
        return tableHandler.labelId;
    };

    tableHandler.getLabelCaption = function () {
        return tableHandler.labelCaption;
    };

    tableHandler.getFormFieldId = function () {
        return tableHandler.formFieldId;
    };

    tableHandler.getSourceTable = function () {
        return tableHandler.sourceTable;
    };

    tableHandler.getDestinationTable = function () {
        return tableHandler.destinationTable;
    };

    /**
     * @returns {string}
     */
    tableHandler.getInitialCheckboxCheckedState = function () {
        return CHECKBOX_CHECKED_STATE_UN_CHECKED;
    };

    /**
     * @param {jQuery} $checkbox
     * @return {boolean}
     */
    tableHandler.isCheckboxActive = function ($checkbox) {
        if (tableHandler.getInitialCheckboxCheckedState() === CHECKBOX_CHECKED_STATE_UN_CHECKED) {
            return $checkbox.prop('checked');
        }

        return !$checkbox.prop('checked');
    };

    /**
     * @param {jQuery} $checkbox
     */
    tableHandler.checkCheckbox = function ($checkbox) {
        var checkedState = tableHandler.getInitialCheckboxCheckedState() === CHECKBOX_CHECKED_STATE_UN_CHECKED;
        $checkbox.prop('checked', checkedState);
    };

    /**
     * @param {jQuery} $checkbox
     */
    tableHandler.unCheckCheckbox = function ($checkbox) {
        var checkedState = tableHandler.getInitialCheckboxCheckedState() !== CHECKBOX_CHECKED_STATE_UN_CHECKED;
        $checkbox.prop('checked', checkedState);
    };

    if (destinationTable.length) {
        destinationTable.on('click', '.remove-item', onRemoveCallback);

        tableAccess.requestTable(destinationTable[0], function (handle) {
            destinationHandle = handle;

            handle.created().then(function () {
                tableHandler.renderSelection();
            });
        });
    }

    return tableHandler;
}

module.exports = {
    /**
     * @param {string} sourceTable
     * @param {string} destinationTable
     * @param {string} labelCaption
     * @param {string} labelId
     * @param {string} formFieldId
     * @param {function} onRemoveCallback
     *
     * @return {TableHandler}
     */
    create: function (sourceTable, destinationTable, labelCaption, labelId, formFieldId, onRemoveCallback) {
        return new TableHandler(sourceTable, destinationTable, labelCaption, labelId, formFieldId, onRemoveCallback);
    },
    CHECKBOX_CHECKED_STATE_CHECKED: CHECKBOX_CHECKED_STATE_CHECKED,
    CHECKBOX_CHECKED_STATE_UN_CHECKED: CHECKBOX_CHECKED_STATE_UN_CHECKED,
};
