#!/bin/bash
#g++ -g main.cpp -o a.out

# fara optimizari
g++ -O0 -g -fno-inline -fno-omit-frame-pointer input.cpp -o a.out
timeout 3s gdb -batch -x stiva.gdb a.out > gdb.txt
#grep -E "Breakpoint [0-9]+," gdb.txt
#grep -E "Value returned is" gdb.txt
grep -E "Breakpoint [0-9]+,|Value returned is" gdb.txt
