import {Extension, EditorState} from "@codemirror/state"
import {
    EditorView, keymap, highlightSpecialChars, drawSelection,
    highlightActiveLine, dropCursor, rectangularSelection,
    crosshairCursor, lineNumbers, highlightActiveLineGutter
} from "@codemirror/view"
import {
    defaultHighlightStyle, syntaxHighlighting, indentOnInput,
    bracketMatching, foldGutter, foldKeymap
} from "@codemirror/language"
import {
    defaultKeymap, history, historyKeymap
} from "@codemirror/commands"
import {
    autocompletion, completionKeymap, closeBrackets,
    closeBracketsKeymap
} from "@codemirror/autocomplete"
import {html} from "@codemirror/lang-html";
import {oneDark} from "@codemirror/theme-one-dark";

let editor
document.addEventListener('DOMContentLoaded', function() {
    const editorElement = document.getElementById('editor');

    if (!editorElement) {
        console.error('Editor element not found!');
        return;
    }

    editor = new EditorView({
        state: EditorState.create({
            doc: `Select a template to see the code...`,
            extensions: [
                html(),
                oneDark,
                lineNumbers(),
                foldGutter(),
                highlightSpecialChars(),
                history(),
                drawSelection(),
                dropCursor(),
                EditorState.allowMultipleSelections.of(true),
                indentOnInput(),
                syntaxHighlighting(defaultHighlightStyle),
                bracketMatching(),
                closeBrackets(),
                autocompletion(),
                rectangularSelection(),
                crosshairCursor(),
                highlightActiveLine(),
                highlightActiveLineGutter(),
                keymap.of([
                    ...closeBracketsKeymap,
                    ...defaultKeymap,
                    ...historyKeymap,
                    ...foldKeymap,
                    ...completionKeymap,
                ])
            ]
        }),
        parent: editorElement
    });
});

document.addEventListener('updateEditor', function(event) {
    if (editor && event.detail.content) {
        editor.dispatch({
            changes: {
                from: 0,
                to: editor.state.doc.length,
                insert: event.detail.content
            }
        });
    }
});
