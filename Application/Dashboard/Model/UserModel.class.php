<?php
namespace Dashboard\Model;
use Think\Model;
class UserModel extends Model
{
    protected $tableName = 'user';
    // 对象的数据表
    protected $trueTableName = 'es_user';
    
    // 字段限定信息
    protected $_validate = array(
        array(
            'ustunum',
            'require',
            '用户名必须！'
        ), // 默认情况下用正则进行验证
        array(
            'upwd',
            'require',
            '密码必须！'
        ),
        array(
            'ustunum',
            '',
            '帐号名称已经存在！',
            0,
            'unique',
            1
        ),
    ); // 在新增的时候验证name字段是否唯一
      // array('value',array(1,2,3),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
      // array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
      // array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式
    /**
     * 自动完成
     */
    protected $_auto = array (
        array('upwd', 'md5', 3, 'function') , // 对password字段在新增和编辑的时候使md5函数处理
    );
    public function getUserIdByUserName($nickname)
    {
        $cond['ustunum']=$nickname;
        $r = $this->where($cond)->getField('uid');
        return $r;
    }
    public function getUserTypeByUserName($nickname)
    {
        $cond['ustunum']=$nickname;
        $r = $this->where($cond)->getField('utype');
        return $r;
    }
    /**
     * *
     *
     * @param string $nickname            
     * @param string $pwd            
     * @param string $userimg            
     * @return bool
     */
    public function doUserRegister($nickname, $pwd, $realname)
    {
        $data['ustunum'] = $nickname;
        $data['upwd'] = $pwd;
        // return $this->add($data);
        // create用来验证
        if ($this->create($data)) {
            return $this->add();
        }
        echo $this->getError();
        return false;
    }

    public function doChangePwd($nickname, $newPwd)
    {
        // 4.修改
        $data['upwd'] = $newPwd;
        $data['rpwd'] = $newPwd;
        if($this->create($data))
        {
            return $this->where(array('ustunum' => $nickname))->save();
        }
        return false;
    }
    public function inituser($uid)
    {
        $cond['uid'] = $uid;
        $ustunum = $this->where($cond)->getField('ustunum');
        var_dump($ustunum);
        $data['rpwd'] = substr($ustunum, -6);
        $data['upwd'] = $data['rpwd'];
        //var_dump($data);
        if ($this->create($data)) {
            return $this->where($cond)->save();
        }
        return false;
    }
    public function deluser($uid)
    {
        $cond['uid'] = $uid;
        D('record')->where($cond)->delete();
        D('finish')->where($cond)->delete();
        $res = $this->delete($uid);
        return $res;
    }
    public function isValidUser($nickname, $pwd)
    {
        $count = $this->where(array(
            'ustunum' => $nickname,
            'upwd' => md5($pwd),
        ))->count();
        return $count;
    }
}