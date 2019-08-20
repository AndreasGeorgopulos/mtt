<?php

namespace App\Services\MttApiService;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AuthorService extends Service implements AuthorServiceInterface{
    use TSingleton;

    /**
     * @param int $page
     * @return Author[]
     */
    public function index()
    {
        $cache_key = 'authors';
        $cache_expire = 10;

        return Cache::get('authors', function () use ($cache_key, $cache_expire) {
            $page = 1;
            $authors = [];
            while ($page > 0) {
                $res = $this->callApi('authors', 'get', ['page' => $page]);
                foreach ($res->data as $data) {
                    $authors[] = new Author($data);
                }
                $page = $page < $res->last_page ? ($page + 1) : 0;
            }

            usort($authors, function ($a1, $a2) {
                return strcmp(Str::lower($this->changeAccentedChars($a1->name)), Str::lower($this->changeAccentedChars($a2->name))) >= 0 ? true : false;
            });

            if (!empty($authors))
                Cache::put($cache_key, $authors, $cache_expire);

            return $authors;
        });
    }

    /**
     * @return Author
     */
    public function getAuthor($id) : ?Author
    {
        $cache_key = 'author_' . $id;
        $cache_expire = 10;

        return Cache::get($cache_key, function () use ($id, $cache_key, $cache_expire) {
            if ($author = $this->callApi('authors/' . $id)) {
                $author = new Author($author);
                Cache::put($cache_key, $author, $cache_expire);
            }
            return $author;
        });
    }

    /**
     * @param Author $author
     * @param int $page
     * @return Post[]
     */
    public function getPostByAuthor(Author $author, int $page = 1)
    {
        $res = $this->callApi('authors/' . $author->id . '/posts', 'get', ['page' => $page]);
        foreach ($res->data as &$d) {
            $d = new Post($d);
        }
        return $res;
    }

    /**
     * @param string $string
     * @return string
     */
    private function changeAccentedChars (string $string) : string {
        return str_replace(
            ['Á', 'É', 'Í', 'Ó', 'Ö', 'Ő', 'Ú', 'Ü', 'Ű', 'á', 'é', 'í', 'ó', 'ö', 'ő', 'ú', 'ü', 'ű'],
            ['a', 'e', 'i', 'o', 'o', 'o', 'u', 'u', 'u', 'a', 'e', 'i', 'o', 'o', 'o', 'u', 'u', 'u'],
            $string
        );
    }
}