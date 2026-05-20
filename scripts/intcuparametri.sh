#!/bin/bash
#g++ -g main.cpp -o a.out

set -e

SESSION_DIR="$2"
cd "$SESSION_DIR" || exit 1

# aici gasim numele functiei
nume=$(echo "$1" | grep -oE '^[^(]+')


# aici construim input.cpp
cat /app/inceput.cpp > input.cpp
cat user.cpp >> input.cpp
echo "int main() {" >> input.cpp
cat supVars.cpp >> input.cpp
echo "int rectracktudor = $1 ; return 0; } " >> input.cpp

# aici punem numele functiei in care vrem sa punem breakpoint pt gdb
echo "break $nume" > stiva.gdb
cat /app/template_stiva.gdb >> stiva.gdb

# fara optimizari
g++ -O0 -g -fno-inline -fno-omit-frame-pointer input.cpp -o /tmp/a.out 2> compilare.txt || true
timeout 1s gdb -batch -x stiva.gdb /tmp/a.out > gdb.txt || true
/tmp/a.out > iesire.txt || true
#grep -E "Breakpoint [0-9]+," gdb.txt
#grep -E "Value returned is" gdb.txt
grep -E "Breakpoint [0-9]+,|Value returned is" gdb.txt
