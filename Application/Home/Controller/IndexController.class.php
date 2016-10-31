<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function index(){
		$exam = D('exam')->select();
		$this->assign('exam', $exam);
		$this->assign('ac_index','active');
		$this->display();
	}
	public function p(){
		$post = I('post.');
		$Record = D('record');
		$data = array();
		$data['eid'] = $post['eid'];
		$data['uid'] = $post['uid'];
		$finish = D('finish');
		$data['fchecked'] = 0;
		$data['ftime'] = $post['ftime'];
		$data['frtime'] = date('Y-m-d H:i:s');
		if($finish->create($data, 1)){
			$res_fid = $finish->add();
		}
		else{
			echo "failed";
		}
		$data['fid'] = $res_fid;
		$score = 0;
		foreach ($post as $k => $v) {
			if($k=='eid'||$k=='uid')
				continue;
			$data['rtype'] = substr($k, 0, 1);
			$data['qid'] = substr($k, 1);
			if($data['rtype']=='T'){
				$data['isChecked'] = $v;
				$q = D('question')->where(array('qid'=>$data['qid']))->getField('qans');
				if($q == $v){
					$score = $score + 1;
				}
				$data['content'] = NULL;
				if($Record->create($data, 1)){
					$Record->add();
				}else{
					//echo "failed";
				}
			}
			if($data['rtype']=='D'){
				$istrue = true;
				foreach ($v as $key => $value) {
					$q = D('question')->where(array('qid'=>$data['qid']))->getField('qans');
					if(strpos($q, $value) === false){
						$istrue = false;
					}
					$data['isChecked'] = $value;
					if($Record->create($data, 1)){
						$Record->add();
						//echo "success";
					}else{
						//echo "failed";
					}
				}
				if($istrue){
					$score = $score + 2;
				}
			}
			if($data['rtype']=='K' || $data['rtype']=='J'){
				$data['content'] = $v;
				$data['isChecked'] = NULL;
				if($Record->create($data, 1)){
					$Record->add();
					//echo "success";
				}else{
					//echo "failed";
				}
			}
		}
		$finish->fchecked = $score;
		$finish->where(array('fid'=>$res_fid))->save();
		$this->success('提交成功', '/Home/Index/questionbank');
	}
	public function e(){
		$eid = I('get.eid');
		if(empty($eid)){
			$eid = 1;
		}
		$this->assign("time", "time_fun()");
		$title = D('exam')->where(array('eid'=>$eid))->getField('etitle');
		$this->assign('title', $title);
		$passed = D('vques')->where(array('eid'=>$eid))->getField('passed');
		$uid = session('logineduserid');

		if($passed == $uid && session('loginedusertype')=='C'){
			$this->error('你已经答过题了', '/Home/Index/index');//javascript:window.location.href=document.referrer;
		}else{
			$this->assign('uid', $uid);
			$map['eid'] = $eid;
			$this->assign('eid', $eid);
			$groups = D('groups');
			$res = $groups->relation(true)->select();
			$this->assign("groups", $res);
			$this->display();
		}

	}
	public function questionbank(){
		$exam = D('exam')->select();
		$this->assign('exam', $exam);
		$this->assign('ac_questionbank','active');
		$this->display();
	}
	public function resultbank(){
		$exam = D('vques')->where(array('passed'=>session('logineduserid')))->select();
		$this->assign('exam', $exam);
		$this->assign('ac_resultbank','active');
		$this->display();
	}
	public function custom(){
		$this->assign('ac_custom','active');
		$this->display();
	}
	public function about(){
		$this->assign('ac_about','active');
		$this->display();
	}
	public function contact(){
		$this->assign('ac_contact','active');
		$this->display();
	}
}