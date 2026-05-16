# Hajnali Kávézó

Webprogramozás 1 gyakorlati beadandó PHP, HTML5, CSS és JavaScript használatával.

Téma: modern kávézó, ahol minden vendég 30% kedvezményt kap, ha hajnali 4:00 és 6:00 között érkezik kávézni.

## Funkciók

- Front-controlleres PHP alkalmazás
- Regisztráció, belépés, kilépés
- Reszponzív, vízszintes menüs felület
- Látványos főoldal saját videóval, YouTube videóval és Google térképpel
- Képgaléria és bejelentkezéshez kötött képfeltöltés
- Kapcsolat űrlap kliens- és szerveroldali ellenőrzéssel
- Üzenetek oldal bejelentkezett felhasználóknak
- CRUD felület a Cukrászda adatbázis `suti` táblájához
- Importált forrásadatok: `data/cukraszda`

## Telepítés

1. Hozz létre egy MariaDB/MySQL adatbázist.
2. Importáld a `database/schema.sql` fájlt.
3. Másold a `config/config.sample.php` fájlt `config/local.php` néven.
4. Állítsd be benne az adatbázis kapcsolatot.
5. Adj írási jogot az `uploads` mappára.

Alap tesztfelhasználó az import után:

- Login: `admin`
- Jelszó: `Admin12345`

