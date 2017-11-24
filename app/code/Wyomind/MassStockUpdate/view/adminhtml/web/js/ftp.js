/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(["jquery"], function (jQuery) {
    "strict mode";
    return {
        test: function (url) {

            if (typeof arguments[1] == "undefined") {

                prefix = "";
            } else {
                prefix = arguments[1] + "_";

            }

            jQuery.ajax({
                url: url,
                data: {
                    ftp_host: jQuery('#' + prefix + 'ftp_host').val(),
                    ftp_port: jQuery('#' + prefix + 'ftp_port').val(),
                    ftp_login: jQuery('#' + prefix + 'ftp_login').val(),
                    ftp_password: jQuery('#' + prefix + 'ftp_password').val(),
                    ftp_dir: jQuery('#' + prefix + 'ftp_dir').val(),
                    ftp_active: jQuery('#' + prefix + 'ftp_active').val(),
                    use_sftp: jQuery('#' + prefix + 'use_sftp').val(),
                    file_path: jQuery('#' + prefix + 'file_path').val()
                },
                type: 'POST',
                showLoader: true,
                success: function (data) {
                    alert({
                        title: "FTP",
                        content: data
                    });
                }
            });
        }
    }
})