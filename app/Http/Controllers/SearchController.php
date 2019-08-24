<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
	function index(Request $request){
    	$pages=0;
    	$items=array();
    	$keyword="";
    	$qualifiers=array();
    	$total_items=0;

    	return view('search',compact('pages','items','keyword','qualifiers','total_items'));
    }


    function search(Request $request){
    	$keyword = $request->input('keyword');
    	if($request->input('quilifiers')) $qualifiers=$request->input('quilifiers');
    	else $qualifiers=array();
    	if($request->input('page')) $current_page = $request->input('page');
    	else  $current_page=1;

    	$response=$this->getGihubRepositories($keyword,$qualifiers,$current_page);

    	if($response){
    		$total_items=$response->total_count;
    		$items=$response->items;
    	}
    	else {
    		$total_items=0;
    		$items=[];
    	}

    	// create language wise data
    	$languages=[];
    	if($items){
    		foreach($items as $item){
    			if(!isset($languages[$item->language])) 
    				$languages[$item->language]=1;
    			else $languages[$item->language]=$languages[$item->language] + 1;
    		}
    	}
    	arsort($languages);
    	
    	
    	$total_links=7;
    	//echo "<pre>"; print_r($items); exit;
    	$pages=$this->generatePagination($total_items, $current_page,$total_links,$keyword,$qualifiers);
    	return view('search',compact('pages','items','total_items','keyword','qualifiers','languages'));
    }

    /**
    * return github repositories
	* @param String $keyword
	* @param Array $qualifiers
	* @param Number $current_page
    */
    private function getGihubRepositories($keyword,$qualifiers,$current_page){
    	if(!$keyword){
    		return false;
    	}
    	$q=$keyword;
    	if(count($qualifiers) > 0){
    		foreach($qualifiers as $qualifier){
    			$q.='+in:'.$qualifier;
    		}
    	}
    	$client = new \GuzzleHttp\Client();
		$res = $client->get("https://api.github.com/search/repositories?q=$q&sort=stars&order=desc&page=".$current_page);


		if($res->getStatusCode() == 200) return json_decode($res->getBody()->getContents()); 
        return false;
    }

    /**
    * return pagination links
	* @param Numbers $total_items
	* @param Number $current_page
	* @param Number $total_link
	* @param String $keyword
	* @param Array $qualifiers
    */
    private function generatePagination($total_items,$current_page,$total_link,$keyword,$qualifiers){

    	$link_fragment=$right_fragments=(int)($total_link / 2);
    	if($total_items % 30 == 0)
    	{
    		$page=$total_items/30;
    	} 
    	else
    	{
    		$page=(int)($total_items / 30) + 1;
    	}

    	// since won't return more than 1000 result items
    	if($page > 34) $page = 34;

    	$links="<ul class='pagination'>";
    	$left_links=[];
    	$right_links=[];

    	for($i=1; $i <= $page; $i++){
    		if($i < $current_page) {
    			$left_links[]=$i;
    		}
    		if($i > $current_page) {
    			$right_links[]=$i;
    		}
    	}

    	$qulifier_uri="";
    	if(count($qualifiers) > 0){
    		foreach($qualifiers as $qualifier){
    			$qulifier_uri.="&quilifiers[]=".$qualifier;
    		}
    	}
    	// first link
    	if($link_fragment < count($left_links)) 
    	{
    		$links.='<li  class="page-item"><a class="page-link" 
				href="'.route('search','keyword='.$keyword.$qulifier_uri.'&page=1').'">First</a>';
		$links.='</li>';
    	}

    	$left_links=array_slice($left_links,-$link_fragment,$link_fragment);
    	$left_counter=count($left_links);
    	$diff=$link_fragment - $left_counter;
    	if($diff > 0) $right_fragments=$link_fragment + $diff;
    	$right_links=array_slice($right_links,0,$right_fragments);

    	

    	if(count($left_links) > 0)
    	{
    		foreach($left_links as $left_link){
    			$links.='<li class="page-item">';
    			$links.='<a class="page-link" 
    					href="'.route('search','keyword='.$keyword.$qulifier_uri.'&page='.$left_link).'">'.$left_link.'</a>';
    			$links.='</li>';
    		}
    	}

    	// curent link
    	$links.='<li  class="page-item active"><a class="page-link" 
				href="'.route('search','keyword='.$keyword.$qulifier_uri.'&page='.$current_page).'">'.$current_page.'</a>';
		$links.='</li>';


    	if(count($right_links) > 0)
    	{
    		foreach($right_links as $right_link){
    			
    			$links.='<li class="page-item">';
    			$links.='<a class="page-link" 
    					href="'.route('search','keyword='.$keyword.$qulifier_uri.'&page='.$right_link).'">'.$right_link.'</a>';
    			$links.='</li>';
    		}
    	}

    	// last link
    	if($page > 0 && $page > $right_link) {
    		$links.='<li  class="page-item"><a class="page-link" 
					href="'.route('search','keyword='.$keyword.$qulifier_uri.'&page='.$page).'">Last</a>';
			$links.='</li>';
    	}


    	return $links."</ul>";
    }
}
