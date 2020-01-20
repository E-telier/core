<div class="calendar">
<?php
	
	if (!isset($datas_calendar)) {
		$datas_calendar = array(
			'calendar_input_id'=>'date',
			'input_title'=>eLang::translate('date', 'ucfirst'),
			'selected'=>date('Y-m-d'),
			'start_date'=>date('Y-m').'-01',
			'end_date'=>date('Y-m-d', strtotime(date('Y-m').'-01'.' +1 year'))
		);
		
		$datas = array();
		if (isset($_GET['calendar_input_id'])) {
			$datas = $_GET;
		}
		if (isset($_POST['calendar_input_id'])) {
			$datas = $_POST;
		}
		
		foreach ($datas as $key => $value){
			$datas_calendar[$key] = $datas[$key];
		}
	}
	
	$start_date = $datas_calendar['start_date'];	
	$day = date('D', strtotime($start_date));
	
	$show_month = intval(substr($start_date, 5, 2));
	
	while($day!='Mon') {
		$start_date = date('Y-m-d', strtotime($start_date.' -1 day'));
		$day = date('D', strtotime($start_date));
	}
	
	$end_date = $datas_calendar['end_date'];	
	$day = date('D', strtotime($end_date));
	while($day!='Sun') {
		$end_date = date('Y-m-d', strtotime($end_date.' +1 day'));
		$day = date('D', strtotime($end_date));
	}
	
	$current_date = $start_date;
	$current_year = intval(substr($current_date, 0, 4));
	$current_month = intval(substr($current_date, 5, 2));	
	$current_day = intval(substr($current_date, 8));
	$current_weeknum = date('W', strtotime($current_date));
	
	$months = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december');
		
	function get_month_days($month_num, $current_year='') {
		
		if ($current_year=='') { $current_year = date('Y'); }
		
		$month_days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		
		$nb = $month_days[$month_num-1];
		
		if ($nb==28 && ($current_year%4)==0) {
			$nb = 29;
		}
		
		return $nb;
		
	}
	
	function merge_date($year, $month, $day) {
		if ($month<10) {
			$month = '0'.$month;
		}
		if ($day<10) {
			$day = '0'.$day;
		}
		
		return $year.'-'.$month.'-'.$day;
	}
	
	$weekdays = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
	$week_name = strtoupper(substr(eLang::translate('week'), 0, 1));
	
	$date = substr($current_date, 6);
	
	$i = 0;
	while ($current_date<=$end_date) {
		$i++;
		
		$show_year = $current_year;
		if ($current_month==12 && $current_day>1) { $show_year++; }
?>
	<div class="month month_<?php echo $show_month; ?>">
		<h3 class="month_name"><?php eLang::show_translate($months[$show_month-1]); ?> <?php echo $show_year; ?></h3>
		<div class="week weekdays">
<?php
		for ($d=0;$d<7;$d++) {
?>
			<div class="day"><?php echo strtoupper(substr(eLang::translate($weekdays[$d]), 0, 1)); ?></div>
<?php
		}
?>
		</div>
<?php
		for ($w=0;$w<6;$w++) {
?>
		<div class="week">
			<div class="weeknum"><?php echo $week_name.' '.$current_weeknum; ?></div>
<?php
			$current_monday = merge_date($current_year, $current_month, $current_day);
			for ($d=0;$d<7;$d++) {
				$current_date = merge_date($current_year, $current_month, $current_day);
?>
			<div class="day date_<?php echo str_replace('-', '', $current_date); ?> <?php if ($current_month==$show_month && $datas_calendar['selected']==$current_date) { ?>selected<?php } ?> <?php if ($current_month==$show_month) { ?>current_month<?php } ?> <?php if ($d>=5) { ?>weekend<?php } ?>">
				<div class="num"><?php echo $current_day; ?></div>
				<div class="date"><?php echo $current_date; ?></div>
			</div>
<?php				
				$current_day++;
				if ($current_day>get_month_days($current_month, $current_year)) {
					$last_monday = $current_monday;
					$last_weeknum = $current_weeknum;
					$current_day = 1;
					$current_month++;
					if ($current_month>12) { 
						$current_month = 1; 
						$current_year++;
					}
				}
			}
?>
		</div>
<?php
			$current_weeknum++;
			if ($current_weeknum>52) { $current_weeknum = 1; }
		}
		
		$current_year = intval(substr($last_monday, 0, 4));
		$current_month = intval(substr($last_monday, 5, 2));
		$current_day = intval(substr($last_monday, 8));
		$current_weeknum = $last_weeknum;
		
		$show_month++;
		if ($show_month>12) { 
			$show_month = 1; 
		}
?>
	</div>
<?php
	}	
?>
</div>
<script type="text/javascript">
<!--
	var calendar_input_id = '<?php echo $datas_calendar['calendar_input_id']; ?>'; 
	if (typeof $!=='undefined') {
		$(document).ready(function() {
			initCalendar();
		});
	} else {
		var head = document.head;
		var script = document.createElement('script');
		script.type = 'text/javascript';
		var path = window.location.href;
		if (path.indexOf('plugins')>=0) { path = path.substring(0, path.indexOf('plugins')); }
		path += 'js/jquery-3.0.0.min.js';		
		script.src = path;

		// Then bind the event to the callback function.
		// There are several events for cross browser compatibility.
		script.onreadystatechange = initCalendar;
		script.onload = initCalendar;

		// Fire the loading
		head.appendChild(script);
	}
	
	function initCalendar() {	
		console.log('initCalendar calendar');		
		myECalendar = new eCalendar();
		myECalendar.init();
	}
	
	function eCalendar() {
		
		this.callback = null;
		this.defaultSelector = '.week:not(.weekdays) .day.current_month';
		this.selector = '.week:not(.weekdays) .day.current_month:not(.unselectable)';
		
		this.init = function() {
			this.setSelector(this.selector);
		}
		
		this.setSelector = function(tSelector) {			
			console.log('setSelector '+tSelector);
			$('.calendar').off('click');
			
			this.selector = tSelector;
			
			var self = this;
			$('.calendar').on('click', this.selector, function() {	
				//console.log(self.selector+' '+this.className+' '+$(this).hasClass('unselectable'));
				self.selectDay(this);
			});			
		}
		
		this.selectDay = function(tDom) {
			console.log(tDom+' '+$(tDom).find('.date').text());
			$('.day.selected').removeClass('selected');
			$(tDom).addClass('selected');
			$('#'+calendar_input_id).val($(tDom).find('.date').text());
			
			if (this.callback!=null) {
				this.callback();
			}
		}
		
	}
	
	
-->
</script>