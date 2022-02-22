<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OrdersModel;
use App\Models\TicketModel;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\Codeigniter4Adapter;

class Tickets extends BaseController
{
    protected $ticketValidationRules = [
        'title' => [
            'rules' => 'required',
            'label' => 'Başlık',
        ],
        'order_id' => [
            'rules' => 'required',
            'label' => 'Sipariş',
        ],
        'technic_id' => [
            'rules' => 'required',
            'label' => 'Teknik destek personeli',
        ],
        'estimated_solve' => [
            'rules' => 'required',
            'label' => 'Tahmini çözüm tarihi'
        ],
    ];

    protected $ticketFormElements = [
        'title' => [
            'type' => 'text',
            'column' => 'col-md-12 mb-2',
            'label' => 'Başlık *',
            'placeholder' => 'SSH başlığı',
        ],
        'message' => [
            'type' => 'textarea',
            'column' => 'col-md-12 mb-2',
            'label' => 'Mesaj',
            'rows' => 2,
            'placeholder' => 'SSH\'ın neden oluşturulduğunu açıklayınız'
        ],
        'estimated_solve' => [
            'type' => 'date',
            'column' => 'col-md-4 mb-2',
            'label' => 'Tahmini Çözüm Tarihi *',
            'rows' => 2,
            'placeholder' => 'Planlanan çözüm tarihi',
        ],
        'solved_at' => [
            'type' => 'date',
            'column' => 'col-md-4 mb-2',
            'label' => 'Çözüm Tarihi',
            'rows' => 2,
            'placeholder' => 'Sorunun çözüldüğü tarih',
        ],
        'confirm' => [
            'type' => 'date',
            'column' => 'col-md-4 mb-4',
            'label' => 'Müşheri Onay Tarihi',
            'rows' => 2,
            'placeholder' => 'Müşteri onay tarihi',
        ],
    ];

    protected $pageButtons = [
        'add_ticket' => [
            'route' => 'add_ticket',
            'id' => 'add',
            'class' => 'btn btn-dark',
            'text' => 'SSH Ekle',
            'icon' => [
                'class' => 'material-icons-outlined',
                'name' => 'add',
            ]
        ],
        'tickets' => [
            'route' => 'tickets',
            'class' => 'btn btn-info',
            'text' => 'Geri Dön',
            'icon' => [
                'class' => 'material-icons-outlined',
                'name' => 'arrow_back',
            ]
        ],
    ];

    public function index()
    {
        helper('form');
        $data = [
            'page_title' => 'SSH',
            'page_desc' => 'SHH\'ları burada listeleyebilir veya düzeneleyebilirsiniz.',
            'active' => ['tickets', 'tickets'],
            'page_buttons' => [
                'search' => [
                    'href' => '#',
                    'id' => 'open-search',
                    'class' => 'btn btn-info',
                    'text' => 'Arama Yap',
                    'icon' => [
                        'class' => 'material-icons-outlined',
                        'name' => 'search',
                    ]
                ],
                $this->pageButtons['add_ticket'],
            ]
        ];
        $search = [];

        $data['success'] = session()->getFlashData('success');
        $data['error'] = session()->getFlashData('error');

        $ticket_model = new TicketModel();

        if ($this->request->getVar('search')) {
            $search = [
                'title' => $this->request->getVar('title'),
                'estimated_solve' => $this->request->getVar('estimated_solve'),
                'order_id' => $this->request->getVar('order_id'),
            ];
        }

        $data['tickets'] = $ticket_model->listAllTickets($search);

        return view('pages/tickets/list', $data);
    }

    public function add()
    {
        helper(['form', 'formElement']);

        $data = [
            'page_title' => 'SSH ekle',
            'page_desc' => 'SSH ekleme formu.',
            'active' => ['tickets', 'add_ticket'],
            'page_buttons' => [$this->pageButtons['tickets']]
        ];

        $ticket_model = new TicketModel();

        if ($this->request->getMethod() == 'post') {
            $validationRules = $this->ticketValidationRules;

            if (session()->get('user_type') == 'technic')
                unset($validationRules['technic_id']);

            $input = $this->validate($validationRules);
            if (!$input) {
                $data['validation'] = $this->validator;
            } else {
                $insert_data = [
                    'title' => $this->request->getVar('title', FILTER_SANITIZE_STRING),
                    'order_id' => $this->request->getVar('order_id', FILTER_SANITIZE_NUMBER_INT),
                    'message'  => $this->request->getVar('message', FILTER_SANITIZE_STRING),
                    'estimated_solve'  => $this->request->getVar('estimated_solve', FILTER_SANITIZE_STRING) != '' ? $this->request->getVar('estimated_solve', FILTER_SANITIZE_STRING) : null,
                    'solved_at' => $this->request->getVar('solved_at', FILTER_SANITIZE_STRING) != '' ? $this->request->getVar('solved_at', FILTER_SANITIZE_STRING) : null,
                    'confirm' => $this->request->getVar('confirm', FILTER_SANITIZE_STRING) != '' ? $this->request->getVar('confirm', FILTER_SANITIZE_STRING) : null,
                ];

                if (!isAdmin() && session()->get('user_type') == 'technic') {
                    $insert_data['technic_id'] = session()->get('id');
                }
                if (isAdmin() || session()->get('user_type') == 'salesman') {
                    $insert_data['salesman_id'] = session()->get('id');
                    $insert_data['technic_id']  = $this->request->getVar('technic_id');
                }

                $ticket_model->save($insert_data);

                $session = session();
                $session->setFlashData('success', 'SSH başarıyla eklendi');
                return redirect()->route('tickets');
            }
        }

        $data['form_elements'] = makeFormElement($this->ticketFormElements, $data['validation'] ?? null, null);

        return view('pages/tickets/add', $data);
    }

    public function edit(int $ticket_id)
    {
        helper(['form', 'formElement']);

        $data = [
            'page_title' => 'SSH düzenle',
            'page_desc' => 'SSH düzenleme formu.',
            'active' => ['tickets', 'add_ticket'],
            'page_buttons' => [$this->pageButtons['tickets']]
        ];

        $ticket_model = new TicketModel();
        $data['ticket'] = $ticket_model->where('id', $ticket_id);

        if (!isAdmin()) {
            if (session()->get('user_type') == 'salesman')
                $ticket_model->where('salesman_id', session()->get('id'));
            if (session()->get('user_type') == 'technic')
                $ticket_model->where('technic_id', session()->get('id'));
        }

        $data['ticket'] = $ticket_model->first();

        if (!$data['ticket']) {
            session()->setFlashData('error', 'SSH bulunamadı!');
            return redirect()->route('tickets');
        }

        if ($this->request->getMethod() == 'post') {
            $validationRules = $this->ticketValidationRules;

            if (session()->get('user_type') == 'technic')
                unset($validationRules['technic_id']);

            $input = $this->validate($validationRules);
            if (!$input) {
                $data['validation'] = $this->validator;
            } else {
                $insert_data = [
                    'title' => $this->request->getVar('title', FILTER_SANITIZE_STRING),
                    'order_id' => $this->request->getVar('order_id', FILTER_SANITIZE_NUMBER_INT),
                    'message'  => $this->request->getVar('message', FILTER_SANITIZE_STRING),
                    'estimated_solve'  => $this->request->getVar('estimated_solve', FILTER_SANITIZE_STRING) != '' ? $this->request->getVar('estimated_solve', FILTER_SANITIZE_STRING) : null,
                    'solved_at' => $this->request->getVar('solved_at', FILTER_SANITIZE_STRING) != '' ? $this->request->getVar('solved_at', FILTER_SANITIZE_STRING) : null,
                    'confirm' => $this->request->getVar('confirm', FILTER_SANITIZE_STRING) != '' ? $this->request->getVar('confirm', FILTER_SANITIZE_STRING) : null,
                ];

                if (!isAdmin() && session()->get('user_type') == 'technic') {
                    $insert_data['technic_id'] = session()->get('id');
                }
                if (isAdmin() || session()->get('user_type') == 'salesman') {
                    $insert_data['salesman_id'] = session()->get('id');
                    $insert_data['technic_id']  = $this->request->getVar('technic_id');
                }

                $ticket_model->update($ticket_id, $insert_data);

                $session = session();
                $session->setFlashData('success', 'SSH başarıyla düzenlendi!');

                return redirect()->route('tickets');
            }
        }

        $data['form_elements'] = makeFormElement($this->ticketFormElements, $data['validation'] ?? null, $data['ticket']);

        return view('pages/tickets/add', $data);
    }

    public function getTickets()
    {
        helper(['dtformatter']);

        $dt = new Datatables(new Codeigniter4Adapter);
        $dt->query('SELECT o.slug, t.title, t.estimated_solve, t.solved_at, t.confirm, t.id FROM tickets t LEFT JOIN orders o ON o.id = t.order_id AND o.deleted_at IS NULL WHERE t.deleted_at IS NULL');

        $dt->edit('slug', function ($data) {
            return '<span class="btn btn-light copyme">#' . $data['slug'] . '</span>';
        });

        $dt->edit('estimated_solve', function ($data) {
            return dtBeautifyDate($data['estimated_solve'], '<span class="badge badge-warning">Eklenmemiş</span>');
        });
        $dt->edit('solved_at', function ($data) {
            return dtBeautifyDate($data['solved_at'], '<span class="badge badge-warning">Çözülmemiş</span>');
        });
        $dt->edit('confirm', function ($data) {
            return dtBeautifyDate($data['confirm'], '<span class="badge badge-info">Onaylanmamış</span>');
        });

        $dt->edit('id', function ($data) {
            return '<div class="btn-group"><a href="' . site_url(route_to('edit_ticket', $data['id'])) . '" class="btn btn-outline-primary  d-flex"><i class="material-icons">edit</i> Düzenle</a>
            <a href="' . site_url(route_to('delete_ticket', $data['id'])) . '" class="btn btn-outline-danger delete d-flex"><i class="material-icons">delete_outline</i> Sil</a></div>';
        });

        echo $dt->generate();
    }

    public function delete($ticket_id)
    {
        $session = session();
        $ticket_model = new TicketModel();

        $tickets = explode(',', $ticket_id);

        $result = $ticket_model->whereIn('id', $tickets)->findAll();

        if (!empty($result)) {
            $ticket_model->deleteTicketWithUpdates($result);
            $session->setFlashData('success', 'SSH başarıyla silindi.');
        } else {
            $session->setFlashData('error', 'SSH bulunamadı.');
        }

        return redirect()->route('tickets');
    }

    public function getTicket()
    {
        helper('dtformatter');

        $ticket_model = new TicketModel();
        $data['tickets'] = $ticket_model->getSingleTicketByOrderID($this->request->getVar('id'));

        if (!isset($data['tickets']['error'])) {
            $data['tickets'] = array_map(function ($item) {
                $item['total_price'] = dtBeautifyPrice($item['total_price']);
                $item['order_created_at'] = dtBeautifyDate($item['order_created_at']);
                $item['created_at'] = dtBeautifyDate($item['created_at']);
                $item['updated_at'] = dtBeautifyDate($item['updated_at']);
                $item['order_completed_at'] = dtBeautifyDate($item['order_completed_at'], '<span class="badge badge-warning">Tamamlanmamış</span>');
                $item['solved_at'] = dtBeautifyDate($item['solved_at'], '<span class="badge badge-warning">Tamamlanmamış</span>');
                $item['confirm'] = dtBeautifyDate($item['confirm'], '<span class="badge badge-warning">Tamamlanmamış</span>');
                return $item;
            }, $data['tickets']);
        } else {
            $this->response->setStatusCode(400);
        }

        return $this->response->setJSON($data);
    }

    public function addUpdateToTicket()
    {
        helper('dtformatter');
        $ticket_model = new TicketModel();
        $message = $this->request->getVar('message');
        $order_id = $this->request->getVar('order_id');
        if (!$order_id || !$message) {
            $this->response->setStatusCode('400');
            $data = [
                'error' => 'Lütfen gerekli alanları doldurun!'
            ];
        } else {
            $ticket_model->insert([
                'order_id' => $order_id,
                'message' => $message,
                'salesman_id' => session()->get('id'),
            ]);
            $data['tickets'] = $ticket_model->getSingleTicketByOrderID($order_id);
            $data['tickets'] = array_map(function ($item) {
                $item['total_price'] = dtBeautifyPrice($item['total_price']);
                $item['order_created_at'] = dtBeautifyDate($item['order_created_at']);
                $item['created_at'] = dtBeautifyDate($item['created_at']);
                $item['updated_at'] = dtBeautifyDate($item['updated_at']);
                $item['order_completed_at'] = dtBeautifyDate($item['order_completed_at'], '<span class="badge badge-warning">Tamamlanmamış</span>');
                $item['solved_at'] = dtBeautifyDate($item['solved_at'], '<span class="badge badge-warning">Tamamlanmamış</span>');
                $item['confirm'] = dtBeautifyDate($item['confirm'], '<span class="badge badge-warning">Tamamlanmamış</span>');
                return $item;
            }, $data['tickets']);
        }

        $data['csrfName'] = csrf_token();
        $data['csrfHash'] = csrf_hash();
        return $this->response->setJSON($data);
    }

    public function deleteTicketUpdate()
    {
        $ticket_model = new TicketModel();
        $ticket_id = $this->request->getVar('ticket_id');
        if (!$ticket_id) {
            $this->response->setStatusCode('400');
            $data = [
                'error' => 'Lütfen gerekli alanları doldurun!'
            ];
        } else {
            $ticketUpdate = $ticket_model->find($ticket_id);
            if ($ticketUpdate) {
                $ticket_model->delete($ticket_id);
                $data['tickets'] = $ticket_model->getSingleTicketByOrderID($ticketUpdate['order_id']);
            }
        }

        return $this->response->setJSON($data);
    }
}
