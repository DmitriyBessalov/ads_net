<?php
//$start = microtime(true);
class SxGeo {
	protected $fh;
	protected $ip1c;
	protected $info;
	protected $range;
	protected $db_begin;
	protected $b_idx_str;
	protected $m_idx_str;
	protected $b_idx_arr;
	protected $m_idx_arr;
	protected $m_idx_len;
	protected $db_items;
	protected $country_size;
	protected $db;
	protected $regions_db;
	protected $cities_db;

	public function __construct($db_file = 'SxGeoCity.dat', $type = 0){
		$this->fh = fopen($db_file, 'rb');
		// Сначала убеждаемся, что есть файл базы данных
		$header = fread($this->fh, 40); // В версии 2.2 заголовок увеличился на 8 байт
		if(substr($header, 0, 3) != 'SxG') die("Can't open {$db_file}\n");
		$info = unpack('Cver/Ntime/Ctype/Ccharset/Cb_idx_len/nm_idx_len/nrange/Ndb_items/Cid_len/nmax_region/nmax_city/Nregion_size/Ncity_size/nmax_country/Ncountry_size/npack_size', substr($header, 3));
		if($info['b_idx_len'] * $info['m_idx_len'] * $info['range'] * $info['db_items'] * $info['time'] * $info['id_len'] == 0) die("Wrong file format {$db_file}\n");
		$this->range       = $info['range'];
		$this->b_idx_len   = $info['b_idx_len'];
		$this->m_idx_len   = $info['m_idx_len'];
		$this->db_items    = $info['db_items'];
		$this->id_len      = $info['id_len'];
		$this->block_len   = 3 + $this->id_len;
		$this->max_region  = $info['max_region'];
		$this->max_city    = $info['max_city'];
		$this->pack        = $info['pack_size'] ? explode("\0", fread($this->fh, $info['pack_size'])) : '';
		$this->b_idx_str   = fread($this->fh, $info['b_idx_len'] * 4);
		$this->m_idx_str   = fread($this->fh, $info['m_idx_len'] * 4);

		$this->db_begin = ftell($this->fh);
			$this->b_idx_arr = array_values(unpack("N*", $this->b_idx_str)); // Быстрее в 5 раз, чем с циклом
			unset ($this->b_idx_str);
			$this->m_idx_arr = str_split($this->m_idx_str, 4); // Быстрее в 5 раз чем с циклом
			unset ($this->m_idx_str);
		$this->info = $info;
		$this->info['regions_begin'] = $this->db_begin + $this->db_items * $this->block_len;
		$this->info['cities_begin']  = $this->info['regions_begin'] + $info['region_size'];
	}

	protected function search_idx($ipn, $min, $max){
			while($max - $min > 8){
				$offset = ($min + $max) >> 1;
				if ($ipn > $this->m_idx_arr[$offset]) $min = $offset;
				else $max = $offset;
			}
			while ($ipn > $this->m_idx_arr[$min] && $min++ < $max){};
		return  $min;
	}

	protected function search_db($str, $ipn, $min, $max){
		if($max - $min > 1) {
			$ipn = substr($ipn, 1);
			while($max - $min > 8){
				$offset = ($min + $max) >> 1;
				if ($ipn > substr($str, $offset * $this->block_len, 3)) $min = $offset;
				else $max = $offset;
			}
			while ($ipn >= substr($str, $min * $this->block_len, 3) && ++$min < $max){};
		}
		else {
			$min++;
		}
		return hexdec(bin2hex(substr($str, $min * $this->block_len - $this->id_len, $this->id_len)));
	}

	public function get_num($ip){
		$ip1n = (int)$ip; // Первый байт
		if($ip1n == 0 || $ip1n == 10 || $ip1n == 127 || $ip1n >= $this->b_idx_len || false === ($ipn = ip2long($ip))) return false;
		$ipn = pack('N', $ipn);
		$this->ip1c = chr($ip1n);
		$blocks = array('min' => $this->b_idx_arr[$ip1n-1], 'max' => $this->b_idx_arr[$ip1n]);

		if ($blocks['max'] - $blocks['min'] > $this->range){
			// Ищем блок в основном индексе
			$part = $this->search_idx($ipn, floor($blocks['min'] / $this->range), floor($blocks['max'] / $this->range)-1);
			// Нашли номер блока в котором нужно искать IP, теперь находим нужный блок в БД
			$min = $part > 0 ? $part * $this->range : 0;
			$max = $part > $this->m_idx_len ? $this->db_items : ($part+1) * $this->range;
			// Нужно проверить чтобы блок не выходил за пределы блока первого байта
			if($min < $blocks['min']) $min = $blocks['min'];
			if($max > $blocks['max']) $max = $blocks['max'];
		}
		else {
			$min = $blocks['min'];
			$max = $blocks['max'];
		}
		$len = $max - $min;
		// Находим нужный диапазон в БД-
        fseek($this->fh, $this->db_begin + $min * $this->block_len);
        return $this->search_db(fread($this->fh, $len * $this->block_len), $ipn, 0, $len);

	}

	protected function readData($seek, $max, $type){
		$raw = '';
		if($seek && $max) {
            fseek($this->fh, $this->info[$type == 1 ? 'regions_begin' : 'cities_begin'] + $seek);
            $raw = fread($this->fh, $max);
		}
		return $this->unpack($this->pack[$type], $raw);
	}

	protected function parseCity($seek, $full = false){
			$city = $this->readData($seek, $this->max_city, 2);
			$region = $this->readData($city['region_seek'], $this->max_region, 1);
			return $region['iso'];
	}

	protected function unpack($pack, $item = ''){
		$unpacked = array();
		$empty = empty($item);
		$pack = explode('/', $pack);
		$pos = 0;
		foreach($pack AS $p){
			list($type, $name) = explode(':', $p);
			$type0 = $type{0};
			if($empty) {
				$unpacked[$name] = $type0 == 'b' || $type0 == 'c' ? '' : 0;
				continue;
			}
			switch($type0){
				case 'T': $l = 1; break;
				case 'n':
				case 'S': $l = 2; break;
				case 'm':
				case 'M': $l = 3; break;
				case 'd': $l = 8; break;
				case 'c': $l = (int)substr($type, 1); break;
				case 'b': $l = strpos($item, "\0", $pos)-$pos; break;
				default: $l = 4;
			}
			$val = substr($item, $pos, $l);
			switch($type0){
				case 'S': $v = unpack('S', $val); break;
				case 'M': $v = unpack('L', $val . "\0"); break;
				case 'T':
				case 'n':
				case 'N': $v = ''; break;
				case 'b': $v = $val; $l++; break;
			}
			$pos += $l;
			$unpacked[$name] = is_array($v) ? current($v) : $v;
		}
		return $unpacked;
	}

	public function getCityFull($ip){
		$seek = $this->get_num($ip);
		return $seek ? $this->parseCity($seek, 1) : false;
	}
}
$SxGeo = new SxGeo();

echo $SxGeo->getCityFull($_GET['ip']);
//echo  microtime(true) - $start;