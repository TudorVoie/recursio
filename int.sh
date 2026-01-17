#!/bin/bash
#g++ -g main.cpp -o a.out

# aici gasim numele functiei
nume=$(echo "$1" | grep -oE '^[^(]+')

# aici construim input.cpp
cat inceput.cpp > input.cpp
cat user.cpp >> input.cpp
echo "int main() {" >> input.cpp
echo "cout << $1 ; return 0; } " >> input.cpp

# aici punem numele functiei in care vrem sa punem breakpoint pt gdb
echo "break $nume" > stiva.gdb
cat template_stiva.gdb >> stiva.gdb

# fara optimizari
g++ -O0 -g -fno-inline -fno-omit-frame-pointer input.cpp -o a.out
timeout 1s gdb -batch -x stiva.gdb a.out > gdb.txt
#grep -E "Breakpoint [0-9]+," gdb.txt
#grep -E "Value returned is" gdb.txt
grep -E "Breakpoint [0-9]+,|Value returned is" gdb.txt