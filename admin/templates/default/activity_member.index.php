<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
      <div class="item-title" >
      <h3><?php echo $_GET['act_name']?></h3>
      <ul class="tab-base">
        <li><a href="index.php?act=activity&op=index" ><span>活动管理</span></a></li>
        <li><a href="JavaScript:void(0);"  class="current" ><span>查看报名</span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="get" name="formSearch" id="formSearch">
    <input type="hidden" value="activity" name="act">
    <input type="hidden" value="member" name="op">
    <input type="hidden" value="<?php echo !empty($_GET['id'])?$_GET['id']:$output['act_m_apply_act_id'];?>" name="act_m_apply_act_id">
    <table class="tb-type1 noborder search">
      <tbody>
        <tr>
          <td><select name="search_field_name" >
              <option <?php if($output['search_field_name'] == 'member_name'){ ?>selected='selected'<?php } ?> value="member_name">会员名</option>
              <option <?php if($output['search_field_name'] == 'member_email'){ ?>selected='selected'<?php } ?> value="member_email">email</option>
               <!--v3-b11 手机号码-->

            </select></td>
          <td><input type="text" value="<?php echo $output['search_field_value'];?>" name="search_field_value" class="txt"></td>
          <td><select name="search_sort" >
              <option value=""><?php echo $lang['nc_sort']?></option>
              <option <?php if($output['search_sort'] == 'member_login_time desc'){ ?>selected='selected'<?php } ?> value="member_login_time desc">最后登陆</option>
              <option <?php if($output['search_sort'] == 'member_login_num desc'){ ?>selected='selected'<?php } ?> value="member_login_num desc">登录次数</option>
            </select></td>

          <td><select name="search_grade" >
              <option value='-1'>会员级别</option>
              <?php if ($output['member_grade']){?>
              	<?php foreach ($output['member_grade'] as $k=>$v){?>
              	<option <?php if(isset($_GET['search_grade']) && $_GET['search_grade'] == $k){ ?>selected='selected'<?php } ?> value="<?php echo $k;?>"><?php echo $v['level_name'];?></option>
              	<?php }?>
              <?php }?>
            </select></td>
          <td><a href="javascript:void(0);" id="ncsubmit" class="btn-search " title="<?php echo $lang['nc_query'];?>">&nbsp;</a>
            <?php if($output['search_field_value'] != '' or $output['search_sort'] != ''){?>
            <a href="index.php?act=activity&op=member&id=<?php echo $_GET['act_name']?>&act_name=<?php echo $_GET['act_name']?>" class="btns "><span><?php echo $lang['nc_cancel_search']?></span></a>
            <?php }?></td>
        </tr>
      </tbody>
    </table>
  </form><div style="text-align:right;"><a class="btns" href="javascript:void(0);" id="ncexport"><span><?php echo $lang['nc_export'];?>Excel</span></a>
  <table class="table tb-type2" id="prompt">
    <tbody>
      <tr class="space odd">
        <th colspan="12"><div class="title">
            <h5><?php echo $lang['nc_prompts'];?></h5>
            <span class="arrow"></span></div></th>
      </tr>
      <tr>
        <td><ul>
            <li><?php echo $lang['member_index_help1'];?></li>
            <li><?php echo $lang['member_index_help2'];?></li>
          </ul></td>
      </tr>
    </tbody>
  </table>
  <form method="post" id="form_member">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2 nobdb">
      <thead>
        <tr class="thead">
          <th>&nbsp;</th>
          <th colspan="2">会员</th>
          <th class="align-center"><span fieldname="logins" nc_type="order_by">登陆次数</span></th>
          <th class="align-center"><span fieldname="last_login" nc_type="order_by">最后登陆时间</span></th>
          <th class="align-center">积分</th>

          <th class="align-center">经验值</th>
          <th class="align-center">级别</th>
          <th class="align-center">报名时间</th>
          <th class="align-center">最后通知时间</th>
          <th class="align-center"><?php echo $lang['nc_handle']; ?></th>
        </tr>
      <tbody>
        <?php if(!empty($output['member_list']) && is_array($output['member_list'])){ ?>
        <?php foreach($output['member_list'] as $k => $v){ ?>
        <tr class="hover member">
          <td class="w24"><input type="checkbox" name='del_id[]' value="<?php echo $v['member_id']; ?>" class="checkitem"></td>
          <td class="w48 picture"><div class="size-44x44"><span class="thumb size-44x44"><i></i><img src="<?php if ($v['member_avatar'] != ''){ echo UPLOAD_SITE_URL.DS.ATTACH_AVATAR.DS.$v['member_avatar'];}else { echo UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('default_user_portrait');}?>?<?php echo microtime();?>"  onload="javascript:DrawImage(this,44,44);"/></span></div></td>
          <td><p class="name"><strong><?php echo $v['member_name']; ?></strong>(真实姓名: <?php echo $v['member_truename']; ?>)</p>
            <p class="smallfont">注册时间:&nbsp;<?php echo $v['member_time']; ?></p>
            
              <div class="im"><span class="email" >
                <?php if($v['member_email'] != ''){ ?>
                <a href="mailto:<?php echo $v['member_email']; ?>" class=" yes" title="<?php echo $lang['member_index_email']?>:<?php echo $v['member_email']; ?>"><?php echo $v['member_email']; ?></a><?php echo $v['member_email']; ?></span>
                <?php }else { ?>
                <a href="JavaScript:void(0);" class="" title="<?php echo $lang['member_index_null']?>" ><?php echo $v['member_email']; ?></a></span>
                <?php } ?>
                <?php if($v['member_ww'] != ''){ ?>
                <a target="_blank" href="http://web.im.alisoft.com/msg.aw?v=2&uid=<?php echo $v['member_ww'];?>&site=cnalichn&s=11" class="" title="WangWang: <?php echo $v['member_ww'];?>"><img border="0" src="http://web.im.alisoft.com/online.aw?v=2&uid=<?php echo $v['member_ww'];?>&site=cntaobao&s=2&charset=<?php echo CHARSET;?>" /></a>
                <?php } ?>
                <?php if($v['member_qq'] != ''){ ?>                
                <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $v['member_qq'];?>&site=qq&menu=yes" class=""  title="QQ: <?php echo $v['member_qq'];?>"><img border="0" src="http://wpa.qq.com/pa?p=2:<?php echo $v['member_qq'];?>:52"/></a>
                <?php } ?>
                <!--v3-b11 显示手机号码-->
               <?php if($v['member_mobile'] != ''){ ?>
               <div style="font-size:13px; padding-left:10px">&nbsp;&nbsp;<?php echo $v['member_mobile']; ?></div>
               <?php } ?>
              </div></td>
          <td class="align-center"><?php echo $v['member_login_num']; ?></td>
          <td class="w150 align-center"><p><?php echo $v['member_login_time']; ?></p>
            <p><?php echo $v['member_login_ip']; ?></p></td>
          <td class="align-center"><?php echo $v['member_points']; ?></td>

          <td class="align-center"><?php echo $v['member_exppoints'];?></td>
          <td class="align-center"><?php echo $v['member_grade'];?></td>
          <td class="align-center">
              <?php echo $v['act_m_apply_time']; ?>
          </td>
          <td class="align-center">
              <?php echo $v['act_m_apply_last_notice']; ?>
          </td>
     <td class="align-center"><a href="index.php?act=notice&op=notice&member_name=<?php echo ltrim(base64_encode($v['member_name']),'='); ?>">通知</a></td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr class="no_data">
          <td colspan="11"><?php echo $lang['nc_no_record']?></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot class="tfoot">
        <?php if(!empty($output['member_list']) && is_array($output['member_list'])){ ?>
        <tr>
        <td class="w24"><input type="checkbox" class="checkall" id="checkallBottom"></td>
          <td colspan="16">
          <label for="checkallBottom"><?php echo $lang['nc_select_all']; ?></label>
            &nbsp;&nbsp;<a href="JavaScript:void(0);" class="btn" onclick="if(confirm('<?php echo $lang['nc_ensure_del']?>')){$('#form_member').submit();}"><span>通知</span></a>
            <div class="pagination"> <?php echo $output['page'];?> </div></td>
        </tr>
        <?php } ?>
      </tfoot>
    </table>
  </form>
</div>
<script>
$(function(){
    
   $('#ncexport').click(function(){
    	$('input[name="op"]').val('export_step1');
    	$('#formSearch').submit();
    });
    
    
    $('#ncsubmit').click(function(){
    	$('input[name="op"]').val('member');$('#formSearch').submit();
    });	
});
</script>
