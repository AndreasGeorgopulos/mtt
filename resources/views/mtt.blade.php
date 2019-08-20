<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Mtt api kliens</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.8/angular.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>

    <style>
        .loading-wrapper {  }
        .loading-dots > div { width: 1.5rem; height: 1.5rem; background-color: #000000; border-radius: 50%; display: inline-block; animation: 1.5s bounce infinite ease-in-out both; }
        .loading-dots .bounce { animation-delay: 0.30s; }
        .loading-dots .bounce2 { animation-delay: 0.15s; }
        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }
    </style>

    <script>
        (function () {
            // Angular JS app
            var app = angular.module('App', [function ($interpolateProvider) {
                // szintaktika váltás
                $interpolateProvider.startSymbol('[[');
                $interpolateProvider.endSymbol(']]');
            }]);

            // Angular JS controller
            app.controller('ApiController', function ($scope, filterFilter, $http, $timeout) {
                // Api url-ek
                const urls = {
                    reset:         '/api/mtt/reset',
                    post_list:     '/api/mtt/posts/[page]/[from]/[to]',
                    post:          '/api/mtt/post/[id]',
                    post_insert:   '/api/mtt/post',
                    author_list:   '/api/mtt/authors',
                    author:        '/api/mtt/author/[id]',
                    authors_posts: '/api/mtt/authors/[id]/posts/[page]',
                };

                // Controller property-k
                $scope.inProgress = false;
                $scope.data = { posts: null, authors: null, selected_post: null, new_post: null };
                $scope.filter = { page: 1, from: null, to: null, author: 0 };


                // Események controller property-k változásaira

                // Author kiválasztása
                $scope.$watch('filter.author', function () {
                    if ($scope.filter.author > 0) {
                        $scope.filter.from = null;
                        $scope.filter.to = null;
                    }
                    $scope.filter.page = 1;
                    $scope.data.selected_post = null;
                    $scope.getPosts();
                });

                // Dátum from mező megadása
                $scope.$watch('filter.from', function () {
                    if ($scope.filter.from || $scope.filter.to) {
                        $scope.filter.author = 0;
                    }
                    $scope.filter.page = 1;
                    $scope.data.selected_post = null;
                    $scope.getPosts();
                });

                // Dátum to mező megadása
                $scope.$watch('filter.to', function () {
                    if ($scope.filter.from || $scope.filter.to) {
                        $scope.filter.author = 0;
                    }
                    $scope.filter.page = 1;
                    $scope.data.selected_post = null;
                    $scope.getPosts();
                });

                // Lapozás
                $scope.$watch('filter.page', function () {
                    $scope.data.selected_post = null;
                    $scope.getPosts();
                });

                // Poszt kiválasztása
                $scope.$watch('data.selected_post', function () {
                    if ($scope.data.selected_post) $('#postModal').modal('show');
                    else $('#postModal').modal('hide');
                });

                // Új poszt
                $scope.$watch('data.new_post', function () {
                    if ($scope.data.authors) {
                        if ($scope.data.new_post) $('#newPostModal').modal('show');
                        else $('#newPostModal').modal('hide');
                    }
                });

                // VÉGE Események controller property-k változásaira

                // Inicializálás
                $scope.init = function () {
                    $scope.getAuthors(0, function () {
                        $scope.getPosts();
                        $scope.data.new_post = null;
                    });
                };

                // Author lista lekérése
                $scope.getAuthors = function (id, completeCallback) {
                    if (id) {
                        $http.get(urls.author.replace('[id]', id)).then(function (response) {
                            if (completeCallback) completeCallback(response);
                        });
                    }
                    else {
                        $http.get(urls.author_list).then(function (response) {
                            $scope.data.authors = response.data;
                            if (completeCallback) completeCallback(response);
                        });
                    }
                };

                // Poszt lekérése
                $scope.getPostById = function (id) {
                    $scope.inProgress = true;
                    $http.get(urls.post.replace('[id]', id)).then(function (response) {
                        $scope.data.selected_post = response.data;
                        $scope.inProgress = false;
                    });
                    return false;
                };

                // Poszt lista lekérése
                $scope.getPosts = function () {
                    function getFormatedDate (date) {
                        if (date) {
                            return date.getFullYear() + '-' + ((date.getMonth() + 1) < 10 ? '0' : '') + (date.getMonth() + 1) + '-' + (date.getDate() < 10 ? '0' : '') + date.getDate();
                        }
                    }

                    let url = $scope.filter.author ? urls.authors_posts : urls.post_list;
                    url = url.replace('[id]', $scope.filter.author);
                    url = url.replace('/[from]', $scope.filter.from ? '/' + getFormatedDate($scope.filter.from) : '');
                    url = url.replace('/[to]', $scope.filter.to ? '/' + getFormatedDate($scope.filter.to) : '');
                    url = url.replace('/[page]', $scope.filter.page ? '/' + $scope.filter.page : '/1');

                    $scope.inProgress = true;
                    $http.get(url).then(function (response) {
                        $scope.data.posts = response.data;
                        $scope.inProgress = false;
                    });
                };

                // Új poszt hozzáadása
                $scope.addNewPost = function () {
                    $scope.inProgress = true;
                    $http.post(urls.post_insert, $scope.data.new_post).then(function (response) {
                        $scope.filter.author = $scope.data.new_post.author_id;
                        $scope.data.new_post = null;
                        $scope.inProgress = false;
                    });
                };

                // Adatbázis reset
                $scope.postsReset = function () {
                    $scope.inProgress = true;
                    $http.get(urls.reset).then(function (response) {
                        $scope.inProgress = false;
                        $scope.init();
                    });
                };
            });
        })();

    </script>
</head>


<body data-ng-app="App">
    <div class="wrapper" data-ng-controller="ApiController" data-ng-init="init()">
        <div class="container">

            <!-- Poszt szűrő form -->
            <form class="form-inline mt-4">
                <div class="form-group input-group-sm mb-3">
                    <label for="s_authors" class="mr-2">Szerzők:</label>
                    <select id="s_authors" class="form-control" data-ng-model="filter.author">
                        <option data-ng-value="0">--- ---</option>
                        <option data-ng-repeat="author in data.authors" data-ng-value="[[author.id]]">[[author.name]]</option>
                    </select>
                </div>

                <div class="form-group input-group-sm ml-3 mb-3">
                    <label for="from" class="mr-2">Dátumtól:</label>
                    <input type="date" class="form-control" data-ng-model="filter.from" id="from" />
                </div>

                <div class="form-group input-group-sm ml-3 mb-3">
                    <label for="to" class="mr-2">Dátumig:</label>
                    <input type="date" class="form-control" data-ng-model="filter.to" id="to" />
                </div>

                <div class="form-group input-group-sm ml-3 mb-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-ng-click="data.new_post = {name: '', body: '', author_id: 0}"><i class="fas fa-plus"></i> Új poszt</button>
                </div>

                <div class="form-group input-group-sm ml-3 mb-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-ng-click="postsReset()"><i class="fas fa-window-restore"></i> Reset</button>
                </div>
            </form>
            <hr />

            <!-- Loading progress -->
            <div data-ng-if="inProgress" class="loading-wrapper text-center vertical-middle">
                <div class="loading-dots">
                    <div class="bounce"></div>
                    <div class="bounce2"></div>
                    <div class="bounce3"></div>
                </div>
            </div>

            <!-- Posztok -->
            <div data-ng-if="data.posts.data">
                <div data-ng-if="data.posts.total == 0" class="text-center">Nincs megjelíthető elem</div>
                <article class="pb-3" data-ng-repeat="post in data.posts.data">
                    <a href="#" data-slug="[[post.slug]]" data-ng-click="getPostById(post.slug)">[[post.name]]</a>
                    <p><sub>[[post.created_at]]</sub></p>
                </article>
            </div>

            <!-- Lapozó -->
            <hr />
            <form class="form-inline" data-ng-if="data.posts.last_page > 1">
                <button class="btn btn-default btn-sm" data-ng-disabled="filter.page == 1" data-ng-click="filter.page = 1"><i class="fas fa-fast-backward"></i></button>
                <button class="btn btn-default btn-sm" data-ng-disabled="filter.page == 1" data-ng-click="filter.page = filter.page - 1"><i class="fas fa-backward"></i></button>
                <input type="number" class="form-control input-sm ml-2" data-ng-model="filter.page" id="page" min="1" max="[[data.posts.last_page]]" /><span class="ml-1 mr-2"> / [[data.posts.last_page]]</span>
                <button class="btn btn-default btn-sm" data-ng-disabled="filter.page == data.posts.last_page" data-ng-click="filter.page = filter.page + 1"><i class="fas fa-forward"></i></button>
                <button class="btn btn-default btn-sm" data-ng-disabled="filter.page == data.posts.last_page" data-ng-click="filter.page = data.posts.last_page"><i class="fas fa-fast-forward"></i></button>
            </form>

            <!-- Poszt modal -->
            <div id="postModal" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">[[data.selected_post.name]]</h4>
                            <button type="button" class="close" data-ng-click="data.selected_post = null">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>Publikálva: [[data.selected_post.created_at]], utolsó módosítás: [[data.selected_post.updated_at]]</p>
                            <p>[[data.selected_post.body]]</p>
                            <a href="#" data-ng-click="filter.author = data.selected_post.author_id">[[data.selected_post.author.name]]</a>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-ng-click="data.selected_post = null">Bezár</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Új poszt modal -->
            <div id="newPostModal" class="modal fade" role="dialog">
                <div class="modal-dialog modal-lg">
                    <form name="newPostForm" class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Új poszt</h4>
                            <button type="button" class="close" data-ng-click="data.new_post = null">&times;</button>
                        </div>

                        <div class="modal-body row">
                            <div class="col-md-6">
                                <label>Szerző *:</label>
                                <select class="form-control" data-ng-model="data.new_post.author_id" required>
                                    <option data-ng-value="0">--- ---</option>
                                    <option data-ng-repeat="author in data.authors" data-ng-value="[[author.id]]">[[author.name]]</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="s_new_title">Cím *:</label>
                                <input class="form-control" data-ng-model="data.new_post.name" required />
                            </div>

                            <div class="col-12 mt-3">
                                <label for="s_new_title">Szöveg *:</label>
                                <textarea class="form-control" data-ng-model="data.new_post.body" required></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div data-ng-if="inProgress" class="loading-wrapper text-center">
                                <div class="loading-dots">
                                    <div class="bounce"></div>
                                    <div class="bounce2"></div>
                                    <div class="bounce3"></div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" data-ng-click="addNewPost()" data-ng-disabled="newPostForm.$invalid || !data.new_post.author_id">Rendben</button>
                            <button type="button" class="btn btn-secondary" data-ng-click="data.new_post = null">Mégsem</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</body>
</html>