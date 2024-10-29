<?
/*
  update: askie 
Homepage: http://www.pkphp.com
   email: askie@sohu.com
*/
class AB_Alexa 
{
    var $badclasses;
    var $site;
    var $cache;

    function alexa() 
    {
        preg_match_all('#\.([a-z0-9]+) \{#i',file_get_contents('http://client.alexa.com/common/css/scramble.css'),$this->badclasses);        
    }
    function descramble ($matches) {
        return @in_array($matches[1],$this->badclasses[1])?'':$matches[2];
    }
    function stats ($site, $section = 4, $item = 5) 
    {
       if (!is_array($this->cache[$site])) 
       {
            $items = array();
            $html = preg_replace('#&nbsp;#','',preg_replace('#(?:<|<)!--.+?--(?:>|>)#','',file_get_contents('http://www.alexa.com/data/details/traffic_details/' . $site)));
            preg_match_all('#</th></tr><tr>.+?</tr>#',$html,$tables);
           
            for ($i=0,$len=count($tables[0]); $i<$len; $i++) 
            {
                preg_match_all('#<td>(.+?)</td>#',$tables[0][$i],$info);
                for ($i2=0,$len2=count($info[1]); $i2<$len2; $i2++) 
                {
                    $info[1][$i2] = preg_replace('#<img alt="(\w+)".+?>#','$1 ',$info[1][$i2]);
                    $items[$i][] = preg_replace_callback('#<span class="(.+?)">(.+?)</span>#im',array(get_class($this), 'descramble'),$info[1][$i2]);
                }
            }
            $this->cache[$site] = $items;
        }
        if ($section == 4 && $item == 5) 
        {
            return $this->cache[$site];
        } 
        elseif ($item == 5) 
        {
            return $this->cache[$site][$section];
        } 
        else 
        {
            return $this->cache[$site][$section][$item];
        }
    }
    function threeMothAlexa($site)
    {
    	$urlinfo=parse_url($site);
        $site=$urlinfo["host"];
    	$score=$this->stats($site,1);
    	return $score[2];
    }
}
//
//$alexa = new AB_Alexa();
//print_r($alexa->stats('http://www.pkphp.com'));
//print_r($alexa->stats('http://www.pkphp.com',1));
//print_r($alexa->stats('http://www.pkphp.com',1,1));
//print_r($alexa->threeMothAlexa('http://www.pkphp.com'));