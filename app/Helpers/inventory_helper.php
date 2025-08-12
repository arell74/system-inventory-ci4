<?php

if (!function_exists('format_currency')) {
    
    //format currency to indonesian Rupiah
    function format_currency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('format_stock_badge')) {
    /**
     * Generate bootstrap badge for stock status
     */
    function format_stock_badge($current, $minimum)
    {
        if ($current == 0) {
            return '<span class="badge bg-danger">Habis</span>';
        } elseif ($current <= $minimum) {
            return '<span class="badge bg-warning">Stok Rendah</span>';
        } else {
            return '<span class="badge bg-success">Normal</span>';
        }
    }
}

if (!function_exists('format_movement_badge')) {
    /**
     * Generate bootstrap badge for movement type
     */
    function format_movement_badge($type)
    {
        $badges = [
            'IN'  => '<span class="badge bg-success"><i class="bi bi-arrow-down"></i> Masuk</span>',
            'OUT' => '<span class="badge bg-danger"><i class="bi bi-arrow-up"></i> Keluar</span>',
            'ADJUSTMENT' => '<span class="badge bg-info"><i class="bi bi-arrow-repeat"></i> Penyesuaian</span>'
        ];
        
        return $badges[$type] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
}

if (!function_exists('time_ago')) {
    /**
     * Convert timestamp to time ago format
     */
    function time_ago($datetime)
    {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'baru saja';
        if ($time < 3600) return floor($time/60) . ' menit yang lalu';
        if ($time < 86400) return floor($time/3600) . ' jam yang lalu';
        if ($time < 2592000) return floor($time/86400) . ' hari yang lalu';
        if ($time < 31104000) return floor($time/2592000) . ' bulan yang lalu';
        return floor($time/31104000) . ' tahun yang lalu';
    }
}