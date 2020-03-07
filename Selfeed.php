<?php
	class data_miner
	{
		public function get_meteo()
		{
			try
			{
				$meteo_page = file_get_contents("http://www.meteofrance.com/previsions-meteo-france/marseille/13000","r");				
			}
			catch (Exception $e)
			{
				echo "Problèmes de connexion: ".$e->getMessage()."\n";
			}
			
			$temperature = [];
			preg_match_all('#[0-9]{1,}°C [a-zA-Z]{8}#', $meteo_page, $temperature);
			$temperature = $temperature[0];
			
			
			$preg_sky = [];
			$sky = [];
			preg_match_all('#title="[^D|^m][a-zA-ZéèÉ]+"#', $meteo_page, $preg_sky);
			
			$preg_day = [];
			$day = []; 
			preg_match_all("#<a>[a-zA-Z]{3} [0-9]{2}</a>#", $meteo_page, $preg_day);
			
			for($count=0;$count<count($preg_sky[0]);$count++)
			{
				if($count <= 13)
				{
					array_push($day,preg_replace('#<a>([a-zA-Z]{3} [0-9]{2})</a>#', "$1", $preg_day[0][$count]));
					array_push($sky,preg_replace('#title="(Ensoleillé|Éclaircies|Pluie|Averses|rafales)+"#', "$1", $preg_sky[0][$count]));
				}
			}
			
			$total = [];
			for($count=0;$count<count($sky);$count++)
			{
				array_push($total, [$day[$count],$sky[$count], $temperature[$count],$temperature[$count+1]]);
			}
			
			return $total;
		}
		
		public function get_wiki_random()
		{
			try
			{
				$wiki_random_page = file_get_contents("https://fr.wikipedia.org/wiki/Sp%C3%A9cial:Page_au_hasard","r");				
			}
			catch (Exception $e)
			{
				echo "Problèmes de connexion: ".$e->getMessage()."\n";
			}
			
			$name = [];
			preg_match("#<title>(.+)</title>#", $wiki_random_page, $name);
			$name = $name[1];
			
			$url = [];
			preg_match('#https:\/\/fr\.wikipedia\.org\/wiki\/[a-zA-Z_0-9]+#', $wiki_random_page, $url);
			$url = $url[0];
			
			$preg_toc= [];
			$summary = [];
			preg_match_all('#<li class="toclevel-1 (.+) href="\#[a-zA-Zéèàçîôâêïëäö_\'É]{3,}[a-zA-Zéèàçîôâêïëäö_\']{3,}#',$wiki_random_page, $preg_toc);
			
			
			for($count=0;$count<count($preg_toc[0]);$count++)
			{
				array_push($summary,(preg_replace('#<li class="toclevel-1 tocsection-[0-9]{1,}"><a href="\#(.+)#', "$1", $preg_toc[0][$count])));
			}
			
			return [$url, $summary, $name];
		}
	}
	
	$selfeed= new data_miner();
?>