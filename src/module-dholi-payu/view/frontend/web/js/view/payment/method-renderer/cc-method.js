define(["underscore","jquery","ko","mage/url","Dholi_Payment/js/payment","Magento_Checkout/js/action/redirect-on-success","Magento_Checkout/js/action/place-order","Magento_Checkout/js/action/select-payment-method","Magento_Payment/js/model/credit-card-validation/credit-card-data","Magento_Checkout/js/model/quote","Magento_Catalog/js/price-utils","Magento_Checkout/js/model/full-screen-loader","Magento_Checkout/js/action/get-totals","Magento_Checkout/js/checkout-data","Magento_Ui/js/model/messageList","Magento_Payment/js/model/credit-card-validation/validator","Magento_Checkout/js/model/payment/additional-validators","mage/translate","mage/validation"],function(q,c,a,d,b,p,i,h,o,m,j,f,n,g,l,e,s,k){return b.extend({defaults:{template:"Dholi_PayU/payment/cc-form",code:"dholi_payments_payu_cc"},parentCode:"dholi_payments_payu",totals:a.observable(window.checkoutConfig.totalsData),initialize:function(){this._super();this._initialize()},_initialize:function(){payU.setLanguage(window.checkoutConfig.payment[this.parentCode].language);payU.setURL(window.checkoutConfig.payment[this.getCode()].url.payments);payU.setPublicKey(window.checkoutConfig.payment[this.getCode()].publicKey);payU.setAccountID(window.checkoutConfig.payment[this.getCode()].accountId)},initObservable:function(){this._super().observe();return this},isActive:function(){return true},getLogoUrl:function(){return window.checkoutConfig.payment.dholi_payments_payu.url.logo},creditCardNumberBlurEvent:function(){let value=this.creditCardNumber();if(value){this.creditCardNumberStatusListen();this.installmentsListen()}},installmentsListen:function(){let self=this;c.ajax({dataType:"json",method:"POST",data:{paymentMethod:this.creditCardBrand,receipt:window.checkoutConfig.payment[this.getCode()].receipt},url:this.getInstallmentsUrl(),context:document.body}).done(function(t){self.installments.removeAll();if(t.code==200){let r=JSON.parse(t.data);let installmentAmount=null;let installmentAmountOriginal=null;let text,value=null;let totalAmount=null;let interest=0;let installment=0;q.each(r.paymentMethodFee[0].pricingFees,function(v,u){installment=parseInt(v.installments);installmentAmount=Number(Math.round(Math.abs(+v.pricing.totalValue/installment||0)+"e+2")+("e-2"));interest=v.pricing.payerDetail.interest;if(installment===1){installmentAmountOriginal=self.getFirstInstallmentAmount();installmentAmount=j.formatPrice(installmentAmountOriginal,m.getPriceFormat());totalAmount=installmentAmount}else{if(installment>self.getTotalInstallments()){return true}if(self.getMinInstallment()>0){if(installmentAmount<self.getMinInstallment()){return true}}totalAmount=j.formatPrice(installmentAmount*installment,m.getPriceFormat());installmentAmountOriginal=installmentAmount;installmentAmount=j.formatPrice(installmentAmount,m.getPriceFormat())}text=installment+"x de ".concat(installmentAmount).concat(" = "+totalAmount).concat((installment===1&&self.getPercentualDiscount()>0?" ("+self.getPercentualDiscount()+"% off)":"")).concat(interest>0?" c/ juros":"");value=installment+"-"+installmentAmountOriginal;self.installments.push({v:value,t:text})})}})},getCvvImageHtml:function(){return""},reloadTotals:function(){f.startLoader();c.ajax({dataType:"json",method:"POST",data:{"payment[method]":g.getSelectedPaymentMethod(),"payment[cc_installments]":c("#".concat(this.getCode()).concat("_installments")).val(),"payment[shipping_amount]":this.getShippingAmount()},url:this.getPaymentUrl(),context:document.body}).done(function(t){n([],c.Deferred())}).fail().always(function(){f.stopLoader()})}})});