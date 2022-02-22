<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class TicketModel extends Model
{
    protected $DBGroup              = 'default';
    protected $table                = 'tickets';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = true;
    protected $protectFields        = true;
    protected $allowedFields        = ['order_id', 'title', 'message', 'estimated_solve', 'solved_at', 'confirm', 'technic_id', 'salesman_id'];

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

    public function listAllTickets(array $search = null)
    {

        $this->select('tickets.id, tickets.title, tickets.message, orders.id as order_id');
        $this->join('orders', 'tickets.order_id = orders.id', 'left');
        $this->join('customers', 'orders.customer_id = customers.id', 'left');
        $this->groupBy(['order_id']);

        if (isset($search['title']) && $search['title'] != '')
            $this->like('tickets.title', $search['title'], 'both');

        if (isset($search['estimated_solve']) && $search['estimated_solve'] != '') {
            $this->where('tickets.estimated_solve >=', $search['estimated_solve'] . ' 00:00:00"');
            $this->where('tickets.estimated_solve <=', $search['estimated_solve'] . ' 23:59:59"');
        } else {
            $today = new Time('today');
            $this->where('tickets.estimated_solve >= ', $today->toDateString() . ' 00:00:00"');
            $this->where('tickets.estimated_solve <= ', $today->toDateString() . ' 23:59:59"');
        }

        if (isset($search['order_id']) && $search['order_id'] != '')
            $this->where('tickets.order_id', $search['order_id']);

        if (!isAdmin()) {
            if (session()->get('user_type') == 'technic')
                $this->where('tickets.technic_id', session('id'));
            if (session()->get('user_type') == 'salesman')
                $this->where('tickets.salesman_id', session('id'));
        }

        return $this->findAll();
    }

    public function getSingleTicketByOrderID($id = null)
    {
        if ($id == null || $id == '') {
            return [
                'error' => 'İlgili SSH için sipariş bulunamadı, lütfen düzenleyin.',
            ];
        }
        $this->select('tickets.id, customers.name, tickets.title, tickets.message, orders.slug, customers.phone, customers.email, customers.address, orders.id as order_id, orders.total_price, orders.created_at as order_created_at, orders.completed_at as order_completed_at, users.full_name, tickets.estimated_solve, tickets.created_at, tickets.updated_at, tickets.confirm, tickets.solved_at');
        $this->join('orders', 'tickets.order_id = orders.id', 'left');
        $this->join('customers', 'orders.customer_id = customers.id', 'left');
        $this->join('users', 'users.id = orders.salesman_id', 'left');
        $this->where('orders.id', $id);

        if (!isAdmin()) {
            if (session()->get('user_type') == 'technic')
                $this->where('tickets.technic_id', session('id'));
            if (session()->get('user_type') == 'salesman')
                $this->where('tickets.salesman_id', session('id'));
        }

        return $this->findAll();
    }

    public function deleteTicketWithUpdates(array $data)
    {
        foreach ($data as $k => $v) {
            $order_id = $this->select('order_id')->where('id', $v['id'])->first();
            $result = $this->where('order_id', $order_id['order_id'])->delete();
        }

        return $result;
    }
}
