<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class OrderProductsModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'order_products';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = true;
    protected $protectFields        = true;
    protected $allowedFields        = ['order_id', 'quantity', 'product_id', 'price', 'estimated_delivery', 'shipping_date', 'deliver_confirm', 'status', 'extras'];

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

    public function beautifyPrice(array $data)
    {
		helper(['number']);
        
        if ( isset($data['price']) ) {
            $data['price'] = number_to_currency($data['price'], 'TRY', 'tr', 2);
        } else {
            $data = array_map(function($item) {
                $item['price'] = number_to_currency($item['price'], 'TRY', 'tr', 2);
                return $item;
            }, $data);
        }
        return $data;
    }

    public function beautifyDate(array $data)
    {
        if ( isset($data['estimated_delivery']) ) {
            $data['estimated_delivery'] = Time::parse($data['estimated_delivery'])->toLocalizedString('d/MM/YYYY');
        } else {
            $data = array_map(function($item) {
                $item['estimated_delivery'] = Time::parse($item['estimated_delivery'])->toLocalizedString('d/MM/YYYY');
                return $item;
            }, $data);
        }
        return $data;
    }

    public function beautifyStatus(array $data, array $orderStatuses)
    {
        if ( isset($data['status']) ) {
            $data['status'] = $orderStatuses[$data['status']]['list_style'];
        } else {
            $data = array_map(function($item) use ($orderStatuses) {
                $item['status'] = $orderStatuses[$item['status']]['list_style'];
                return $item;
            }, $data);
        }
        return $data;
    }
}
