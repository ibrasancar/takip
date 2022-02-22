<?php

namespace App\Models;

use App\Database\Migrations\UserMeta;
use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table                = 'users';
    protected $primaryKey           = 'id';

    protected $useAutoIncrement     = true;

    protected $returnType           = 'array';
    protected $useSoftDeletes       = true;
    protected $protectFields        = true;
    protected $allowedFields        = ['level', 'user_type', 'full_name', 'email', 'password', 'phone', 'address', 'created_at'];

    // Dates
    protected $useTimestamps        = true;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [
        'email' => [
            'is_unique' => 'Bu e-posta adresi ile kayıt bulunmakta.',
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = ['beforeInsert', 'clearEmptyString'];
    protected $afterInsert          = [];
    protected $beforeUpdate         = ['beforeInsert', 'clearEmptyString', 'checkPasswordForUpdate'];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = ['getUserMeta'];
    protected $beforeDelete         = ['deleteAllMeta'];
    protected $afterDelete          = [];

    public function checkPasswordForUpdate(array $data)
    {
        if ($data['data']['password'] == null || $data['data']['password'] == '') 
            unset($data['data']['password']);
        return $data;
    }

    public function clearEmptyString(array $data)
    {
        if (isset($data['data'])) {
            $data['data'] = array_map(function ($item) {
                return $item != '' ? $item : null;
            }, $data['data']);
        } else {
            $data = array_map(function ($item) {
                return $item != '' ? $item : null;
            }, $data);
        }
        return $data;
    }

    protected function beforeInsert(array $data)
    {
        $data = $this->passwordHash($data);
        return $data;
    }

    public function passwordHash(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    public function getUserMeta(array $data)
    {
        // FIXME (1 OR ARRAY CHECK THEN RETURN)
        // if ( isset($data['data']) ) {
        //     $data['data'] = array_map(function($item) {
        //         $usermeta_model = new UserMetaModel();
        //         $item['user_meta'] = $usermeta_model->where('user_id', $item['id'])->find();
        //         return $item;
        //     }, $data['data']);            
        // }

        return $data;
    }

    public function getUsersByUserType(string $userType)
    {
        return $this->where('user_type', $userType);
    }

    public function addMeta(array $data)
    {
        $model = new UserMetaModel();
        $model->where('user_id', $data['user_id'])->where('meta_title', $data['meta_title']);
        $check = $model->countAllResults();

        if ($check > 0) {
            // FIXME update current meta
            $meta_data = $model
                ->where('user_id', $data['user_id'])
                ->where('meta_title', $data['meta_title'])
                ->first();
            $model->update($meta_data['id'], $data);
        } else {
            $model->save($data);
        }
    }

    public function getSingleMeta(int $user_id, string $meta_title)
    {
        $model = new UserMetaModel();
        return $model->where('user_id', $user_id)->where('meta_title', $meta_title)->first()['meta_value'] ?? null;
    }

    public function deleteAllMeta(array $data)
    {
        $model = new UserMetaModel();
        $model->where('user_id', $data['id'])->delete();
    }
}
