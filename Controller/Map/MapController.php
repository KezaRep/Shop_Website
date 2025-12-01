<?php
require_once __DIR__ . '/../../Model/Map/MapModel.php';

class MapController {
    public function indexAction() {
        $model = new MapModel();
        $stores = $model->getAllStores();

        $json_stores = json_encode($stores);
        
        require_once __DIR__ . '/../../View/Map/Map.php';
    }
}
?>