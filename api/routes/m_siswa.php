<?php

/**
 * Validasi
 * @param  array $data
 * @param  array $custom
 * @return array
 */
function validasi($data, $custom = array()) {
    $validasi = array(
        'nama' => 'required',
    );

    $cek = validate($data, $validasi, $custom);
    return $cek;
}

/**
 * Get list Siswa
 */
$app->get('/m_siswa/index', function ($request, $response) {
    $params = $request->getParams();

    $sort = "id ASC";
    $offset = isset($params['offset']) ? $params['offset'] : 0;
    $limit = isset($params['limit']) ? $params['limit'] : 10;

    $db = $this->db;

    /** Select siswa from database */
    $db->select("*")
    ->from("m_siswa")
    ->orderBy('id ASC');

    /** Add filter */
    if (isset($params['filter'])) {
        $filter = (array) json_decode($params['filter']);
        foreach ($filter as $key => $val) {
            $db->where($key, 'LIKE', $val);
        }
    }

    /** Set limit */
    if (!empty($limit)) {
        $db->limit($limit);
    }

    /** Set offset */
    if (!empty($offset)) {
        $db->offset($offset);
    }

    /** Set sorting */
    if (!empty($params['sort'])) {
        $db->sort($sort);
    }

    $modelss = $db->findAll();
    $totalItem = $db->count();

    return successResponse($response, ['list' => $modelss, 'totalItems' => $totalItem]);
});

/**
 * Save
 */
$app->post('/m_siswa/save', function ($request, $response) {
    $data = $request->getParams();
    $db = $this->db;

    $validasi = validasi($data);

    if ($validasi === TRUE) {
        try {
            if (!empty($data['id'])) {
                $model = $db->update('m_siswa', $data, ['id' => $data['id']]);
            } else {
                $model = $db->insert('m_siswa', $data);
            }
            return successResponse($response, $model);
        } catch (Exception $e) {
            return unprocessResponse($response, ['data gagal disimpan']);
        }
    }

    return unprocessResponse($response, $validasi);
});

/**
* Delete
*/
$app->delete('/m_siswa/delete/{id}', function ($request, $response) {
  $db = $this->db;
  $delete = $db->delete('m_siswa', array('id' => $request->getAttribute('id')));

  if ($delete) {
    return successResponse($response, ['data berhasil dihapus']);
  }else{
    return unprocessResponse($response, ['data gagal dihapus']);
  }

  return unprocessResponse($response, ['Kategori Terpakai, Tidak bisa di hapus']);
});
