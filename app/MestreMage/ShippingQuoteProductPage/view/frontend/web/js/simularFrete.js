require([
    'jquery',
    'underscore',
    'Magento_Checkout/js/checkout-data'
], function ($, _, storage) {
    'use strict';

      $(document).ready(function(){
          $('#sendFrete').prop('disabled',false);
 
    var prod_filho_config = 0;
        $( "#product-options-wrapper div" ).click(function() {
            selpro();
        });

    function selpro () {
        var selected_options = {};
        if ($('div.swatch-attribute').length) {

            $('div.swatch-attribute').each(function (k, v) {
                var attribute_id,option_selected;
                
                if($(v).attr('attribute-id')){
                    attribute_id = $(v).attr('attribute-id');
                }

                if($(v).attr('option-selected')){
                    option_selected = $(v).attr('option-selected');
                }
                

                if($(v).attr('data-attribute-id')){
                    attribute_id = $(v).attr('data-attribute-id');
                }
                
                if($(v).attr('data-option-selected')){
                    option_selected = $(v).attr('data-option-selected');
                }

                //console.log(attribute_id, option_selected);
                if (!attribute_id || !option_selected) {
                    return;
                }
                selected_options[attribute_id] = option_selected;
            });

            var product_id_index = $('[data-role=swatch-options]').data('mageSwatchRenderer').options.jsonConfig.index;
            var found_ids = [];
            $.each(product_id_index, function (product_id, attributes) {
                var productIsSelected = function (attributes, selected_options) {
                    return _.isEqual(attributes, selected_options);
                }
                if (productIsSelected(attributes, selected_options)) {
                    found_ids.push(product_id);
                }
            });
            prod_filho_config = found_ids[0];
        }

    }

    if (localStorage.getItem('postcode')) {
        $('#cep').val(localStorage.getItem('postcode'));
    }

    $('#sendFrete').on('click', function (e) {
        e.preventDefault();

        var data = $('#product_addtocart_form').serializeArray();
        var info = {};

        $('#containerFrete').slideUp();

        for (var i = 0, l = data.length; i < l; i++) {
            if (data[i].name.search('super_attribute') != -1) {
                if (data[i].value == "") {
                    $('#containerFrete').html('');
                    $('#containerFrete').append('<li>'+msg_prod_confg+'</li>');
                    $('#containerFrete').slideDown('slow');
                    return false;
                }
            }
            info[data[i].name] = data[i].value;
        }
        if(prod_filho_config){
            info['product'] = prod_filho_config;
        }

       var cep_busca =  $('#cep').val();

        localStorage.setItem('postcode',cep_busca);

        info['state'] = $('#state').val();
        info['country'] = $('#country').val();
        info['cep'] = cep_busca.replace(/[^\d]+/g,'');
        info['tipobusca'] = '1';
        $.ajax({
            url: base_url_mm,
            method: 'get',
            data: info,
            beforeSend: function () {
                $('#sendFrete').val(txt_butom_carregar);
            },
            success: function (data) {
                $('#containerFrete').html('');
                data = $.parseJSON(data);
                _.each(data, function (element, index, list) {
                    $('#containerFrete').append('<li>' + element.title + ' - ' + element.price + '</li>');
                })
                $('#containerFrete').slideDown('slow');
                $('#sendFrete').val(txt_butom_buscar);
                if (storage.getShippingAddressFromData()) {
                    var storageData = storage.getShippingAddressFromData();
                    storageData.postcode = cep_busca.replace(/[^\d]+/g,'');
                    storage.setShippingAddressFromData(storageData);
                } else {
                    storage.setShippingAddressFromData({
                        postcode: cep_busca.replace(/[^\d]+/g,''),
                        country_id: '',
                        region: '',
                        region_id: ''
                    });
                }
            },
            error: function () {
                alert(msg_cep_confg);
            }
        });
    });

    if ($("#region").length) {
        $('#country').on('change', function (e) {
            e.preventDefault();
            var info = {};
            info['country'] = $('#country').val();
            info['tipobusca'] = '2';
            $.ajax({
                url: base_url_mm,
                method: 'get',
                data: info,
                beforeSend: function () {
                    $('#region').html('<label class="label" for="state"><span>'+state_linha+'</span></label> <div class="control"> <input type="text" disabled value="'+txt_butom_carregar+'" id="state" /> </div>');
                },
                success: function (data) {
                    $('#region').html(data);
                },
                error: function () {
                    alert("error in select ajax");
                }
            });
        });
    }

var modal_msg = $('#modal-cep-mestremage');

    $('#link-buscar-cep').click(function(){
        modal_msg.show();
    });

    $('.modal-interna-header span.close-modal').click(function(){
        modal_msg.hide();
    });
});

});


