<?php

namespace App\Services\MttApiService;

class PostService extends Service implements PostServiceInterface {
    use TSingleton;

    /**
     * @return Post[]
     */
    public function index(int $page = 1, \DateTime $from = null, \DateTime $to = null) : object
    {
        $res = $this->callApi('posts', 'get', [
            'page' => $page,
            'from' => !empty($from) ? $from->format('Y-m-d') : null,
            'to' => !empty($to) ? $to->format('Y-m-d') : null,
        ]);

        foreach ($res->data as &$d) {
            $d = new Post($d);
        }

        return $res;
    }

    /**
     * @return Post
     */
    public function getPost($idOrSlug) : ?Post
    {
        if ($res = $this->callApi('posts/' . $idOrSlug, 'get')) {
            $post = new Post($res);
            $post->author = AuthorService::getInstance()->getAuthor($post->author_id);
            return $post;
        }
    }

    /**
     * @return Post
     */
    public function insert(Post $post)
    {
        return $this->callApi('posts', 'post', $post->toArray());
    }


    public function reset () {
        return $this->callApi('reset');
    }
}