const dropdown = document.getElementById("myDropdown");

<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
document.addEventListener("DOMContentLoaded", function () {
  fibonacci(); // default function when page loads
});

=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
// Dropdown change handler
dropdown.addEventListener("change", function () {
  const value = this.value;
  if (value === "fibonacci") fibonacci();
  else if (value === "factorial") factorial();
  else if (value === "knapsack") knapsack();
  else if (value === "coinChange") coinChange();
  else if (value === "hanoi") towerOfHanoi();
  else if (value === "sumDigits") sumDigits();
  else if (value === "binarySearch") binarySearch();
  else if (value === "power") powerFunction();
  else if (value === "palindrome") palindromeCheck();
  else if (value === "powerSet") powerSet();
});

// Recursive function templates
function fibonacci() {
  document.getElementById("textarea1").value = `int fibonacci(int x) {
  if(x == 0 || x == 1) return x;
  return fibonacci(x-1) + fibonacci(x-2);
}`;
  document.getElementById("textarea2").value = `fibonacci(6);`;
}

function factorial() {
  document.getElementById("textarea1").value = `int factorial(int x) {
  if(x == 0 || x == 1) return 1;
  return x * factorial(x-1);
}`;
  document.getElementById("textarea2").value = `factorial(5);`;
}

function knapsack() {
  document.getElementById("textarea1").value =
    `int knapsack(int W, int wt[], int val[], int n) {
  if(n == 0 || W == 0) return 0;
  if(wt[n-1] > W) return knapsack(W, wt, val, n-1);
  return max(val[n-1] + knapsack(W - wt[n-1], wt, val, n-1),
             knapsack(W, wt, val, n-1));
}`;
  document.getElementById("textarea2").value = `int wt[] = {10,20,30};
int val[] = {60,100,120};
int W = 50;
int n = 3;
knapsack(W, wt, val, n);`;
}

function coinChange() {
  document.getElementById("textarea1").value =
    `int coinChange(int coins[], int n, int sum) {
  if(sum==0) return 1;
  if(sum<0 || n<=0) return 0;
  return coinChange(coins, n-1, sum) + coinChange(coins, n, sum - coins[n-1]);
}`;
  document.getElementById("textarea2").value = `int coins[] = {1,2,5};
int n = 3;
int sum = 5;
coinChange(coins, n, sum);`;
}

function towerOfHanoi() {
  document.getElementById("textarea1").value =
    `void towerOfHanoi(int n, char from, char to, char aux) {
  if(n==0) return;
  towerOfHanoi(n-1, from, aux, to);
  towerOfHanoi(n-1, aux, to, from);
}`;
  document.getElementById("textarea2").value = `towerOfHanoi(3,'A','C','B');`;
}

function sumDigits() {
  document.getElementById("textarea1").value = `int sumDigits(int n) {
  if(n==0) return 0;
  return (n%10) + sumDigits(n/10);
}`;
  document.getElementById("textarea2").value = `sumDigits(12345);`;
}

function binarySearch() {
  document.getElementById("textarea1").value =
    `int binarySearch(int arr[], int l, int r, int x) {
  if(l>r) return -1;
  int mid = l + (r-l)/2;
  if(arr[mid]==x) return mid;
  if(arr[mid]>x) return binarySearch(arr,l,mid-1,x);
  return binarySearch(arr,mid+1,r,x);
}`;
  document.getElementById("textarea2").value = `int arr[] = {1,3,5,7,9};
binarySearch(arr,0,4,5);`;
}

function powerFunction() {
  document.getElementById("textarea1").value = `int power(int a,int b) {
  if(b==0) return 1;
  return a * power(a,b-1);
}`;
  document.getElementById("textarea2").value = `power(2,8);`;
}

function palindromeCheck() {
  document.getElementById("textarea1").value =
    `bool isPalindrome(string s,int l,int r) {
  if(l>=r) return true;
  if(s[l]!=s[r]) return false;
  return isPalindrome(s,l+1,r-1);
}`;
  document.getElementById("textarea2").value = `isPalindrome("racecar",0,6);`;
}

function powerSet() {
  document.getElementById("textarea1").value =
    `void powerSet(string s,string current,int index) {
  if(index==s.size()) {
    return;
  }
  powerSet(s,current,index+1);
  powerSet(s,current+s[index],index+1);
}`;
  document.getElementById("textarea2").value = `powerSet("abc","","0");`;
}
