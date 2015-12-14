<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
  <div class="text-intro"> <?php echo '活动主题：';?> <?php echo $output['adv_info']['title'];?></div>
</div>
<form method="GET">
  <input type="hidden" name="act" value="store_promotion_booth"/>
  <input type="hidden" name="op" value="adposition_apply"/>
  <input type="hidden" value="<?php echo intval($_GET['adv_id']);?>" name="adv_id"/>
  <table class="ncsc-default-table" >
    <thead>
      <tr>
        <th class="w50"></th>
        <th class="w300 tl">名称</th>
        <th>类型</th>
        <th>有效时间</th>
        <th class="w120">审核状态</th>
      </tr>
    </thead>
    <tbody>
      <?php if(!empty($output['list']) and is_array($output['list'])){?>
      <?php foreach ($output['list'] as $k=>$v){ ?>
      <tr class="bd-line">
          <td><div class="pic-thumb" style="display: none"><a href="index.php?act=goods&goods_id=<?php echo $v['goods_id']; ?>" target="_blank"><img src="<?php echo cthumb($v['goods_image'], 60,$_SESSION['store_id']);?>"></a></div></td>
        <td class="tl"><dl class="goods-name">
            <dt><a target="_blank" href="<?php echo urlShop('goods', 'index', array('goods_id' => $v['goods_id']));?>"><?php echo $v['goods_name'];?></a></dt>
            <dd><?php echo $v['act_name'];?></dd>
          </dl></td>
        <td><?php echo str_replace(array('groupbuy','p_xianshi','p_mansong','p_bundling'), array('抢购','限时折扣','满送','优惠套餐'), $v['item_cate']); ?></td>
        <td><?php echo date('Y-m-d',$v['start_time']);?>~<?php echo date('Y-m-d',$v['end_time']);?></td>
        <td><?php if($v['adp_apply_state']=='1'){
          			echo '已通过';
          		  }elseif(in_array($v['adp_apply_state'],array('0','3'))){
          		  	echo '审核中';
          		  }
          	?></td>
      </tr>
      <?php }?>
      <?php }else{?>
      <tr>
        <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
      </tr>
      <?php }?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="20"></td>
      </tr>
    </tfoot>
  </table>
</form>

  <div class="div-goods-select">
    <form method="GET">
      <input type="hidden" name="act" value="store_promotion_booth"/>
      <input type="hidden" name="op" value="adposition_apply"/>
      <input type="hidden" name="adp_id" value="<?php echo $_GET['adp_id'];?>"/>
      <table class="search-form">
        <tr>
          <th class="w250"><strong>选择参加推广的活动，勾选并提交平台审核</strong></th>
          <td class="w160"><input type="text" class="text w150" name="name" value="<?php echo $output['search']['name'];?>" placeholder="搜索活动名称"/></td>
          <td class="w70 tc"><label class="submit-border">
              <input type="submit" class="submit" value="提交"/>
            </label></td><td></td>
        </tr>
      </table>
    </form>
    <form method="POST" id="apply_form" onsubmit="ajaxpost('apply_form','','','onerror');" action="index.php?act=store_promotion_booth&op=adposition_apply_save">
      <input type="hidden" name="adp_id" value="<?php echo $_GET['adp_id'];?>"/>
      <?php if(!empty($output['pos']) and is_array($output['pos'])){?>
      <div class="search-result">
        <ul class="goods-list">
          <?php foreach ($output['pos'] as $po){?>
          <li>
              <div class="goods-thumb" style="display: none"><a href="<?php echo urlShop('goods', 'index', array('goods_id' => $goods['goods_id']));?>" target="_blank"></a></div>
            <dl class="goods-info">
              <dt>
                <input type="checkbox" value="<?php echo $po['id'];?>" class="vm" name="item_id[]"/>
                <input type="hidden"  value="<?php echo $po['type'];?>" class="vm" name="item_cate[]"/>
                <input type="hidden"  value="<?php echo $po['name'];?>" class="vm" name="item_name[]"/>
                <label><?php echo $po['name'];?></label>
              </dt>
              <dd>活动类型：<?php echo str_replace(array('groupbuy','p_xianshi','p_mansong','p_bundling'), array('抢购','限时折扣','满送','优惠套餐'), $po['type']); ?></dd>
            </dl>
          </li>
          <?php }?>
          <div class="clear"></div>
        </ul>
      </div>
      <div class="pagination"><?php echo $output['show_page'];?></div>
      <?php }else{?>
      <div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];//您尚未发布任何商品?></span></div>
      <?php }?>
      <?php if(!empty($output['pos']) and is_array($output['pos'])){?>
      <div class="bottom tc p10">
        <input type="submit" class="submit" style="display: inline; *display: inline; zoom: 1;" value="选择完毕，参加活动"/>
      </div>
      <?php }?>
    </form>
  </div>
