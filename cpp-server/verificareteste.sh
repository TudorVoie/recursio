#!/bin/bash

SESSION_DIR="$1"
TEST_FILE="$2"

total_punctaj=0
total_tests=$(jq length "$TEST_FILE")

for i in $(seq 0 $((total_tests - 1))); do
    intrare=$(jq -r ".[$i].intrare" "$TEST_FILE")
    expected=$(jq -r ".[$i].iesire" "$TEST_FILE")
    punctaj=$(jq -r ".[$i].punctaj" "$TEST_FILE")

    # compilează cu intrarea curentă
    ./intrezolvare.sh "$intrare" "$SESSION_DIR"

    if [ ! -f "$SESSION_DIR/a.out" ]; then
        actual="ERROR"
    else
        actual=$("$SESSION_DIR/a.out" 2>/dev/null || echo "ERROR")
    fi

    if [ "$actual" = "$expected" ]; then
        echo "YES"
        total_punctaj=$((total_punctaj + punctaj))
    else
        echo "NO"
    fi
done

echo "---"
echo "$total_punctaj/$((total_tests * 10))"
