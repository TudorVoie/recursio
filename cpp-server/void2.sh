#!/bin/bash

set -e

SESSION_DIR="$1"
cd "$SESSION_DIR" || exit 1

# aici luam functia si parametrul apelarii si ce returneaza si le punem in fisierele lor pentru a putea scrie pe arbore
grep -oE "\(.+\)" text_test.txt > apelari.txt