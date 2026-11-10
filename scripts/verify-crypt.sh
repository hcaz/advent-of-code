#!/usr/bin/env bash
#
# Fail if any staged puzzle file would be committed as plaintext.
#
# This repo is mirrored to public GitHub and GitLab, so a plaintext puzzle
# input or puzzle text in a commit is a disclosure, not just a mistake.
#
# git-crypt chooses the clean filter from .gitattributes *at the moment of
# staging*. Add a path before its attribute pattern exists and the content is
# staged unencrypted, while `git status` still looks clean. The only reliable
# check is to read the blob back out of the index and look for git-crypt's
# magic header -- that proves the filter actually ran.

set -uo pipefail

# Paths whose contents must never be committed in plaintext.
PROTECTED='(^|/)(input\.txt|step_one\.md|step_two\.md)$'

staged=$(git diff --cached --name-only --diff-filter=ACMR | grep -E "$PROTECTED" || true)

if [ -z "$staged" ]; then
    exit 0
fi

fail=0
checked=0

while IFS= read -r file; do
    [ -n "$file" ] || continue
    checked=$((checked + 1))

    # git-crypt blobs begin with "\0GITCRYPT\0".
    if ! git cat-file -p ":$file" 2>/dev/null | head -c 10 | grep -qa 'GITCRYPT'; then
        echo "PLAINTEXT IN INDEX: $file" >&2
        fail=1
    fi
done <<< "$staged"

if [ "$fail" -ne 0 ]; then
    cat >&2 <<'MSG'

Refusing the commit: the files above are staged unencrypted.

This repo is publicly mirrored, so committing them would publish puzzle
inputs or puzzle text.

Most likely cause: the path is not covered by a git-crypt pattern in
.gitattributes, or it was staged before that pattern was committed. To fix:

  1. git restore --staged <path>
  2. Add the pattern to .gitattributes and commit THAT ALONE first.
  3. Confirm with: git check-attr filter diff -- <path>
     It must report "filter: git-crypt", not "unspecified".
  4. Re-stage the files and commit.

MSG
    exit 1
fi

echo "verify-crypt: $checked staged puzzle file(s) encrypted in index"
exit 0
