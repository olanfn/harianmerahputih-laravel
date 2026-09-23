import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const shell = document.querySelector('[data-admin-shell]');
    document.querySelectorAll('[data-admin-menu]').forEach((button) => {
        button.addEventListener('click', () => {
            const opened = shell?.classList.toggle('is-menu-open') ?? false;
            document.querySelector('.admin-menu-button')?.setAttribute('aria-expanded', String(opened));
        });
    });

    const richEditor = document.querySelector('[data-rich-editor]');
    if (richEditor) {
        const surface = richEditor.querySelector('[data-rich-surface]');
        const input = richEditor.querySelector('[data-rich-input]');
        const execute = (command, value = null) => {
            surface.focus();
            document.execCommand(command, false, value);
            input.value = surface.innerHTML;
        };

        richEditor.querySelectorAll('[data-rich-command]').forEach((button) => button.addEventListener('click', () => execute(button.dataset.richCommand)));
        richEditor.querySelectorAll('[data-rich-align]').forEach((button) => button.addEventListener('click', () => execute(button.dataset.richAlign)));
        richEditor.querySelector('[data-rich-block]')?.addEventListener('change', (event) => execute('formatBlock', event.target.value));
        richEditor.querySelector('[data-rich-size]')?.addEventListener('change', (event) => execute('fontSize', event.target.value));
        richEditor.querySelector('[data-rich-link]')?.addEventListener('click', () => {
            const link = window.prompt('Masukkan URL tautan (https://...)');
            if (link) execute('createLink', link);
        });
        surface.addEventListener('input', () => { input.value = surface.innerHTML; });
        richEditor.addEventListener('submit', () => { input.value = surface.innerHTML; });
    }

    const editor = document.querySelector('.media-editor');
    if (!editor) return;

    const list = document.querySelector('#media-list');
    const input = document.querySelector('#media-input');
    const drop = document.querySelector('#media-drop');
    const body = document.querySelector('#article-body');
    const csrf = document.querySelector('input[name="_token"]')?.value;
    let dragged = null;

    const sync = () => {
        list.querySelectorAll('.media-item').forEach((item, index) => {
            item.querySelectorAll('input[type="hidden"]').forEach((hidden) => hidden.remove());
            [['id', item.dataset.id], ['role', item.querySelector('[data-field="role"]').value], ['sort_order', index], ['alt_text', item.querySelector('[data-field="alt_text"]').value], ['caption', item.querySelector('[data-field="caption"]').value]].forEach(([key, value]) => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = `media[${index}][${key}]`;
                hidden.value = value;
                item.append(hidden);
            });
        });
    };

    const addRow = (media) => {
        const row = document.createElement('article');
        row.className = 'media-item';
        row.draggable = true;
        row.dataset.id = media.id;
        row.dataset.url = media.url;
        row.innerHTML = `<div class="media-item__preview"><img src="${media.url}" alt=""><span class="media-item__handle">⋮⋮</span></div><div class="media-item__fields"><label><span>Peran</span><select data-field="role"><option value="featured">Gambar utama</option><option value="gallery" selected>Galeri</option><option value="inline">Inline</option></select></label><label><span>Alt text</span><input data-field="alt_text"></label><label><span>Caption</span><input data-field="caption"></label><button class="media-remove" type="button" data-remove>Hapus relasi</button><small>Berhasil diunggah</small></div>`;
        list.append(row);
        sync();
    };

    const upload = (file) => {
        const status = document.createElement('p');
        status.textContent = `Mengunggah ${file.name}...`;
        document.querySelector('#media-upload-status').append(status);
        const send = () => {
            const form = new FormData();
            form.append('image', file);
            fetch(editor.dataset.uploadUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' }, body: form })
                .then((response) => response.ok ? response.json() : Promise.reject())
                .then((media) => { status.remove(); addRow(media); })
                .catch(() => { status.innerHTML = `Gagal: ${file.name} <button type="button">Coba lagi</button>`; status.querySelector('button').onclick = send; });
        };
        send();
    };

    const handleFiles = (files) => Array.from(files).slice(0, 20).forEach(upload);
    input.addEventListener('change', (event) => handleFiles(event.target.files));
    drop.addEventListener('dragover', (event) => { event.preventDefault(); drop.classList.add('is-over'); });
    drop.addEventListener('dragleave', () => drop.classList.remove('is-over'));
    drop.addEventListener('drop', (event) => { event.preventDefault(); drop.classList.remove('is-over'); handleFiles(event.dataTransfer.files); });
    list.addEventListener('click', (event) => { if (event.target.matches('[data-remove]')) { event.target.closest('.media-item').remove(); sync(); } });
    list.addEventListener('dragstart', (event) => { dragged = event.target.closest('.media-item'); });
    list.addEventListener('dragover', (event) => event.preventDefault());
    list.addEventListener('drop', (event) => { event.preventDefault(); const target = event.target.closest('.media-item'); if (dragged && target && dragged !== target) list.insertBefore(dragged, target); sync(); });
    list.addEventListener('change', sync);

    document.querySelector('#insert-media')?.addEventListener('click', () => {
        const selected = list.querySelector('.media-item select[data-field="role"] option:checked[value="inline"]')?.closest('.media-item');
        if (!selected) return;
        const token = `[[media:${selected.dataset.id}]]`;
        const start = body.selectionStart ?? body.value.length;
        const end = body.selectionEnd ?? start;
        body.setRangeText(token, start, end, 'end');
        body.focus();
        sync();
    });

    sync();
});
