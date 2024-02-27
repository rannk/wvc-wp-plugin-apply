<?php
  $countries = _RK_WVC()->getCountries();
  foreach($countries as $k => $v){
      $countr .= '"'.$k.'":"'.$v.'",';
  }
  $countr = substr($countr, 0, -1);

?>
<script>
    var countriesArr = {<?=$countr?>}
</script>
<div class="wrap">
    <p class="search-box">
        <label class="screen-reader-text" for="post-search-input">Search Pages:</label>
        <input type="search" id="post-search-input" name="s" value="">
        <input type="button" id="wvc-apply-search-submit" class="button" value="Search Records">
    </p>
    <form action="" method="post">
  <div class="tablenav top">
    <div class="alignleft actions bulkactions">
        <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label><select name="action" id="bulk-action-selector-top">
            <option value="-1">Bulk actions</option>
            <option value="trash">Move to Trash</option>
        </select>
        <input type="submit" id="doaction" name="doaction" class="button action" value="Apply">
    </div>

      <div class="tablenav-pages"><span class="displaying-num"><?=$applyListsCount['total']?> items</span>
          <?php
          if($applyListsCount['current_page'] == 1){
              echo ' <span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>';
              echo ' <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>';
          }else{
              echo ' <a class="first-page button" href="/wp-admin/admin.php?page=rk_wvc_product_apply_lists&cp=1&s='.$_REQUEST['s'].'"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>';
              echo ' <a class="prev-page button" href="/wp-admin/admin.php?page=rk_wvc_product_apply_lists&cp='.($applyListsCount['current_page'] - 1).'&s='.$_REQUEST['s'].'"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>';
          }
          ?>
          <span class="screen-reader-text">Current Page</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text"><?=$applyListsCount['current_page']?> of <span class="total-pages"><?=$applyListsCount['pages']?></span></span></span>
          <?php
          if($applyListsCount['current_page'] == $applyListsCount['pages']){
              echo ' <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>';
              echo ' <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>';
          }else{
              echo ' <a class="next-page button" href="/wp-admin/admin.php?page=rk_wvc_product_apply_lists&cp='.($applyListsCount['current_page'] + 1).'&s='.$_REQUEST['s'].'"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>';
              echo ' <a class="last-page button" href="/wp-admin/admin.php?page=rk_wvc_product_apply_lists&cp='.$applyListsCount['pages'].'&s='.$_REQUEST['s'].'"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a></span>';
          }
          ?>
      </div>
  </div>
    <table class="wp-list-table widefat fixed striped table-view-list">
        <thead>
        <tr>
            <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox">
                <label for="cb-select-all-1"><span class="screen-reader-text">Select All</span></label>
            </td>
            <th id="name">Name</th>
            <th id="company">Company</th>
            <th id="phone">Phone</th>
            <th id="email">Email</th>
            <th id="date">Date</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(count($applyLists) > 0){
            foreach($applyLists as $v){
                $uInfo = json_decode($v['uInfo'], true);
        ?>
        <tr>
            <input type="hidden" value='<?=$v['uInfo']?>' id="uInfo-<?=$v['id']?>">
            <input type="hidden" value='<?=$v['pdInfo']?>' id="pdInfo-<?=$v['id']?>">
            <th scope="row" class="check-column">
                <input id="cb-select-<?=$v['id']?>" type="checkbox" name="post[]" value="<?=$v['id']?>">
                <label for="cb-select-<?=$v['id']?>">
                    <span class="screen-reader-text">Select <?=$uInfo['billing_first_name']?> <?=$uInfo['billing_last_name']?></span>
                </label>
            </th>
            <td><a href="#;" class="applyDetailView" appid="<?=$v['id']?>"><?=$uInfo['billing_first_name']?> <?=$uInfo['billing_last_name']?> </a></td>
            <td><?=$uInfo['billing_company']?></td>
            <td><?=$uInfo['billing_phone']?></td>
            <td><?=$uInfo['billing_email']?></td>
            <td><?=date("Y-m-d H:i:s", $v['addtime'])?></td>
        </tr>
        <?php
            }
        }
        ?>
        </tbody>
    </table>
    <div class="tablenav bottom">
        <div class="tablenav-pages"><span class="displaying-num"><?=$applyListsCount['total']?> items</span>
            <?php
            if($applyListsCount['current_page'] == 1){
                echo ' <span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>';
                echo ' <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>';
            }else{
                echo ' <a class="first-page button" href="/wp-admin/admin.php?page=rk_wvc_product_apply_lists&cp=1&s='.$_REQUEST['s'].'"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>';
                echo ' <a class="prev-page button" href="/wp-admin/admin.php?page=rk_wvc_product_apply_lists&cp='.($applyListsCount['current_page'] - 1).'&s='.$_REQUEST['s'].'"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>';
            }
            ?>
            <span class="screen-reader-text">Current Page</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text"><?=$applyListsCount['current_page']?> of <span class="total-pages"><?=$applyListsCount['pages']?></span></span></span>
            <?php
            if($applyListsCount['current_page'] == $applyListsCount['pages']){
                echo ' <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>';
                echo ' <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>';
            }else{
                echo ' <a class="next-page button" href="/wp-admin/admin.php?page=rk_wvc_product_apply_lists&cp='.($applyListsCount['current_page'] + 1).'&s='.$_REQUEST['s'].'"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>';
                echo ' <a class="last-page button" href="/wp-admin/admin.php?page=rk_wvc_product_apply_lists&cp='.$applyListsCount['pages'].'&s='.$_REQUEST['s'].'"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a></span>';
            }
            ?>
        </div>
    </div>
    </form>
</div>

<div id="wvcApplyDetailModal" class="modal wvcModal" style="width: 700px">
    <header style="padding-left: 20px;">
        <h3>Apply Details</h3>
    </header>
    <div class="content">
        <div style="margin: 20px;min-height:300px;">
            <div class="row-2">
                <div class="line1">
                    <label>Name:</label>
                    <div class="sj1">
                        <span class="fillInfo" prop="billing_first_name">
                        </span>
                        <span class="fillInfo" prop="billing_last_name">
                        </span>
                    </div>
                </div>
                <div class="line1">
                    <label>Company:</label>
                    <div class="fillInfo sj1" prop="billing_company">
                    </div>
                </div>
                <div class="line1">
                    <label>Phone:</label>
                    <div class="fillInfo sj1" prop="billing_phone">
                    </div>
                </div>
                <div class="line1">
                    <label>Email:</label>
                    <div class="fillInfo sj1" prop="billing_email">
                    </div>
                </div>
                <div class="line1">
                    <label>Address:</label>
                    <div class="sj1">
                        <span class="fillInfo" prop="billing_address_1"></span>
                        <span class="fillInfo" prop="billing_address_2"></span>
                    </div>
                </div>
                <div class="line1">
                    <label>Country:</label>
                    <div id="countryInfo" class="sj1">
                    </div>
                </div>
                <div class="line1">
                    <label>City:</label>
                    <div class="fillInfo" prop="billing_city">
                    </div>
                </div>
                <div class="line1">
                    <label>PostCode:</label>
                    <div class="fillInfo sj1" prop="billing_postcode">
                    </div>
                </div>
                <div class="line1">
                    <label>Notes:</label>
                    <div class="fillInfo sj1" prop="order_comments">
                    </div>
                </div>
            </div>
            <div class="row-2">
                <label>Sample Lists</label>
                <ul style="height: 400px;overflow: scroll" id="sampleLists">

                </ul>
            </div>
        </div>
        <div class="footer">
            <a class="button button-primary modalClose" for="wvcApplyDetailModal" style="margin-right: 30px;">Close</a>
        </div>
    </div>
</div>