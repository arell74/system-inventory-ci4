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
        $this->setPageData('Dashboard', 'Ringkasan sistem inventory dan statistik real-time');

        $stats = [
            'total_products' => $this->productModel->where('is_active', true)->countAllResults(),
            'total_categories' => $this->categoryModel->where('is_active', true)->countAllResults(),
            'inventory_value' => $this->productModel->getTotalInventoryValue(),
            'low_stock_count' => count($this->productModel->getLowStockProducts()),
            'out_of_stock_count' => $this->productModel->where('current_stock', 0)
                                                     ->where('is_active', true)
                                                     ->countAllResults()
        ];

        // Get recent activities
        $recentMovements = $this->stockMovementModel->getMovementsWithProduct(10);
        $lowStockProducts = $this->productModel->getLowStockProducts(8);
        $topProducts = $this->getTopProductsByValue(5);

        // Get chart data
        $chartData = [
            'monthly_movements' => $this->getMonthlyMovementsChart(),
            'category_distribution' => $this->getCategoryDistributionChart(),
            'stock_status_pie' => $this->getStockStatusChart()
        ];

        // Get quick stats for cards
        $quickStats = [
            'today_movements' => $this->stockMovementModel->where('DATE(created_at)', date('Y-m-d'))
                                                          ->countAllResults(),
            'this_week_in' => $this->stockMovementModel->where('type', 'IN')
                                                       ->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
                                                       ->selectSum('quantity', 'total')
                                                       ->first()['total'] ?? 0,
            'this_week_out' => $this->stockMovementModel->where('type', 'OUT')
                                                        ->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
                                                        ->selectSum('quantity', 'total')
                                                        ->first()['total'] ?? 0
        ];

        $data = array_merge($stats, [
            'recent_movements' => $recentMovements,
            'low_stock_products' => $lowStockProducts,
            'top_products' => $topProducts,
            'chart_data' => $chartData,
            'quick_stats' => $quickStats
        ]);

        return $this->render('dashboard/index', $data);
    }

    private function getTopProductsByValue($limit = 5)
    {
        return $this->productModel->select('
                products.*, 
                categories.name as category_name,
                (products.current_stock * products.price) as total_value
            ')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.is_active', true)
            ->where('products.current_stock >', 0)
            ->orderBy('total_value', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    private function getMonthlyMovementsChart()
    {
        $movements = $this->stockMovementModel->getMonthlyMovements();
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        $chartData = [
            'labels' => [],
            'stock_in' => [],
            'stock_out' => []
        ];

        // Get last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $monthNum = date('n', strtotime("-$i months"));
            $monthName = $months[$monthNum - 1];
            $chartData['labels'][] = $monthName;
            $chartData['stock_in'][] = 0;
            $chartData['stock_out'][] = 0;
        }

        // Fill with actual data
        foreach ($movements as $movement) {
            $monthIndex = array_search($months[$movement['month'] - 1], $chartData['labels']);
            if ($monthIndex !== false) {
                if ($movement['type'] == 'IN') {
                    $chartData['stock_in'][$monthIndex] = intval($movement['total_quantity']);
                } else if ($movement['type'] == 'OUT') {
                    $chartData['stock_out'][$monthIndex] = intval($movement['total_quantity']);
                }
            }
        }

        return $chartData;
    }

    private function getCategoryDistributionChart()
    {
        $categories = $this->categoryModel->select('
                categories.name,
                COUNT(products.id) as product_count,
                SUM(products.current_stock * products.price) as total_value
            ')
            ->join('products', 'products.category_id = categories.id', 'left')
            ->where('categories.is_active', true)
            ->groupBy('categories.id')
            ->orderBy('product_count', 'DESC')
            ->findAll();

        return [
            'labels' => array_column($categories, 'name'),
            'data' => array_column($categories, 'product_count'),
            'values' => array_column($categories, 'total_value')
        ];
    }

    private function getStockStatusChart()
    {
        $outOfStock = $this->productModel->where('current_stock', 0)
                                         ->where('is_active', true)
                                         ->countAllResults();
        
        $lowStock = $this->productModel->where('current_stock <=', 'min_stock', false)
                                       ->where('current_stock >', 0)
                                       ->where('is_active', true)
                                       ->countAllResults();
        
        $normalStock = $this->productModel->where('current_stock >', 'min_stock', false)
                                          ->where('is_active', true)
                                          ->countAllResults();

        return [
            'labels' => ['Habis', 'Stok Rendah', 'Normal'],
            'data' => [$outOfStock, $lowStock, $normalStock],
            'colors' => ['#dc3545', '#ffc107', '#198754']
        ];
    }
}
