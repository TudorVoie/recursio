#!/bin/bash


#g++ -g main.cpp -o a.out

# aici gasim numele functiei
nume=$(echo "$1" | grep -oE '^[^(]+')


# in primul sed adaug new line dupa fiecare ;
# apoi in al doilea sed adaug new line dupa fiecare {
# in al treilea sed, adaug flush la fiecare cout

cat inceput.cpp > input.cpp

sed 's/;/;\n/g' user.cpp | sed 's/{/{\n/g' | sed -E '/cout[[:space:]]*<</{
  /<<[[:space:]]*flush/! s/;[[:space:]]*$/ << "[TUDOR]" << flush << endl;/
}' >> input.cpp

# aici pun numele functiei in care vreau sa pun breakpoint pt gdb
echo "break $nume" > stiva.gdb
cat template_stiva.gdb >> stiva.gdb

# si aici construiesc inputul
echo "int main() {" >> input.cpp
echo $1 ";" >> input.cpp
echo "return 0; } " >> input.cpp

# fara optimizari
g++ -O0 -g -fno-inline -fno-omit-frame-pointer input.cpp -o a.out
timeout 3s gdb -batch -x stiva.gdb a.out > gdb.txt
#grep -E "Breakpoint [0-9]+," gdb.txt
#grep -E "Value returned is" gdb.txt
#grep -E "Breakpoint [0-9]+,|Value returned is" gdb.txt
#grep -oP '.*(?=\[TUDOR\]$)' gdb.txt
grep -E "Breakpoint [0-9]+,|Value returned is|\[TUDOR\]$" gdb.txt | sed 's/\[TUDOR\]$//'
