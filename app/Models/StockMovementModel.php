<?php

namespace App\Models;

use CodeIgniter\Model;

class StockMovementModel extends Model
{
    protected $table            = 'stock_movements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_id',
        'type',
        'quantity',
        'previous_stock',
        'current_stock',
        'reference_no',
        'notes',
        'created_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'product_id' => 'required|integer|is_not_unique[products.id]',
        'type'       => 'required|in_list[IN,OUT,ADJUSTMENT]',
        'quantity'   => 'required|integer|greater_than[0]',
        'notes'      => 'permit_empty|max_length[500]'
    ];
    protected $validationMessages   = [
        'product_id' => [
            'required'      => 'Produk harus dipilih',
            'is_not_unique' => 'Produk tidak valid'
        ],
        'type' => [
            'required' => 'Tipe pergerakan harus dipilih',
            'in_list'  => 'Tipe pergerakan tidak valid'
        ],
        'quantity' => [
            'required'     => 'Jumlah harus diisi',
            'integer'      => 'Jumlah harus berupa angka',
            'greater_than' => 'Jumlah harus lebih dari 0'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    //Custom Methods

    //Get movements with product info
    public function getMovementsWithProduct($limit = null, $offset = null)
    {
        $builder = $this->select('
                        stock_movements.*,
                        products.name as product_name,
                        products.sku as product_sku,
                        categories.name as category_name
                    ')
            ->join('products', 'products.id = stock_movements.product_id')
            ->join('categories', 'categories.id = products.category_id')
            ->orderBy('stock_movements.created_at', 'DESC');

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }

    //Get movements by product ID
    public function getMovementsByProduct($productId, $limit = null)
    {
        $builder = $this->where('product_id', $productId)
                        ->orderBy('created_at', 'DESC');
        
        if ($limit !== null) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    //Create stock movement and update product stock
    public function createMovement($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Get current product stock
            $productModel = new ProductModel();
            $product = $productModel->find($data['product_id']);
            
            if (!$product) {
                throw new \Exception('Produk tidak ditemukan');
            }

            $previousStock = $product['current_stock'];
            
            // Calculate new stock based on movement type
            switch ($data['type']) {
                case 'IN':
                    $newStock = $previousStock + $data['quantity'];
                    break;
                case 'OUT':
                    $newStock = $previousStock - $data['quantity'];
                    if ($newStock < 0) {
                        throw new \Exception('Stok tidak mencukupi');
                    }
                    break;
                case 'ADJUSTMENT':
                    $newStock = $data['quantity']; // For adjustment, quantity is the final stock
                    $data['quantity'] = abs($newStock - $previousStock);
                    break;
                default:
                    throw new \Exception('Tipe pergerakan tidak valid');
            }

            // Prepare movement data
            $movementData = array_merge($data, [
                'previous_stock' => $previousStock,
                'current_stock'  => $newStock,
                'created_by'     => $data['created_by'] ?? 'System'
            ]);

            // Generate reference number if not provided
            if (empty($movementData['reference_no'])) {
                $movementData['reference_no'] = $this->generateReferenceNo($data['type']);
            }

            // Insert movement record
            $movementId = $this->insert($movementData);
            
            if (!$movementId) {
                throw new \Exception('Gagal menyimpan pergerakan stok');
            }

            // Update product stock
            $productModel->updateStock($data['product_id'], $newStock);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi gagal');
            }

            return $movementId;

        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    //Generate reference number
     public function generateReferenceNo($type)
    {
        $prefix = [
            'IN'  => 'IN',
            'OUT' => 'OUT',
            'ADJUSTMENT' => 'ADJ'
        ];

        $today = date('Ymd');
        $prefixCode = $prefix[$type];
        
        // Get last number for today
        $lastMovement = $this->like('reference_no', $prefixCode . $today, 'after')
                             ->orderBy('reference_no', 'DESC')
                             ->first();
        
        $nextNumber = 1;
        if ($lastMovement) {
            $lastNumber = intval(substr($lastMovement['reference_no'], -4));
            $nextNumber = $lastNumber + 1;
        }
        
        return $prefixCode . $today . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    //Get movement statistics
     public function getMovementStats($startDate = null, $endDate = null)
    {
        $builder = $this->select('
                        type,
                        COUNT(*) as count,
                        SUM(quantity) as total_quantity
                    ')
                    ->groupBy('type');
        
        if ($startDate && $endDate) {
            $builder->where('created_at >=', $startDate)
                    ->where('created_at <=', $endDate);
        }
        
        $results = $builder->findAll();
        
        $stats = [
            'IN' => ['count' => 0, 'total_quantity' => 0],
            'OUT' => ['count' => 0, 'total_quantity' => 0],
            'ADJUSTMENT' => ['count' => 0, 'total_quantity' => 0]
        ];
        
        foreach ($results as $result) {
            $stats[$result['type']] = [
                'count' => $result['count'],
                'total_quantity' => $result['total_quantity']
            ];
        }
        
        return $stats;
    }

    //Get monthly movement data for charts
    public function getMonthlyMovements($year = null)
    {
        $year = $year ?: date('Y');
        
        return $this->select('
                        MONTH(created_at) as month,
                        type,
                        SUM(quantity) as total_quantity
                    ')
                    ->where('YEAR(created_at)', $year)
                    ->groupBy('MONTH(created_at), type')
                    ->orderBy('month')
                    ->findAll();
    }
}
