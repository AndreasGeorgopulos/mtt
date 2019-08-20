<h1>MTT Digital teszt feladatok</h1>

<p>Copyright &copy; 2019, Georgopulosz Andreasz</p>
<p>&nbsp;</p>

<p>A tesztfeladatok Laravel 5.8-as keretrendszer felhasználásával készültek. Az 1-es és 2-es feladatok konzolos 
akalmazások, melyek indítás után bekérik a bemeneti adatokat. Nem megfelelő adat esetén hibaüzenetet adnak, 
egyébként megtörténik a kiértékelésük. A 3-as feladat böngésző alapú AngularJS 1.7.8 és Bootstrap 4.3.1 
használatával.</p>

<p>
A feladatok megtekintéséhez szükséges eszközök:
<ul>
    <li>Composer (<a href="https://getcomposer.org">Letöltés</a>)</li>
    <li>Git (<a href="https://git-scm.com/downloads">Letöltés</a>)</li>
    <li>Minimum PHP 7.1.3</li>
</ul>
</p>

<p>
Lépések:
<ul>
    <li>project mappa ([mappa]) létrehozása, majd belépés a mappába</li>
    <li>git clone https://github.com/AndreasGeorgopulos/mtt .</li>
    <li>composer install</li>
    <li>a storage/app és a storage/framework mappák és almappák írás/olvasás engedélyezése</li>
</ul>
</p>


<h2>1. Tömb és string műveletek</h2>

<h3>1.1 Részhalmaz összege</h3>
<p>Indítás: php artisan task:sum-of-subset<br/>
Forráskód: App/Console/Commands/SumOfSubset.php</p>

<h3>1.2. Palindrom</h3>
<p>Indítás: php artisan task:palindrome<br/>
Forráskód: App/Console/Commands/Palindrome.php</p>

<h3>1.3. Zárójelek</h3>
<p>Indítás: php artisan task:bracket-closures<br/>
Forráskód: App/Console/Commands/BracketClosures.php</p>

<h2>2. XML fájl kezelés</h2>
<p>Indítás: php artisan task:xml<br/>
Forráskód: App/Console/Commands/Movies.php<br/>
Xml file: storage/app/mtt/data.xml<br/>
Módosított xml file: storage/app/mtt/data_modified.xml</p>

<p><i>Futtatáskor a film kategóriák és a megjelenés évének szétválasztása akkor történik meg, 
ha a módosított xml file nem létezik.A megjelenés éve és kategória megadása után listázza a filmeket.</i></p>


<h2>3. REST HTTP kliens</h2>
<p>Indítás: php artisan serve (Böngésző: http://localhost:8000)</p>

Forráskód:
<ul>
    <li>Route config: routes/api.php, routes/web.php</li>
    <li>Controller-ek: App/Http/Controllers/Api/Mtt</li>
    <li>Service (interface, model, stb): App/Services/MttApiService</li>
    <li>Frontend (html, css, js): resources/views/mtt.blade.php</li>
</ul>