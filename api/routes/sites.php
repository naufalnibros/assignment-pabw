<?php
$app->get('/', function ($request, $responsep) {

});

$app->get('/site/session', function ($request, $response) {

    // bypass login
    if(empty($_SESSION)){
        $_SESSION = ["user" => ["id" => 1,"username" => "admin", "nama" => "Super Administrator","m_roles_id"=>1,"akses" => 
                [
                    "master_roles"  => FALSE,
                    "master_user"   => FALSE,
                    "master_siswa"  => TRUE
                ]
            ]
        ];
    }

    if (isset($_SESSION['user']['id'])) {
        return successResponse($response, $_SESSION);
    }
    return unprocessResponse($response, ['undefined']);
})->setName("session");

$app->post('/site/login', function ($request, $response) {
    $params = $request->getParams();
    $sql    = $this->db;

    $username = isset($params['username']) ? $params['username'] : '';
    $password = isset($params['password']) ? $params['password'] : '';

    $model = $sql->select("m_user.*,m_roles.akses")
        ->from("m_user")
        ->where("username", "=", $username)
        ->andWhere("password", "=", sha1($password))
        ->leftJoin("m_roles", "m_roles.id = m_user.m_roles_id")
        ->find();

    if (!empty($model)) {
        $_SESSION['user']['id']                  = $model->id;
        $_SESSION['user']['username']            = $model->username;
        $_SESSION['user']['nama']                = $model->nama;
        $_SESSION['user']['m_roles_id'] = $model->m_roles_id;
        $_SESSION['user']['akses']               = json_decode($model->akses);

        return successResponse($response, $_SESSION);
    }
    return unprocessResponse($response, ['Authentication Systems gagal, username atau password Anda salah.']);
})->setName("login");

$app->get('/site/logout', function () {
    session_destroy();
})->setName("logout");
