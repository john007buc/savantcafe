<?php
namespace John\ArticleBundle\MyClasses;

class Pagination
{
	public $items_per_page;
	public $page_number;
	public $links_class_name;
	private $page;
    private $phpPage;
    private $divResponseId;
    private $rewriteUrl;
    private $query_string;
	
	function __construct($options){
		$items_count= (array_key_exists('items_count',$options))?$options['items_count']:0;


        if(array_key_exists('items_per_page',$options) && $options['items_per_page']!=0 )
            $this->items_per_page=$options['items_per_page'];

        else
            exit();


		
		if($this->items_per_page!=0 && $items_count>=0)
		$this->page_number=ceil($items_count/$this->items_per_page);
		else
		exit();

         $this->phpPage=(array_key_exists('php_page',$options))?$options['php_page']:null;

        $this->divResponseId=(array_key_exists('divResponseId',$options))?$options['divResponseId']:null;

		$this->links_class_name=(array_key_exists('class_name',$options))?$options['class_name']:null;
        $this->rewriteUrl=(array_key_exists('rewrite_url',$options))?$options['rewrite_url']:null;
		//$this->page=(isset($_GET['ID'])&& is_numeric($_GET['ID']))?($_GET['ID']):1;
        $this->page=(array_key_exists('current_page',$options)&&!is_null($options['current_page']))?$options['current_page']:1;
        $this->query_string=(array_key_exists('query_string',$options))?$options['query_string']:null;

	}

	
	function getLimits()
	{
		
		$limits=array();
		$limits[0]=($this->page-1)*$this->items_per_page;
		$limits[1]=$limits[0]+$this->items_per_page;
		return $limits;
	}
	
	function getLinks($ajax_send=null)
	{
		$current_page=($this->page<1)?1:($this->page>$this->page_number)?$this->page_number:$this->page;
		
		if($this->page_number<=10)
		{
		  $start_page=1;
		  $end_page=$this->page_number;
          if($ajax_send==true)
              return $this->links($start_page,$end_page,$ajax_send);
            else
		  return $this->links($start_page,$end_page);
		}else 
		{
			
			$start_page=(($current_page-5)<1)?1:($current_page-5);
			
			$end_page=($start_page+9)>$this->page_number?$this->page_number:($start_page+9);
			if($end_page==$this->page_number)
				$start_page=$this->page_number-9;
			
			/*if($start_page<4)
			$end_page=10;
			else
			{
				$end_page=(($current_page+4)>$this->page_number)?$this->page_number:($current_page+4);
				
				//if($end_page==$this->page_number)
				//$start_page=$this->page_number-10;
			}	*/


            if($ajax_send==true)
                return $this->links($start_page,$end_page,$ajax_send);
            else
			
			 return $this->links($start_page,$end_page);
		}
		
		
		
	}
	
	function links($from,$to,$ajax=null)
	{
        if($this->page_number>1)
        {
            if(isset($this->links_class_name)&& !empty($this->links_class_name))
            $link="<div class='{$this->links_class_name}'>";
            else
            $link="<div>";
            if($this->page>1)
            {

                if(!is_null($ajax))
                    $link.="<a href='#' class='first' onclick=\"processAjax('{$this->divResponseId}','GET','{$this->phpPage}?ID=1');return false;\">First</a> &nbsp;
                   <a href='#' onclick=\" processAjax('{$this->divResponseId}','GET','{$this->phpPage}?ID=".($this->page-1)."');return false;\">Previous</a> &nbsp;";

                else if(!is_null($this->rewriteUrl))
                    $link.="<a href='".$this->rewriteUrl."/1{$this->query_string}' class='first'>First</a> &nbsp;
                    <a href='".$this->rewriteUrl."/".($this->page-1)."{$this->query_string}'>Previous</a> &nbsp;";
                else
                     $link.="<a href='".$_SERVER['PHP_SELF']."?ID=1' class='first'>First</a> &nbsp;
                  <a href='".$_SERVER['PHP_SELF']."?ID=".($this->page-1)."'>Previous</a> &nbsp;";





            }



            for($i=$from;$i<=$to;$i++)
            {

                if(!is_null($ajax))
                  $link .= "<a href='#' onclick=\" processAjax('{$this->divResponseId}','GET','{$this->phpPage}?ID={$i}');return false; \">{$i}</a>";
                else if(!is_null($this->rewriteUrl))
                    $link .=($i==$this->page)?"<a href='#' id='current-page' onlclick='return false;'>{$i}</a>": "<a href='".$this->rewriteUrl."/{$i}{$this->query_string}'>{$i}</a>";
                else
                    $link .= "<a href='".$_SERVER['PHP_SELF']."?ID={$i}'>{$i}</a>";

            }

            if($this->page<$this->page_number)
            {
                if(!is_null($ajax))
                    return $link."&nbsp;<a href='#' onclick=\" processAjax('{$this->divResponseId}','GET','{$this->phpPage}?ID=".($this->page+1)."');return false;\">Next</a>
                    <a href='#' onclick=\" processAjax('{$this->divResponseId}','GET','{$this->phpPage}?ID=".($this->page_number)."');return false;\" class= 'last'>Last</a> &nbsp; <span> Page {$this->page} of {$this->page_number}</span></div>";
                else if(!is_null($this->rewriteUrl))
                    return $link."&nbsp;<a href='".$this->rewriteUrl."/".($this->page+1)."{$this->query_string}'>Next</a>
                    <a href='".$this->rewriteUrl."/".($this->page_number)."{$this->query_string}' class= 'last'>Last</a> &nbsp; <span>Page {$this->page} of {$this->page_number}</span></div>";
                else
                  return $link."&nbsp;<a href='".$_SERVER['PHP_SELF']."?ID=".($this->page+1)."'>Next</a>
                  <a href='".$_SERVER['PHP_SELF']."?ID=".($this->page_number)."' class= 'last'>Last</a> &nbsp; <span>Page {$this->page} of {$this->page_number}</span></div>";
            }
            else
            return $link."</div>";
        }
	}
}


?>

