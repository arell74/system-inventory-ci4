<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\StockMovementModel;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{

    protected $productModel;
    protected $categoryModel;
    protected $stockMovementModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->stockMovementModel = new StockMovementModel();
    }
    
    public function index()
    {
        $this->setPageData('Dashboard', 'Ringkasan sistem inventory');

        // Get statistics
        $stats = [
            'total_products'     => $this->productModel->where('is_active', true)->countAllResults(),
            'total_categories'   => $this->categoryModel->where('is_active', true)->countAllResults(),
            'inventory_value'    => $this->productModel->getTotalInventoryValue(),
            'low_stock_count'    => count($this->productModel->getLowStockProducts()),
            'low_stock_products' => $this->productModel->getLowStockProducts(5) // Top 5
        ];

        // Get monthly movement data for chart
        $monthlyMovements = $this->stockMovementModel->getMonthlyMovements();
        $chartData = $this->prepareChartData($monthlyMovements);

        $data = array_merge($stats, [
            'chart_data' => $chartData
        ]);

        //$this->render
        return $this->render('dashboard/index', $data);
    }

    private function prepareChartData($movements)
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $chartData = [
            'labels' => [],
            'stock_in' => [],
            'stock_out' => []
        ];

        // Initialize arrays with zeros
        for ($i = 1; $i <= 12; $i++) {
            $chartData['labels'][] = $months[$i-1];
            $chartData['stock_in'][] = 0;
            $chartData['stock_out'][] = 0;
        }

        // Fill with actual data
        foreach ($movements as $movement) {
            $monthIndex = $movement['month'] - 1;
            if ($movement['type'] == 'IN') {
                $chartData['stock_in'][$monthIndex] = intval($movement['total_quantity']);
            } else if ($movement['type'] == 'OUT') {
                $chartData['stock_out'][$monthIndex] = intval($movement['total_quantity']);
            }
        }

        return $chartData;
    }
}
