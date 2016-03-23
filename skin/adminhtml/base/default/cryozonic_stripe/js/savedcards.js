var Cryozonic_SaveNewCard = function()
{
    var saveButton = document.getElementById('cryozonic-savecard-button');
    var wait = document.getElementById('cryozonic-savecard-please-wait');
    saveButton.style.display = "none";
    wait.style.display = "block";

    if (typeof Stripe != 'undefined')
    {
        createStripeToken(function(err)
        {
            if (err)
            {
                alert(err);
                saveButton.style.display = "block";
                wait.style.display = "none";
            }
            else
                document.getElementById("new-card").submit();
        });
        return false;
    }

    return true;
}
