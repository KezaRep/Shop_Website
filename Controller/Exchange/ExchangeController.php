<?php
include('Model/Exchange/ExchangeModel.php');
class ExchangeController {
    private $exchangeModel;

    public function __construct() {
        $this->exchangeModel = new ExchangeModel();
    }

    public function DetailAction    ($exchangeId) {
        $exchange = $this->exchangeModel->getExchangeById($exchangeId);
        if ($exchange) {
            include 'View/Exchange/Detail.php';
        } else {
            echo "Exchange not found.";
        }
    }
}