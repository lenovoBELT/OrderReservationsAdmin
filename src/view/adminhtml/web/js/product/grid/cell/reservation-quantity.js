/**
 * LenovoBARBO | ReservationQtyAdmin
 *
 * @vendor  LenovoBARBO
 * @package ReservationQtyAdmin
 *
 * @copyright Copyright (c) 2025 LenovoBARBO
 *
 * @author Leandro Barboza dos Santos <barboza63sd@hotmail.com>
 **/

define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Beltnutrition_ReservationQtyAdmin/product/grid/cell/reservation-quantity.html'
        },

        /**
         * Get reservation quantity data from orders
         *
         * @param {Object} record - Record object
         * @returns {Array} Result array
         */
        getReservationQuantityData: function (record) {
            return record[this.index] ? record[this.index] : [];
        }
    });
});
