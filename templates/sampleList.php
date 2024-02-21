<div style="font-size: 16px;">
    <span>Sample List</span>
</div>
<div>
    <ul class="wvcSampleList">
        <?php
        $index = 0;
        foreach($sampleList as $row){
        ?>
        <li>
            <h5><?=$row['title']?></h5>
            <div><?=$row['spec']?></div>
            <div><?=$row['brand']?> <?=$row['weight']?></div>
            <a class="icon remove" index="<?=$index++?>">
                <span class="btIco btIcoDefaultType btIcoSmallSize btIcoDefaultColor btIconCircleShape"><span data-ico-fa="" class="btIcoHolder"></span></span>
            </a>
        </li>
        <?php }?>
    </ul>
    <a href="#;" class="btBtn btnOutlineStyle btnNormalColor btnSmall btnFullWidth btnRightPosition wvc_apply_btn" style="text-align: center !important;display: block; margin-left: 17px !important;">Get Sample</a>
</div>
