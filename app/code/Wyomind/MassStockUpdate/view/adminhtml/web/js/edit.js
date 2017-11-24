/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */


require(["jquery", "wyomind_MassStockUpdate_cron", "wyomind_MassStockUpdate_mapping", "wyomind_MassStockUpdate_additional"], function ($, cron, mapping, additional) {
    'use strict';
    $(function () {

        function Object2Array(obj) {
            return Object.keys(obj).map(function (x) {
                return obj[x];
            });
        }
        /* ========= Cron tasks  ================== */

        jQuery(document).on('change', '.cron-box', function () {
            jQuery(this).parent().toggleClass('selected');
            cron.updateSetting();
        });

        cron.loadSetting();

        /* ======== Custom rules ================== */
        mapping.customRules = CodeMirror.fromTextArea(document.getElementById('custom_rules'), {
            matchBrackets: true,
            mode: "application/x-httpd-php",
            indentUnit: 2,
            indentWithTabs: false,
            lineWrapping: true,
            lineNumbers: true,
            styleActiveLine: true,
        });
        jQuery(document).on('change', '#use_custom_rules', function () {
            if (jQuery(this).val() == 1) {
                mapping.customRules.refresh();
            }
        });



        /* ======== Mapping ======================= */
        jQuery("#refresh").prop("disabled", true);
        jQuery(document).on('change', '.mapping-field', function () {
            mapping.update(true);
            jQuery("#refresh").prop("disabled", false);
        });


        setTimeout(function () {
            mapping.refresh();

        }, 1000);



        jQuery(".update-preview,.CodeMirror").on('change', function () {
            jQuery("#refresh").prop("disabled", false);
            mapping.customRules.refresh();

        });

        /*============== Additional default Values ================*/
        additional.init();
        jQuery(document).on('click', '#new-default-value', function () {
            additional.add();
        });
        jQuery(document).on('click', '.remove-default-value', function (evt) {

            additional.remove(evt.currentTarget);
        });
        jQuery(document).on('change', '#default-values .attribute,#default-values .storeviews,#default-values .value', function () {
            additional.update();
        });

    });
});

