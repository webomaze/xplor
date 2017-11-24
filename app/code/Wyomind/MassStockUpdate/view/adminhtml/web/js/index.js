/**
 * Copyright © 2016 Wyomind All rights reserved.
 * See LICENSE.txt for license details.
 */


require(["jquery", "Magento_Ui/js/modal/confirm", "wyomind_MassStockUpdate_updater", "domReady", "jquery/ui", "Magento_Ui/js/modal/modal"], function ($, confirm, updater) {
    $(function () {


        'use strict';


        var initializer = null;
        initializer = setInterval(function () {
            if ($(".data-grid").length > 0) {
                updater.init();
                clearInterval(initializer);
            }
        }, 200);




    });
});