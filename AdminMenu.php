<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class AdminMenu extends CI_Model {
    public $side_bar_menu = '';
    public function __construct() {
        parent::__construct();
    }
    public function get_menu($role_id) {
        $this->db->select('ms.menu_id as id,ms.menu_name,ms.menu_title,ms.type,ms.link,ms.is_active,ms.parent as parentid,ms.icons,rm.is_active,rm.role_id');
        $this->db->from('menu_settings ms');
        $this->db->join('role_menus rm', 'rm.menu_id =  ms.menu_id', 'INNER');
        $this->db->group_by('ms.menu_id','desc'); 
        $this->db->where('ms.menu_id !=',1); 
        $query = $this->db->get();
        $rResult_array = $query->result_array();
        $menu = array(
            'menus' => array(),
            'parent_menus' => array(),
        );
        foreach ($rResult_array as $key => $value) {
            $menu['role_id'][$rResult_array[$key]['role_id']] = $rResult_array[$key]['role_id'];
            $menu['menus'][$rResult_array[$key]['id']] = $value;
            $menu['parent_menus'][$rResult_array[$key]['parentid']][] = $rResult_array[$key]['id'];
        }
        
        $this->load->model('Common');
        $all_roles_selected = $this->Common->get_records('role_menus', array('menu_id'), array('role_id'=>$role_id), $start = '', $limit = '', $flag_total_count = "NO");
        $side_bar_menu = $this->buildMenu1(0, $menu,$all_roles_selected);
        return $side_bar_menu;
    }
    public function buildMenu($parent, $menu,$all_roles_selected) {
        $html = "";
        if (isset($menu['parent_menus'][$parent])) {

            if ($parent <= 0)
                $html .= "<ul class=''>";
            else
                $html .= '<ul class="">';

            $countfor_active = 0;
            foreach ($menu['parent_menus'][$parent] as $menu_id) {
                    $li_parentclass='menu_'.$menu_id;
                    if (!isset($menu['parent_menus'][$menu_id])) {
                         if(inMultiArray($menu_id, $all_roles_selected)){
                             $html .= "<li data-checkstate='checked' name='" . $li_parentclass . "' class='litreeclcick'><span>" . $menu['menus'][$menu_id]['menu_name'] . "</span></li>";
                         }else{
                             $html .= "<li data-checkstate='unchecked' name='" . $li_parentclass . "' class='litreeclcick'><span>" . $menu['menus'][$menu_id]['menu_name'] . "</span></li>";
                         }
                    }
                    if (isset($menu['parent_menus'][$menu_id])) {
                        if(inMultiArray($menu_id, $all_roles_selected)){
                            $html .= "<li data-checkstate='' name='" . $li_parentclass . "' class='litreeclcick'><span>" . $menu['menus'][$menu_id]['menu_name'] . "</span>";
                        }  else {
                            $html .= "<li data-checkstate='unchecked' name='" . $li_parentclass . "' class='litreeclcick'><span>" . $menu['menus'][$menu_id]['menu_name'] . "</span>";
                        }
                        $html .= $this->buildMenu1($menu_id, $menu,$all_roles_selected);
                        $html .= "</li>";
                    } 
            }
            $html .= "</ul>";
        }
        return $html;
    }
    
    public function get_permission($role_id) {
        $this->db->select('t2.name as rolePremission');
        $this->db->from('role_to_permission t1');
        $this->db->join('permission t2', 't1.permission_id = t2.permission_id', 'INNER');
        $this->db->where('t1.role_id', $role_id);
        $query = $this->db->get();
        $permissions = array();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            foreach ($result as $key => $value) {
                $permissions[$value["rolePremission"]] = TRUE;
            }
        }
        return $permissions;
    }
    public function hasPrivilege($request_permisson,$permission) {
        if (array_key_exists($request_permisson, $permission)) {
            return TRUE;
        }else{
           return NULL;
        }
    }
}
