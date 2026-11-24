<?php include __DIR__ . '/../../includes/header.php'; ?>
<?php include __DIR__ . '/../../includes/menu.php'; ?>

<div class="search-bar-container">
    <div class="search-form" id="searchBoxWrapper">
        <i class="fas fa-search search-icon"></i>
        <input type="text" name="search" id="searchInput" placeholder="Tìm kiếm nhanh sân bóng theo tên sân hoặc địa chỉ..." value="<?php echo htmlspecialchars($search_term); ?>" autocomplete="off">
        
        <span id="searchSpinner" style="display: none; color: #0d9488; margin-right: 8px;"><i class="fas fa-spinner fa-spin"></i></span>
        
        <button type="button" id="clearSearchBtn" onclick="clearSearch()" style="display: <?php echo !empty($search_term) ? 'flex' : 'none'; ?>; background: none; border: none; color: #94a3b8; font-size: 1.1rem; cursor: pointer; padding: 4px; margin-right: 4px; align-items: center; justify-content: center; transition: color 0.2s;" title="Xóa tìm kiếm">
            <i class="fas fa-times-circle"></i>
        </button>

        <div style="width: 1px; height: 20px; background: #e2e8f0; margin: 0 6px 0 2px;"></div>

        <button type="button" id="filterToggleBtn" onclick="toggleFilterPanel()" title="Bộ lọc nâng cao">
            <i class="fas fa-sliders-h"></i>
            <span id="filterActiveBadge"></span>
        </button>
    </div>

    <!-- Expandable Filter Drawer -->
    <div id="filterPanel" style="display: none; background: #ffffff; border-radius: 16px; padding: 1.25rem 1.5rem; margin-top: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; box-sizing: border-box; overflow: hidden; max-width: 100%;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; align-items: start;">
            <div style="min-width: 0;">
                <label style="display: block; font-weight: 600; color: #374151; font-size: 0.88rem; margin-bottom: 0.65rem;">
                    Loại quy mô sân:
                </label>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <button type="button" class="filter-pill active" data-type="loai_san" data-value="all" onclick="selectFilter('loai_san', 'all', this)">Tất cả</button>
                    <button type="button" class="filter-pill" data-type="loai_san" data-value="san_5" onclick="selectFilter('loai_san', 'san_5', this)">Sân 5</button>
                    <button type="button" class="filter-pill" data-type="loai_san" data-value="san_7" onclick="selectFilter('loai_san', 'san_7', this)">Sân 7</button>
                </div>
            </div>

            <div style="min-width: 0;">
                <label style="display: block; font-weight: 600; color: #374151; font-size: 0.88rem; margin-bottom: 0.65rem;">
                    Mức giá mong muốn (VNĐ):
                </label>
                <div style="display: flex; align-items: center; gap: 8px; width: 100%; box-sizing: border-box;">
                    <input type="number" id="inputMinPrice" placeholder="Từ (VD: 200k)" value="<?php echo (!empty($min_price) && $min_price > 0) ? (int)$min_price : ''; ?>" min="0" step="10000" style="flex: 1 1 0; min-width: 0; width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem; outline: none; box-sizing: border-box;" oninput="debounceApplyPriceFilter()">
                    <span style="color: #9ca3af; font-weight: 600; flex-shrink: 0;">-</span>
                    <input type="number" id="inputMaxPrice" placeholder="Đến (VD: 500k)" value="<?php echo (!empty($max_price) && $max_price > 0) ? (int)$max_price : ''; ?>" min="0" step="10000" style="flex: 1 1 0; min-width: 0; width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.88rem; outline: none; box-sizing: border-box;" oninput="debounceApplyPriceFilter()">
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; align-items: center; margin-top: 1.25rem; padding-top: 0.75rem; border-top: 1px solid #f3f4f6;">
            <button type="button" onclick="resetAllFilters()" style="background: none; border: none; color: #ef4444; font-size: 0.85rem; font-weight: 600; cursor: pointer; padding: 4px 8px; display: inline-flex; align-items: center; gap: 6px; transition: opacity 0.2s;">
                <i class="fas fa-trash-alt"></i> Xóa bộ lọc
            </button>
        </div>
    </div>
</div>



<div class="san-bong-container" id="pitchListContainer">
    <?php include __DIR__ . '/pitch_cards.php'; ?>
</div>

<div id="paginationContainer">
    <?php include __DIR__ . '/pagination.php'; ?>
</div>

<div id="homeFilterData" 
     data-page="<?php echo (int)($page ?? 1); ?>" 
     data-loai-san="<?php echo htmlspecialchars($loai_san ?? 'all'); ?>" 
     data-min-price="<?php echo (!empty($min_price) && $min_price > 0) ? (int)$min_price : ''; ?>" 
     data-max-price="<?php echo (!empty($max_price) && $max_price > 0) ? (int)$max_price : ''; ?>" 
     style="display: none;"></div>

<script src="assets/js/home.js?v=1.0.1"></script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
