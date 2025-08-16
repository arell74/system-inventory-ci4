<?php

namespace App\Controllers;

use App\Database\Migrations\StockMovements;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\StockMovementModel;

class ReportController extends BaseController
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

    /**
     * Stock Report - Current inventory status
     */
    public function stock()
    {
        $this->setPageData('Laporan Stok', 'Analisis kondisi stok inventory saat ini');

        // Get filters
        $categoryFilter = $this->request->getGet('category');
        $stockStatus = $this->request->getGet('stock_status');
        $sortBy = $this->request->getGet('sort_by') ?: 'name';
        $sortOrder = $this->request->getGet('sort_order') ?: 'ASC';

        // Build query
        $builder = $this->productModel->select("
                products.*, 
                categories.name as category_name,
                (products.current_stock * products.price) as stock_value,
                CASE 
                    WHEN products.current_stock = 0 THEN 'out_of_stock'
                    WHEN products.current_stock <= products.min_stock THEN 'low_stock'
                    ELSE 'normal'
                END as stock_status
            ")
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.is_active', true);

        // Apply filters
        if ($categoryFilter) {
            $builder->where('products.category_id', $categoryFilter);
        }

        if ($stockStatus) {
            switch ($stockStatus) {
                case 'out_of_stock':
                    $builder->where('products.current_stock', 0);
                    break;
                case 'low_stock':
                    $builder->where('products.current_stock <=', 'products.min_stock', false)
                            ->where('products.current_stock >', 0);
                    break;
                case 'normal':
                    $builder->where('products.current_stock >', 'products.min_stock', false);
                    break;
                case 'overstocked':
                    $builder->where('products.current_stock >', 'products.min_stock * 3', false);
                    break;
            }
        }

        // Apply sorting
        $validSorts = ['name', 'current_stock', 'stock_value', 'category_name'];
        if (in_array($sortBy, $validSorts)) {
            $builder->orderBy($sortBy, $sortOrder);
        }

        $products = $builder->findAll();

        // Calculate summary statistics
        $totalProducts = count($products);
        $totalValue = array_sum(array_column($products, 'stock_value'));
        $totalQuantity = array_sum(array_column($products, 'current_stock'));
        $outOfStock = count(array_filter($products, fn($p) => $p['current_stock'] == 0));
        $lowStock = count(array_filter($products, fn($p) => $p['current_stock'] > 0 && $p['current_stock'] <= $p['min_stock']));
        
        // Category breakdown
        $categoryBreakdown = [];
        foreach ($products as $product) {
            $catName = $product['category_name'];
            if (!isset($categoryBreakdown[$catName])) {
                $categoryBreakdown[$catName] = [
                    'products' => 0,
                    'total_stock' => 0,
                    'total_value' => 0
                ];
            }
            $categoryBreakdown[$catName]['products']++;
            $categoryBreakdown[$catName]['total_stock'] += $product['current_stock'];
            $categoryBreakdown[$catName]['total_value'] += $product['stock_value'];
        }

        $categories = $this->categoryModel->getActiveCategories();

        $data = [
            'products' => $products,
            'categories' => $categories,
            'summary' => [
                'total_products' => $totalProducts,
                'total_value' => $totalValue,
                'total_quantity' => $totalQuantity,
                'out_of_stock' => $outOfStock,
                'low_stock' => $lowStock,
                'normal_stock' => $totalProducts - $outOfStock - $lowStock
            ],
            'category_breakdown' => $categoryBreakdown,
            'filters' => [
                'category' => $categoryFilter,
                'stock_status' => $stockStatus,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder
            ]
        ];

        return $this->render('reports/stock', $data);
    }

    /**
     * Movement Report - Stock movement analysis
     */
    public function movements()
    {
        $this->setPageData('Laporan Pergerakan', 'Analisis pergerakan stok dalam periode tertentu');

        // Get filters
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01'); // First day of month
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-d');
        $categoryFilter = $this->request->getGet('category');
        $productFilter = $this->request->getGet('product');
        $movementType = $this->request->getGet('type');

        // Get movements data
        $builder = $this->stockMovementModel->select('
                stock_movements.*, 
                products.name as product_name,
                products.sku as product_sku,
                products.price as product_price,
                categories.name as category_name
            ')
            ->join('products', 'products.id = stock_movements.product_id')
            ->join('categories', 'categories.id = products.category_id')
            ->where('DATE(stock_movements.created_at) >=', $startDate)
            ->where('DATE(stock_movements.created_at) <=', $endDate);

        if ($categoryFilter) {
            $builder->where('categories.id', $categoryFilter);
        }

        if ($productFilter) {
            $builder->where('products.id', $productFilter);
        }

        if ($movementType) {
            $builder->where('stock_movements.type', $movementType);
        }

        $movements = $builder->orderBy('stock_movements.created_at', 'DESC')->findAll();

        // Calculate analytics
        $analytics = $this->calculateMovementAnalytics($movements, $startDate, $endDate);

        // Get top products by movement
        $topProducts = $this->getTopMovementProducts($movements);

        // Get daily movement trend
        $dailyTrend = $this->getDailyMovementTrend($movements, $startDate, $endDate);

        $categories = $this->categoryModel->getActiveCategories();
        $products = $this->productModel->getProductsWithCategory();

        $data = [
            'movements' => $movements,
            'analytics' => $analytics,
            'top_products' => $topProducts,
            'daily_trend' => $dailyTrend,
            'categories' => $categories,
            'products' => $products,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'category' => $categoryFilter,
                'product' => $productFilter,
                'type' => $movementType
            ]
        ];

        return $this->render('reports/movements', $data);
    }

    /**
     * Valuation Report - Inventory valuation analysis
     */
    public function valuation()
    {
        $this->setPageData('Valuasi Inventory', 'Analisis nilai inventory dan profitability');

        $categoryFilter = $this->request->getGet('category');
        $valuationMethod = $this->request->getGet('method') ?: 'current'; // current, cost, market

        // Get products with valuation data
        $builder = $this->productModel->select('
                products.*, 
                categories.name as category_name,
                (products.current_stock * products.price) as current_value,
                (products.current_stock * products.cost_price) as cost_value,
                (products.current_stock * products.price) - (products.current_stock * products.cost_price) as potential_profit
            ')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.is_active', true)
            ->where('products.current_stock >', 0);

        if ($categoryFilter) {
            $builder->where('products.category_id', $categoryFilter);
        }

        $products = $builder->orderBy('current_value', 'DESC')->findAll();

        // Calculate summary
        $totalCurrentValue = array_sum(array_column($products, 'current_value'));
        $totalCostValue = array_sum(array_column($products, 'cost_value'));
        $totalPotentialProfit = array_sum(array_column($products, 'potential_profit'));
        $averageMargin = $totalCurrentValue > 0 ? (($totalCurrentValue - $totalCostValue) / $totalCurrentValue) * 100 : 0;

        // Category valuation breakdown
        $categoryValuation = [];
        foreach ($products as $product) {
            $catName = $product['category_name'];
            if (!isset($categoryValuation[$catName])) {
                $categoryValuation[$catName] = [
                    'products' => 0,
                    'total_quantity' => 0,
                    'current_value' => 0,
                    'cost_value' => 0,
                    'potential_profit' => 0
                ];
            }
            $categoryValuation[$catName]['products']++;
            $categoryValuation[$catName]['total_quantity'] += $product['current_stock'];
            $categoryValuation[$catName]['current_value'] += $product['current_value'];
            $categoryValuation[$catName]['cost_value'] += $product['cost_value'];
            $categoryValuation[$catName]['potential_profit'] += $product['potential_profit'];
        }

        // Add margin percentage to each category
        foreach ($categoryValuation as $catName => &$catData) {
            $catData['margin_percentage'] = $catData['current_value'] > 0 ? 
                (($catData['current_value'] - $catData['cost_value']) / $catData['current_value']) * 100 : 0;
        }

        $categories = $this->categoryModel->getActiveCategories();

        $data = [
            'products' => $products,
            'categories' => $categories,
            'summary' => [
                'total_current_value' => $totalCurrentValue,
                'total_cost_value' => $totalCostValue,
                'total_potential_profit' => $totalPotentialProfit,
                'average_margin' => $averageMargin,
                'total_products' => count($products)
            ],
            'category_valuation' => $categoryValuation,
            'filters' => [
                'category' => $categoryFilter,
                'method' => $valuationMethod
            ]
        ];

        return $this->render('reports/valuation', $data);
    }

    /**
     * Analytics Dashboard - Advanced analytics
     */
    public function analytics()
    {
        $this->setPageData('Analytics Dashboard', 'Advanced analytics dan insights bisnis');

        $period = $this->request->getGet('period') ?: '30'; // days

        // Calculate date range
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime("-{$period} days"));

        // Get comprehensive analytics
        $analytics = [
            'inventory_turnover' => $this->calculateInventoryTurnover($period),
            'abc_analysis' => $this->calculateABCAnalysis(),
            'demand_forecast' => $this->calculateDemandForecast($period),
            'reorder_suggestions' => $this->getReorderSuggestions(),
            'performance_metrics' => $this->getPerformanceMetrics($period),
            'trends' => $this->getTrendAnalysis($period)
        ];

        $data = [
            'analytics' => $analytics,
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        return $this->render('reports/analytics', $data);
    }

    /**
     * Export Stock Report
     */
    public function exportStock($format = 'excel')
    {
        // Implementation for Excel/PDF export
        // This would use libraries like PhpSpreadsheet for Excel or TCPDF for PDF
        
        if ($format === 'excel') {
            return $this->exportStockExcel();
        } elseif ($format === 'pdf') {
            return $this->exportStockPDF();
        }
        
        return redirect()->back()->with('error', 'Format export tidak valid');
    }

    // Private helper methods

    private function calculateMovementAnalytics($movements, $startDate, $endDate)
    {
        $totalMovements = count($movements);
        $totalIn = 0;
        $totalOut = 0;
        $totalAdjustments = 0;
        $totalInQuantity = 0;
        $totalOutQuantity = 0;

        foreach ($movements as $movement) {
            switch ($movement['type']) {
                case 'IN':
                    $totalIn++;
                    $totalInQuantity += $movement['quantity'];
                    break;
                case 'OUT':
                    $totalOut++;
                    $totalOutQuantity += $movement['quantity'];
                    break;
                case 'ADJUSTMENT':
                    $totalAdjustments++;
                    break;
            }
        }

        $periodDays = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1;
        $avgMovementsPerDay = $totalMovements / $periodDays;

        return [
            'total_movements' => $totalMovements,
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'total_adjustments' => $totalAdjustments,
            'total_in_quantity' => $totalInQuantity,
            'total_out_quantity' => $totalOutQuantity,
            'net_movement' => $totalInQuantity - $totalOutQuantity,
            'avg_movements_per_day' => round($avgMovementsPerDay, 2),
            'period_days' => $periodDays
        ];
    }

    private function getTopMovementProducts($movements)
    {
        $productStats = [];
        
        foreach ($movements as $movement) {
            $productId = $movement['product_id'];
            $productName = $movement['product_name'];
            
            if (!isset($productStats[$productId])) {
                $productStats[$productId] = [
                    'product_name' => $productName,
                    'product_sku' => $movement['product_sku'],
                    'total_movements' => 0,
                    'total_in' => 0,
                    'total_out' => 0,
                    'net_movement' => 0
                ];
            }
            
            $productStats[$productId]['total_movements']++;
            
            if ($movement['type'] === 'IN') {
                $productStats[$productId]['total_in'] += $movement['quantity'];
                $productStats[$productId]['net_movement'] += $movement['quantity'];
            } elseif ($movement['type'] === 'OUT') {
                $productStats[$productId]['total_out'] += $movement['quantity'];
                $productStats[$productId]['net_movement'] -= $movement['quantity'];
            }
        }
        
        // Sort by total movements
        uasort($productStats, function($a, $b) {
            return $b['total_movements'] - $a['total_movements'];
        });
        
        return array_slice($productStats, 0, 10); // Top 10
    }

    private function getDailyMovementTrend($movements, $startDate, $endDate)
    {
        $dailyStats = [];
        $currentDate = $startDate;
        
        // Initialize all dates with zero values
        while ($currentDate <= $endDate) {
            $dailyStats[$currentDate] = [
                'date' => $currentDate,
                'in_quantity' => 0,
                'out_quantity' => 0,
                'movements_count' => 0
            ];
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }
        
        // Populate with actual data
        foreach ($movements as $movement) {
            $date = date('Y-m-d', strtotime($movement['created_at']));
            
            if (isset($dailyStats[$date])) {
                $dailyStats[$date]['movements_count']++;
                
                if ($movement['type'] === 'IN') {
                    $dailyStats[$date]['in_quantity'] += $movement['quantity'];
                } elseif ($movement['type'] === 'OUT') {
                    $dailyStats[$date]['out_quantity'] += $movement['quantity'];
                }
            }
        }
        
        return array_values($dailyStats);
    }

    private function calculateInventoryTurnover($period)
    {
        // Simplified inventory turnover calculation
        $movements = $this->stockMovementModel->where('type', 'OUT')
                                            ->where('created_at >=', date('Y-m-d', strtotime("-{$period} days")))
                                            ->findAll();
        
        $totalSold = array_sum(array_column($movements, 'quantity'));
        $avgInventory = $this->productModel->selectSum('current_stock')->first()['current_stock'] ?? 0;
        
        $turnoverRate = $avgInventory > 0 ? ($totalSold / $avgInventory) * (365 / $period) : 0;
        
        return [
            'turnover_rate' => round($turnoverRate, 2),
            'total_sold' => $totalSold,
            'avg_inventory' => $avgInventory,
            'period_days' => $period
        ];
    }

    private function calculateABCAnalysis()
    {
        // ABC Analysis based on stock value
        $products = $this->productModel->select('
                products.*, 
                (products.current_stock * products.price) as stock_value
            ')
            ->where('is_active', true)
            ->where('current_stock >', 0)
            ->orderBy('stock_value', 'DESC')
            ->findAll();

        $totalValue = array_sum(array_column($products, 'stock_value'));
        $runningValue = 0;
        $abc = ['A' => [], 'B' => [], 'C' => []];

        foreach ($products as $product) {
            $runningValue += $product['stock_value'];
            $percentage = ($runningValue / $totalValue) * 100;

            if ($percentage <= 80) {
                $abc['A'][] = $product;
            } elseif ($percentage <= 95) {
                $abc['B'][] = $product;
            } else {
                $abc['C'][] = $product;
            }
        }

        return [
            'categories' => $abc,
            'summary' => [
                'A_count' => count($abc['A']),
                'B_count' => count($abc['B']),
                'C_count' => count($abc['C']),
                'total_products' => count($products),
                'total_value' => $totalValue
            ]
        ];
    }

    private function calculateDemandForecast($period)
    {
        // Simple demand forecast based on historical out movements
        $movements = $this->stockMovementModel->select('
                product_id, 
                SUM(quantity) as total_out,
                COUNT(*) as movement_count
            ')
            ->where('type', 'OUT')
            ->where('created_at >=', date('Y-m-d', strtotime("-{$period} days")))
            ->groupBy('product_id')
            ->findAll();

        $forecasts = [];
        foreach ($movements as $movement) {
            $dailyDemand = $movement['total_out'] / $period;
            $weeklyForecast = $dailyDemand * 7;
            $monthlyForecast = $dailyDemand * 30;

            $forecasts[$movement['product_id']] = [
                'daily_demand' => round($dailyDemand, 2),
                'weekly_forecast' => round($weeklyForecast),
                'monthly_forecast' => round($monthlyForecast),
                'movement_count' => $movement['movement_count']
            ];
        }

        return $forecasts;
    }

    private function getReorderSuggestions()
    {
        // Get products that need reordering based on various criteria
        $products = $this->productModel->select('
                products.*, 
                categories.name as category_name
            ')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.is_active', true)
            ->where('products.current_stock <=', 'products.min_stock * 1.5', false)
            ->orderBy('(products.current_stock / products.min_stock)', 'ASC')
            ->findAll();

        $suggestions = [];
        foreach ($products as $product) {
            $stockRatio = $product['min_stock'] > 0 ? $product['current_stock'] / $product['min_stock'] : 0;
            $urgency = 'low';
            
            if ($product['current_stock'] == 0) {
                $urgency = 'critical';
            } elseif ($stockRatio <= 0.5) {
                $urgency = 'high';
            } elseif ($stockRatio <= 1.0) {
                $urgency = 'medium';
            }

            $suggestedOrder = max($product['min_stock'] * 2 - $product['current_stock'], $product['min_stock']);

            $suggestions[] = [
                'product' => $product,
                'urgency' => $urgency,
                'stock_ratio' => round($stockRatio, 2),
                'suggested_order_quantity' => $suggestedOrder,
                'days_until_stockout' => $this->calculateDaysUntilStockout($product['id'])
            ];
        }

        return $suggestions;
    }

    private function getPerformanceMetrics($period)
    {
        // Various performance metrics
        return [
            'stock_accuracy' => $this->calculateStockAccuracy(),
            'order_fulfillment_rate' => $this->calculateOrderFulfillmentRate($period),
            'carrying_cost_ratio' => $this->calculateCarryingCostRatio(),
            'stockout_frequency' => $this->calculateStockoutFrequency($period)
        ];
    }

    private function getTrendAnalysis($period)
    {
        // Trend analysis for various metrics
        return [
            'stock_level_trend' => $this->calculateStockLevelTrend($period),
            'movement_volume_trend' => $this->calculateMovementVolumeTrend($period),
            'value_trend' => $this->calculateValueTrend($period)
        ];
    }

    // Additional helper methods would go here...
    private function calculateStockAccuracy() { return 95.5; } // Placeholder
    private function calculateOrderFulfillmentRate($period) { return 98.2; } // Placeholder
    private function calculateCarryingCostRatio() { return 15.3; } // Placeholder
    private function calculateStockoutFrequency($period) { return 2.1; } // Placeholder
    private function calculateStockLevelTrend($period) { return []; } // Placeholder
    private function calculateMovementVolumeTrend($period) { return []; } // Placeholder
    private function calculateValueTrend($period) { return []; } // Placeholder
    private function calculateDaysUntilStockout($productId) { return rand(5, 30); } // Placeholder

    private function exportStockExcel()
    {
        // Excel export implementation
        return $this->response->download('stock_report.xlsx', null);
    }

    private function exportStockPDF()
    {
        // PDF export implementation
        return $this->response->download('stock_report.pdf', null);
    }
}