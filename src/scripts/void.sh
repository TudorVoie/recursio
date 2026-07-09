#!/bin/bash

set -e

#g++ -g main.cpp -o a.out

# aici gasim numele functiei
nume=$(echo "$1" | grep -oE '^[^(]+')

SESSION_DIR="$2"
cd "$SESSION_DIR" || exit 1

# in primul sed adaug new line dupa fiecare ;
# apoi in al doilea sed adaug new line dupa fiecare {
# in al treilea sed, adaug flush la fiecare cout

cat /app/inceput.cpp > input.cpp

# sed 's/;/;\n/g' user.cpp | sed 's/{/{\n/g' | sed -E '/cout[[:space:]]*<</{
#  /<<[[:space:]]*flush/! s/;[[:space:]]*$/ << "[TUDOR]" << flush << endl;/
# }' >> input.cpp

 cat user.cpp >> input.cpp

# sed 's/{/{\n/g' user.cpp | \
# sed -E '/cout[[:space:]]*<</{
#    /<<[[:space:]]*flush/! s/;[[:space:]]*$/ << "[TUDOR]" << flush;/
# }' >> input.cpp

#sed -E '/cout[[:space:]]*<</ s/cout[[:space:]]*<</cout << "[TUDOR] " << /' user.cpp >> input.cpp

# aici pun numele functiei in care vreau sa pun breakpoint pt gdb
echo "break $nume" > stiva.gdb
cat /app/template_stiva.gdb >> stiva.gdb


#std::ios::sync_with_stdio(false);
#std::cout.tie(nullptr);
#std::cout << std::unitbuf;

# si aici construiesc inputul
echo "int main() {" >> input.cpp
#echo "cout.setf(ios::unitbuf);" >> input.cpp
#echo "ios::sync_with_stdio(false);" >> input.cpp
#echo "cout.tie(nullptr);" >> input.cpp
#echo "cout << std::unitbuf;" >> input.cpp
echo $1 ";" >> input.cpp
echo "return 0; } " >> input.cpp

# fara optimizari
g++ -O0 -g -fno-inline -fno-omit-frame-pointer input.cpp -o /tmp/a.out 2> compilare.txt || true
timeout 3s gdb -batch -x stiva.gdb /tmp/a.out > gdb.txt || true
/tmp/a.out > iesire.txt || true
#grep -E "Breakpoint [0-9]+," gdb.txt
#grep -E "Value returned is" gdb.txt
#grep -E "Breakpoint [0-9]+,|Value returned is" gdb.txt
#grep -oP '.*(?=\[TUDOR\]$)' gdb.txt
#grep -E "Breakpoint [0-9]+,|Value returned is|input.cpp|\[TUDOR\]\s$" gdb.txt | sed 's/\[TUDOR\]$//' | head -n -1 || true
# extragem cum trebuie si ce ne trebuie
grep -E "Breakpoint [0-9]+,|input\.cpp|.*\[TUDOR\].*" gdb.txt | grep -v "cout" | head -n -1 | tail -n +2 | sed -E 's/^[[:space:]]*f[[:space:]]*\(.*\)[[:space:]]+at[[:space:]]+input\.cpp:[0-9]+$/Value/' || true
