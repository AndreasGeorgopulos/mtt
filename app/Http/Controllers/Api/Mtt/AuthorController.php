<?php

namespace App\Http\Controllers\Api\Mtt;

use App\Services\MttApiService\AuthorService;
use App\Http\Controllers\Controller;

class AuthorController extends Controller
{
    public function authorList()
    {
        return response()->json(AuthorService::getInstance()->index());
    }

    public function getAuthor($id)
    {
        return response()->json(AuthorService::getInstance()->getAuthor((int) $id));
    }

    public function getPostByAuthor($id, $page = 1)
    {
        $authorService = AuthorService::getInstance();
        if ($author = $authorService->getAuthor($id)) {
            return response()->json($authorService->getPostByAuthor($author, $page));
        }
    }
}
