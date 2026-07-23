import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.addEventListener('load', () => document.getElementById('pageLoader')?.classList.add('loaded'));

document.querySelectorAll('[data-password-toggle]').forEach(button => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordToggle);
        input.type = input.type === 'password' ? 'text' : 'password';
        button.querySelector('i')?.classList.toggle('fa-eye-slash');
    });
});

window.setupImageUpload = (inputId, zoneId, previewId, promptId, removeId) => {
    const input = document.getElementById(inputId);
    const zone = document.getElementById(zoneId);
    const preview = document.getElementById(previewId);
    const prompt = document.getElementById(promptId);
    const remove = document.getElementById(removeId);
    if (!input || !zone) return;
    const show = file => {
        if (!file?.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = event => { preview.src = event.target.result; preview.hidden = false; prompt.hidden = true; remove.hidden = false; zone.classList.add('has-preview'); };
        reader.readAsDataURL(file);
    };
    input.addEventListener('change', () => show(input.files[0]));
    ['dragenter', 'dragover'].forEach(name => zone.addEventListener(name, event => { event.preventDefault(); zone.classList.add('dragging'); }));
    ['dragleave', 'drop'].forEach(name => zone.addEventListener(name, event => { event.preventDefault(); zone.classList.remove('dragging'); }));
    zone.addEventListener('drop', event => { input.files = event.dataTransfer.files; show(input.files[0]); });
    remove.addEventListener('click', event => { event.preventDefault(); input.value = ''; preview.src = ''; preview.hidden = true; prompt.hidden = false; remove.hidden = true; zone.classList.remove('has-preview'); });
};

document.addEventListener('DOMContentLoaded', () => {
    window.setupImageUpload('foto', 'uploadZone', 'imagePreview', 'uploadPrompt', 'removePreview');
});
