<?php

namespace App\Services\MttApiService;

interface PostServiceInterface
{
    /**
     * @return Post[]
     */
    function index(int $page = 1, \DateTime $from = null, \DateTime $to = null);

    /**
     * @return Post
     */
    function getPost($idOrSlug);

    /**
     * @return Post
     */
    function insert(Post $post);
}
