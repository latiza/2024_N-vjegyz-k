<?php
$jelszo = "juliska";
print "<p><b>Jelszo:</b> {$jelszo}<br>";/**mi az hogy hash érték, 
A hash függvények (kiejtése: hes, magyarul hasítófüggvények) olyan informatikában használt eljárások, amelyekkel bármilyen hosszúságú adatot adott hosszúságra képezhetünk le. Az így kapott véges adat neve hash/hasító érték. Ezek az algoritmusok az 1980-as évek legvégén az elektronikus aláírás megjelenésével váltak szükségessé. A hasítófüggvények a számítástechnikában, elsősorban a tároló technikában, már az 1950-es évek elején megjelentek.
nézzük röviden, vannak olyan függvények, amik igazából csak egy ujjlenyomatot készítenek, pontosabban egy hash értéket, ami arra jó, hogy van egy adat, és annak csak a sértetlenségét tudom megvizsgálni, hogy bele manipuláltak, vagy nem és ezek a függvények, egy bizonyos bemenet esetén, egy meghatározott kimetet gyártanak, de olyat amit nem tudunk visszafejteni. Persze azért megvan rá a módszertan, de vegyük azt az alapesetet, hogy nem tudjuk visszafejetni, mert nem tudjuk hogyan működik. Amit most alkotunk azért még visszafejthető és még mindig nem a legbiztonságosabb, de már lényegesen jobb lesz mint az előző. 
 */
print "<p><b>md5():</b> ".md5($jelszo)."<br>";
/**titkosítási módszer aminek a neve md5, ezek már nem annyira biztonságosak, mint amikor régen használták, de gyakorlatilag egy 32 karakter hosszú zöldséget fog nekünk előállítani, */
print "<p><b>sha1():</b> ".sha1($jelszo)."<p>";
/**ez a sha1 pedig egy 40 karakteres zöldséget, de ettől már vannak erősebb módszerek is
 * Tehát ezzel kiíratom az összes olyan adott értéket, hogy amikor legeneráltam a jelszó az összes titkos értéket, akkor azt elvileg nem tudom vissza fejteni, de a gyakorlatban más kérdés. Futtassuk le, szépen kiírja a jelszavakat.  Két hexadecimális érték készült, ezek a juliska szóból készültek igazából. 
 * Válasszuk a sha1-es titkosítást, ami már egyel jobb mint az md5, de nem is nasan-ak csinálunk alkalmazást, hanem magunknak egy kis mintát, amivel el lehet indulni az életben. az előző fájlunkat egy az egybe átmásolhatjuk egy új fájlba aminek adhatjuk a index-hashel.html nevet, és tulajdonképpen egyetlen sort írunk át a 19 sor körül:
if ($email == "jancsi@gmail.com" && sha1($jelszo) == "49cef48df229f6e608f4b57c11ef05c4f014f0c6")

 */

?>