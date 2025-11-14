<?php
require "../include/bittorrent.php";
dbconn();
loggedinorreturn();

// 使用medal.1.9.6.php的查询逻辑，添加优先级排序
$query = \App\Models\Medal::query()->where('display_on_medal_page', 1)
    ->orderBy('priority', 'desc')->orderBy("id", 'desc');
$total = (clone $query)->count();
$perPage = 20;
list($paginationTop, $paginationBottom, $limit, $offset) = pager($perPage, $total, "?");
$rows = (clone $query)->offset($offset)->take($perPage)->orderBy('id', 'desc')->get();
$title = nexus_trans('medal.label');
$columnNameLabel = nexus_trans('label.name');
$columnImageLargeLabel = nexus_trans('medal.fields.image_large');
$columnPriceLabel = nexus_trans('medal.fields.price');
$columnDurationLabel = nexus_trans('medal.fields.duration');
$columnDescriptionLabel = nexus_trans('medal.fields.description');
$columnBuyLabel = nexus_trans('medal.buy_btn');
$columnSaleBeginEndTimeLabel = nexus_trans('medal.fields.sale_begin_end_time');
$columnInventoryLabel = nexus_trans('medal.fields.inventory');
$columnBonusAdditionLabel = nexus_trans('medal.fields.bonus_addition');
$columnGiftLabel = nexus_trans('medal.gift_btn');
$columnGiftFeeLabel = nexus_trans('medal.fields.gift_fee');

$cssRow = get_css_row();
$themeTokens = [];
if (is_array($cssRow)) {
    if (!empty($cssRow['name'])) {
        $themeTokens[] = strtolower(preg_replace('/[^a-z0-9]+/', '-', $cssRow['name']));
    }
    $isDarkTheme = stripos($cssRow['name'] ?? '', 'dark') !== false || stripos($cssRow['uri'] ?? '', 'dark') !== false;
    $themeTokens[] = $isDarkTheme ? 'theme-dark' : 'theme-light';
}
if (empty($themeTokens)) {
    $themeTokens[] = 'theme-light';
}
$themeAttr = htmlspecialchars(implode(' ', array_unique(array_filter($themeTokens))), ENT_QUOTES);

$css = <<<CSS
.header-container {
    position: relative;
}

.header-container h1 {
    margin: 0;
    text-align: center;
}

.layout-toggle {
    position: absolute;
    top: 0;
    right: 0;
}

.layout-btn,
.medal-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 12px;
    min-height: 28px;
    font-size: 12px;
    box-sizing: border-box;
    cursor: pointer;
}

#tableView .medal-btn {
    min-width: 88px;
}

input.uid {
    width: 60px;
    box-sizing: border-box;
    padding: 0 6px;
    margin-right: 6px;
}

.medal-gift-fee {
    margin-left: 6px;
    font-size: 12px;
    white-space: nowrap;
}

.view-container {
    width: 100%;
}

.medal-grid {
    --medal-card-bg: var(--glass-bg, var(--background-light, rgba(0, 0, 0, 0.02)));
    --medal-card-border: var(--glass-border, rgba(0, 0, 0, 0.12));
    --medal-badge-bg: rgba(0, 0, 0, 0.08);
}

.medal-grid[data-theme~="theme-dark"] {
    --medal-card-bg: var(--glass-bg, rgba(255, 255, 255, 0.08));
    --medal-card-border: var(--glass-border, rgba(255, 255, 255, 0.2));
    --medal-badge-bg: rgba(255, 255, 255, 0.18);
}

.medal-grid-body {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px 16px;
    padding: 10px 0;
}

.medal-grid-item {
    padding: 12px;
    background-color: var(--medal-card-bg);
    border: 1px solid var(--medal-card-border);
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    min-height: 100%;
    position: relative;
    transition: border-color 0.2s ease, background-color 0.2s ease;
}

.medal-grid-item:hover {
    border-color: var(--primary-light, var(--medal-card-border));
}

.medal-id {
    align-self: flex-end;
    font-size: 12px;
    padding: 2px 8px;
    border-radius: 999px;
    background-color: var(--medal-badge-bg);
}

.medal-cover {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 248px;
}

.medal-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease, filter 0.3s ease;
    filter: grayscale(1);
}

.medal-image:hover {
    transform: scale(1.05);
    filter: grayscale(0);
}

.medal-image.purchased {
    filter: grayscale(0);
}

.medal-name {
    font-weight: bold;
    text-align: center;
}

.medal-description {
    text-align: center;
    min-height: 1.4em;
}

.medal-sale-window {
    text-align: center;
    font-size: 12px;
}

.medal-table {
    width: 100%;
    border-collapse: collapse;
}

.medal-table td {
    border: 1px solid var(--medal-card-border);
    padding: 5px 8px;
    text-align: center;
    background-color: transparent;
}

.medal-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: 100%;
}

.medal-actions > div {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
}

.medal-preview {
    max-width: 60px;
    max-height: 60px;
}

@media (max-width: 600px) {
    .header-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .layout-toggle {
        position: static;
        text-align: center;
    }

    .medal-grid-body {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }
}
CSS;

\Nexus\Nexus::css($css, 'header', false);

// 添加布局切换按钮 - 使用经典按钮样式
$layoutToggle = <<<TOGGLE
<div class="layout-toggle">
    <input type="button" id="toggleViewBtn" class="layout-btn" value="切换为新版">
</div>
TOGGLE;

$headerTitle = htmlspecialchars($title, ENT_QUOTES);
$header = '<div class="header-container"><h1>'.$headerTitle.'</h1>'.$layoutToggle.'</div>';
stdhead($title);
begin_main_frame();

// 表格视图
$tableView = <<<TABLE
<div id="tableView" class="view-container">
<table border="1" cellspacing="0" cellpadding="5" width="100%">
<thead>
<tr>
<td class="colhead">ID</td>
<td class="colhead">$columnImageLargeLabel</td>
<td class="colhead">$columnDescriptionLabel</td>
<td class="colhead" style="width: 115px">$columnSaleBeginEndTimeLabel</td>
<td class="colhead">$columnDurationLabel</td>
<td class="colhead">$columnBonusAdditionLabel</td>
<td class="colhead">$columnPriceLabel</td>
<td class="colhead">$columnInventoryLabel</td>
<td class="colhead">$columnBuyLabel</td>
<td class="colhead">$columnGiftLabel</td>
</tr>
</thead>
TABLE;

// 网格视图
$gridView = <<<GRID
<div id="gridView" class="view-container" style="display: none;">
<div class="medal-grid" data-theme="$themeAttr">
    <div class="medal-grid-body">
GRID;

$now = now();
$user = \App\Models\User::query()->findOrFail($CURUSER['id']);
$userMedals = $user->valid_medals->keyBy('id');

foreach ($rows as $row) {
    $buyDisabled = $giftDisabled = ' disabled';
    $buyClass = $giftClass = '';
    $class = '';
    try {
        $row->checkCanBeBuy();
        if ($userMedals->has($row->id)) {
            $class = 'purchased';
            $buyBtnText = nexus_trans('medal.buy_already');
        } elseif ($CURUSER['seedbonus'] < $row->price) {
            $buyBtnText = nexus_trans('medal.require_more_bonus');
        } else {
            $buyBtnText = nexus_trans('medal.buy_btn');
            $buyDisabled = '';
            $buyClass = 'buy';
        }
        if ($CURUSER['seedbonus'] < $row->price * (1 + ($row->gift_fee_factor ?? 0))) {
            $giftBtnText = nexus_trans('medal.require_more_bonus');
        } else {
            $giftBtnText = nexus_trans('medal.gift_btn');
            $giftDisabled = '';
            $giftClass = 'gift';
        }
    } catch (\Exception $exception) {
        $buyBtnText = $giftBtnText = $exception->getMessage();
        if ($userMedals->has($row->id)) {
            $class = 'purchased';
            $buyBtnText = $exception->getMessage() == nexus_trans('medal.grant_only') ? nexus_trans('medal.grant_already') : nexus_trans('medal.buy_already');
        }
    }
    $buyBtnValue = htmlspecialchars($buyBtnText, ENT_QUOTES);
    $giftBtnValue = htmlspecialchars($giftBtnText, ENT_QUOTES);
    $imageLarge = htmlspecialchars($row->image_large, ENT_QUOTES);
    $imageClassAttr = htmlspecialchars(trim('medal-image ' . $class), ENT_QUOTES);
    $nameEscaped = htmlspecialchars($row->name, ENT_QUOTES);
    $saleBegin = htmlspecialchars($row->sale_begin_time ?? nexus_trans('nexus.no_limit'), ENT_QUOTES);
    $saleEnd = htmlspecialchars($row->sale_end_time ?? nexus_trans('nexus.no_limit'), ENT_QUOTES);
    $durationTextEsc = htmlspecialchars($row->durationText, ENT_QUOTES);
    $bonusAdditionText = htmlspecialchars((($row->bonus_addition_factor ?? 0) * 100).'%', ENT_QUOTES);
    $priceText = htmlspecialchars(number_format($row->price), ENT_QUOTES);
    $inventoryText = htmlspecialchars((string)($row->inventory ?? nexus_trans('label.infinite')), ENT_QUOTES);
    $giftFeeText = htmlspecialchars((($row->gift_fee_factor ?? 0) * 100).'%', ENT_QUOTES);

    $buyAction = sprintf(
        '<input type="button" class="medal-btn%s" data-id="%s" value="%s"%s>',
        $buyClass ? ' ' . $buyClass : '',
        $row->id,
        $buyBtnValue,
        $buyDisabled
    );
    $giftAction = sprintf(
        '<input type="number" class="uid"%s placeholder="UID"><input type="button" class="medal-btn%s" data-id="%s" value="%s"%s><span class="medal-gift-fee nowrap">%s: %s</span>',
        $giftDisabled,
        $giftClass ? ' ' . $giftClass : '',
        $row->id,
        $giftBtnValue,
        $giftDisabled,
        $columnGiftFeeLabel,
        $giftFeeText
    );
    $giftActionGrid = sprintf(
        '<input type="number" class="uid"%s placeholder="UID"><input type="button" class="medal-btn%s" data-id="%s" value="%s"%s>',
        $giftDisabled,
        $giftClass ? ' ' . $giftClass : '',
        $row->id,
        $giftBtnValue,
        $giftDisabled
    );
    
    // 表格行
    $tableRow = sprintf(
        '<tr><td>%s</td><td><img src="%s" class="medal-preview preview" /></td><td><h1>%s</h1>%s</td><td>%s ~<br>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
        $row->id,
        $imageLarge,
        $nameEscaped,
        $row->description,
        $saleBegin,
        $saleEnd,
        $durationTextEsc,
        $bonusAdditionText,
        $priceText,
        $inventoryText,
        $buyAction,
        $giftAction
    );
    
    // 网格项
    $gridItem = sprintf(
        '
            <div class="medal-grid-item">
                <div class="medal-id">ID: %s</div>
                <div class="medal-cover"><img src="%s" class="%s" alt="%s" /></div>
                <div class="medal-name">%s</div>
                <div class="medal-description">%s</div>
                <div class="medal-sale-window">%s ~ %s</div>
                
                <table class="medal-table">
                    <tbody>
                        <tr>
                            <td>%s</td>
                            <td>%s</td>
                        </tr>
                        <tr>
                            <td>%s</td>
                            <td>%s</td>
                        </tr>
                        <tr>
                            <td>%s</td>
                            <td>%s</td>
                        </tr>
                        <tr>
                            <td>%s</td>
                            <td>%s</td>
                        </tr>
                        <tr>
                            <td>%s</td>
                            <td>%s</td>
                        </tr>
                    </tbody>
                </table>
                <div class="medal-actions">
                    <div class="medal-buy-action">%s</div>
                    <div class="medal-gift-action">%s</div>
                </div>
            </div>
        ',
        $row->id,
        $imageLarge,
        $imageClassAttr,
        $nameEscaped,
        $nameEscaped,
        $row->description,
        $saleBegin,
        $saleEnd,
        $columnDurationLabel,
        $durationTextEsc,
        $columnBonusAdditionLabel,
        $bonusAdditionText,
        $columnPriceLabel,
        $priceText,
        $columnInventoryLabel,
        $inventoryText,
        $columnGiftFeeLabel,
        $giftFeeText,
        $buyAction,
        $giftActionGrid
    );
    
    $tableView .= $tableRow;
    $gridView .= $gridItem;
}

// 完成表格视图
$tableView .= '</tbody></table></div>';

// 完成网格视图
$gridView .= '</div></div></div>';

echo $header . $tableView . $gridView . $paginationBottom;
end_main_frame();
$confirmBuyMsg = nexus_trans('medal.confirm_to_buy');
$confirmGiftMsg = nexus_trans('medal.confirm_to_gift');
$js = <<<JS
jQuery('.buy').on('click', function (e) {
    let medalId = jQuery(this).attr('data-id')
    layer.confirm("{$confirmBuyMsg}", function (index) {
        let params = {
            action: "buyMedal",
            params: {medal_id: medalId}
        }
        console.log(params)
        jQuery.post('ajax.php', params, function(response) {
            console.log(response)
            if (response.ret != 0) {
                layer.alert(response.msg)
                return
            }
            window.location.reload()
        }, 'json')
    })
})
jQuery('.gift').on('click', function (e) {
    let medalId = jQuery(this).attr('data-id')
    let uid = jQuery(this).prev().val()
    if (!uid) {
        layer.alert('Require UID')
        return
    }
    layer.confirm("{$confirmGiftMsg}" + uid + " ?", function (index) {
        let params = {
            action: "giftMedal",
            params: {medal_id: medalId, uid: uid}
        }
        console.log(params)
        jQuery.post('ajax.php', params, function(response) {
            console.log(response)
            if (response.ret != 0) {
                layer.alert(response.msg)
                return
            }
            window.location.reload()
        }, 'json')
    })
})

// 布局切换功能
jQuery('#toggleViewBtn').on('click', function() {
    if (jQuery('#tableView').is(':visible')) {
        // 当前显示表格视图，切换到网格视图
        jQuery('#tableView').hide();
        jQuery('#gridView').show();
        jQuery('#toggleViewBtn').val('切换为经典版');
        // 保存用户偏好
        localStorage.setItem('medalViewPreference', 'grid');
    } else {
        // 当前显示网格视图，切换到表格视图
        jQuery('#tableView').show();
        jQuery('#gridView').hide();
        jQuery('#toggleViewBtn').val('切换为新版');
        // 保存用户偏好
        localStorage.setItem('medalViewPreference', 'table');
    }
});

// 页面加载时检查用户偏好
jQuery(document).ready(function() {
    const viewPreference = localStorage.getItem('medalViewPreference');
    if (viewPreference === 'grid') {
        // 如果用户偏好是网格视图，则切换到网格视图
        jQuery('#tableView').hide();
        jQuery('#gridView').show();
        jQuery('#toggleViewBtn').val('切换为经典版');
    } else {
        // 默认显示表格视图
        jQuery('#tableView').show();
        jQuery('#gridView').hide();
        jQuery('#toggleViewBtn').val('切换为新版');
    }
});
JS;
\Nexus\Nexus::js($js, 'footer', false);
stdfoot();
