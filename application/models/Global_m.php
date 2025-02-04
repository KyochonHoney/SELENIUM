<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Global_m extends CI_Model
{
    function __construct()
    {
        parent::__construct();
		
    }
	// select
	function GET($table = '', $where = array(), $page = '', $offset = '')
	{
		if ( $page > 1 )
		{
			$limit = ($page-1) * $offset;
		}
		else
		{
			$limit = 0;
		}
		$this->db->order_by($table.".idx", "desc"); 
		if($limit && $offset)
		{
			$query = $this->db->get_where($table, $where, $limit, $offset);
		}
		else
		{
			$query = $this->db->get_where($table, $where);
		}
		$result = $query->result();
		return $result;
	}
	function JOIN_GET_LIST($table ='', $field = '', $where = '', $page = '', $offset = '', $order_by = 'idx', $type = '', $order_by_type = 'desc')
	{
		
		$limit_query = '';	
		if($type != 'count')
		{
			if ( $page > 1 )
			{
				$limit = ($page-1) * $offset;
			}
			else
			{
				$limit = 0;
			}
			if ( $page != '' AND $offset != '' )
			{
				$limit_query = ' LIMIT '.$limit.', '.$offset;
			}
		}
		$sql = "	SELECT ".$field." FROM ".$table." where 1 ".$where." order by ".$order_by." ".$order_by_type." ".$limit_query;
		

		$query = $this->db->query($sql);
		if($type == 'count')
		{
			$result = $query->num_rows();
		}
		else
		{
			$result = $query->result();
		}	

    	return $result;		
	}
	function GET_LIST($table = '', $where = '', $page = '', $offset = '')
	{
		if ( $page > 1 )
		{
			$limit = ($page-1) * $offset;
		}
		else
		{
			$limit = 0;
		}

		$limit_query = '';	
    	if ( $page != '' AND $offset != '' )
     	{
     		$limit_query = ' LIMIT '.$limit.', '.$offset;
     	}
		$sql = "	SELECT * FROM ".$table." where 1 ".$where." order by idx desc ".$limit_query;
		$query = $this->db->query($sql);
		$result = $query->result();
    	return $result;
	}
	function GET_ROW($table = '', $where = array())
	{	
		$query = $this->db->get_where($table, $where);
		$result = $query->row();
		return $result;
	}
	function GET_ALL($table = '', $where = array())
	{
		$query = $this->db->get_where($table, $where);
		$result = $query->result();
		return $result;
	}
	// update
	function PUT($table = '', $data = array(), $where = array())
	{
		$update = $this->db->update($table, $data, $where);
		return $update;	
	}
	// insert
	function POST($table = '', $data = array())
	{
		$this->db->set('reg_date', 'NOW()', FALSE);
		$insert = $this->db->insert($table, $data); 
		return $insert;
	}
	// delete
	function DELETE($table = '', $where = array())
	{
		$this->db->delete($table, $where); 
	}	
	function passing_time($datetime) {
		$time_lag = time() - strtotime($datetime);
		
		if($time_lag < 60) {
			$posting_time = "방금";
		} elseif($time_lag >= 60 and $time_lag < 3600) {
			$posting_time = floor($time_lag/60)."분 전";
		} elseif($time_lag >= 3600 and $time_lag < 86400) {
			$posting_time = floor($time_lag/3600)."시간 전";
		}  else {
			$posting_time = date("Y.m.d", strtotime($datetime));
		} 		
		return $posting_time;
	}	
	function get_client_ip() {
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
			$ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	function get_visitor_cnt($date)
	{
		
		$sql = "select count(*) as cnt from visitor where DATE_FORMAT(reg_date,'%Y-%m-%d') = ".$date;
		
		$query = $this->db->query($sql);
		$result = $query->row();
		return $result->cnt;
	}
	function get_keyword_cnt($keyword)
	{
		
		$sql = "select count(*) as cnt from board_data where 1 and (title like '%".$keyword."%' or content like '%".$keyword."%' or writer like '%".$keyword."%')";
		
		$query = $this->db->query($sql);
		$result = $query->row();
		return $result->cnt;
	}
	function get_visitor_total_cnt()
	{
		
		$sql = "select count(*) as cnt from visitor";
		
		$query = $this->db->query($sql);
		$result = $query->row();
		return $result->cnt;
	}


	function get_board_cnt($board_code)
	{
		$sql = "select count(*) as cnt from board_data where board_code = '".$board_code."'";
		$query = $this->db->query($sql);
		$result = $query->row();
		return $result->cnt;
	}

	function get_board_comment_cnt($board_code)
	{
		$sql = "select count(*) as cnt from board_comment where board_code = '".$board_code."'";
		$query = $this->db->query($sql);
		$result = $query->row();
		return $result->cnt;
	}
	function get_board_data_comment_cnt($idx)
	{
		$sql = "select count(*) as cnt from board_comment where parent_idx = '".$idx."'";
		$query = $this->db->query($sql);
		$result = $query->row();
		return $result->cnt;
	}
	
	function create_temp_password()
	{
		$comb = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		 $pass = array(); 
		 $combLen = strlen($comb) - 1; 
		 for ($i = 0; $i < 8; $i++) {
			 $n = rand(0, $combLen);
			 $pass[] = $comb[$n];
		 }
	     return implode($pass);
	}
	function get_popularity($config, $idx = null)
	{
		if($idx)
		{
			$where = ' and idx != "'.$idx.'"';
		}
		else
		{
			$where = '';
		}

		$sql = "select * from board_data where board_code = '".$config->board_code."' ".$where."  order by hits desc limit 5";
		$query = $this->db->query($sql);
		$result = $query->result();
		return $result;
	}
	function get_many_comment($config)
	{
		$sql = "		
		
		select a.*, b.cnt from board_data as a left outer join (SELECT count(*) as cnt, parent_idx FROM `board_comment` where board_code = '".$config->board_code."' group by parent_idx) as b on a.idx = b.parent_idx where a.board_code = '".$config->board_code."' order by b.cnt desc limit 5";
		$query = $this->db->query($sql);
		$result = $query->result();
		return $result;				
	}
	function content_preview($content)
	{
		$content = preg_replace("/<img[^>]+\>/i", "", $content);
		$content = preg_replace("!<iframe(.*?)<\/iframe>!is","",$content);
		$content = strip_tags($content);
		$suffix = '...';
		$str = $content;
		$len = 20000;
		$s = substr($content, 0, $len);
		$cnt = 0;
		for ($i=0; $i<strlen($s); $i++)
			if (ord($s[$i]) > 127)
				$cnt++;
		$s = substr($s, 0, $len - ($cnt % 3));
		if (strlen($s) >= strlen($str))
			$suffix = "";
		return $s . $suffix;
	}
	function get_main_popularity()
	{
		$sql = "select * from board_data where 1 and board_code != 'notice' order by hits desc limit 12";
		$query = $this->db->query($sql);
		$result = $query->result();
		return $result;
	}
	function getcertnum()
	{
		$cert = $this->create_temp_password();
		$check = $this->get_row("cert", array("code" => $cert));
		if($check)
		{
			$this->getcertnum();
		}
		else
		{
			return $cert;
		}
	}
	function get_member_level()
	{
		$idx = $this->session->userdata('idx');
		if($idx)
		{
			$admin = $this->get_row("admin", array("member_idx" => $idx));
			if($admin)
			{
				$level = 4;
			}
			else
			{
				$use = $this->get_row("use_info", array("date_format(limit_date, '%Y-%m-%d') >=" => date("Y-m-d"), "member_idx" => $idx));
				if($use)
				{
					$level = 3;
				}
				else
				{
					$level = 2;
				}
			}

		}
		else
		{
			$level = 1;
		}
		return $level;
	}
}