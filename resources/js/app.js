import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-password-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
            const wrap = btn.closest('.relative');
            const input = wrap?.querySelector('input');
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.setAttribute('aria-label', isPassword ? 'Скрыть пароль' : 'Показать пароль');
            const iconShow = btn.querySelector('[data-icon="show"]');
            const iconHide = btn.querySelector('[data-icon="hide"]');
            if (iconShow && iconHide) {
                iconShow.classList.toggle('hidden', !isPassword);
                iconHide.classList.toggle('hidden', isPassword);
            }
        });
    });
});
