<?


class Model{
	
	const DIR='/Volumes/p3popular';
	#const DIR='/home/ftp/Radio/p3popular';
	const MINSIZE = '81095911';
	
	public $latestBuild;
	
	public $show;
	
	public function getShowWithLS(){
	
		
		chdir(self::DIR);
		$ls = `ls -l p3Pop*`;
		$file = array_slice(explode("\n",$ls),-21,20);
		foreach($file as $f){
			
			$matches = array();
			$pattern = '/.*(.{3} .{1} .{2}:.{2}) p3Populär-(.{10})-(.{1})\.mp3/';
			$pattern = '/.*(\S{3} \S{1} \S{2}:\S{2}).*/';
			preg_match($pattern,$f,$matches);
			echo '<pre>';var_dump($matches);echo '</pre>';die();
			$show[] = array(
				'title' => '2009-09-18 del 1',
				'url'	=> 'http://p3popular.sipola.se/p3Populär-2009-09-18-1.mp3',
				'length' => '83593008',
				'pubDate' => date_create('2009-09-18 13:15:00')->format(DATE_RSS)
				
			);
		}
		
		
	}
	
public function getShowM4A(){
		
		if(! is_dir(self::DIR)){
			throw new Exception('directory '.self::DIR.' does not exist');
		}
		chdir(self::DIR);
		$dir = scandir(self::DIR);
		
		foreach($dir as $file){
			$start = substr(strtolower($file),0,5);
			if($start != 'p3pop'){
				continue;
			}
			$size = filesize(self::DIR.'/'.$file);
			if($size < self::MINSIZE){
				continue;
			}
			$mtime = filemtime(self::DIR.'/'.$file);
			if($mtime > $this->latestBuild){
				$this->latestBuild = $mtime;
			}
			$tmp = explode("-",$file);
			$year=$tmp[1];
			$month=$tmp[2];
			$day=$tmp[3];
			$part = substr($tmp[4],0,1);
			
			switch($part){
				case 1:
					$hour = 10;
					break;
				case 2:
					$hour = 11;
					break;
				case 'm':
					$hour = 13;
					break;
				default:
					throw new Exception('unknown part');
					break;
			}
			$show[] = array(
				'title' => "$year-$month-$day del $part",
				'url'	=> "http://p3popular.sipola.se/$file",
				'length' => $size,
				'pubDate' => date_create("$year-$month-$day $hour:00:00")->format(DATE_RSS)
				#'pubDate' => date_create("@$mtime")->format(DATE_RSS)
			);
			#print_r($show);die();
			
		}
		#print_r($dir);
		
		$this->show = $show;
		return;
		
	}
	
	
	public function getShow(){
		
		if(! is_dir(self::DIR)){
			throw new Exception('directory '.self::DIR.' does not exist');
		}
		chdir(self::DIR);
		$dir = scandir(self::DIR);
		
		foreach($dir as $file){
			$start = substr(strtolower($file),0,5);
			if($start != 'p3pop'){
				continue;
			}
			$size = filesize(self::DIR.'/'.$file);
			if($size < self::MINSIZE){
				continue;
			}
			$mtime = filemtime(self::DIR.'/'.$file);
			if($mtime > $this->latestBuild){
				$this->latestBuild = $mtime;
			}
			$tmp = explode("-",$file);
			$year=$tmp[1];
			$month=$tmp[2];
			$day=$tmp[3];
			$part = substr($tmp[4],0,1);
			
			switch($part){
				case 1:
					$hour = 10;
					break;
				case 2:
					$hour = 11;
					break;
				case 'm':
					$hour = 13;
					break;
				default:
					throw new Exception('unknown part');
					break;
			}
			$show[] = array(
				'title' => "$year-$month-$day del $part",
				'url'	=> "http://p3popular.sipola.se/$file",
				'length' => $size,
				'pubDate' => date_create("$year-$month-$day $hour:00:00")->format(DATE_RSS)
				#'pubDate' => date_create("@$mtime")->format(DATE_RSS)
			);
			#print_r($show);die();
			
		}
		#print_r($dir);
		
		$this->show = $show;
		return;
		
	}
	
}


class Controller{
	
	
	public function index(){
		
		$m = new Model();
		$v = new View();
		$m->getShow();
		$v->render($m);
	}
	
	
}


class View{
	
	public function render(Model $model){
		$show = $model->show;
		$build = date_create('@'.$model->latestBuild)->format(DATE_RSS);
		#$pub = date_create('now')->format(DATE_RSS);
		//$pub = date_create('@'.$model->latestBuild)->format(DATE_RSS);
		$xml = new DOMDocument('1.0', 'UTF-8');
		// we want a nice output
		$xml->formatOutput = true;
		/*
		 * <hrxml xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		 *	xsi:noNamespaceSchemaLocation="file:LonXML.xsd">
		 */
		$rssNode = $xml->appendChild( $xml->createElement('rss') );

		$rssNode->setAttribute('version',  '2.0' );
		$rssNode->setAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
		
		$channel = $rssNode->appendChild( $xml->createElement('channel') );
		
		$channel->appendChild( $xml->createElement('title','P3 Populär') );
		$channel->appendChild( $xml->createElement('description','P3 Populär podcast') );
		$channel->appendChild( $xml->createElement('link','http://p3popular.sipola.se/podcastFeed/') );
		$channel->appendChild( $xml->createElement('language','sv-se') );
		$channel->appendChild( $xml->createElement('copyright','Sveriges Radio') );
		$channel->appendChild( $xml->createElement('lastBuildDate',$build) );
		//$channel->appendChild( $xml->createElement('pubDate',$pub) );
		
		$channel->appendChild( $xml->createElement('itunes:author','zippo@sovjet.sipola.se') );
		$channel->appendChild( $xml->createElement('itunes:subtitle','Ripped podcast') );
		$channel->appendChild( $xml->createElement('itunes:explicit','no') );
		$image = $channel->appendChild( $xml->createElement('itunes:image'));
		$image->appendChild( $xml->createElement('title','P3 Populär'));
		$image->appendChild( $xml->createElement('link','http://www.sr.se/sida/default.aspx?ProgramId=2785'));
		$image->appendChild( $xml->createElement('url','http://www.sr.se/diverse/images/sr_14_90_90.jpg'));
		
		$image = $channel->appendChild( $xml->createElement('itunes:image'));
		$image->setAttribute('href','http://www.sr.se/diverse/images/sr_14_300_300.jpg');
		
		$category = $channel->appendChild( $xml->createElement('itunes:category'));
		$category->setAttribute('text','Technology');
		
		foreach($show as $s){
			$item = $channel->appendChild( $xml->createElement('item') );
			$item->appendChild( $xml->createElement('title',$s['title']) );
			$item->appendChild( $xml->createElement('link','http://sr.se/p3popular') );
			$item->appendChild( $xml->createElement('guid',$s['url']) );
			$item->appendChild( $xml->createElement('description','P3 Populär '.$s['title']) );
			$enc = $item->appendChild( $xml->createElement('enclosure') );
			$enc->setAttribute('url',$s['url']);
			$enc->setAttribute('length',$s['length']);
			$enc->setAttribute('type','audio/mpeg');
			$item->appendChild( $xml->createElement('category','Podcasts') );
			$item->appendChild( $xml->createElement('pubDate',$s['pubDate']) );
			
		}
		header('Content-Type: application/xml');
		echo $xml->saveXML();
		#error_log($xml->saveXML());
	}
}


$c = new Controller();
#echo '<pre>';
$c->index();
#echo '</pre>';
