<?php
try {
    $conn = new PDO("pgsql:host=aws-1-us-east-2.pooler.supabase.com;port=6543;dbname=postgres", "postgres.lokjiueialuwrulybgut", "g0UNVXoLuA8uaPtH");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT * FROM catalogo WHERE referencia_codigo LIKE '%RESEN%'");
    print_r($stmt->fetchAll());
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
