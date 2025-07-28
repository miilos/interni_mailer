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
const editorExtensions = [
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

document.addEventListener('DOMContentLoaded', function() {
    const editorElement = document.getElementById('editor');

    if (!editorElement) {
        console.error('Editor element not found!');
        return;
    }

    editor = new EditorView({
        state: EditorState.create({
            doc: `Select a template to see the code...`,
            extensions: editorExtensions
        }),
        parent: editorElement
    });

    window.editor = {
        getContent: () => {
            return editor ? editor.state.doc.toString() : ''
        },
        setContent: (newContent) => {
            if (editor) {
                const newState = EditorState.create({
                    doc: newContent,
                    extensions: editorExtensions
                });

                editor.setState(newState);
            }
        }
    }
});
