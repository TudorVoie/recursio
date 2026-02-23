#!/bin/bash

set -e
nume=$(echo "$1" | grep -oE '^[^(]+')

SESSION_DIR="$2"
cd "$SESSION_DIR" || exit 1

cat ../../inceput.cpp > input.cpp
cat user.cpp >> input.cpp
echo "int main() {" >> input.cpp
echo "cout << $1 ; return 0; } " >> input.cpp

g++ -O0 -g -fno-inline -fno-omit-frame-pointer input.cpp -o a.out