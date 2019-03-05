<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class System_model extends CI_Model
{
    /*
     * 表单或文档类型
     */
    //信息显示
    public function TypeM_selectMes($type)
    {
        $data = $this->db->query("SELECT id,TypNam,TypCTm,TypCPe,TypEls FROM type_mes WHERE TypSta = 0 and TypeFT=".$type."")->result_array();
        return $data;
    }
    //新增类型
    public function TypeM_addNew($Type,$data)
    {
        $this->db->insert($Type,$data);
        $data['NId'] = $this->db->insert_id();
        $data['rowNum'] = $this->db->affected_rows();
        
        return $data;
    }
    //删除数据
    public function TypeDel($id,$type)
    {
        $this->db->delete($type,array('id'=>$id));
    }
    //修改类型
    public function TypeM_Edit($Type,$data,$id)
    {
        $this->db->update($Type,$data,array('id'=>$id));
    }
    //查找类型流转信息
    public function TypeDomGet($TypeId){
        //获取工作流数量
        $sqlCheDom = "select c.RolNam  from type_mes a,circle_detailm b,role c where a.id = '".$TypeId."' and a.CirSmp = b.CirSmp and b.DepIdS = c.id order by a.id";
        $data = $this->db->query($sqlCheDom)->result_array();
        $data['RowNum'] = count($data);
        return $data;
    }
    /*
     * 部门及人员设置
     */
    //信息显示
    public function RoleM_selectMes()
    {
        $data = $this->db->query("SELECT id,RolNam,RolCTm,RolCPe,RolEls FROM role WHERE RolSta = 0")->result_array();
        return $data;
    }
    //新增部门
    public function RoleM_addNew($data)
    {
        $this->db->insert('role',$data);
    }
    //删除数据
    public function RoleDel($id)
    {
        $this->db->delete('role',array('id'=>$id));
        //验证是否删除成功
        $sql = "select id from role where id = '".$id."'";
        $query = $this->db->query($sql);
        $row = $query->row_array();
        if(isset($row))
        {
            return 0;
        }
        return 1;
    }
    //修改部门
    public function RoleM_Edit($data,$id)
    {
        $this->db->update('role',$data,array('id'=>$id));
    }
    //部门人员信息查询【无分配】
    public function SelPeoMes()
    {
        $data = $this->db->query('SELECT id,UseAcc,UsePeo FROM `user` WHERE RolSta = 0 and UseSta=1')->result_array();
        return $data;
    }
    //部门详情及人员信息查询
    public function SelPeoMesED($id)
    {
        //部门基本信息
        $sqlSel = 'select id,RolNam,RolCTm,RolCPe,RolEls from role where id = '.$id.'';
        $data['deptMes'] = $this->db->query($sqlSel)->result_array();
        //部门人员信息
            //查部门绑定的用户id
        $data['roleMesSelEd'] = $this->db->query('SELECT c.id,c.UseAcc,c.UsePeo FROM link_userol b,`user` c WHERE b.RolIdS = '.$id.' and b.UseIdS = c.id and b.DatSta = 0 and c.UseSta=1 ')->result_array();
            //部门未绑定的用户id
        $arrayMes = array();
        foreach($data['roleMesSelEd'] as $v)
        {
            $arrayMes[] = $v['id'];
        }
//      $data['array'] = $arrayMes;
        $data['roleMesSelNo'] = array();
        if(count($arrayMes))
        {
            $this->db->where_not_in('id', $arrayMes);
        }
        $this->db->where('UseSta',1);
        $this->db->where('RolSta',0);
        $this->db->select('id,UseAcc,UsePeo');
        $data['roleMesSelNo'] = $this->db->get('user')->result_array();
        
//      /*
//       * 根据部门id，查询：【check_userol】中的部门id为指定id的用户，并组建返回值
//       * 查询表【user】中部门分配状态为0的用户，并组建返回值
//       * */
//      $data['roleMesSelEd'] = $this->db->query('SELECT id,UseAcc,UsePeo FROM check_userol WHERE RolIdS = '.$id.' ')->result_array();
//      $this->db->where('UseSta',1);
//      $this->db->where('RolSta',0);
//      $this->db->select('id,UseAcc,UsePeo');
//      $data['roleMesSelNo'] = $this->db->get('user')->result_array();
////      $sql = $this->db->get_compiled_select('user');
////      echo $sql;
        return $data;
    }
    //删除旧的用户分配数据和分配信息
    public function RoleLinUse_Del($RoleId,$CirSmp)
    {
        //根据部门id删除用户分配标志，删除旧的部门关联数据
        /*
         * 根据部门ID查询表link_userol：查出此部门相关的用户id
         * 根据用户id删除用户的部门状态【将其改成未分配部门的状态】
         * 并根据用户id删除用户相关的部分信息
         * */
        //获取相关的用户id
        $query = $this->db->select('UseIdS,RolIdS');
        $this->db->where('RolIdS',$RoleId);
        $query = $this->db->get('link_userol');
//      $query = $this->db->select('UseIdS,RolIdS');
//      $this->db->where('RolIdS',$RoleId);
//      $sql = $this->db->get_compiled_select('link_userol');
//      echo $sql;
        foreach($query -> result() as $row)
        {
//          echo $row->UseIdS;
            //删除用户表的旧分配标志
            $this->db->where('id',$row->UseIdS);
            $this->db->update('user',array('RolSta'=>0));
            //删除旧部门关联数据
            $this->db->where('LinSmp !=', $CirSmp);
            $this->db->delete('link_userol',array('RolIdS'=>$RoleId));
        }
    }
    
    //保存部门人员设置
    public function RoleLinUse_Set($RoleId,$UsePeo,$UseAcc,$CirSmp)
    {
        /*
         * 根据用户名称，用户账号，查询：用户id
         * 重新分配用户分配标志
         * 重新创建用户信息和相关数据
         * */
        $sql = "SELECT id FROM `user` WHERE UseAcc='".$UseAcc."' and UsePeo='".$UsePeo."'";
        $UseId = $this->db->query($sql)->result_array();
        //更新用户表【创建新分配标志】
        $sqlUpdate = "UPDATE user set RolSta=1 where id=".$UseId[0]['id']."";
        $this->db->query($sqlUpdate);
        //更新关联表【创建新部门关联数据】
        $sqlInsert = "insert into link_userol (UseIdS,RolIdS,DatSta,LinSmp) VALUES(".$UseId[0]['id'].",".$RoleId.",0,'".$CirSmp."')";
        $this->db->query($sqlInsert);
        $RowNum = $this->db->affected_rows();
        if($RowNum){
            $data = 'success';
        }
        return $data;
    }
    /*
     * 功能实现---用户管理
     */
    //显示列表
    public function Account_ShowMes()
    {
        $sql = "select id,UseAcc,UsePeo,UsePho,UseTim,UseSta from user ";
        $data = $this->db->query($sql)->result_array();
        foreach( $data as &$v )
        {
            switch( $v['UseSta'] )
            {
                case 0:
                    $v['UseStaMes'] = '注册,未通过';
                    break;
                case 1:
                    $v['UseStaMes'] = '正常';
                    break;
                case 2:
                    $v['UseStaMes'] = '已注销';
                    break;
                case 3:
                    $v['UseStaMes'] = '删除';
                    break;
                default:break;
            }
        }
        return $data;
    }
    //显示详情
    public function Account_ShowDetail($MId)
    {
        $data = $this->db->query("select RolSta,UseAcc,UsePho,UseEls,UseSta,UsePeo,UseTim,UsePPe,UsePTm,UseCPe,UseCTm from user where id = '".$MId."'")->result_array();
        //如果已经分配部门则查找部门信息
        if($data[0]['RolSta'] == 1)
        {
            $sql_Dep = "select RolNam from Check_UseRol where UseIdS = '".$MId."'";
            $row = $this->db->query($sql_Dep)->result_array();
            $DepartMea = '';
            if(isset($row)){
                $DepartMea = $row[0]['RolNam'];
            }
        }
        
        foreach( $data as &$v )
        {
            if(isset($DepartMea)){
                $v['depart'] = $DepartMea;
            }
            
            switch( $v['UseSta'] )
            {
                case 0:
                    $v['UseStaMes'] = '已注册,未通过';
                    break;
                case 1:
                    $v['UseStaMes'] = '已通过';
                    break;
                case 2:
                    $v['UseStaMes'] = '已注销';
                    break;
                case 3:
                    $v['UseStaMes'] = '已删除';
                    break;
                default:break;
            }
        }
        return $data;
    }
    //账号动作【注销通过删除】
    public function Account_ChangeSta($MId,$MesData)
    {
        $this->db->where( 'id' , $MId );
        $this->db->update('user',$MesData);
        $data = $this->db->affected_rows();
        $this->db->delete('user',array('UseSta'=>3));
        return $data;
    }
    
}