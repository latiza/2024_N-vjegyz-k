<?php
function generatePagination($currentPage, $totalPages) {
    $pagination = "<p>";
    $pagination .= ($currentPage != 1) ? "<a href=\"?oldal=1\">Első</a> | " : "Első | ";
    $pagination .= ($currentPage > 1 && $currentPage <= $totalPages) ? "<a href=\"?oldal=" . ($currentPage - 1) . "\">Előző</a> | " : "Előző | ";

    for ($page = 1; $page <= $totalPages; $page++) {
        $pagination .= ($currentPage != $page) ? "<a href=\"?oldal={$page}\">{$page}</a> | " : $page . " | ";
    }
    $pagination .= ($currentPage < $totalPages) ? "<a href=\"?oldal=" . ($currentPage + 1) . "\">Következő</a> | " : "Következő | ";
    $pagination .= ($currentPage != $totalPages) ? "<a href=\"?oldal={$totalPages}\">Utolsó</a>" : "Utolsó";
    $pagination .= "</p>";

    return $pagination;
}

