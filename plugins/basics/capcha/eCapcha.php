<?php
	/*echo $lalala
	test();*/
Class eCapcha {
		
	private $name;
	private $list_1a;
	private $list_a1;
	private $list_img;
	private $capcha;
		
	public function __construct($t_name = 'capcha', $length = 5, $seed = 15) {
		
		$this->name = $t_name;
		
		$alpha = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');		
		$numbers = '1378387528865875332083814206171776691473035982534904287554687311595628638823537875937519577818577805321712268066130019278766111959092164201989380952572010654858632788659361533818279682303019520353018529689957736225994138912497217752834791315155748572424541506959508295331168617278558890750983817546374649393192550604009277016711390098488240128583616035637076601047101819429555961989467678374494482553797747268471040475346462080466842590694912933136770289891521047521620569660240580381501935112533824300355876402474964732639141992726042699227967823547816360093417216412199245863150302861829745557067498385054945885869269956909272107975093029553211653449872027559602364806654991198818347977535663698074265425278625518184175746728909777727938000816470600161452491921732172147723501414419735685481613611573525521334757418494684385233239073941433345477624168625189835694855620992192221842725502542568876717904946016534668049886272327917860857843838279679766814541009538837863609506800642251252051173929848';
		
		$this->list_img = array();
		$this->list_img['a'] = 15;
		$this->list_img['b'] = 8;
		$this->list_img['c'] = 3;
		$this->list_img['d'] = 6;
		$this->list_img['e'] = 16;
		$this->list_img['f'] = 20;
		$this->list_img['g'] = 17;
		$this->list_img['h'] = 13;
		$this->list_img['i'] = 22;
		$this->list_img['j'] = 12;
		$this->list_img['k'] = 1;
		$this->list_img['l'] = 2;
		$this->list_img['m'] = 24;
		$this->list_img['n'] = 11;
		$this->list_img['o'] = 14;
		$this->list_img['p'] = 18;
		$this->list_img['q'] = 19;
		$this->list_img['r'] = 4;
		$this->list_img['s'] = 21;
		$this->list_img['t'] = 5;
		$this->list_img['u'] = 23;
		$this->list_img['v'] = 7;
		$this->list_img['w'] = 9;
		$this->list_img['x'] = 26;
		$this->list_img['y'] = 10;
		$this->list_img['z'] = 25;
		
		$this->list_1a = $alpha;
		$this->list_a1 = array();
		
		for($i=0;$i<count($this->list_1a);$i++) {
			
			$index = $numbers[$i+$seed];
			$count = 0;
			//echo $i.':'.$index.'-'.count($alpha)."<br />\n";
			while($index>=count($alpha)) {				
				$index-=count($alpha);
				//echo $index."<br />\n";
				$count++;
				if ($count>20) { die('stop'); }
			}
					
			$this->list_1a[$i] = $alpha[$index];
			$this->list_a1[$alpha[$index]] = $i;
			array_splice($alpha, $index, 1);
					
		}
		
		$this->capcha = array();
		for ($i=0;$i<$length;$i++) {
			$this->capcha[] = rand(0,count($this->list_1a)-1);			
		}
		/*
		print_r($this->list_1a);
		print_r($this->list_a1);
		print_r($this->capcha);
		*/
	}
	
	private function convert_a1($index) {		
		return $this->list_a1[$index];
	}
	private function convert_1a($index) {		
		return $this->list_1a[$index];
	}
		
	public function show_capcha() {
				
		$nb = count($this->capcha);
		$images = '';
		for ($i=0;$i<$nb;$i++) {
			$letter = $this->convert_1a($this->capcha[$i]);
			$images .= '<img src="'.eMain::root_url().'plugins/basics/capcha/'.$this->list_img[$letter].'.png" width="20" height="20" />';
		}
		echo '
			<div class="capcha">				
				<label for="'.$this->name.'">'.eText::iso_htmlentities(eLang::translate('please rewrite these letters', 'ucfirst')).' :</label>
				'.$images.'
				<input type="text" id="'.$this->name.'" name="'.$this->name.'" value="" required="true" class="needed" /> 
				<input type="hidden" name="soluce_'.$this->name.'" value="'.implode($this->capcha).'" /> 
			</div>
			';	
	}
	
	public function check_capcha($string) {
		$string = strtolower(trim($string));
		$nb = strlen($string);
		$intstring = "";
		for ($c=0;$c<$nb;$c++) {
			$intstring.=$this->convert_a1($string[$c]);
		}
		return $intstring;
	}
}
?>