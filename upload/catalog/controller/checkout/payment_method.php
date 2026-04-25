<?php
namespace Opencart\Catalog\Controller\Checkout;

class PaymentMethod extends \Opencart\System\Engine\Controller {
    
    // --- TAMBAHAN: FUNGSI UNTUK MEMBACA FILE .env ---
    private function loadEnv(): void {
    $path = dirname(rtrim(DIR_OPENCART, '/')) . '/.env';
    
    error_log('[Midtrans] Mencari .env di: ' . $path);
    error_log('[Midtrans] File exists: ' . (file_exists($path) ? 'YA' : 'TIDAK'));

    if (!file_exists($path)) {
        error_log('[Midtrans] .env tidak ditemukan!');
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name  = trim($name);
            $value = trim($value);
            $_ENV[$name] = $value;
            putenv("{$name}={$value}");
        }
    }
}

    public function index(): string {
        $this->load->language('checkout/payment_method');
        if (isset($this->session->data['payment_method'])) {
            $data['payment_method'] = $this->session->data['payment_method']['name'];
            $data['code'] = $this->session->data['payment_method']['code'];
        } else {
            $data['payment_method'] = '';
            $data['code'] = '';
        }
        if (isset($this->session->data['comment'])) {
            $data['comment'] = $this->session->data['comment'];
        } else {
            $data['comment'] = '';
        }
        if (isset($this->session->data['agree'])) {
            $data['agree'] = $this->session->data['agree'];
        } else {
            $data['agree'] = '';
        }
        $this->load->model('catalog/information');
        $information_info = $this->model_catalog_information->getInformation((int)$this->config->get('config_checkout_id'));
        if ($information_info) {
            $data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information.info', 'language=' . $this->config->get('config_language') . '&information_id=' . $this->config->get('config_checkout_id')), $information_info['title']);
        } else {
            $data['text_agree'] = '';
        }
        $data['language'] = $this->config->get('config_language');
        return $this->load->view('checkout/payment_method', $data);
    }

    public function getMethods(): void {
        $this->load->language('checkout/payment_method');
        $json = [];
        if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) || !$this->cart->hasMinimum()) {
            $json['redirect'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'), true);
        }
        if (!$json) {
            if (!isset($this->session->data['customer'])) {
                $json['error'] = $this->language->get('error_customer');
            }
            if ($this->config->get('config_checkout_payment_address') && !isset($this->session->data['payment_address'])) {
                $json['error'] = $this->language->get('error_payment_address');
            }
            if ($this->cart->hasShipping()) {
                if (!isset($this->session->data['shipping_address']['address_id'])) {
                    $json['error'] = $this->language->get('error_shipping_address');
                }
                if (!isset($this->session->data['shipping_method'])) {
                    $json['error'] = $this->language->get('error_shipping_method');
                }
            }
        }
        if (!$json) {
            $payment_address = [];
            if ($this->config->get('config_checkout_payment_address') && isset($this->session->data['payment_address'])) {
                $payment_address = $this->session->data['payment_address'];
            } elseif ($this->config->get('config_checkout_shipping_address') && isset($this->session->data['shipping_address']['address_id'])) {
                $payment_address = $this->session->data['shipping_address'];
            }
            $this->load->model('checkout/payment_method');
            $payment_methods = $this->model_checkout_payment_method->getMethods($payment_address);
            if ($payment_methods) {
                $json['payment_methods'] = $this->session->data['payment_methods'] = $payment_methods;
            } else {
                $json['error'] = sprintf($this->language->get('error_no_payment'), $this->url->link('information/contact', 'language=' . $this->config->get('config_language')));
            }
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function save(): void {
        $this->load->language('checkout/payment_method');
        $json = [];
        if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) || !$this->cart->hasMinimum()) {
            $json['redirect'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'), true);
        }
        if (!$json) {
            if ($this->config->get('config_checkout_payment_address') && !isset($this->session->data['payment_address'])) {
                $json['error'] = $this->language->get('error_payment_address');
            }
            if ($this->cart->hasShipping()) {
                if (!isset($this->session->data['shipping_address']['address_id'])) {
                    $json['error'] = $this->language->get('error_shipping_address');
                }
                if (!isset($this->session->data['shipping_method'])) {
                    $json['error'] = $this->language->get('error_shipping_method');
                }
            }
            if (isset($this->request->post['payment_method']) && isset($this->session->data['payment_methods'])) {
                $payment = explode('.', $this->request->post['payment_method']);
                if (!isset($payment[0]) || !isset($payment[1]) || !isset($this->session->data['payment_methods'][$payment[0]]['option'][$payment[1]])) {
                    $json['error'] = $this->language->get('error_payment_method');
                }
            } else {
                $json['error'] = $this->language->get('error_payment_method');
            }
        }
        if (!$json) {
            $this->session->data['payment_method'] = $this->session->data['payment_methods'][$payment[0]]['option'][$payment[1]];
            $json['success'] = $this->language->get('text_success');
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getSnapToken(): void {
        $this->loadEnv();
        $json = [];
        
        

        if (!$this->cart->hasProducts()) {
            $json['error'] = 'Cart kosong.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        if (!isset($this->session->data['customer'])) {
            $json['error'] = 'Customer belum login.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $customer = $this->session->data['customer'];
        $this->load->model('setting/extension');
        $totals = [];
        $taxes = $this->cart->getTaxes();
        $total = 0;
        $results = $this->model_setting_extension->getExtensionsByType('total');
        $sort_order = [];
        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
        }
        array_multisort($sort_order, SORT_ASC, $results);
        foreach ($results as $result) {
            if ($this->config->get('total_' . $result['code'] . '_status')) {
                $this->load->model('extension/' . $result['extension'] . '/total/' . $result['code']);
                $model = 'model_extension_' . $result['extension'] . '_total_' . $result['code'];
                $this->$model->getTotal($totals, $taxes, $total);
            }
        }

        // FIX 1: Fallback total dulu SEBELUM konversi dan round
        if ($total <= 0) {
            $total = $this->cart->getTotal();
        }

        $store_currency = $this->config->get('config_currency');
        if ($store_currency !== 'IDR') {
            $this->load->model('localisation/currency');
            $currency_info = $this->model_localisation_currency->getCurrencyByCode('IDR');
            $store_currency_info = $this->model_localisation_currency->getCurrencyByCode($store_currency);
            $rate_idr = ($currency_info && (float)$currency_info['value'] > 0) ? (float)$currency_info['value'] : 16000;
            $rate_store = ($store_currency_info && (float)$store_currency_info['value'] > 0) ? (float)$store_currency_info['value'] : 1;
            $total = ($total / $rate_store) * $rate_idr;
        }

        // FIX 2: $gross_amount selalu ter-set di sini, di luar blok if
        $gross_amount = (int) round($total);

        $temp_order_ref = 'SNAP-' . uniqid();
        $this->session->data['midtrans_order_ref'] = $temp_order_ref;

        $server_key = getenv('MIDTRANS_SERVER_KEY');
		if (!$server_key) {
            $json['error'] = 'SERVER KEY KOSONG 😭';
            $this->response->setOutput(json_encode($json));
            return;
        }   
        $isProduction = (getenv('MIDTRANS_IS_PRODUCTION') === 'true');
		error_log($server_key);

        $base_url = $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $params = [
            'transaction_details' => [
                'order_id'     => $temp_order_ref,
                'gross_amount' => $gross_amount,
            ],
            'customer_details' => [
                'first_name' => $customer['firstname'],
                'last_name'  => $customer['lastname'],
                'email'      => $customer['email'],
                'phone'      => $customer['telephone'] ?: '08000000000',
            ],
            'callbacks' => [
                'finish' => $this->url->link('checkout/success', 'language=' . $this->config->get('config_language'))
            ]
        ];

        // FIX 3: var_dump($server_key) DIHAPUS — menyebabkan output HTML bercampur JSON

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => $base_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($params),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Basic ' . base64_encode($server_key . ':')
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $response  = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        $result = json_decode($response, true);
        if ($http_code == 201 && isset($result['token'])) {
            $json['snap_token'] = $result['token'];
        } else {
            $error_msg = $result['error_messages'][0] ?? $result['message'] ?? 'Unknown error';
            $json['error'] = 'Gagal mendapatkan token Midtrans: ' . $error_msg;
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function saveMidtrans(): void {
        $json = [];
        $this->session->data['payment_method'] = [
            'name' => 'Midtrans',
            'code' => 'midtrans.midtrans'
        ];
        $this->session->data['payment_methods']['midtrans']['option']['midtrans'] = $this->session->data['payment_method'];
        if (isset($this->request->post['midtrans_result'])) {
            $this->session->data['midtrans_result'] = json_decode(html_entity_decode($this->request->post['midtrans_result']), true);
        }
        $json['success'] = true;
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}