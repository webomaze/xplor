/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(["jquery",  "Magento_Ui/js/modal/confirm", "dynatable_plugin"], function (jQuery, confirm) {
    "strict mode";
    return {
        lastColumnIndex: 0,
        inProgress: false,
        /** 
         * Get <select> of attributes
         * @param {type} attributes list of all attributes
         * @returns {jQuery|this.getSelectAttributes.select} select dom object
         */
        getSelectAttributes: function (attributes) {

            var select = jQuery("<select/>");

            for (group in attributes) {
                if (group != "storeviews") {
                    var optgroup = jQuery("<optgroup/>");
                    optgroup.prop('label', group);


                    attributes[group].each(function (subrow) {
                        var option = jQuery("<option/>");
                        option.addClass(subrow.style);
                        option.val(subrow.id);
                        option.html(subrow.label);
                        optgroup.append(option);
                    });
                    select.append(optgroup);

                }
            }



            select.addClass('mapping-field attribute');
            return select;
        },
        /**
         * Get <select multiple> of all store views 
         * @param {type} attributes list of all attributes
         * @returns {jQuery|this.getSelectAttributes.select} select dom object
         */
        getSelectStoreviews: function (attributes) {


            var storeviews = jQuery("<select multiple/>");

            for (group in attributes) {
                if (group == "storeviews") {


                    var option = jQuery("<option/>");
                    option.val(attributes[group].value);
                    option.html(attributes[group].label);
                    storeviews.append(option);

                    attributes[group].children.each(function (website) {

                        var optgroupWebsite = jQuery("<optgroup/>");
                        optgroupWebsite.prop('label', website.label);
                        console.log(website)
                        if (typeof website.children != "undefined") {
                            website.children.each(function (store) {

                                var optiongroupStore = jQuery("<optgroup style='margin-left:15px'/>");
                                optiongroupStore.prop('label', store.label);
                                if (typeof store.children != "undefined") {
                                    store.children.each(function (storeview) {
                                        var option = jQuery("<option/>");
                                        option.val(storeview.value);
                                        option.html(storeview.label);
                                        optiongroupStore.append(option);
                                    })
                                }

                                optgroupWebsite.append(optiongroupStore);
                            })
                        }
                        storeviews.append(optgroupWebsite);
                    });


                }
            }

            storeviews.addClass('mapping-field storeview');
            return storeviews;
        },
        refresh: function () {
            if (this.inProgress) {
                return;
            }
            if (typeof mappingRefreshUrl === "undefined" || mappingRefreshUrl === "") {
                return;
            }
            this.inProgress = true;
            var data = {};

            jQuery('#mapping-preview-warning').css({display: "block"});
            jQuery('#mapping-preview-error').css({display: "none"});
            jQuery("*[class^=dynatable]").remove();
            jQuery('#mapping-preview-warning').html("Refreshing data ...");
            jQuery('#mapping-preview').html("");

            jQuery('#custom_rules').val(this.customRules.getValue());

            jQuery(document).find('#edit_form input, #edit_form select, #edit_form textarea').each(function () {
                var id = jQuery(this).prop('id');
                var val = jQuery(this).val();
                if (!id.startsWith('d-') && !id.startsWith('h-') && id != "") {
                    data[id] = val;
                }
            });
            jQuery.ajax({
                url: mappingRefreshUrl,
                type: 'POST',
                showLoader: true,
                data: data,
                success: function (response) {

                    var mapping = jQuery('#mapping').val();
                    if (mapping == "") {
                        mapping = "[]";
                    }
                    mapping = jQuery.parseJSON(mapping);
                    if (!(mapping instanceof Array)) {
                        mapping = new Array();
                    }

                    jQuery('#mapping-preview-error').css({display: "none"});
                    jQuery('#mapping-preview-warning').css({display: "none"});

                    var attributes = response.mapping;

                    var data = response.data;

                    var table = jQuery('#mapping-preview');
                    table.html("");

                    if (typeof data != "undefined") {
                        if (data.error) {
                            jQuery('#mapping-preview-error').css({display: "block"});
                            jQuery('#mapping-preview-error').html(data.message);
                            this.inProgress = false;
                            jQuery("#refresh").prop("disabled", false);
                            return;
                        } else {

                            data = data.data;
                        }
                    } else {
                        jQuery('#mapping-preview-error').css({display: "block"});
                        jQuery('#mapping-preview-error').html("An error occured!");
                        this.inProgress = false;
                        jQuery("#refresh").prop("disabled", false);
                        return;
                    }

                    var thead = jQuery("<thead/>");
                    var tbody = jQuery("<tbody/>");

                    // headers
                    if (data[0]) {
                        var useCustomRules = jQuery('#use_custom_rules').val();

                        var counter = 0;
                        var row = jQuery("<tr/>");
                        data[0].each(function (i) {
                            var th = jQuery("<th/>").html("$cell[" + counter + "]").addClass('rule-id');
                            row.append(th);
                            counter++;
                        });

                        thead.append(row);


                        var counter = 0;
                        var row = jQuery("<tr/>");

                        data[0].each(function (i) {
                            if (counter == 0) { // first column => identifier
                                var id = jQuery("#identifier").val().toUpperCase();
                                if (id == "ENTITY_ID") {
                                    id = "ID";
                                }
                                row.append(jQuery("<th/>").html(id));
                            } else { // other column
                                var select = this.getSelectAttributes(attributes);
                                var storeviews = this.getSelectStoreviews(attributes);

                                // minus one because of the first column used for the product identifier
                                if (mapping[counter - 1] != undefined && mapping[counter - 1] instanceof Object) {

                                }
                                var th = jQuery("<th/>");
                                row.append(th.append(select));
                                /* Fix the display of the multiple optgroup*/
                                storeviews = storeviews.html(storeviews.html().replace(">", ">\n"));
                                row.append(th.append(storeviews));

                                if (mapping[counter - 1] != undefined && mapping[counter - 1].id != null && mapping[counter - 1] instanceof Object) {

                                    select.val(mapping[counter - 1].id);
                                    if (typeof mapping[counter - 1].storeviews != "undefined" && mapping[counter - 1].storeviews != null) {

                                        mapping[counter - 1].storeviews.each(function (i, e) {
                                            jQuery(storeviews.find("option[value=" + i + "]")[0]).prop("selected", true);
                                        });
                                    }

                                } else {
                                    select.val("Ignored/ignored");
                                }
                                if (select.find("OPTION:selected").hasClass("storeviews-dependent")) {
                                    select.next().show();
                                } else {
                                    select.next().hide();
                                }

                            }
                            counter++;
                        }.bind(this));



                        thead.append(row);
                    }

                    // data
                    var counter = 0;
                    for (var i = 0; i < data.length; i++) {

                        var row = jQuery("<tr/>");
                        counter = 0;
                        data[i].each(function (j) {
                            var td = jQuery("<td/>");
                            if (counter == 0) { // first column => identifier
                                td.addClass("sku");
                            }
                            row.append(td.html(j));
                            counter++;
                        });



                        tbody.append(row);

                    }

                    table.append(thead);
                    table.append(tbody);

                    this.lastColumnIndex = counter;


                    this.inProgress = false;
                    jQuery("#refresh").prop("disabled", true);
                    this.update();

                    jQuery('#mapping-preview').dynatable();


                  






                }.bind(this),
                error: function (xhr, status, error) {
                    this.inProgress = false;
                    jQuery("#refresh").prop("disabled", false);
                }
            });
        },
        update: function (onchange) {
            var data = new Array();
            jQuery('.mapping-field.attribute').each(function () {
                storeviews = new Array;

                jQuery(this).next().find("OPTION:selected").each(function (i, option) {
                    storeviews.push(jQuery(option).attr("value"))
                })
                data.push({
                    "label": jQuery(this).find('option:selected').text(),
                    "id": jQuery(this).val(),
                    "storeviews": storeviews
                });

                if (jQuery(this).find("OPTION:selected").hasClass("storeviews-dependent")) {
                    jQuery(this).next().show();
                } else {
                    jQuery(this).next().hide();
                }
            });
            jQuery('#mapping').val(JSON.stringify(data));



            this.strategy = new Array();


            data.each(function (column) {
                if (column.id != null) {
                    this.strategy.push(column.id.split("/")[0]);
                }
            }.bind(this))

            if (this.strategy.indexOf("Image") == -1) {
                jQuery("#entries_tabs_image_section").parent().css({display: "none"});
            } else {
                jQuery("#entries_tabs_image_section").parent().css({display: ""});
            }
           
        }
    }
})