#include <stdio.h>
#include <string.h>
#include <iostream>
#include <fstream>

using namespace std;

int a[101][101];
int verifica[200];

int main()
{
    FILE *f = fopen("text_test.txt", "r");
    char line[1024];
    char target1[] = "Breakpoint";
    char target2[] = "Value";
    if (!f)
    {
        cerr << "Error: text_test.txt not found\n";
        return 1;
    }

    int i=0,sum=1,j=1,maxim=-1,maximi=-1,ok=0,k;
    while (fgets(line, sizeof(line), f))
    {
        if (strstr(line, target1))
        {
            ok=1;
            if(maximi<i) maximi=i;
            verifica[i+1]=1;
            if(a[i+1][1]!=0)
            {
                for(j=2; j<=100; j++)
                    if(a[i+1][j]==0)
                    {
                        a[i+1][j]+=1;
                        if(maxim<j) maxim=j;
                        j=101;
                    }
            }
            else
            {
                a[i+1][1]+=1;
                if(maxim<1) maxim=1;
            }
            i++;
        }
        else if (strstr(line, target2))
        {
            if(ok==1)
                for(j=2; j<=100; j++)
                {
                    if(a[i][j]==0)
                    {
                        a[i][j-1]=9;
                        j=101;
                    }
                }
            ok=0;
            if(maximi<i) maximi=i;
            i--;
            if(verifica[i]==1)
                verifica[i]=0;
            else
            {
                for(j=2; j<=100; j++)
                {
                    if(a[i][j]==0)
                    {
                        a[i][j-1]+=1;
                        j=101;
                    }
                }
            }
        }
    }

    fclose(f);

    for(i=1; i<maximi; i++)
    {
        j=1;
        sum=0;
        while(a[i][j]!=0)
        {
            if(a[i][j]!=-1 && a[i][j]!=9)
                sum+=a[i][j];
            else sum+=1;
            if(a[i][j]==9 || a[i][j]==-1)
            {
                k=1;
                while(a[i+1][k]!=0) k++;
                if(k>maxim) maxim=k;
                for(j=k+1; j>sum; j--) a[i+1][j]=a[i+1][j-1];
                a[i+1][sum]=-1;
            }
            j++;
        }
    }
    // --- Write matrix to text file with dimensions ---
    ofstream out("matrix.txt");
    out << maximi <<" "<< maxim << "\n\n";

    for(i=1; i<=maximi; i++)
    {
        for(j=1; j<=maxim; j++)
        {
            int val = a[i][j];
            out << val;
            if (j < maxim) out << " "; // space only between numbers

        }
        out << endl;
    }
    out.close();

    cout << "Matrix saved to matrix.txt\n";
    return 0;
}
