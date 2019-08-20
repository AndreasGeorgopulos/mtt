<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api\Mtt', 'middleware' => ['api']], function () {
    Route::match(['get'],  '/mtt/reset',                       'PostController@reset'            )->name('api_reset');
    Route::match(['get'],  '/mtt/posts/{page?}/{from?}/{to?}', 'PostController@postList'         )->name('api_post_list');
    Route::match(['get'],  '/mtt/post/{idOrSlug}',             'PostController@getPost'          )->name('api_post');
    Route::match(['post'], '/mtt/post',                        'PostController@addNewPost'       )->name('api_post_insert');
    Route::match(['get'],  '/mtt/authors',                     'AuthorController@authorList'     )->name('api_author_list');
    Route::match(['get'],  '/mtt/authors/{id}',                'AuthorController@getAuthor'      )->name('api_author');
    Route::match(['get'],  '/mtt/authors/{id}/posts/{page?}',  'AuthorController@getPostByAuthor')->name('api_authors_posts');
});
