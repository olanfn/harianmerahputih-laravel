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

    const ticker = document.querySelector('[data-ticker]');
    if (ticker) {
        const headline = ticker.querySelector('[data-ticker-headline]');
        const items = [...ticker.querySelectorAll('[data-ticker-item]')].map((item) => ({ title: item.textContent.trim(), url: item.href }));
        const previous = ticker.querySelector('[data-ticker-prev]');
        const next = ticker.querySelector('[data-ticker-next]');
        let index = 0;
        let rotation;

        const render = (nextIndex) => {
            if (!headline || items.length === 0) return;
            index = (nextIndex + items.length) % items.length;
            headline.href = items[index].url;
            headline.textContent = items[index].title;
            headline.classList.remove('is-changing');
            void headline.offsetWidth;
            headline.classList.add('is-changing');
        };

        const stopRotation = () => window.clearInterval(rotation);
        const startRotation = () => {
            stopRotation();
            if (items.length > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                rotation = window.setInterval(() => render(index + 1), 5000);
            }
        };

        if (items.length <= 1) {
            previous?.setAttribute('disabled', '');
            next?.setAttribute('disabled', '');
        } else {
            previous?.addEventListener('click', () => { render(index - 1); startRotation(); });
            next?.addEventListener('click', () => { render(index + 1); startRotation(); });
            ticker.addEventListener('mouseenter', stopRotation);
            ticker.addEventListener('mouseleave', startRotation);
            ticker.addEventListener('focusin', stopRotation);
            ticker.addEventListener('focusout', (event) => {
                if (!ticker.contains(event.relatedTarget)) startRotation();
            });
            startRotation();
        }
    }

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

    const articlePage = document.querySelector('.article-page');
    const articleBody = articlePage?.querySelector('.article-page__body');
    const fontButton = articlePage?.querySelector('[data-article-font]');
    const shareButton = articlePage?.querySelector('[data-article-share]');
    const shareMenu = articlePage?.querySelector('[data-article-share-menu]');
    const socialButtons = articlePage?.querySelectorAll('[data-article-social]');
    const actionStatus = articlePage?.querySelector('[data-article-action-status]');

    if (articlePage && articleBody && fontButton) {
        const fontLevels = ['', 'article-page__body--large', 'article-page__body--xlarge'];
        let fontLevel = 0;

        const showFontLevel = (level) => {
            fontLevels.forEach((className) => className && articleBody.classList.remove(className));
            if (fontLevels[level]) articleBody.classList.add(fontLevels[level]);
            fontButton.setAttribute('aria-label', level === 2 ? 'Kembalikan ukuran teks' : 'Perbesar ukuran teks');
            fontButton.setAttribute('aria-pressed', String(level > 0));
            fontButton.title = level === 0 ? 'Perbesar ukuran teks' : level === 1 ? 'Perbesar teks lagi' : 'Kembalikan ukuran teks';
        };

        fontButton.addEventListener('click', () => {
            fontLevel = (fontLevel + 1) % fontLevels.length;
            showFontLevel(fontLevel);
        });
        showFontLevel(fontLevel);
    }

    if (articlePage && shareButton) {
        const shareUrl = document.querySelector('link[rel="canonical"]')?.href || window.location.href;
        const shareTitle = document.querySelector('meta[property="og:title"]')?.content || document.title;
        const setActionStatus = (message) => {
            if (!actionStatus) return;
            actionStatus.textContent = message;
            window.setTimeout(() => { actionStatus.textContent = ''; }, 3500);
        };

        const setShareMenu = (open) => {
            if (!shareMenu) return;
            shareMenu.hidden = !open;
            shareButton.setAttribute('aria-expanded', String(open));
        };

        shareButton.addEventListener('click', async () => {
            if (shareMenu) {
                setShareMenu(shareMenu.hidden);
                return;
            }

            try {
                if (navigator.share) {
                    await navigator.share({ title: shareTitle, text: shareTitle, url: shareUrl });
                    setActionStatus('Artikel siap dibagikan.');
                    return;
                }

                if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(shareUrl);
                    setActionStatus('Tautan artikel disalin.');
                    return;
                }

                window.prompt('Salin tautan artikel ini:', shareUrl);
            } catch (error) {
                if (error?.name !== 'AbortError') setActionStatus('Tautan belum dapat dibagikan. Silakan salin URL artikel.');
            }
        });

        const copyArticleUrl = async (platform) => {
            try {
                if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(shareUrl);
                } else {
                    window.prompt('Salin tautan artikel ini:', shareUrl);
                }

                const platformUrl = platform === 'instagram' ? 'https://www.instagram.com/' : 'https://www.tiktok.com/';
                window.open(platformUrl, '_blank', 'noopener,noreferrer');
                setActionStatus(`Tautan disalin. Tempel ke Story ${platform === 'instagram' ? 'Instagram' : 'TikTok'}.`);
            } catch (error) {
                if (error?.name !== 'AbortError') setActionStatus('Tautan belum dapat disalin. Silakan salin URL artikel.');
            }
        };

        socialButtons?.forEach((button) => {
            if (button.tagName === 'BUTTON') {
                button.addEventListener('click', () => {
                    setShareMenu(false);
                    copyArticleUrl(button.dataset.articleSocial);
                });
            }
        });

        document.addEventListener('click', (event) => {
            if (shareMenu && !event.target.closest('.article-share')) setShareMenu(false);
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') setShareMenu(false);
        });
    }

    const showcaseMedia = document.querySelector('[data-showcase-media]');
    if (showcaseMedia) {
        const input = showcaseMedia.querySelector('#showcase-media-input');
        const drop = showcaseMedia.querySelector('.media-drop');
        const status = showcaseMedia.querySelector('#showcase-media-status');
        const preview = showcaseMedia.querySelector('#showcase-media-preview');
        const mediaId = showcaseMedia.querySelector('#showcase-media-id');
        const csrf = document.querySelector('input[name="_token"]')?.value;

        const showError = (message) => {
            status.textContent = message;
            status.classList.add('is-error');
        };

        const upload = (file) => {
            status.textContent = `Mengunggah ${file.name}...`;
            status.classList.remove('is-error');
            const form = new FormData();
            form.append('image', file);
            fetch(showcaseMedia.dataset.uploadUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' }, body: form })
                .then(async (response) => {
                    const payload = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(payload.message || 'Upload foto gagal.');
                    return payload;
                })
                .then((media) => {
                    mediaId.value = media.id;
                    preview.hidden = false;
                    preview.replaceChildren();
                    const image = document.createElement('img');
                    image.src = media.url;
                    image.alt = file.name;
                    const name = document.createElement('span');
                    name.textContent = media.name || file.name;
                    preview.append(image, name);
                    status.textContent = 'Foto berhasil diunggah.';
                })
                .catch((error) => showError(error.message));
        };

        input.addEventListener('change', (event) => {
            const [file] = event.target.files;
            if (file) upload(file);
        });
        drop.addEventListener('dragover', (event) => { event.preventDefault(); drop.classList.add('is-over'); });
        drop.addEventListener('dragleave', () => drop.classList.remove('is-over'));
        drop.addEventListener('drop', (event) => {
            event.preventDefault();
            drop.classList.remove('is-over');
            const [file] = event.dataTransfer.files;
            if (file) upload(file);
        });
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
