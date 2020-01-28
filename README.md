# User-Roles-Permission-Menu-TreeView
User Roles , Permission , Menu,Tree

I am using codeigniter 3 so below function will go in your controller so that you can fetch data which is needed to build module & permission

public function menu_module_permission() {
        $role_id=(int) $this->uri->segment(4 ,0);
        $role_name=$this->uri->segment(5,0);
        $data['role_name']=$role_name;
        $data['role_id']=$role_id;
        $this->load->model('Common');
        $data['modules'] = $this->common->get_records('permission', $column = '', $where = '', $start = '', $limit = '', $flag_total_count = "NO");
        $data['module_permission']=$this->common->get_row_records('SELECT * FROM `role_to_permission` WHERE role_to_permission.role_id='.$role_id.'');
        $data['main'] = 'menu_permisson';
        $this->load->vars($data);
        $this->load->view($this->dashboard);
}


..........................................................................................................

Your view will go like below
NOTE: do_save_module_per ajx when you check uncheck permission will be reflected accordingly.

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
<section class="custome-content-header">
    <div>
        Manage Menu & Module Permission 
        <small></small>
    </div>
</section>
<div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Menu Permission (<?php echo ucfirst($role_name_sel); ?>)</h3>
                </div>
                <div class="box-body">
                    <div id="event_result"></div>
                    <div id="treeview2">
                        <?php echo $this->AdminMenu->get_menu1($role_id); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Module Permission</h3>
                </div>
                <div class="box-body no-padding">
                    <table class="table">
                        <tr>
                            <th style="width: 10px">S.No</th>
                            <th>Module</th>
                            <th style="width: 40px">Permission</th>
                        </tr>
                        <?php $count = 1;
                        foreach ($modules as $key => $value) {
                            ?>
                            <tr>
                                <td><?php echo $count; ?></td>
                                <td><?php echo $modules[$key]['name']; ?></td>
                                <td>
                                  <?php if (inMultiArray($modules[$key]['permission_id'], $module_permission)) { ?>
                                       <input type="checkbox" class="minimal" id="rp_<?php echo $modules[$key]['permission_id'].'_'.$role_id;?>" checked="checked"/>
                                    <?php } else { ?>
                                        <input type="checkbox" class="minimal" id="rp_<?php echo $modules[$key]['permission_id'].'_'.$role_id;?>" name="modulepermisson" id="modulepermisson" />
                                    <?php } ?>
                                </td>
                            </tr> 
                            <?php $count++; ?>
                            <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

<script>
    $(function () {
        var tree = $("#treeview2");
        tree.jstree({
            plugins: ["checkbox", 'json_data'],
            core: {
                "themes": {
                    "icons": false,
                    "variant": "large",
                    "multiple": false,
                    "check_callback": true,
                }
            },
            "checkbox": {
                "keep_selected_style": false,
                "two_state": true,
            }
        });
        tree.jstree(true).open_all();
        $('li[data-checkstate="checked"]').each(function () {
            tree.jstree('check_node', $(this));
        });
        tree.jstree(true).close_all();

        $(function () {
            $('#treeview2').on('deselect_node.jstree Event', function (e, data) {
                var count_remaing = data.node.parents.length + 1;
                $("#total_count").val(count_remaing);
                var checked_menu_id = '';
                var parent_menu_id = '';
                if (data.node.children.length > 0) {
                    $.each(data.node.children, function (key, value) {
                        checked_menu_id += $("#" + value).attr('name') + ',';
                    }).join(',');
                    parent_menu_id = $("#" + data.node.id).attr('name');
                } else {
                    checked_menu_id = $("#" + data.node.id).attr('name');
                    parent_menu_id = $("#" + data.node.parent).attr('name');
                }

                var rid_c = "<?php echo $role_id; ?>";
                $.ajax({
                    url: base_url + 'Settings/do_save_menu_per',
                    type: 'POST',
                    dataType: 'json',
                    data: {mid: checked_menu_id, rid: rid_c, status: 'unchecked', 'parent': parent_menu_id, 'countr': $("#total_count").val()},
                    beforeSend: function () {
                        $(".faster_ajax_loader").css('display', 'block');
                    },
                    complete: function () {
                        $(".faster_ajax_loader").css('display', 'none');
                    },
                    success: function (response) {
                        $("#total_count").val(response.remainingChild);
                        showalert_mpop(response.msg, 'gobal_msg');
                    }
                });
            });
        });
        $(function () {
            $('#treeview2').on('select_node.jstree Event', function (e, data) {
                //console.log(data);
                var checked_menu_id = '';
                var parent_menu_id = '';
                if (data.node.children.length > 0) {

                    $.each(data.node.children, function (key, value) {
                        checked_menu_id += $("#" + value).attr('name') + ',';
                    }).join(',');
                    parent_menu_id = $("#" + data.node.id).attr('name');
                } else {
                    checked_menu_id = $("#" + data.node.id).attr('name');
                    parent_menu_id = $("#" + data.node.parent).attr('name');
                }

                var rid_c = "<?php echo $role_id; ?>";
                $.ajax({
                    url: base_url + 'Settings/do_save_menu_per',
                    type: 'POST',
                    dataType: 'json',
                    data: {mid: checked_menu_id, rid: rid_c, status: 'checked', 'parent': parent_menu_id},
                    beforeSend: function () {
                        $(".faster_ajax_loader").css('display', 'block');
                    },
                    complete: function () {
                        $(".faster_ajax_loader").css('display', 'none');
                    },
                    success: function (response) {
                        showalert_mpop(response.msg, 'gobal_msg');
                    }
                });
            });
        });
        $('.minimal').on('click', function(event){
            var is_chk_unch='';
            if(this.checked) {
                 is_chk_unch='y';
            }else{
               is_chk_unch='n';
            }
            
            $.ajax({
                url: base_url + 'Settings/do_save_module_per',
                type: 'POST',
                dataType: 'json',
                data: {is_chk: is_chk_unch, id: $(this).prop('id')},
                beforeSend: function () {
                    $(".faster_ajax_loader").css('display', 'block');
                },
                complete: function () {
                    $(".faster_ajax_loader").css('display', 'none');
                },
                success: function (response) {
                    showalert_mpop(response.msg, 'gobal_msg');
                }
            });
        });
   });
</script>
........................................................................................................
