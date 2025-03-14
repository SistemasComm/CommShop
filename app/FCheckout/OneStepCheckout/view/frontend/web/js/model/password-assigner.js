/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global alert*/
define([
    'jquery'
], function ($) {
    'use strict';
    /** Override default place order action and add agreement_ids to request */
    return function (paymentData) {
        var password = $('[name="code-password"] [name="code-password"]').val();
        var confirm_password = $('[name="code-confirm_password"] [name="code-confirm_password"]').val();
        var mm_dob = $('[name="shippingAddress.mm_dob"] [name="mm_dob"]').val();
        var mm_gender = $('[name="shippingAddress.mm_gender"] [name="mm_gender"]').val();
        var inscricao_estadual = $('[name="shippingAddress.inscricao_estadual"] [name="inscricao_estadual"]').val();
        var estado = $('[name="shippingAddress.estado"] [name="estado"]').val();
        var razao_social = $('[name="shippingAddress.razao_social"] [name="razao_social"]').val();
        paymentData['confirm_password'] = confirm_password;
        paymentData['code_password'] = password;
        paymentData['mm_dob'] = mm_dob;
        paymentData['mm_gender'] = mm_gender;
        paymentData['inscricao_estadual'] = inscricao_estadual;
        paymentData['estado'] = estado;
        paymentData['razao_social'] = razao_social;
    };
});
