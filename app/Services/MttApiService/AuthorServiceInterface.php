<?php

namespace App\Services\MttApiService;

interface AuthorServiceInterface
{
    /**
     * @return Author[]
     */
    function index();

    /**
     * @return Author
     */
    function getAuthor($id);

    /**
     * @return Post[]
     */
    function getPostByAuthor(Author $author);
}
