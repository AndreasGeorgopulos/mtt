<?php

namespace App\Services\MttApiService;

class Post {
    use TModel;

    public $id, $name, $slug, $body, $author_id, $created_at, $updated_at, $author;
}