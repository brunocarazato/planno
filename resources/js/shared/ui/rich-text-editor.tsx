import Placeholder from '@tiptap/extension-placeholder';
import { EditorContent, useEditor, useEditorState } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import { Bold, Italic, List, ListOrdered } from 'lucide-react';
import { type ReactNode, useEffect, useMemo } from 'react';

import { cn } from '../lib/utils';

type RichTextEditorProps = {
    id: string;
    invalid?: boolean;
    onChange: (value: string) => void;
    placeholder: string;
    value: string;
};

function toEditorContent(value: string) {
    if (!value.trim()) {
        return '';
    }

    const containsSupportedHtml = /<(p|strong|em|ul|ol|li|br)\b/i.test(value);

    if (containsSupportedHtml) {
        return value;
    }

    const escapedValue = value
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    return `<p>${escapedValue.replaceAll('\n', '<br>')}</p>`;
}

export function RichTextEditor({ id, invalid = false, onChange, placeholder, value }: RichTextEditorProps) {
    const extensions = useMemo(
        () => [
            StarterKit.configure({
                blockquote: false,
                code: false,
                codeBlock: false,
                heading: false,
                horizontalRule: false,
                link: false,
                strike: false,
                underline: false,
            }),
            Placeholder.configure({ placeholder }),
        ],
        [placeholder],
    );
    const editor = useEditor({
        content: toEditorContent(value),
        editorProps: {
            attributes: {
                'aria-describedby': `${id}-formatting-help`,
                'aria-invalid': String(invalid),
                'aria-label': placeholder,
                class: 'rich-text-editor__content',
                id,
            },
        },
        extensions,
        immediatelyRender: false,
        onUpdate: ({ editor: currentEditor }) => {
            onChange(currentEditor.isEmpty ? '' : currentEditor.getHTML());
        },
    });

    const activeFormats = useEditorState({
        editor,
        selector: ({ editor: currentEditor }) => ({
            bold: currentEditor?.isActive('bold') ?? false,
            bulletList: currentEditor?.isActive('bulletList') ?? false,
            italic: currentEditor?.isActive('italic') ?? false,
            orderedList: currentEditor?.isActive('orderedList') ?? false,
        }),
    });

    useEffect(() => {
        if (!editor) {
            return;
        }

        const nextContent = toEditorContent(value);
        const currentContent = editor.isEmpty ? '' : editor.getHTML();

        if (currentContent !== nextContent) {
            editor.commands.setContent(nextContent, { emitUpdate: false });
        }
    }, [editor, value]);

    return (
        <div
            className={cn(
                'mt-1 overflow-hidden rounded-md border bg-white transition focus-within:ring-2',
                invalid
                    ? 'border-red-500 focus-within:border-red-500 focus-within:ring-red-100'
                    : 'border-[#b9c4b7] focus-within:border-[#0f766e] focus-within:ring-[#d9e2d7]',
            )}
        >
            <div
                aria-label="Formatação do texto"
                className="flex items-center gap-1 border-b border-[#dfe5d8] bg-[#f7f9f4] px-2 py-1.5"
                role="toolbar"
            >
                <ToolbarButton
                    active={activeFormats?.bold ?? false}
                    label="Negrito"
                    onClick={() => editor?.chain().focus().toggleBold().run()}
                    shortcut="Ctrl+B"
                >
                    <Bold aria-hidden="true" className="h-4 w-4" />
                </ToolbarButton>
                <ToolbarButton
                    active={activeFormats?.italic ?? false}
                    label="Itálico"
                    onClick={() => editor?.chain().focus().toggleItalic().run()}
                    shortcut="Ctrl+I"
                >
                    <Italic aria-hidden="true" className="h-4 w-4" />
                </ToolbarButton>
                <span aria-hidden="true" className="mx-1 h-5 w-px bg-[#d5ddd1]" />
                <ToolbarButton
                    active={activeFormats?.bulletList ?? false}
                    label="Lista com marcadores"
                    onClick={() => editor?.chain().focus().toggleBulletList().run()}
                >
                    <List aria-hidden="true" className="h-4 w-4" />
                </ToolbarButton>
                <ToolbarButton
                    active={activeFormats?.orderedList ?? false}
                    label="Lista numerada"
                    onClick={() => editor?.chain().focus().toggleOrderedList().run()}
                >
                    <ListOrdered aria-hidden="true" className="h-4 w-4" />
                </ToolbarButton>
            </div>

            <EditorContent editor={editor} />
            <span className="sr-only" id={`${id}-formatting-help`}>
                Use a barra para aplicar negrito, itálico, lista com marcadores ou lista numerada.
            </span>
        </div>
    );
}

function ToolbarButton({
    active,
    children,
    label,
    onClick,
    shortcut,
}: {
    active: boolean;
    children: ReactNode;
    label: string;
    onClick: () => void;
    shortcut?: string;
}) {
    const title = shortcut ? `${label} (${shortcut})` : label;

    return (
        <button
            aria-label={label}
            aria-pressed={active}
            className={cn(
                'inline-flex h-8 w-8 items-center justify-center rounded text-[#51605c] transition hover:bg-white hover:text-[#17211f] focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-[#0f766e]',
                active && 'bg-white text-[#0f766e] shadow-sm ring-1 ring-[#ccd8ca]',
            )}
            onClick={onClick}
            title={title}
            type="button"
        >
            {children}
        </button>
    );
}
