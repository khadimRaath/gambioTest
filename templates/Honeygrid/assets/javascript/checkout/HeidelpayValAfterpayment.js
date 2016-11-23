document.addEventListener('DOMContentLoaded', function(){
	jQuery(document).ready(function(){ 
		
		jQuery('#checkout_payment .green.button').click(function(e){
			// get useful informations
			var iFrame = document.getElementById('paymentFrameIframe');
			var iFrameSrc = getDomainFromUrl(iFrame.src);
			var paymentForm = jQuery('#checkout_payment');
			
			// send data to payment
			var data = new Array();
			data = new Object();
			data = {};
			
			iFrame.contentWindow.postMessage(JSON.stringify(data),iFrameSrc);

			// receive message from payment
			if (window.addEventListener) {
				window.addEventListener('message', receiveMessage);
			} else if (window.attachEvent) {
				window.attachEvent('onmessage', receiveMessage);
			}
			
			return false;
						
		});
		
	}); 
});

/**
 * extracts the domain from a given url
 * @param {string} url 
 * @returns {String}
 */
function getDomainFromUrl(url) { 
	var arr = url.split("/"); 
	return arr[0] + "//" + arr[2]; 
	}
/**
 * receives a JSON-Post message frpm HP
 * @param e
 * @returns {Boolean}
 */
function receiveMessage(e) {
	
	var antwort = JSON.parse(e.data);
	
	if (antwort["PROCESSING.RESULT"] == "ACK") {
		return true;
	} else {
		
		//get the iFrameSource to check the sender
		var iFrame = document.getElementById('paymentFrameIframe');
		var iFrameSrc = getDomainFromUrl(iFrame.src); 
		
		// check sender, in case of different senders redirect to checkout_payment
		if (e.origin !== iFrameSrc) {
			top.location.href = document.location.href;
		}  
		
		// Checking occurred errors and display them
		var errors = getErrorsHPF(antwort);
		var ausgabe = '';
		
		if (errors['missing'].length > 0 || errors['wrong'].length > 0) {
			
			// check PROCESSING.MISSING.PARAMETERS
			if (errors['missing'].length > 0) {
				for (var i = 0; i < errors['missing'].length; i++) {
					if (errors['missing'][i] == 'account.number') {
						ausgabe += '<li>'+jQuery('.msg_missnumber').html()+'</li>';
					} 
					else if (errors['missing'][i] == 'account.expiry_month') {
						ausgabe += '<li>'+jQuery('.msg_missmonth').html()+'</li>';
					} 
					else if (errors['missing'][i] == 'account.expiry_year') {
						ausgabe += '<li>'+jQuery('.msg_missyear').html()+'</li>';
					} 
					else if (errors['missing'][i] == 'account.holder') {
						ausgabe += '<li>'+jQuery('.msg_missholder').html()+'</li>';
					} 
				} // End for-Loop
			} // End if (errors['missing'].length > 0)
			
			// check PROCESSING.WRONG.PARAMETERS
			if (errors['wrong'].length > 0) {
				for (var i = 0; i < errors['wrong'].length; i++) {
					if (errors['wrong'][i] == 'account.number') {
						ausgabe += '<li>'+jQuery('.msg_wrongnumber').html()+'</li>';
					} 
					else if (errors['wrong'][i] == 'account.expiry_month') {
						ausgabe += '<li>'+jQuery('.msg_wrongmonth').html()+'</li>';
					} 
					else if (errors['wrong'][i] == 'account.expiry_year') {
						ausgabe += '<li>'+jQuery('.msg_wrongyear').html()+'</li>';
					} 
					else if (errors['wrong'][i] == 'account.verification') {
						ausgabe += '<li>'+jQuery('.msg_wrongverif').html()+'</li>';
					} 
					else {
						ausgabe += '';
					} 
				} // End for-Loop
			} // End if (errors['wrong'].length >0)
		}

		if(jQuery('#main_inside .order_payment .errorText').length == 0){
			console.log('noch keine Fehlerausgabe gefunden');
			jQuery('.errorText').remove();
			jQuery('#checkout_payment').before('<div class="errorText alert alert-danger" style="display:none;"><ul></ul></div>');
					
			jQuery('#main_inside .gateway h2').after('<div class="errorText"><ul></ul></div>');
			jQuery('.errorText ul').append('<li>'+jQuery('.msg_fill').html()+'</li>'+ausgabe);
			jQuery('.errorText').show();
			
		} else {
			console.log('HAT FEHLER');
			jQuery('.errorText ul li').remove();
			jQuery('.errorText ul').append('<li>'+jQuery('.msg_fill').html()+'</li>'+ausgabe);
			jQuery('.errorText').show();
			
		}
		
		jQuery('html, body').animate({scrollTop: 0}, 0);
		return false;
	}

}	

/**
 * returns a array of failures of a NOK-JSON-Post-Answer from HPF
 * @param responseJSON
 * @returns array errors('missing',wrong)
 */
function getErrorsHPF(responseJSON) {
	var errors = new Array();
	errors['missing'] = new Array(),
	errors['wrong'] = new Array();
			
	
	if (responseJSON['PROCESSING.RESULT'] === 'NOK') {
		for (var i = 0; i < responseJSON['PROCESSING.MISSING.PARAMETERS'].length; i++) {
			errors['missing'].push(responseJSON['PROCESSING.MISSING.PARAMETERS'][i]);
		}
		for (var i = 0; i < responseJSON['PROCESSING.WRONG.PARAMETERS'].length; i++) {
			errors['wrong'].push(responseJSON['PROCESSING.WRONG.PARAMETERS'][i]);
		}
	}
	return errors;
}
