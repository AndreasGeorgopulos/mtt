<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Movies extends Command
{
    private $xml_file_original, $xml_file_modified;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:xml';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'XML fájl kezelés';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->xml_file_original = storage_path('app/mtt/data.xml');
        $this->xml_file_modified = storage_path('app/mtt/data_mofified.xml');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            printf('%s', $this->description . chr(10));
            $this->checkXmlFiles();
            $year = $this->readLineYear();
            $genre = $this->readLineGenre();
            $this->listMovies($year, $genre, function (array $nodes) : void {
                printf('%s', chr(10));
                if (empty($nodes)) {
                    printf('Nincs megjeleníthető film');
                    return;
                }

                foreach ($nodes as $n) {
                    printf('%s (%d)%s', $n->title, $n->date, chr(10));
                }
            });
        }
        catch (\Exception $ex) {
            printf('%s', $ex->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    private function checkXmlFiles () : void {
        if (empty($this->xml_file_original) || empty($this->xml_file_modified)) {
            throw new \Exception('Az xml file-ok elérési útvonalai nincsenek beállítva.');
        }
        else if (!File::exists($this->xml_file_original)) {
            throw new \Exception('A(z) ' . $this->xml_file_original . ' nem elérhető.');
        }

        if (!File::exists($this->xml_file_modified)) {
            $this->splitGenres(function () : void {
                printf('Film kategóriák szétbontva%s', chr(10));
                $this->setDate(function () : void {
                    printf('Megjelenés éve külön mezőben, címből eltávolítva%s', chr(10));
                });
            });
        }
    }

    /**
     * @return int
     * @throws \Exception
     */
    private function readLineYear () : int {
        printf('%sFilmek listázása%sMegjelenés éve: ', chr(10), chr(10));
        $year = readline();
        if (!preg_match('/^[0-9]+$/', $year)) {
            throw new \Exception('Hibás adat, a megjelenés éve egész szám kell legyen.');
        }
        return $year;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function readLineGenre () : string {
        printf('Kategória: ');
        if (!($genre = Str::ucfirst(Str::lower(readline())))) {
            throw new \Exception('Hibás adat, a kategóriát kötelező megadni.');
        }
        return $genre;
    }

    /**
     * @throws Exception
     */
    private function splitGenres (object $completeCallback = null) : void {
        $movies = $this->openXml($this->xml_file_original);
        foreach ($movies as $movie) {
            if (!$movie->genre) {
                throw new \Exception('Hiányzó tag (genre)');
            }

            $genres = $movie->addChild('genres');
            foreach (explode('|', $movie->genre) as $genre) {
                $genres->addChild('genre', Str::ucfirst(Str::lower($genre)));
            }
            unset($movie->genre);
        }

        $movies->asXML($this->xml_file_modified);

        if (is_callable($completeCallback)) {
            $completeCallback();
        }
    }

    /**
     * @throws Exception
     */
    private function setDate (object $completeCallback = null) : void {
        $movies = $this->openXml($this->xml_file_modified);
        foreach ($movies as $movie) {
            if (!$movie->title) {
                throw new \Exception('Hiányzó tag (title)');
            }

            if (preg_match("/\([1-2][0-9]{3}\)$/i", $movie->title, $matches)) {
                $movie->title = trim(str_replace($matches[0], '', $movie->title));
                $movie->addChild('date', str_replace(['(', ')'], '', $matches[0]));
            }
        }

        $movies->asXML($this->xml_file_modified);

        if (is_callable($completeCallback)) {
            $completeCallback();
        }
    }

    /**
     * @param int $year
     * @param string $genre
     * @param object|null $completeCallback
     * @return array
     */
    private function listMovies (int $year, string $genre, object $completeCallback = null) : array {
        $movies = $this->openXml($this->xml_file_modified);
        $nodes = $movies->xpath('movie[date >= \'' . $year . '\' and genres/genre = \'' . $genre . '\']');
        usort($nodes, function ($node_1, $node_2) {
            return $node_2->date - $node_1->date;
        });

        if (is_callable($completeCallback)) {
            $completeCallback($nodes);
        }

        return $nodes;
    }

    /**
     * @param string $filename
     * @return SimpleXMLElement
     * @throws Exception
     */
    private function openXml (string $filename) : \SimpleXMLElement {
        if (!($xml = @file_get_contents($filename))) {
            throw new \Exception('A(z) ' . $filename . ' megnyitása sikertelen.');
        }
        return new \SimpleXMLElement($xml);
    }
}
