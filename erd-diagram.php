<?php
// erd-diagram.php - مخطط ERD مرئي واحترافي
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $database = DB::getDatabaseName();
    echo "📊 جاري إنشاء مخطط ERD لقاعدة البيانات: $database\n\n";
    
    // الحصول على جميع الجداول
    $tables = DB::select('SHOW TABLES');
    $tableKey = 'Tables_in_' . $database;
    
    // الحصول على العلاقات (المفاتيح الأجنبية)
    $foreignKeys = [];
    foreach ($tables as $table) {
        $tableName = $table->$tableKey;
        try {
            $fkQuery = DB::select("
                SELECT 
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = ?
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$database, $tableName]);
            
            foreach ($fkQuery as $fk) {
                $foreignKeys[] = [
                    'source_table' => $tableName,
                    'source_column' => $fk->COLUMN_NAME,
                    'target_table' => $fk->REFERENCED_TABLE_NAME,
                    'target_column' => $fk->REFERENCED_COLUMN_NAME
                ];
            }
        } catch (\Exception $e) {
            // تخطي إذا لم توجد معلومات عن المفاتيح الأجنبية
        }
    }
    
    // بدء إنشاء HTML
    $html = '<!DOCTYPE html>
    <html lang="ar" dir="ltr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>مخطط ERD - ' . htmlspecialchars($database) . '</title>
        <style>
            * { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                padding: 20px;
            }
            .container {
                max-width: 1400px;
                margin: 0 auto;
                background: rgba(255,255,255,0.95);
                border-radius: 20px;
                padding: 30px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            }
            h1 {
                text-align: center;
                color: #333;
                margin-bottom: 10px;
                font-size: 32px;
            }
            .subtitle {
                text-align: center;
                color: #666;
                margin-bottom: 30px;
                font-size: 14px;
            }
            .erd-container {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                justify-content: center;
            }
            .table-card {
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                border: 1px solid #e0e0e0;
                min-width: 220px;
                max-width: 280px;
                flex: 1 0 auto;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            .table-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            }
            .table-header {
                background: linear-gradient(135deg, #4a90d9, #357abd);
                color: white;
                padding: 12px 16px;
                font-weight: bold;
                font-size: 16px;
                border-radius: 12px 12px 0 0;
                text-align: center;
                position: relative;
                word-break: break-all;
            }
            .table-header .row-count {
                font-size: 11px;
                opacity: 0.8;
                font-weight: normal;
                display: block;
                margin-top: 3px;
            }
            .column-list {
                padding: 4px 0;
            }
            .column-item {
                padding: 7px 14px;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 13px;
                transition: background 0.2s;
            }
            .column-item:hover {
                background: #f8f9fe;
            }
            .column-item:last-child {
                border-bottom: none;
            }
            .col-name {
                font-weight: 500;
                color: #333;
                font-size: 13px;
            }
            .col-type {
                color: #888;
                font-size: 11px;
                background: #f0f0f0;
                padding: 2px 8px;
                border-radius: 10px;
                margin-left: 8px;
            }
            .col-badges {
                display: flex;
                gap: 4px;
                align-items: center;
            }
            .badge {
                font-size: 10px;
                padding: 2px 8px;
                border-radius: 10px;
                font-weight: bold;
                letter-spacing: 0.5px;
            }
            .badge-pk {
                background: #fee2e2;
                color: #dc2626;
            }
            .badge-fk {
                background: #dbeafe;
                color: #2563eb;
            }
            .badge-null {
                background: #f3f4f6;
                color: #9ca3af;
                font-weight: normal;
            }
            .badge-index {
                background: #fef3c7;
                color: #d97706;
            }
            .badge-auto {
                background: #d1fae5;
                color: #059669;
            }
            .relations {
                margin-top: 30px;
                padding: 20px;
                background: #f8f9fa;
                border-radius: 12px;
            }
            .relations h2 {
                color: #333;
                font-size: 18px;
                margin-bottom: 15px;
            }
            .relation-item {
                display: inline-block;
                background: white;
                padding: 8px 16px;
                margin: 4px 8px 4px 0;
                border-radius: 8px;
                border: 1px solid #e5e7eb;
                font-size: 13px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
            .relation-item .arrow {
                color: #4a90d9;
                font-weight: bold;
                margin: 0 8px;
            }
            .stats {
                display: flex;
                justify-content: center;
                gap: 30px;
                margin-bottom: 25px;
                flex-wrap: wrap;
            }
            .stat-box {
                text-align: center;
                padding: 10px 25px;
                background: #f8f9fa;
                border-radius: 10px;
                min-width: 120px;
            }
            .stat-box .number {
                font-size: 28px;
                font-weight: bold;
                color: #4a90d9;
            }
            .stat-box .label {
                font-size: 12px;
                color: #666;
                margin-top: 4px;
            }
            .download-btn {
                display: inline-block;
                background: #4a90d9;
                color: white;
                padding: 10px 30px;
                border-radius: 8px;
                text-decoration: none;
                margin-top: 15px;
                font-weight: bold;
                transition: background 0.3s;
                border: none;
                cursor: pointer;
                font-size: 14px;
            }
            .download-btn:hover {
                background: #357abd;
            }
            .print-btn {
                background: #6b7280;
            }
            .print-btn:hover {
                background: #4b5563;
            }
            .btn-group {
                display: flex;
                gap: 10px;
                justify-content: center;
                flex-wrap: wrap;
                margin-top: 20px;
            }
            @media print {
                body { background: white; padding: 0; }
                .container { box-shadow: none; padding: 15px; }
                .table-card:hover { transform: none; }
                .btn-group { display: none; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>📊 مخطط العلاقات (ERD)</h1>
            <div class="subtitle">قاعدة البيانات: ' . htmlspecialchars($database) . ' | تم الإنشاء: ' . date('Y-m-d H:i:s') . '</div>
            
            <div class="stats">
                <div class="stat-box">
                    <div class="number">' . count($tables) . '</div>
                    <div class="label">📋 الجداول</div>
                </div>
                <div class="stat-box">
                    <div class="number">' . count($foreignKeys) . '</div>
                    <div class="label">🔗 العلاقات</div>
                </div>
            </div>
            
            <div class="erd-container">';

    // عرض الجداول
    foreach ($tables as $table) {
        $tableName = $table->$tableKey;
        $columns = DB::select("DESCRIBE $tableName");
        
        // حساب عدد الصفوف التقريبي
        try {
            $countResult = DB::select("SELECT COUNT(*) as count FROM $tableName");
            $rowCount = $countResult[0]->count ?? 0;
        } catch (\Exception $e) {
            $rowCount = '?';
        }
        
        $html .= '<div class="table-card">
            <div class="table-header">
                ' . htmlspecialchars($tableName) . '
                <span class="row-count">' . $rowCount . ' صف</span>
            </div>
            <div class="column-list">';
        
        foreach ($columns as $col) {
            $badges = '';
            
            // Primary Key
            if ($col->Key === 'PRI') {
                $badges .= '<span class="badge badge-pk">PK</span>';
            }
            
            // Foreign Key (من العلاقات)
            $isFK = false;
            foreach ($foreignKeys as $fk) {
                if ($fk['source_table'] === $tableName && $fk['source_column'] === $col->Field) {
                    $isFK = true;
                    break;
                }
            }
            if ($isFK) {
                $badges .= '<span class="badge badge-fk">FK</span>';
            }
            
            // Index
            if ($col->Key === 'MUL' && !$isFK) {
                $badges .= '<span class="badge badge-index">IDX</span>';
            }
            
            // Auto Increment
            if (strpos($col->Extra, 'auto_increment') !== false) {
                $badges .= '<span class="badge badge-auto">AI</span>';
            }
            
            // Null
            if ($col->Null === 'YES') {
                $badges .= '<span class="badge badge-null">NULL</span>';
            }
            
            $html .= '<div class="column-item">
                <span>
                    <span class="col-name">' . htmlspecialchars($col->Field) . '</span>
                    <span class="col-type">' . htmlspecialchars($col->Type) . '</span>
                </span>
                <span class="col-badges">' . $badges . '</span>
            </div>';
        }
        
        $html .= '</div></div>';
    }

    $html .= '</div>'; // نهاية erd-container

    // عرض العلاقات
    if (count($foreignKeys) > 0) {
        $html .= '<div class="relations">
            <h2>🔗 العلاقات بين الجداول</h2>';
        
        foreach ($foreignKeys as $fk) {
            $html .= '<div class="relation-item">
                <strong>' . htmlspecialchars($fk['source_table']) . '</strong>
                .<span style="color:#666;">' . htmlspecialchars($fk['source_column']) . '</span>
                <span class="arrow">➜</span>
                <strong>' . htmlspecialchars($fk['target_table']) . '</strong>
                .<span style="color:#666;">' . htmlspecialchars($fk['target_column']) . '</span>
            </div>';
        }
        
        $html .= '</div>';
    }

    $html .= '
            <div class="btn-group">
                <button class="download-btn" onclick="window.print()">🖨️ طباعة / حفظ كـ PDF</button>
                <button class="download-btn print-btn" onclick="downloadAsImage()">📥 حفظ كـ صورة</button>
            </div>
        </div>
        
        <script>
            function downloadAsImage() {
                // استخدام html2canvas لتحويل الصفحة إلى صورة
                var script = document.createElement("script");
                script.src = "https://html2canvas.hertzen.com/dist/html2canvas.min.js";
                document.head.appendChild(script);
                script.onload = function() {
                    html2canvas(document.querySelector(".container"), {
                        scale: 2,
                        useCORS: true,
                        backgroundColor: "#ffffff"
                    }).then(function(canvas) {
                        var link = document.createElement("a");
                        link.download = "erd-diagram.png";
                        link.href = canvas.toDataURL("image/png");
                        link.click();
                    });
                };
            }
        </script>
    </body>
    </html>';

    // حفظ الملف
    file_put_contents(__DIR__ . '/erd-diagram.html', $html);
    echo "✅ تم إنشاء مخطط ERD بنجاح!\n";
    echo "📁 الملف: " . realpath(__DIR__ . '/erd-diagram.html') . "\n";
    echo "\n🚀 لفتح المخطط: افتح الملف في المتصفح\n";
    echo "   أو استخدم: start erd-diagram.html (في Windows)\n";
    
} catch (\Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    echo "ملف: " . $e->getFile() . " السطر: " . $e->getLine() . "\n";
}