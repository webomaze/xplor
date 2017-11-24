/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(["jquery"], function (jQuery) {
    "strict mode";
    return {
        loadSetting: function () {
            if (jQuery('#cron_settings').val() == "") {
                jQuery('#cron_settings').val('{"days":[],"hours":[]}');
            }
            var val = jQuery.parseJSON(jQuery('#cron_settings').val());
            if (val !== null) {
                val.days.each(function (elt) {
                    jQuery('#d-' + elt).parent().addClass('selected');
                    jQuery('#d-' + elt).prop('checked', true);
                });
                val.hours.each(function (elt) {
                    var hour = elt.replace(':', '');
                    jQuery('#h-' + hour).parent().addClass('selected');
                    jQuery('#h-' + hour).prop('checked', true);
                });
            }
        },
        /**
         * Update the json representation of the cron schedule
         */
        updateSetting: function () {
            var days = new Array();
            var hours = new Array();
            jQuery('.cron-box.day').each(function () {
                if (jQuery(this).prop('checked') === true) {
                    days.push(jQuery(this).attr('value'));
                }
            });
            jQuery('.cron-box.hour').each(function () {
                if (jQuery(this).prop('checked') === true) {
                    hours.push(jQuery(this).attr('value'));
                }
            });
            jQuery('#cron_settings').val(JSON.stringify({days: days, hours: hours}));
        }
    }
})