-- Minden érték lekérése
SELECT * FROM tablanev;

-- Adott oszlopok lekérése
SELECT oszlop1, oszlop2, oszlop3 FROM tablanev;

-- Az oszlopok nevének körülírása backtick-kel
SELECT `oszlop1`, `oszlop2`, `oszlop3` FROM tablanev;

-- Feltétellel adatlekérés
SELECT * FROM tablanev WHERE feltetel;

-- Adott oszlopok lekérése feltétellel
SELECT oszlop1, oszlop2, oszlop3 FROM tablanev WHERE feltetel;

-- Keresés LIKE segítségével
SELECT oszlop1, oszlop2, oszlop3 FROM tablanev WHERE oszlop1 LIKE 'kifejezes%';  -- Kezdő kifejezés
SELECT oszlop1, oszlop2, oszlop3 FROM tablanev WHERE oszlop1 LIKE '%kifejezes%';  -- Bármely helyen

-- Rendezés ASC [A-Z-ig], DESC [Z-A-ig]
SELECT * FROM tablanev ORDER BY oszlop1 ASC;  -- Növekvő sorrend
SELECT * FROM tablanev ORDER BY oszlop1 DESC;  -- Csökkenő sorrend
SELECT * FROM tablanev WHERE feltetel ORDER BY oszlop1 DESC;  -- Feltétellel és csökkenő sorrend
SELECT * FROM tablanev WHERE feltetel ORDER BY oszlop1 DESC, oszlop2 ASC;  -- Több oszlop szerinti rendezés

-- Adott darabszám lekérése
SELECT * FROM tablanev LIMIT 10;  -- Az első 10 rekord
SELECT * FROM tablanev LIMIT 10, 10;  -- 10-től kezdődő következő 10 rekord

-- Rekord beszúrása
INSERT INTO tablanev (oszlop1, oszlop2, oszlop3) VALUES ('ertek1', ertek2, ertek3);

-- Rekordok törlése
DELETE FROM tablanev WHERE feltetel;

-- Rekordok módosítása
UPDATE tablanev SET oszlop1 = ertek1, oszlop2 = 'ertek2' WHERE feltetel;

-- Több tábla összekapcsolása JOIN használatával
-- Inner Join: Két tábla közös rekordjait adja vissza
SELECT t1.oszlop1, t2.oszlop2 FROM tabla1 t1
JOIN tabla2 t2 ON t1.kozos_oszlop = t2.kozos_oszlop;

-- Left Join: Az első táblából minden rekordot visszaad, és a második táblából csak a megfelelő rekordokat
SELECT t1.oszlop1, t2.oszlop2 FROM tabla1 t1
LEFT JOIN tabla2 t2 ON t1.kozos_oszlop = t2.kozos_oszlop;

-- Right Join: A második táblából minden rekordot visszaad, és az első táblából csak a megfelelő rekordokat
SELECT t1.oszlop1, t2.oszlop2 FROM tabla1 t1
RIGHT JOIN tabla2 t2 ON t1.kozos_oszlop = t2.kozos_oszlop;

-- Union: Két SELECT eredményének egyesítése
SELECT oszlop1 FROM tabla1
UNION
SELECT oszlop1 FROM tabla2;
