/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(["jquery", "wyomind_MassStockUpdate_mapping"], function (jQuery, mapping, data) {
    "strict mode";
    return {
        update: function () {
            var data = new Array;
            jQuery('.default-value').each(function (i, value) {

                index = jQuery(value).find(".attribute")[0].selectedIndex
                storeViewDependant = jQuery(value).find(".attribute OPTION:eq(" + index + ")").hasClass("storeviews-dependent");
                if (storeViewDependant) {
                    jQuery(value).find(".storeviews").css({display: "inline"})
                } else {
                    jQuery(value).find(".storeviews").css({display: "none"})
                }

                data.push({
                    id: jQuery(value).find(".attribute:eq(0)").val(),
                    storeviews: jQuery(value).find(".storeviews:eq(0)").val(),
                    value: jQuery(value).find(".value:eq(0)").val()
                });
            })

            jQuery("#default_values").val(JSON.stringify(data));
//        $$("BUTTON.updateOnChange")[0].removeClassName("disabled");

        },
        init: function () {

            node = Builder.node("a", {id: "new-default-value", href: "javascript:void(0)"}, [
                Builder.node("img", {src: MassStockUpdate.data.images.add.src}),
                Builder.node("span", [' ', MassStockUpdate.data.translate.add]),
            ]);

            jQuery("#default-values").append(node)

            if (typeof jQuery.parseJSON(jQuery("#default_values").val()) == "object") {
                defaultValues = jQuery.parseJSON(jQuery("#default_values").val());
            } else {
                defaultValues = [];
            }
            if (defaultValues) {
                defaultValues.each(function (defaultValue) {

                    this.add(defaultValue.id, defaultValue.storeviews, defaultValue.value);
                }.bind(this))
            }

        },
        add: function (id, storeviews, value) {
            if (typeof attribute == "undefined") {
                attribute = null;
            }
            if (typeof storeviews == "undefined") {
                storeviews = new Array;
            }
            if (typeof attribute == "undefined") {
                value = null;
            }

            AttrSelect = Builder.node("select", {class: "attribute", size: 10})
            storeSelect = Builder.node("select", {class: "storeviews", multiple: true, size: 10})
            div = Builder.node("div", {class: "default-value"});

            for (type in MassStockUpdate.data.attributes) {
                if (type != "storeviews") {

                    var elt = MassStockUpdate.data.attributes[type];
                    optgroup = Builder.node("optgroup", {label: type});

                    if (!Array.isArray(elt)) {
                        elt = Object2Array(elt);
                    }

                    elt.each(function (p) {

                        if (typeof p != "undefined") {
                            option = Builder.node("option", {value: p.id, class: p.style}, [p.label]);
                            optgroup.append(option);
                        }
                    });
                    AttrSelect.append(optgroup);
                }

                if (type == "storeviews") {

                    var elt = MassStockUpdate.data.attributes[type];
                    option = Builder.node("option", {value: MassStockUpdate.data.attributes[type].value}, [MassStockUpdate.data.attributes[type].label]);
                    storeSelect.append(option);
                    elt.children.each(function (p) {

                        optgroup1 = Builder.node("optgroup", {label: p.label});
                        storeSelect.append(optgroup1);
                        p.children.each(function (p) {
                            optgroup2 = Builder.node("optgroup", {label: p.label, style: "margin-left:10px"});
                            p.children.each(function (p) {

                                option = Builder.node("option", {value: p.value}, [p.label]);
                                optgroup2.append(option);
                            });
                            storeSelect.append(optgroup2);
                        });
                    });
                }
            }

            div.append(AttrSelect)
            jQuery(AttrSelect).val(id);

            div.append(storeSelect)
            jQuery(storeSelect).val(storeviews);
            div.append(Builder.node("textarea", {class: "value", onchange: " update()"}, [value]))

            remove = Builder.node("a", {class: "remove-default-value", href: "javascript:void(0)"}, [
                Builder.node("img", {src: MassStockUpdate.data.images.remove.src}),
                Builder.node("span", {title: MassStockUpdate.data.translate.remove}),
            ]);
            div.append(remove)
            mapping = jQuery.parseJSON(jQuery('#mapping').val());

            variable = Builder.node("span", {class: "column"}, '$cell[' + Math.round(mapping.length + jQuery(".default-value").length + 1) + ']');
            div.prepend(variable)
            div.prepend(Builder.node("div", {class: "legend"}, [
                Builder.node("label", MassStockUpdate.data.translate.attribute),
                Builder.node("label", MassStockUpdate.data.translate.value)
            ]))

            jQuery("#new-default-value").before(div)



            jQuery(".default-value SELECT.attribute").each(function (AttrSelect) {
                //    jQuery(AttrSelect).find("OPTION:eq("+AttrSelect.selectedIndex+")").scrollIntoView();
            })


            this.update();
        },
        remove: function (button) {

            jQuery(button).parents()[0].remove();
            this.update();
//            jQuery(".default-value").each(function (i, e) {
//                jQuery(e).remove()
//            });
//            jQuery("#new-default-value").remove();
//            this.init();
        }
    }
})