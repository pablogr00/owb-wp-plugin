<?php

class WarehouseManager
{
    private $filePath;
    private $warehouses = [];

    // Constructor: Carga los almacenes desde el archivo JSON
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        $this->loadWarehouses();
    }

    // Cargar los almacenes desde el archivo JSON
    private function loadWarehouses()
    {
        if (file_exists($this->filePath)) {
            $json_data = file_get_contents($this->filePath);
            $this->warehouses = json_decode($json_data, true);
        } else {
            $this->warehouses = [];
        }
    }

    // Función para obtener el nombre del almacén basado en lot_stock_id[0]
    public function getWarehouseByLotStockId($lot_stock_id)
    {
        foreach ($this->warehouses['warehouses'] as $warehouse) {
            if ($warehouse['lot_stock_id'][0] == $lot_stock_id) {
                return $warehouse['name'];
            }
        }
        return null;
    }

    // Función para buscar almacenes por coincidencias en el nombre y devolver su nombre y lot_stock_id[0]
    public function searchWarehousesByName($searchTerm)
    {
        $matchedWarehouses = [];

        foreach ($this->warehouses as $warehouse) {
            if (stripos($warehouse['name'], $searchTerm) !== false) {
                $matchedWarehouses[] = [
                    'name' => $warehouse['name'],
                    'lot_stock_id' => $warehouse['lot_stock_id'][0]
                ];
            }
        }

        return $matchedWarehouses;
    }
}
