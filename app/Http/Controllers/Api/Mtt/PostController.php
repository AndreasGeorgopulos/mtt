<?php

namespace App\Http\Controllers\Api\Mtt;

use App\Services\MttApiService\AuthorService;
use App\Services\MttApiService\Post;
use App\Services\MttApiService\PostService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class PostController extends Controller
{

    public function postList (int $page = 1, $from = null, $to = null)
    {
        if (!empty($from)) $from = new \DateTime(date('Y-m-d H:i:s', strtotime($from)));
        if (!empty($to)) $to = new \DateTime(date('Y-m-d H:i:s', strtotime($to)));
        return response()->json(PostService::getInstance()->index($page, $from, $to));
    }

    public function getPost ($idOrSlug)
    {
        return response()->json(PostService::getInstance()->getPost($idOrSlug));
    }

    public function addNewPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'body' => 'required',
            'author_id' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if (is_numeric($value) && !($author = AuthorService::getInstance()->getAuthor($value))) {
                        $fail('The ' . $attribute . ' is invalid.');
                    }
                }
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->toArray()], 404);
        }

        $post = new Post();
        $post->name = $request->get('name');
        $post->slug = $request->get('slug', !empty($post->name) ? Str::slug($post->name, '-') : null);
        $post->body = $request->get('body');
        $post->author_id = $request->get('author_id');

        return response()->json(['success' => true, 'data' => PostService::getInstance()->insert($post)]);
    }

    public function reset () {
        return response()->json(PostService::getInstance()->reset());
    }

}
