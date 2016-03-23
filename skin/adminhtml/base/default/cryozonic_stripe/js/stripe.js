var stripeTokens = {};

var initStripe = function(apiKey)
{
    // Why would it not be loaded?
    if (typeof Stripe == "undefined")
    {
        var resource = document.createElement('script');
        resource.src = "https://js.stripe.com/v2/";
        var script = document.getElementsByTagName('script')[0];
        script.parentNode.insertBefore(resource, script);

        setTimeout(function(){
            Stripe.setPublishableKey(apiKey);
        }, 500);
    }
    else
        Stripe.setPublishableKey(apiKey);
};

var createStripeToken = function(done)
{
    // First check if the "Use new card" radio is selected, return if not
    var newCardRadio = document.getElementById('new_card');
    var newCardForm = document.getElementById('new-card');
    if ((!newCardRadio || !newCardRadio.checked) && !newCardForm) return done();

    // Validate the card
    var cardName = document.getElementById('cryozonic_stripe_cc_owner');
    var cardNumber = document.getElementById('cryozonic_stripe_cc_number');
    var cardCvc = document.getElementById('cryozonic_stripe_cc_cid');
    var cardExpMonth = document.getElementById('cryozonic_stripe_expiration');
    var cardExpYear = document.getElementById('cryozonic_stripe_expiration_yr');

    var isValid = cardName && cardName.value && cardNumber && cardNumber.value && cardCvc && cardCvc.value && cardExpMonth && cardExpMonth.value && cardExpYear && cardExpYear.value;

    if (!isValid) return done('Invalid card details');

    var cardDetails = {
        name: cardName.value,
        number: cardNumber.value,
        cvc: cardCvc.value,
        exp_month: cardExpMonth.value,
        exp_year: cardExpYear.value
    };

    // AVS
    if (typeof avs_address_line1 != 'undefined')
    {
        cardDetails.address_line1 = avs_address_line1;
        cardDetails.address_zip = avs_address_zip;
    }
    else if (avs_enabled)
    {
        return done('You must first enter your billing address.')
    }

    var cardKey = JSON.stringify(cardDetails);

    if (stripeTokens[cardKey])
    {
        setStripeToken(stripeTokens[cardKey]);
        return done();
    }

    try { checkout.setLoadWaiting('payment'); } catch (e) {}
    Stripe.card.createToken(cardDetails, function (status, response)
    {
        try { checkout.setLoadWaiting(false); } catch (e) {}
        if (response.error)
        {
            if (typeof IWD != "undefined")
            {
                IWD.OPC.Checkout.hideLoader();
                IWD.OPC.Checkout.xhr = null;
                IWD.OPC.Checkout.unlockPlaceOrder();
            }
            alert(response.error.message);
        }
        else
        {
            var token = response.id + ':' + response.card.brand + ':' + response.card.last4;
            stripeTokens[cardKey] = token;
            setStripeToken(token);
            done();
        }
    });
};

function setStripeToken(token)
{
    try
    {
        var input, inputs = document.getElementsByClassName('cryozonic-stripejs-token');
        if (inputs && inputs[0]) input = inputs[0];
        else input = document.createElement("input");
        input.setAttribute("type", "hidden");
        input.setAttribute("name", "payment[cc_stripejs_token]");
        input.setAttribute("class", 'cryozonic-stripejs-token');
        input.setAttribute("value", token);
        var form = document.getElementById('co-payment-form');
        if (!form) form = document.getElementById('order-billing_method_form');
        if (!form)
        {
            form = document.getElementById('new-card');
            input.setAttribute("name", "newcard[cc_stripejs_token]");
        }
        form.appendChild(input);
        disableInputs(true);
    } catch (e) {}
}

function disableInputs(disabled)
{
    var elements = document.getElementsByClassName('stripe-input');
    for (var i = 0; i < elements.length; i++)
    {
        // Don't disable the save cards checkbox
        if (elements[i].type != "checkbox" && elements[i].type != "hidden")
            elements[i].disabled = disabled;
    }
}

var enableInputs = function()
{
    disableInputs(false);
};
