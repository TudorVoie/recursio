
# Self-host / Ghid de instalare Recursio
Server-ul nostru (VPS OVHCloud) are la dispoziție 12GB RAM si un nVME rapid. Nu este nevoie de atât, si 8 sunt suficienți. Folosim de asemenea Debian 13 pentru sistemul de operare.
<br>
Inainte de toate, instalati Docker folosind acest ghid oficial:
[Install Docker Engine on Debian | Docker Docs](https://docs.docker.com/engine/install/debian/)
<br>
Apoi, instalăm celelalte dependințe și clonăm proiectul:
```
apt update && apt upgrade -y
apt install git gcc g++ gdb php8.4-fpm php8.4-curl curl grep perl sed -y
git clone https://github.com/TudorVoie/recursio && cd recursio
```
Compilăm partea de analizare stack trace:
```
g++ -Wall main.cpp
```
Compilăm mediul etanș de execuție a codului:
```
docker build -t cpp-debugger .
```
Inițializăm directoare și fișierele necesare:
```
mkdir shares sessions
chmod -R +x *.sh
chmod -R 700 *
```
Adăugăm cheia API de la OpenAI
```
cp .env.example ../.env
nano .env
```
și adăugăm
```
CHATGPT=xxxxxx
```
Rulăm proiectul:
```
php -S 0.0.0.0:8000 
```
Acum Recursio este disponibil pe http://localhost:8000.
O comandă de mentenanță pentru server, ca să șteargă sesiunile vechi care ocupă spațiul inutil:
```
0 * * * * find /path/to/recursio/sessions -mindepth 1 -maxdepth 1 -type d -mmin +360 -exec rm -rf {} +
```
Aceasta se adaugă în crontab-ul utilizatorului ce rulează serverul. (`crontab -e`)
<br>
Recomandăm să folosiți [nginx](https://nginx.org) ca și web server și reverse proxy (noi asta folosim). De asemenea, folosim [Cloudflare](https://cloudflare.com) și [Cloudflare Tunnel](https://developers.cloudflare.com/tunnel/) pentru securizarea și expunerea în fața întregului internet.
