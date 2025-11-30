// Validaciones básicas y mejoras UX
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form[data-validate="true"]');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
        const required = form.querySelectorAll('[data-required="true"]');
        let ok = true;
        required.forEach(el => {
            if (!el.value || (el.type === 'number' && Number(el.value) < 0)) {
            el.classList.add('input-error');
            ok = false;
            } else {
            el.classList.remove('input-error');
            }
        });
        if (!ok) {
            e.preventDefault();
            alert('Por favor completa correctamente los campos requeridos.');
        }
        });
    });
});
