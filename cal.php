<?php

class paulCalendar
{
	private $baseName="calendar.php";
	private $targetUrl;
	private $month;
	private $year;
	private $cellspacing=0;
	private $cellpadding=5;
	private $border=1;
	private $days_in_month=array(0,31,28,31,30,31,30,31,31,30,31,30,31);
	private $month_names=array('','January','February','March','April','May','June','July','August',
					'September','October','November','December');
	private $is_leapyear=false;

	function __construct() {
		$this->setMonth(date('m'));
		$this->setYear(date('Y'));
	}
	function setCellSpacing($int) {
		$this->cellspacing=$int;
	}
	function setCellPadding($int) {
		$this->cellpadding=$int;
	}
	function setBorder($int) {
		$this->border=$int;
	}
	function setTargetUrl($url) {
		$this->targetUrl=$url;
	}
	function setBaseName($name) {
		$this->baseName=$name;
	}
	function setMonth($m) {
		if($m >0 AND $m<13) {
			$this->month=$m;
		}
	}
	function setYear($y) {
		$this->year=$y;
		$this->checkLeapYear();
		if($this->is_leapyear) {
			$this->days_in_month[2]=29;
		} else {
			$this->days_in_month[2]=28;
		}
	}	
	function checkLeapYear() {
		if($this->year%4 == 0) {
			$this->is_leapyear=true;
		}
		if($this->year%100 == 0) {
			if($this->year%400 == 0) {
				$this->is_leapyear=false;
			} else {
				$this->is_leapyear=true;
			}
		}
	}
	function output() {
		$monthName=$this->month_names[$this->month];
		$title=$monthName." ".$this->year;
		// Year Navigation Stuff
		$ly_year=$this->year-1;
		$ny_year=$this->year+1;
		$ly_month=$this->month;
		$ny_month=$this->month;
		$last_year=sprintf("<a href=\"%s?year=%d&month=%d\">&lt;&lt;</a>",$this->baseName,$ly_year,$ly_month);
		$next_year=sprintf("<a href=\"%s?year=%d&month=%d\">&gt;&gt;</a>",$this->baseName,$ny_year,$ny_month);
		// Month Navigation Stuff
		$nm_month=$this->month+1;
		$nm_year=$this->year;
		if($nm_month > 12) {
			$nm_month=1;
			$nm_year+=1;
		}
		$lm_month=$this->month-1;
		$lm_year=$this->year;
		if($lm_month < 1) {
			$lm_month=12;
			$lm_year-=1;
		}
		$last_month=sprintf("<a href=\"%s?year=%d&month=%d\">&lt;</a>",$this->baseName,$lm_year,$lm_month);
		$next_month=sprintf("<a href=\"%s?year=%d&month=%d\">&gt;</a>",$this->baseName,$nm_year,$nm_month);
		$table="<table cellpadding=\"{$this->cellpadding}\" cellspacing=\"{$this->cellspacing}\" border=\"{$this->border}\">\n";
		$table.="<tr><td>{$last_year}</td><td>{$last_month}</td><td colspan=\"3\">{$title}</td><td>{$next_month}</td><td>{$next_year}</td>\n";
		$table.="<tr><td>Mon</td><td>Tue</td><td>Wed</td><td>Thu</td><td>Fri</td><td>Sat</td><td>Sun</td></tr>\n";
		// Month Grid
		$tmpDate=mktime(12,12,12,$this->month,1,$this->year);
		$DOW = date('w',$tmpDate);
		if($DOW == 0) {
			$DOW = 7;
		}
		for($i=1; $i <= $this->days_in_month[$this->month]; $i++) {
			$linkArray[]=sprintf("<a href=\"%s?year=%d&month=%d&day=%d\">%d</a>",$this->targetUrl,$this->year,$this->month,$i,$i);
		}
		//print "<pre>"; var_dump($linkArray); exit();
		for($i=1; $i<$DOW; $i++) {
			array_unshift($linkArray,"&nbsp;");
		}
		while(count($linkArray)%7 != 0) {
			array_push($linkArray,"&nbsp;");
		}
		$count=0;
		$table.="<tr>";
		foreach($linkArray as $link) {
			$count++;
			$table.="<td>{$link}</td>";
			if($count%7 == 0) {
				$table.="</tr>\n<tr>";
			}
		}
		$table.="</tr>\n";

		$table.="</table>\n";
		return $table;

		
	}
}
