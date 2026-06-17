<?php
/**
 * Reusable TinyMCE Initialization Partial
 *
 * Variables:
 * @var string $selector     CSS selector (default: .admin-richtext-source)
 * @var int    $height       Editor height (default: 500)
 * @var string $uploadUrl    URL for image upload
 * @var string $deleteUrl    URL for image deletion
 */

$selector  = $selector ?? '.admin-richtext-source';
$height    = $height ?? 520;
$uploadUrl = $uploadUrl ?? base_url('admin/konten/upload-image');
$deleteUrl = $deleteUrl ?? base_url('admin/konten/delete-image');
?>

<!-- Load TinyMCE only once -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@7.6.1/tinymce.min.js" referrerpolicy="origin"></script>

<script>
(function () {
    const initRichText = () => {
        // Check if selector exists in DOM
        if (!document.querySelector('<?= $selector ?>')) return;

        // Remove existing instances to prevent conflicts
        if (typeof tinymce !== 'undefined') {
            tinymce.remove('<?= $selector ?>');
        }

        tinymce.init({
            selector: '<?= $selector ?>',
            height: <?= $height ?>,
            menubar: false,
            license_key: 'gpl',
            branding: false,
            promotion: false,
            resize: true,
            toolbar_mode: 'wrap',
            toolbar_sticky: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount',
                'pagebreak', 'nonbreaking', 'emoticons'
            ].join(' '),
            toolbar: [
                'undo redo | blocks | bold italic underline strikethrough subscript superscript | removeformat removefont',
                'fontfamily fontsize lineheight | forecolor backcolor | emoticons',
                'alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | hr',
                'table | link image media | charmap emoticons hr pagebreak nonbreaking',
                'fullscreen code preview | help',
            ].join(' | '),
            block_formats: 'Paragraf=p; Judul 2=h2; Judul 3=h3; Judul 4=h4; Judul 5=h5; Judul 6=h6',
            font_family_formats: 'Public Sans=Public Sans,system-ui,sans-serif;Arial=arial,helvetica,sans-serif;Georgia=georgia,serif;Times New Roman=times new roman,times,serif;Verdana=verdana,geneva,sans-serif;Courier New=courier new,monospace;',
            font_size_formats: '8pt 10pt 11pt 12pt 14pt 15pt 16pt 18pt 20pt 24pt 28pt 32pt 36pt',
            line_height_formats: '1 1.15 1.3 1.5 1.75 2 2.5 3',
            color_cols: 10,
            color_map: [
                '000000','Hitam','1f2937','Slate 800','374151','Slate 700','6b7280','Slate 500',
                '9ca3af','Slate 400','d1d5db','Slate 300','e5e7eb','Slate 200','f3f4f6','Slate 100',
                'ffffff','Putih','ef4444','Merah','f97316','Oranye','f59e0b','Amber','eab308','Kuning',
                '84cc16','Lime','22c55e','Hijau','14b8a6','Teal','06b6d4','Cyan','0ea5e9','Sky',
                '3b82f6','Biru','6366f1','Indigo','8b5cf6','Ungu','a855f7','Violet','d946ef','Fuchsia',
                'ec4899','Pink','f43f5e','Rose','7c2d12','Coklat','064e3b','Hijau tua',
                '0c4a6e','Biru tua','312e81','Indigo tua',
            ],
            link_default_protocol: 'https',
            relative_urls: false,
            remove_script_host: false,
            image_title: true,
            image_description: true,
            automatic_uploads: true,
            file_picker_types: 'image',
            
            // Image Upload Handler
            images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '<?= $uploadUrl ?>');
                xhr.withCredentials = true;
                
                xhr.upload.onprogress = (event) => {
                    if (event.lengthComputable) {
                        progress((event.loaded / event.total) * 100);
                    }
                };

                xhr.onload = () => {
                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('Upload gagal. Coba lagi.');
                        return;
                    }
                    let json = null;
                    try { json = JSON.parse(xhr.responseText); } catch (e) {
                        reject('Respons upload tidak valid.');
                        return;
                    }
                    if (!json || typeof json.location !== 'string') {
                        reject((json && json.error) ? json.error : 'Upload gagal.');
                        return;
                    }
                    resolve(json.location);
                };
                
                xhr.onerror = () => reject('Koneksi upload gagal.');
                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                xhr.send(formData);
            }),

            media_live_embeds: true,
            content_css: [
                'https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,400;0,600;0,700;1,400&display=swap',
            ],
            content_style: 'body { font-family: "Public Sans", system-ui, sans-serif; font-size: 15px; line-height: 1.65; margin: 1rem; max-width: 52rem; }',
            extended_valid_elements: 'iframe[src|width|height|frameborder|allowfullscreen|title|loading|referrerpolicy|sandbox|class],img[src|alt|title|width|height|loading|class],span[style|class]',
            
            setup: function (editor) {
                editor.ui.registry.addButton('removefont', {
                    tooltip: 'Hapus gaya font',
                    text: 'Remove font style',
                    onAction: function () {
                        editor.formatter.remove('fontname');
                        editor.formatter.remove('fontsize');
                        editor.formatter.remove('lineheight');
                        editor.execCommand('RemoveFormat');
                    },
                });

                editor.on('change input undo redo', function () {
                    editor.save();
                });

                // Auto-delete logic
                let _prevEditorImages = [];
                const getEditorImages = () =>
                    editor.dom.select('img')
                        .map(img => img.getAttribute('src') || '')
                        .filter(src => src.includes('/uploads/editor/'));

                editor.on('init', function () {
                    _prevEditorImages = getEditorImages();
                });

                editor.on('change undo redo', function () {
                    const current = getEditorImages();
                    const removed = _prevEditorImages.filter(src => !current.includes(src));
                    removed.forEach(src => {
                        fetch('<?= $deleteUrl ?>', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            credentials: 'same-origin',
                            body: JSON.stringify({ src }),
                        }).catch(() => { });
                    });
                    _prevEditorImages = current;
                });
            }
        });
    };

    // Initialize on load
    if (document.readyState === 'complete') {
        initRichText();
    } else {
        window.addEventListener('load', initRichText);
    }
})();
</script>
