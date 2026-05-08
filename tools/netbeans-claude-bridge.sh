#!/bin/bash
# NetBeans -> Claude Code Bridge
# Provides non-hanging integration by using claude -c -p (continue + print)
# instead of the interactive /ide socket connection.
#
# Usage in NetBeans External Tools:
#   ${project}/tools/netbeans-claude-bridge.sh --mode explain --file "${file}" --selected "${selected}" --line "${line}"

set -euo pipefail

MODE="ask"
FILE=""
SELECTED=""
LINE=""

usage() {
    echo "Usage: $0 --mode <explain|refactor|ask|review|doc> [--file PATH] [--selected TEXT] [--line N]"
    exit 1
}

while [[ $# -gt 0 ]]; do
    case $1 in
        --mode)     MODE="$2"; shift 2 ;;
        --file)     FILE="$2"; shift 2 ;;
        --selected) SELECTED="$2"; shift 2 ;;
        --line)     LINE="$2"; shift 2 ;;
        -h|--help)  usage ;;
        *) echo "Unknown option: $1"; usage ;;
    esac
done

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$PROJECT_ROOT"

build_prompt() {
    local context=""
    if [[ -n "$FILE" && -f "$FILE" ]]; then
        context="File: $FILE"
        [[ -n "$LINE" ]] && context="$context (around line $LINE)"
    fi

    case $MODE in
        explain)
            if [[ -n "$SELECTED" ]]; then
                echo -e "Explain the following code. $context\n\n\`\`\`php\n$SELECTED\n\`\`\`"
            elif [[ -n "$FILE" && -f "$FILE" ]]; then
                echo -e "Explain the purpose and structure of this file.\n\nFile: $FILE\n\`\`\`php\n$(cat "$FILE")\n\`\`\`"
            else
                echo -e "Explain the current file. $context"
            fi
            ;;
        refactor)
            if [[ -n "$SELECTED" ]]; then
                echo -e "Refactor the following code. Keep behavior identical. Improve clarity, performance, and style. Return only the refactored code block.\n\n$context\n\n\`\`\`php\n$SELECTED\n\`\`\`"
            elif [[ -n "$FILE" && -f "$FILE" ]]; then
                echo -e "Review and suggest refactoring improvements for this file. $context"
            else
                echo -e "Refactor the current code. $context"
            fi
            ;;
        ask)
            local user_question=""
            if command -v osascript >/dev/null 2>&1; then
                user_question=$(osascript \
                    -e 'Tell application "System Events" to display dialog "Ask Claude:" default answer "" buttons {"Cancel", "Ask"} default button "Ask" cancel button "Cancel"' \
                    -e 'text returned of result' 2>/dev/null || true)
            else
                echo "Error: --mode ask requires macOS (osascript)." >&2
                exit 1
            fi
            if [[ -z "$user_question" ]]; then
                echo "No question provided. Exiting." >&2
                exit 0
            fi
            if [[ -n "$SELECTED" ]]; then
                echo -e "$user_question\n\nContext from $FILE (line $LINE):\n\`\`\`php\n$SELECTED\n\`\`\`"
            elif [[ -n "$FILE" ]]; then
                echo -e "$user_question\n\nContext: working on file $FILE"
            else
                echo "$user_question"
            fi
            ;;
        review)
            if [[ -n "$FILE" && -f "$FILE" ]]; then
                echo -e "Review this file for bugs, security issues, code smells, and style violations. Provide a concise summary.\n\nFile: $FILE\n\`\`\`php\n$(cat "$FILE")\n\`\`\`"
            else
                echo "Review the current file. $context"
            fi
            ;;
        doc)
            if [[ -n "$FILE" && -f "$FILE" ]]; then
                echo -e "Generate PHPDoc documentation for the classes/functions in this file. Output the documented code.\n\nFile: $FILE\n\`\`\`php\n$(cat "$FILE")\n\`\`\`"
            else
                echo "Generate documentation. $context"
            fi
            ;;
        *)
            echo "Unknown mode: $MODE" >&2
            usage
            ;;
    esac
}

PROMPT=$(build_prompt)

# Skip empty prompts
if [[ -z "$PROMPT" ]]; then
    exit 0
fi

echo "--- Claude Code ($MODE) ---" >&2

# Run Claude Code: continue session + print output + allow tools + auto permissions
claude -c -p \
    --allowed-tools "Read,Edit,Bash" \
    --permission-mode auto \
    "$PROMPT"
