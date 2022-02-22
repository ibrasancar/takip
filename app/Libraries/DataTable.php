<?php

namespace App\Libraries;

use Config\Services;

class DataTable
{
    public function process($modelClass, $columns, $where = [], $join = [], $group_by = [], $metas = [])
    {
        helper('formatter');

        $modelClass = '\\App\\Models\\' . $modelClass;
        $model = new $modelClass;

        foreach ($columns as $column) {
            $fields[] = $column['name'];
        }

        $select = implode(', ', $fields);

        $model->select($select);


        if (empty($where) === false) {
            $model->where($where);
        }

        if (empty($join) === false) {
            foreach ($join as $k => $v) {
                // BUG (en son eklenen id'nin çekilmesi gerekiyor)
                if (isset($v['meta_title'])) {
                    $model->join($v['table'], $v['firstColumn'] . " = " . $v['secondColumn'] . ' AND ' . $v['table'] . '.deleted_at IS NULL AND ' . $v['table'] . '.meta_title = "' . $v['meta_title'] . '"', 'LEFT');
                } else {
                    $model->join($v['table'], $v['firstColumn'] . " = " . $v['secondColumn'] . ' AND ' . $v['table'] . '.deleted_at IS NULL', 'LEFT');
                }
            }
        }


        if (empty($group_by) === false) {
            $model->groupBy($group_by['group_by']);
        }

        $request = Services::request();
        $get = $request->getGet();
        $getColumns = $get['columns'];

        foreach ($get['order'] as $order) {
            if ($getColumns[$order['column']]['orderable'] === 'true') {
                $model->orderBy($columns[$order['column']]['name'], strtoupper($order['dir']));
            }
        }


        $recordsTotal = $model->countAllResults(false);
        $match = $get['search']['value'];

        if (empty($match) === false) {
            $count = 0;
            foreach ($getColumns as $getColumn) {
                if ($getColumn['searchable'] === 'true') {
                    $count += 1;
                    $field = $columns[$getColumn['data']]['name'];

                    $field = explode(' as ', $field);
                    $field = $field[0];

                    if ($count === 1) {
                        $model->like($field, $match);
                    } else {
                        $model->orLike($field, $match);
                    }
                }
            }
        } else {
        }

        $recordsFiltered = $model->countAllResults(false);

        $model->limit($get['length'], $get['start']);

        $rows = $model->find();

        // check row for meta
        $rows = array_map(function ($item) {
            if (empty($metas) === true) {
                unset($item['user_meta']);
            } else {
                // FIXME GET USER META 
                // $item['meta'] = array_map(function($meta_item) {
                //     $item['meta'] = 
                // }, $item['user_meta']); 
                unset($item['user_meta']);
            }
            return $item;
        }, $rows);

        $data = [];

        foreach ($rows as $row) {
            $i = 0;
            $d = [];

            foreach ($row as $value) {

                $column = $columns[$i];

                if (array_key_exists('formatter', $column) === true) {
                    $route = array_key_exists('route', $column) ? $column['route'] : null;
                    $value = call_user_func($column['formatter'], $value, $row, $route);
                }

                $d[] = $value;
                $i += 1;
            }

            $data[] = $d;
        }

        $response = [
            'draw' => intval($get['draw']),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];


        return $response;
    }

    //--------------------------------------------------------------------
}
