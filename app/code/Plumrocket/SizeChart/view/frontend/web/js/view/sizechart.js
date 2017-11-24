/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_SizeChart
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

define(['jquery', 'uiComponent'], function ($, Component) {
    'use strict';
    return Component.extend({
        initialize: function() {
            this._super();

            function showSC() {
                $sc.show();
                resizehowSC();
            };

            function hideSC() {
                $sc.hide();
                resizehowSC();
            };

            function resizehowSC() {
                if ($sc.is(':visible')) {
                    $('body').css({'overflow':'hidden'});
                    var p = $sc.find('.pschart-popup-internal');
                    var top = parseInt(($(window).height() - p.outerHeight()) / 2);
                    if (top < 5) {
                        top = 5;
                    };
                    p.css('margin-top',top+'px');
                } else {
                    $('body').removeAttr('style');
                };
            };

            function moveSizeChartButtom(id, params) {
                var found  = false;
                var buttom = $('.pschart-sizechart-link-'+id);

                for (var i = 0; i < params.attributesIds.length; i++) {
                    var element = $('div [attribute-id=' + params.attributesIds[i] + ']');
                    if (element.length) {
                        buttom.appendTo(element);
                        found = true;
                        break;
                    }
                }

                if (!found) {
                    for (var i = 0; i < params.keys.length; i++)
                    {
                        $(".product-options-wrapper label span").each(function(){
                            var $l = $(this);
                            if ($l.text().toLowerCase().indexOf(params.keys[i]) > -1 ) {
                                buttom.appendTo($l.parent().parent());
                                found = true;
                                return false;
                            }
                        })
                    }
                }
            }

            var attributes  = $.parseJSON(this.attributes);
            var sizeChartId = attributes.id;

            var h = $('#pschart-showsizes-holder');
            $('body').prepend(h.children());
            h.remove();

            var $sc = $('.pschart-showsizes-'+sizeChartId);

            $('.pschart-sizechart-link-'+sizeChartId).click(function(){
                showSC();
            });

            $('.pschart-sizechart-link-close').click(function(){
                hideSC();
            });

            $sc.find('.pschart-popup').click(function(){
                if (!$(this).find('.pschart-popup-internal').is(':hover')) {
                    hideSC();
                };
            });

            $(document).keydown(function(e) {
                if (e.keyCode == 27) {
                    hideSC();
                };
            });

            $(window).resize(function(){
                resizehowSC();
            });

            $(document).ready(function() {
                if (attributes.move) {
                    moveSizeChartButtom(sizeChartId, attributes.params);
                }
                $('.pschart-sizechart-link-'+sizeChartId).show();
            });

            return this;
        },

    });
});
