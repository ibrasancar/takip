<?php

namespace App\Models;

use CodeIgniter\Model;

class ManufacturersModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'manufacturers';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = true;
    protected $protectFields        = true;
    protected $allowedFields        = ['name', 'contact_name', 'email', 'phone', 'address', 'created_at'];

    // Dates
    protected $useTimestamps        = true;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = ['clearEmptyString'];
    protected $afterInsert          = [];
    protected $beforeUpdate         = ['clearEmptyString'];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

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
}
