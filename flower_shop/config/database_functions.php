<?php
function addColumnIfNotExists($pdo, $table, $column, $definition) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                          WHERE TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->execute([$table, $column]);
    $exists = $stmt->fetchColumn();
    
    if (!$exists) {
        $pdo->exec("ALTER TABLE $table ADD COLUMN $column $definition");
    }
}

// Gunakan fungsi ini untuk memastikan kolom ada
addColumnIfNotExists($pdo, 'users', 'is_verified', 'BOOLEAN DEFAULT FALSE');
addColumnIfNotExists($pdo, 'users', 'created_at', 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP');