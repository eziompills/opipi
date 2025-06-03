<?php
/**
 * Pagination helper.
 * Usage:
 *   list($rows, $navHtml) = paginate_query(
 *       $pdo,
 *       'SELECT * FROM salons WHERE name LIKE :q OR city LIKE :q',
 *       [':q' => "%{$q}%"],
 *       25
 *   );
 */
function paginate_query(PDO $pdo, string $sql, array $params = [], int $perPage = 25): array
{
    $page = max(1, (int)($_GET['page'] ?? 1));
    $countSql = 'SELECT COUNT(*) cnt FROM (' . $sql . ') AS sub';
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalRows = (int)$countStmt->fetchColumn();
    $totalPages = (int)ceil($totalRows / $perPage);

    $offset = ($page - 1) * $perPage;
    $sqlPaginated = $sql . " LIMIT {$perPage} OFFSET {$offset}";
    $dataStmt = $pdo->prepare($sqlPaginated);
    $dataStmt->execute($params);
    $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

    // Generate navigation HTML
    if ($totalPages <= 1) {
        $navHtml = '';
    } else {
        $navHtml = '<nav aria-label="Pagination"><ul class="pagination justify-content-center my-4">';
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = $i === $page ? ' active' : '';
            $link = htmlspecialchars(build_query_url(['page' => $i]));
            $navHtml .= "<li class='page-item{$active}'><a class='page-link' href='{$link}'>{$i}</a></li>";
        }
        $navHtml .= '</ul></nav>';
    }
    return [$rows, $navHtml];
}

/**
 * Build current URL with updated query params.
 */
function build_query_url(array $updates): string
{
    $params = array_merge($_GET, $updates);
    return strtok($_SERVER['REQUEST_URI'], '?') . '?' . http_build_query($params);
}
?>
