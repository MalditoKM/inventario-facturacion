<?php
function paginar($query, $params, $por_pagina = 10, $page = 1, $pdo) {
    $offset = ($page - 1) * $por_pagina;
    $stmt = $pdo->prepare($query . " LIMIT $por_pagina OFFSET $offset");
    $stmt->execute($params);
    $data = $stmt->fetchAll();

    // Total
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM (" . $query . ") as t");
    $stmt->execute($params);
    $total = $stmt->fetchColumn();

    return [$data, $total];
}
?>