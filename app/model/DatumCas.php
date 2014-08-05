<?php

/**
 * Třída pro práci s časy
 *
 * @author Petr Stefan
 */
namespace App\Model;

use Nette;

class DatumCas extends Nette\Object{
    
    public function defaultHodinyOd()
    {
	if(date('i')<30)
	    return date('H:30:00');
	elseif(date('H')== 23)
	    return date('23:30:00');
	else 
	    return date('H:00:00', mktime(date('H')+1, 0, 0, date('m'), date('j'),date('Y')));
    }
    
    public function defaultHodinyDo()
    {
	if(date('i')<30)
	    return date('H:00:00', mktime(date('H')+1, 0, 0, date('m'), date('j'),date('Y')));
	elseif(date('H')== 23)
	    return date('23:59:00');
	else
	    return date('H:30:00', mktime(date('H')+1, 0, 0, date('m'), date('j'),date('Y')));
    }
    
    /**
     * 
     * @param string $od    čas zažátku 
     * @param string $do    čas konce
     * @param string $po    interval v hodinách
     * @param string $_24   zařadit 23:59

     * @return array	    pole hodin
     */
    public function rozsahHodin($_24 = false, $od = '00:00',$do = '23:30' ,$po = 0.5)
    {
	$tOd = strtotime($od);
	$tDo = strtotime($do);
	$time = array();
	$time[$od.':00'] = $od;
	while($tOd < $tDo){
	    
	    $tOd = strtotime('+'.($po*60).' minutes',$tOd);
	    $time[date('H:i:s', $tOd)] = date('H:i', $tOd);
	}
	($_24)? ($time['23:59:00'] = '23:59'):''; 
	return $time;
    }
    
    /**
     * 
     * @param int $rok		rok
     * @param int $mesic	číslo měsíce
     * @return array		pole dnů v daném měsící pro daný rok
     */
    public function dnyVmesici($rok = NULL, $mesic = NULL)
    {
//	($rok == NULL)? $rok = date('Y'):'';
//	(((($rok%4)==0)&&((($rok%100)!=0) || (($rok%400)==0))))? $unor='29':$unor='28';
//	$dnyVMesici = array(1=>31,2=>$unor,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);
	if($rok == NULL && $mesic == NULL){
	   $pocetDnu = date('t');
	}else{
	    $pocetDnu = date('t',  mktime(0,0,0,$mesic,1,$rok));
	}
	for($i=1; $i<=$pocetDnu; $i++)
	    $dny[$i] = $i;
	return $dny;
	
    }
    
    /**
     * 
     * @return array  pole s měsíci 
     */
    private function mesiceVroce()
    {
	for($i=1; $i<=12; $i++)
	    $mesice[$i] = $i;
	return $mesice;
    }
}