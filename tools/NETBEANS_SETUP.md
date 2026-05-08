# NetBeans -> Claude Code Bridge Setup

This bridge replaces the buggy `/ide` socket connection with a reliable shell-based integration using `claude -c -p` (continue session + print output).

## How It Works

Instead of NetBeans talking to Claude Code through an IDE socket (which hangs), the bridge:
1. Runs `claude -c -p` from the project directory.
2. Continues the most recent Claude Code session for this project (preserves context).
3. Prints the response and exits.
4. NetBeans captures the output in its Output window.

## Prerequisites

- macOS (uses `osascript` for the interactive "Ask" mode).
- `claude` CLI installed and on your `$PATH`.
- You have run `claude` at least once in this project directory (so a session exists to continue).

## Installation

1. The script is already at `tools/netbeans-claude-bridge.sh` and is executable.
2. No further installation needed.

## NetBeans External Tools Configuration

Go to **Tools -> Options -> PHP -> Frameworks & Tools -> External Tools** (or **Tools -> Options -> Miscellaneous -> External Tools** depending on your NetBeans version).

Add the following tools:

### 1. Claude: Explain
- **Name:** `Claude: Explain`
- **Command:** `${project}/tools/netbeans-claude-bridge.sh`
- **Arguments:** `--mode explain --file "${file}" --selected "${selected}" --line "${line}"`
- **Working Directory:** `${project}`

### 2. Claude: Refactor
- **Name:** `Claude: Refactor`
- **Command:** `${project}/tools/netbeans-claude-bridge.sh`
- **Arguments:** `--mode refactor --file "${file}" --selected "${selected}" --line "${line}"`
- **Working Directory:** `${project}`

### 3. Claude: Ask
- **Name:** `Claude: Ask`
- **Command:** `${project}/tools/netbeans-claude-bridge.sh`
- **Arguments:** `--mode ask --file "${file}" --selected "${selected}" --line "${line}"`
- **Working Directory:** `${project}`

### 4. Claude: Review
- **Name:** `Claude: Review`
- **Command:** `${project}/tools/netbeans-claude-bridge.sh`
- **Arguments:** `--mode review --file "${file}" --line "${line}"`
- **Working Directory:** `${project}`

### 5. Claude: Generate Docs
- **Name:** `Claude: Generate Docs`
- **Command:** `${project}/tools/netbeans-claude-bridge.sh`
- **Arguments:** `--mode doc --file "${file}"`
- **Working Directory:** `${project}`

## Keyboard Shortcuts (Optional)

After creating the tools, go to **Tools -> Options -> Keymap** and search for each tool name to assign shortcuts.

Suggested bindings:
- `Claude: Explain` → `Ctrl+Shift+E`
- `Claude: Refactor` → `Ctrl+Shift+R`
- `Claude: Ask` → `Ctrl+Shift+A`
- `Claude: Review` → `Ctrl+Shift+W`
- `Claude: Generate Docs` → `Ctrl+Shift+D`

## Modes

| Mode      | Description                                                              |
|-----------|--------------------------------------------------------------------------|
| `explain` | Explains selected code (or the current file if nothing is selected).   |
| `refactor`| Suggests improvements for selected code or the current file.             |
| `ask`     | Opens a macOS dialog for a custom question; includes selection context.|
| `review`  | Reviews the current file for bugs, security, and style issues.           |
| `doc`     | Generates PHPDoc for the current file.                                   |

## Notes

- The bridge uses `--permission-mode auto` and `--allowed-tools "Read,Edit,Bash"` so Claude can inspect and modify files automatically.
- Context is preserved because `-c` continues the same session each time.
- If no text is selected, the tool falls back to analyzing the entire file.
- Multi-line selections with quotes can sometimes be truncated by NetBeans macro expansion; for complex blocks, use `--mode ask` and describe the range instead.