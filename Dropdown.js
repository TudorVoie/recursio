const dropdown = document.getElementById("myDropdown");

document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("DOMContentLoaded", function () {
  const selected = dropdown.value;

  if (selected === "custom") return; // NU face nimic

  if (selected === "fibonacci") fibonacci();
  else if (selected === "factorial") factorial();
  else if (selected === "knapsack") knapsack();
  else if (selected === "coinChange") coinChange();
  else if (selected === "sumDigits") sumDigits();
  else if (selected === "binarySearch") binarySearch();
  else if (selected === "power") powerFunction();
  else if (selected === "palindrome") palindromeCheck();
});
});

// Dropdown change handler
dropdown.addEventListener("change", function () {
  const value = this.value;
  if(value === "custom") {
    document.getElementById("textarea1").value = ``;
    document.getElementById("textarea2").value = ``;
    document.getElementById("textarea3").value = ``;
  }
  else if (value === "fibonacci") fibonacci();
  else if (value === "factorial") factorial();
  else if (value === "knapsack") knapsack();
  else if (value === "coinChange") coinChange();
  else if (value === "sumDigits") sumDigits();
  else if (value === "binarySearch") binarySearch();
  else if (value === "power") powerFunction();
  else if (value === "palindrome") palindromeCheck();
});

// Recursive function templates
function fibonacci() {
  document.getElementById("textarea1").value = `int fibonacci(int x) {
  if(x == 0 || x == 1) return x;
  return fibonacci(x-1) + fibonacci(x-2);
}`;
  document.getElementById("textarea2").value = `fibonacci(6);`;
  document.getElementById("textarea3").value = ``;
}

function factorial() {
  document.getElementById("textarea1").value = `int factorial(int x) {
  if(x == 0 || x == 1) return 1;
  return x * factorial(x-1);
}`;
  document.getElementById("textarea2").value = `factorial(5);`;
  document.getElementById("textarea3").value = ``;
}

function knapsack() {
  document.getElementById("textarea1").value =
    `int knapsack(int W, int wt[], int val[], int n) {
  if(n == 0 || W == 0) return 0;
  if(wt[n-1] > W) return knapsack(W, wt, val, n-1);
  return max(val[n-1] + knapsack(W - wt[n-1], wt, val, n-1),
             knapsack(W, wt, val, n-1));
}`;
  document.getElementById("textarea3").value = `int wt[] = {10,20,30};
int val[] = {60,100,120};
int W = 50;
int n = 3;
`;
document.getElementById("textarea2").value = 'knapsack(W, wt, val, n);';
}

function coinChange() {
  document.getElementById("textarea1").value =
    `int coinChange(int coins[], int n, int sum) {
  if(sum==0) return 1;
  if(sum<0 || n<=0) return 0;
  return coinChange(coins, n-1, sum) + coinChange(coins, n, sum - coins[n-1]);
}`;
  document.getElementById("textarea2").value = `coinChange(coins, n, sum);`;
  document.getElementById("textarea3").value = `int coins[] = {1,2,5};
int n = 3;
int sum = 5;`;
}


function sumDigits() {
  document.getElementById("textarea1").value = `int sumDigits(int n) {
  if(n==0) return 0;
  return (n%10) + sumDigits(n/10);
}`;
  document.getElementById("textarea2").value = `sumDigits(12345);`;
  document.getElementById("textarea3").value = ``;
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
  document.getElementById("textarea2").value = `binarySearch(arr,l,r,x);`;
  document.getElementById("textarea3").value = `int arr[] = {1,3,5,7,9};
  int x = 5;
  int l = 0;
  int r = 4;`;
}

function powerFunction() {
  document.getElementById("textarea1").value = `int power(int a,int b) {
  if(b==0) return 1;
  return a * power(a,b-1);
}`;
  document.getElementById("textarea2").value = `power(2,8);`;
  document.getElementById("textarea3").value = ``;
}

function palindromeCheck() {
  document.getElementById("textarea1").value =
    `bool isPalindrome(string s,int l,int r) {
  if(l>=r) return true;
  if(s[l]!=s[r]) return false;
  return isPalindrome(s,l+1,r-1);
}`;
  document.getElementById("textarea2").value = `isPalindrome("racecar",0,6);`;
  document.getElementById("textarea3").value = ``;
}
