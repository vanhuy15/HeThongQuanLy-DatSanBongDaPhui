<?php
/**
 * View Partial: Pagination
 */
?>
<?php if (!empty($total_pages) && $total_pages > 1): 
    $pagination_range = function_exists('get_pagination_range') ? get_pagination_range($page, $total_pages, 1) : range(1, $total_pages);
?>
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="javascript:void(0)" onclick="changePage(<?php echo ($page - 1); ?>)" title="Trang trước"><i class="fas fa-chevron-left" style="font-size: 0.75rem;"></i></a>
    <?php endif; ?>
    <?php foreach ($pagination_range as $p): ?>
        <?php if ($p === '...'): ?>
            <span class="pagination-ellipsis" style="display: inline-flex; align-items: center; justify-content: center; min-width: 28px; height: 32px; color: #94a3b8; font-weight: 700; font-size: 0.85rem; user-select: none;">...</span>
        <?php else: ?>
            <a href="javascript:void(0)" onclick="changePage(<?php echo $p; ?>)" class="<?php echo ($page == $p) ? 'active' : ''; ?>">
                <?php echo $p; ?>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($page < $total_pages): ?>
        <a href="javascript:void(0)" onclick="changePage(<?php echo ($page + 1); ?>)" title="Trang sau"><i class="fas fa-chevron-right" style="font-size: 0.75rem;"></i></a>
    <?php endif; ?>
</div>
<?php endif; ?>

