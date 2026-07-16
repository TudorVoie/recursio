#!/bin/bash

set -e

SESSION_DIR="$1"
cd "$SESSION_DIR" || exit 1

# aici luam functia si parametrul apelarii si ce returneaza
grep -oE "\(.+\)" text_test.txt \
| perl -pe '
    s/([a-zA-Z_]\w*)=0x[0-9a-fA-F]+/\1/g;
    s/([a-zA-Z_]\w*)=std::.*?(?=,\s*[a-zA-Z_]\w*=|\)$)/\1/g;
    s/[a-zA-Z_]\w*=(-?\d+)/\1/g;
' \
| sed -E 's/[()]//g' \
> apelari.txt

grep "Value returned is" text_test.txt | awk '{print $6}' > returnari.txt