#include <stdio.h>
#include <string.h>
#include <iostream>
#include <fstream>

using namespace std;

int a[101][101], a2[101][101];
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

    int i=0,sum=1,j=1,maxim=-1,maximi=-1,ok=0,k,x;

    while (fgets(line, sizeof(line), f))
    {
        //pentru breakpoint
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
        //pentru value
        else if (strstr(line, target2))
        {
            //pentru capat de creanga
            if(ok==1)
                for(j=2; j<=100; j++)
                {
                    if(a[i][j]==0)
                    {
                        a[i][j-1]=-2;
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
    //pentru spatiu coloana ocupata anterior
    for(i=1; i<maximi; i++)
    {
        j=1;
        sum=0;
        while(a[i][j]!=0)
        {
            if(a[i][j]!=-1 && a[i][j]!=-2)
                sum+=a[i][j];
            else sum+=1;
            if(a[i][j]==-2 || a[i][j]==-1)
            {
                k=1;
                while(a[i+1][k]!=0) k++;
                if(k>maxim) maxim=k;
                for(x=k+1; x>sum; x--) a[i+1][x]=a[i+1][x-1];
                a[i+1][sum]=-1;
            }
            j++;
        }
    }
    //suma patratelor
    for(i=maximi-1; i>=1; i--)
    {
        j=1;
        sum=1;
        while(a[i][j]!=0 && j<=maxim)
        {
            int rez=0;

            if(a[i][j]!=-2 && a[i][j]!=-1)
            {
                for(k=1; k<=a[i][j]; k++)
                {
                    if(a[i+1][sum]!=-2 && a[i+1][sum]!=-1)
                        rez+=a[i+1][sum];
                    else if(a[i+1][sum]==-2)
                        rez++;
                    sum++;
                }

                a[i][j]=rez;
            }
            else sum++;
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
