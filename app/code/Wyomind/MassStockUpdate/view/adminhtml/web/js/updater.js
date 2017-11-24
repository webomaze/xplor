
/**
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */


define(["jquery"], function (jQuery) {
    "strict mode";
    return {
        init: function () {
            data = new Array();
            jQuery('.updater').each(
                    function (i, u) {
                        profile = [u.id.replace("profile_", ""), jQuery(u).attr('name'), jQuery(u).attr('data-cron')];
                        data.push(profile);

                    }
            )

            jQuery.ajax({
                url: updater_url,
                type: 'POST',
                showLoader: false,
                data: {data: data},
                success: function (response) {

                    if (typeof response == "object") {
                        ;
                        response.each(function (r) {
                            jQuery("#profile_" + r.id).replaceWith(r.content)
                        })
                    }
                    require(["wyomind_MassStockUpdate_updater"], function (updater) {
                        setTimeout(function () {

                            updater.init()
                        }, 1000)
                    })

                }
            })

        }
    }

})