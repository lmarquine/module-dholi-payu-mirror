define([
		'jquery',
		'Dholi_Payment/js/payment'
	],
	function ($, DholiPayment) {
		'use strict';

		return DholiPayment.extend({
			defaults: {
				template: 'Dholi_PayU/payment/baloto-form',
				code: 'dholi_payments_payu_baloto',
			},

			initialize: function () {
				this._super();
			},

			isActive: function () {
				return true;
			},

			getLogoUrl: function () {
				return window.checkoutConfig.payment['dholi_payments_payu'].url.logo;
			},

			getInstructions: function () {
				return window.checkoutConfig.payment[this.getCode()].instructions;
			}
		});
	}
);