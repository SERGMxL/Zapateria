document.addEventListener('DOMContentLoaded', function () {
    const tipoPago = document.getElementById('tipo_pago');
    const creditCardFields = document.getElementById('credit-card-fields');

    tipoPago.addEventListener('change', function () {
        if (tipoPago.value === 'credito') {
            creditCardFields.style.display = 'block';
        } else {
            creditCardFields.style.display = 'none';
        }
    });
});
