define([
    'jquery',
    'ko',
    'Dholi_Payment/js/vault',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils'
], function ($, ko, DholiVault, quote, priceUtils) {
    'use strict';

    return DholiVault.extend({
        defaults: {
            template: 'Dholi_PayU/payment/vault-form'
        },

        totals: ko.observable(window.checkoutConfig.totalsData),
        installments: ko.observableArray(),

        initialize: function () {
            let self = this;
            self._super();
        },

        getInstallments: function () {
            let self = this;
            self.installments.removeAll();

            $.ajax({
                dataType: 'json',
                method: 'POST',
                data: {
                    paymentMethod: self.getCardType(),
                    receipt: window.checkoutConfig.payment[self.getParentCode()].receipt,
                    amount: self.getTotalOrderAmountForInstallments()
                },
                url: self.getInstallmentsUrl(),
                context: document.body
            }).done(function (response) {
                if (response.code == 200) {
                    let r = JSON.parse(response.data);

                    let installmentAmount = null;
                    let installmentAmountOriginal = null;
                    let text, value = null;
                    let totalAmount = null;
                    let interest = 0;
                    let installment = 0;
                    let priceFormat = quote.getPriceFormat();

                    _.each(r.paymentMethodFee[0].pricingFees, function (element, index) {
                        installment = parseInt(element.installments);
                        installmentAmount = Number(Math.round(Math.abs(+element.pricing.totalValue / installment || 0) + 'e+7') + ('e-7'));

                        interest = element.pricing.payerDetail.interest;

                        if (installment === 1) {
                            installmentAmountOriginal = self.getFirstInstallmentAmount();
                            installmentAmount = priceUtils.formatPrice(installmentAmountOriginal, priceFormat);
                            totalAmount = installmentAmount;
                        } else {
                            if (installment > self.getTotalInstallments()) {
                                return true;
                            }
                            if (self.getMinInstallment() > 0) {
                                if (installmentAmount < self.getMinInstallment()) {
                                    return true;
                                }
                            }
                            totalAmount = priceUtils.formatPrice(installmentAmount * installment, priceFormat);
                            installmentAmountOriginal = installmentAmount;
                            installmentAmount = priceUtils.formatPrice(installmentAmount, priceFormat);
                        }

                        text = installment + 'x de '.concat(installmentAmount).concat(' = ' + totalAmount).concat((installment === 1 && self.getPercentualDiscount() > 0 ? ' (' + self.getPercentualDiscount() + '% off)' : '')).concat(interest > 0 ? ' c/ juros' : '');
                        value = installment + '-' + installmentAmountOriginal;

                        self.installments.push({'v': value, 't': text});
                    });
                }
            });
        }
    });
});
