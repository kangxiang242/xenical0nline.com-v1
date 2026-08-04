<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{ weVal: $wire.$entangle('{{ $getStatePath() }}') }"
        x-init="
            if (!document.querySelector('link[href*=\'wangEditor.css\']')) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = '{{ asset('/vendor/wangEditor5/style.css') }}';
                document.head.appendChild(link);
            }

            if (typeof window.wangEditor === 'undefined') {
                const script = document.createElement('script');
                script.src = '{{ asset('/vendor/wangEditor5/index.js') }}';
                script.onload = () => initEditor();
                document.body.appendChild(script);
            } else {
                initEditor();
            }

            function initEditor() {
                if (window.__weInstance) {
                    try { window.__weInstance.destroy(); } catch(e) {}
                }

                const toolbar = document.getElementById('we-toolbar-{{ $getStatePath() }}');
                const editorEl = document.getElementById('we-editor-{{ $getStatePath() }}');
                if (!toolbar || !editorEl) return;

                const editor = window.wangEditor.createEditor({
                    selector: editorEl,
                    config: {
                        placeholder: '請輸入內容...',
                        onChange: () => {
                            weVal = editor.getHtml();
                        },
                    },
                });

                const toolbarInstance = window.wangEditor.createToolbar({
                    editor,
                    selector: toolbar,
                    config: {
                        insertKeys: {},
                    },
                });

                window.__weInstance = editor;

                if (weVal && !editorEl.hasAttribute('data-has-content')) {
                    editor.setHtml(weVal);
                    editorEl.setAttribute('data-has-content', 'true');
                }
            }
        "
        wire:ignore
    >
        <div id="we-toolbar-{{ $getStatePath() }}" class="wang-editor-toolbar" style="border: 1px solid #dbe3e6; border-bottom: none; border-radius: 4px 4px 0 0;"></div>
        <div
            id="we-editor-{{ $getStatePath() }}"
            class="wang-editor-content"
            style="height: 500px; overflow-y: auto; border: 1px solid #dbe3e6; border-radius: 0 0 4px 4px; z-index: 10;"
        ></div>
    </div>
</x-dynamic-component>